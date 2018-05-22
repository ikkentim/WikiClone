<?php namespace Ikkentim\WikiClone;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

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

        $name = $tag . '/' . $name;
        $ret = collect(self::disk()->files($tag))
            ->first(function ($in) use ($name) {
                return strtolower($in) == strtolower($name);
            });

        return array_last(explode('/', $ret));
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
            return true;
        }

        if ($tag != null) {
            return self::isValidTagName($tag) && self::disk()->exists($tag);
        } else {
            return config('default_tag') != null && self::tagExists(config('default_tag'));
        }
    }

    static function provide($name = null, $tag = null)
    {
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
}