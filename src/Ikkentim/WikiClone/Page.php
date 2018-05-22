<?php namespace Ikkentim\WikiClone;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Ikkentim\WikiClone\GitHubUrls;

class Page
{
    static function sanitizeName($name)
    {
        if (empty($name) || $name == '/') {
            $name = config('wikiclone.default');
        }

        // Find the title of the page
        return strtolower($name);
    }

    static function isValidTagName($tag)
    {
        return $tag == null || !str_contains($tag, ['\\', '/', '..']);
    }

    static function sanitizeTag($tag)
    {
        if (!config('wikiclone.tags')) {
            return null;
        }

        $tag = self::tagOrDefault($tag);

        if (!self::isValidTagName($tag)) {
            return config('wikiclone.default_tag');
        }

        return $tag;
    }

    static function toNameOnDisk($name, $tag = null)
    {
        if (!self::isValidTagName($tag)) {
            return null;
        }

        $name = self::sanitizeName($name);
        $tag = self::sanitizeTag($tag);

        if($tag !== null) {
            $name = $tag . '/' . $name;
        }

        $ret = collect(self::disk()->files($tag))
            ->first(function ($idx, $in) use ($name) {
                return strtolower($in) == strtolower($name);
            });


        $exp = explode('/', $ret);
        return $exp[sizeof($exp) - 1];
    }


    /**
     * @return FilesystemAdapter
     */
    public static function disk()
    {
        return Storage::disk(config('wikiclone.storage_provider'));
    }

    private static function existsInternal($name, $tag = null)
    {
        if ($tag == null) {
            return $name && self::disk()->exists($name);
        }

        return $name && self::disk()->exists($tag . '/' . $name);
    }

    static function exists($name, $tag = null)
    {
        $name = self::toNameOnDisk($name, $tag);

        return self::existsInternal($name, $tag);
    }

    static function hasTags()
    {
        return self::tags()->count() > 0;
    }

    static function tagOrDefault($tag = null)
    {
        if (!config('wikiclone.tags')) {
            return null;
        }

        if ($tag == null || !is_string($tag) || strlen($tag) == 0) {
            $tag = config('wikiclone.default_tag');
        }

        return $tag;
    }

    static function tags()
    {
        if (!config('wikiclone.tags')) {
            return [];
        }

        $tags = collect(self::disk()->directories());
        $fn = config('wikiclone.tags_sort');

        return $fn == null
            ? $tags
            : $tags->sort($fn)
                ->values();
    }

    static function tagExists($tag = null)
    {
        if (!config('wikiclone.tags')) {
            return $tag === null;
        }

        if ($tag != null) {
            return self::isValidTagName($tag) && self::disk()->exists($tag);
        } else {
            return config('default_tag') != null && self::tagExists(config('default_tag'));
        }
    }

    static function provide($name = null, $tag = null)
    {
        if($tag !== null && !config('wikiclone.tags')) {
            return null;
        }

        $tag = self::sanitizeTag($tag);

        if (!self::tagExists($tag)) {
            return null;
        }

        $name = self::toNameOnDisk($name, $tag);

        if (!self::existsInternal($name, $tag)) {
            return null;
        }

        if (!config('wikiclone.tags')) {
            return self::disk()->get($name);
        }
        return self::disk()->get($tag . '/' . $name);
    }

    static function editUrl($name = null, $tag = null)
    {
        $repoType = config('wikiclone.repository_type');
        $editTag = config('wikiclone.edit_tag');

        if($repoType !== 'github_wiki' && $repoType !== 'github_repository') {
            return null;
        }

        if($editTag == null) {
            return null;
        }

        if($tag !== null && !config('wikiclone.tags')) {
            return null;
        }

        $tag = self::sanitizeTag($tag);

        if($editTag != $tag && $repoType == 'github_repository') {
            return null;
        }

        if(!self::tagExists($tag)) {
            return null;
        }

        $name = self::toNameOnDisk($name, $tag);

        if (!self::existsInternal($name, $tag)) {
            return null;
        }

        switch($repoType) {
            case 'github_wiki':
                return GitHubUrls::getWikiURL(config('wikiclone.repository'), $name);
            case 'github_repository':
                return GitHubUrls::getFileURL(config('wikiclone.repository'), config('wikiclone.default_tag'), $name);
        }
    }
}
