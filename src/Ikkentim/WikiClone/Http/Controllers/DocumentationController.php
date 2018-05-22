<?php namespace Ikkentim\WikiClone\Http\Controllers;

use Ikkentim\WikiClone\Page;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentationController extends Controller
{
    public function index($path = null)
    {
        // Split path
        $pathComponents = explode('/', $path);

        if (count($pathComponents) > 2) {
            throw new NotFoundHttpException();
        }

        $tag = null;
        $fileName = null;
        if (count($pathComponents) == 2) {
            $tag = $pathComponents[0];
            $fileName = $pathComponents[1];
        } else {
            $fileName = $pathComponents[0];
        }

        if($tag === '') {
            $tag = null;
        }
        if($fileName === '') {
            $fileName = null;
        }

        // Find page
        $page = Page::provide($fileName, $tag);

        if ($page == null && $tag == null) {
            $tag = $fileName;
            $fileName = null;

            $page = Page::provide($fileName, $tag);
        }

        if ($page == null) {
            throw new NotFoundHttpException();
        }

        // Find proper name
        $fileName = Page::toNameOnDisk($fileName);

        // Show view
        return view('wikiclone::documentation')
            ->with('title', str_replace('-', ' ', $fileName))
            ->with('fileName', $fileName)
            ->with('editUrl', Page::editUrl($fileName, $tag))
            ->with('content', $page)
            ->with('sidebar', Page::provide('_Sidebar', $tag))
            ->with('footer', Page::provide('_Footer', $tag))
            ->with('tags', Page::tags())
            ->with('tag', Page::tagOrDefault($tag));
    }
}
