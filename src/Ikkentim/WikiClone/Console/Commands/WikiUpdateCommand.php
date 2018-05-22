<?php namespace Ikkentim\WikiClone\Console\Commands;

use Gitonomy\Git\Admin;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Tag;
use Gitonomy\Git\Repository;
use Ikkentim\WikiClone\MarkdownDoc;
use Ikkentim\WikiClone\Page;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;

class WikiUpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'wiki:update {--force : Overwrite existing tags} {--delete= : Delete a specific tag, allowing it\'s documentation to be rewritten}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the contents of the wiki';
    /**
     * @var MarkdownDoc
     */
    protected $parser;
    /**
     * @var FilesystemAdapter
     */
    protected $disk;
    protected $localRepositoryPath;
    protected $repositoryUrl;
    /**
     * @var Repository
     */
    protected $repository;
    protected $tags;
    protected $url_prefix;
    protected $html_replacements;

    private function prepare()
    {
        switch (config('wikiclone.repository_type')) {
            case 'github_wiki':
                $this->repositoryUrl = 'https://github.com/' . config('wikiclone.repository') . '.wiki.git';
                break;
            case 'github_repository':
            case 'github_repo':
                $this->repositoryUrl = 'https://github.com/' . config('wikiclone.repository') . '.git';
                break;
            case 'repository':
            case 'repo':
                $this->repositoryUrl = config('wikiclone.repository');
                break;
        }

        $this->tags = config('wikiclone.tags');
        $this->url_prefix = config('wikiclone.url_prefix');
        $this->html_replacements = config('html_replacements');

        if (!is_bool($this->tags)) {
            $this->tags = false;
        }

        $this->parser = new MarkdownDoc;

        $this->disk = Page::disk();
        $this->localRepositoryPath = tempnam(sys_get_temp_dir(), 'repo');
    }

    private function cloneOptions()
    {
        if ($this->tags) {
            return [];
        } else {
            return ['--depth=1'];
        }
    }

    private function cloneRepository()
    {
        $this->info("Cloning into repository {$this->repositoryUrl}...");

        // Wipe previous clones from temp directory.
        unlink($this->localRepositoryPath);

        $this->repository = Admin::cloneTo($this->localRepositoryPath, $this->repositoryUrl, false,
            $this->cloneOptions());
    }

    private function deleteDocumentation($tag = null)
    {
        $this->info("Clearing existing documentation of " . $tag);

        $this->disk->delete($this->disk->files($tag));
    }

    private function parse($prefix = '')
    {
        // Convert .md to .html
        $this->info("Converting documentation");
        foreach (scandir($this->localRepositoryPath) as $file) {
            if (!ends_with($file, '.md')) {
                continue;
            }

            // Convert Markdown to HTML
            $contents = file_get_contents("{$this->localRepositoryPath}/$file");
            $html = $this->parser->transform($contents);

            // Prefix URLs
            if (strlen($this->url_prefix)) {
                $html = preg_replace('/<a href="((?!http:\/\/|https:\/\/|#).*)">(.*)<\/a>/', '<a href="'
                    . $this->url_prefix
                    . '/$1">$2</a>', $html);
            }

            // Replace HTML replacements.
            if (is_array($this->html_replacements)) {
                foreach ($this->html_replacements as $set) {
                    $html = str_replace($set['search'], $set['replace'], $html);
                }
            }

            // Store the documentation
            $name = substr($file, 0, strlen($file) - strlen('.md'));
            $name = str_replace(' ', '-', $name);
            $name = str_replace('/', '-', $name);
            $this->disk->put($prefix . $name, $html);

            $this->info("Storing $name");
        }
    }

    private function deleteRepository()
    {
        $this->info("Deleting local repository");
        self::rrmdir($this->localRepositoryPath);
    }

    private function isInWhitelist($list, $value)
    {
        $whitelist = config("wikiclone.{$list}_whitelist");
        $blacklist = config("wikiclone.{$list}_blacklist");

        if ($whitelist != null && !in_array($value, $whitelist)) {
            return false;
        }

        if ($blacklist != null && in_array($value, $blacklist)) {
            return false;
        }

        return true;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->prepare();

        if ($this->repositoryUrl == null || strlen($this->repositoryUrl) <= 0) {
            $this->error('Invalid wikiclone.repository or wikiclone.repository_type config value.');
            return;
        }

        $this->cloneRepository();

        if ($this->option('delete')) {
            $this->deleteDocumentation($this->option('delete'));
        }

        if (!$this->tags) {
            $this->info('Getting documentation from default branch...');

            $this->deleteDocumentation();
            $this->parse();
        } else {
            if ($this->option('force')) {
                $this->info('Forcibly deleted all files');
                foreach ($this->disk->files() as $file) {
                    $this->disk->delete($file);
                }
                foreach ($this->disk->directories() as $directory) {
                    $this->disk->deleteDirectory($directory);
                }
            }


            $refs = $this->repository->getReferences();

            /** @var Branch $branch */
            foreach ($refs->getBranches() as $branch) {
                $localName = $branch->getName();
                if ($branch->isRemote()) {
                    $localName = substr($localName, strlen('origin/'));
                    if ($refs->hasBranch($localName)) {
                        continue;
                    }
                }
                if (!Page::isValidTagName($localName) ||
                    !$this->isInWhitelist('branches', $localName)
                ) {
                    continue;
                }

                $this->info("Getting documentation from '{$localName}' branch...");

                $this->deleteDocumentation($localName);

                // Checkout
                if ($branch->isLocal()) {
                    $this->repository->getWorkingCopy()
                        ->checkout($branch);
                } else {
                    $this->repository->getWorkingCopy()
                        ->checkout($branch, $localName);
                }


                // Parse
                $this->parse($localName . '/');
            }

            /** @var Tag $tag */
            foreach ($refs->getTags() as $tag) {
                if (!Page::isValidTagName($tag->getName()) ||
                    Page::tagExists($tag->getName()) ||
                    !$this->isInWhitelist('tags', $tag->getName())
                ) {
                    continue;
                }

                $this->info("Getting documentation from '{$tag->getName()}' tag...");

                $this->deleteDocumentation($tag->getName());

                // Checkout
                $this->repository->getWorkingCopy()
                    ->checkout($tag);

                // Parse
                $this->parse($tag->getName() . '/');
            }
        }

        $this->deleteRepository();
    }

    /**
     * Recursively remove directory.
     * @param $dir
     */
    private static function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        self::rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
