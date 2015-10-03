<?php namespace Ikkentim\WikiClone\Console\Commands;

use Gitonomy\Git\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Ikkentim\WikiClone\MarkdownDoc;

class WikiUpdateCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wiki:update';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the contents of the wiki';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // Open file systems
        $disk = Storage::disk(config('wikiclone.storage_provider'));
        $repository = 'https://github.com/' . config('wikiclone.repository') . '.wiki.git';
        $localRepositoryPath = tempnam(sys_get_temp_dir(), 'repo');
        $parser = new MarkdownDoc;

        // Clone the wiki repository
        $this->info("Cloning into repository $repository...");
        unlink($localRepositoryPath);
        Admin::cloneTo($localRepositoryPath, $repository, false, ['--depth=1']);

        // Clear existing documentation
        $this->info("Clearing existing documentation");
        $disk->delete($disk->files());

        // Convert .md to .html
        $this->info("Converting documentation");
        foreach (scandir($localRepositoryPath) as $file)
        {
            if (!ends_with($file, '.md'))
            {
                continue;
            }

            // Convert markdown to html
            $html = $parser->transform(file_get_contents("$localRepositoryPath/$file"));
            if (strlen(config('wikiclone.url_prefix')))
            {
                $html = preg_replace('/<a href="((?!http:\/\/|https:\/\/|#).*)">(.*)<\/a>/', '<a href="'
                                                                                              . config('wikiclone.url_prefix')
                                                                                              . '/$1">$2</a>', $html);
            }

            foreach(config('wikiclone.html_replacements') as $set)
            {
                $html = str_replace($set['search'], $set['replace'], $html);
            }

            // Store the documentation
            $name = substr($file, 0, strlen($file) - strlen('.md'));
            $name = str_replace(' ', '-', $name);
            $name = str_replace('/', '', $name);
            $disk->put($name, $html);

            $this->info("Storing $name");
        }

        // Delete the repository
        $this->info("Deleting local repository");
        self::rrmdir($localRepositoryPath);
    }

    private static function rrmdir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        self::rrmdir($dir . "/" . $object);
                    }
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
