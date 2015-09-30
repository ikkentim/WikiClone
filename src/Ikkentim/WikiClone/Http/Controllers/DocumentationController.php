<?php namespace Ikkentim\WikiClone\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationController extends Controller {
    public function index($fileName = null)
    {
        $disk = Storage::disk(config('wikiclone.storage_provider'));

        if (empty($fileName) || $fileName == '/')
        {
            $fileName = config('wikiclone.default');
        }

        // Find the title of the page
        $fileName = strtolower($fileName);
        $fileName = collect($disk->files())
            ->filter(function ($in) use ($fileName)
            {
                return strtolower($in) == strtolower($fileName);
            })
            ->first();

        // Check for existance of the requested documentation page
        if (!$fileName || !$disk->exists($fileName))
        {
            throw new NotFoundHttpException();
        }

        return view('wikiclone::documentation')
            ->with('title', str_replace('-', ' ', $fileName))
            ->with('fileName', $fileName)
            ->with('content', $disk->get($fileName))
            ->with('sidebar', $disk->exists('_Sidebar') ? $disk->get('_Sidebar') : null)
            ->with('footer', $disk->exists('_Footer') ? $disk->get('_Footer') : null);
    }
}