<?php namespace Ikkentim\WikiClone\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationController extends Controller {
    public function index($uri = null)
    {
        $disk = Storage::disk(config('wikiclone.storage_provider'));

        if (empty($uri) || $uri == '/')
        {
            $uri = config('wikiclone.default');
        }

        // Find the title of the page
        $uri = strtolower($uri);
        $uri = collect($disk->files())
            ->filter(function ($in) use ($uri)
            {
                return strtolower($in) == strtolower($uri);
            })
            ->first();

        // Check for existance of the requested documentation page
        if (!$uri || !$disk->exists($uri))
        {
            throw new NotFoundHttpException();
        }

        return view('wikiclone::documentation')
            ->with('title', str_replace('-', ' ', $uri))
            ->with('uri', $uri)
            ->with('content', $disk->get($uri))
            ->with('sidebar', $disk->exists('_Sidebar') ? $disk->get('_Sidebar') : null)
            ->with('footer', $disk->exists('_Footer') ? $disk->get('_Footer') : null);
    }
}