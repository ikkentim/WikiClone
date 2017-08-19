<?php namespace Ikkentim\WikiClone;

class GitHubUrls {
    public static function getURL()
    {
        return 'https://github.com/';
    }

    public static function getRepositoryURL($repository)
    {
        return static::getURL() . $repository . '/';
    }

    public static function getReleasesURL($repository)
    {
        return static::getRepositoryURL($repository) . 'releases/';
    }

    public static function getIssuesURL($repository)
    {
        return static::getRepositoryURL($repository) . 'issues/';
    }

    public static function getWikiURL($repository, $page = null)
    {
        return static::getRepositoryURL($repository) . 'wiki/' . $page;
    }

    public static function getWikiEditURL($repository, $page)
    {
        return static::getWikiURL($repository, $page) . '/_edit';
    }
}