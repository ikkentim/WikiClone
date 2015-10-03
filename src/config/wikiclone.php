<?php
return [

    /*
    |--------------------------------------------------------------------------
    | GitHub Wiki repository
    |--------------------------------------------------------------------------
    |
    | Here you can set the name of the repository the site will be mirroring
    | from GitHub. The value must be formatted 'username/repository'.
    |
    */

    'repository'        => 'ikkentim/wikiclone',

    /*
    |--------------------------------------------------------------------------
    | URL prefix
    |--------------------------------------------------------------------------
    |
    | Here you can set the URL prefix used within the routes. If set to /wiki,
    | requests to example.com/wiki/* will route to the documentation served
    | from this mirror.
    |
    */

    'url_prefix'        => '/wiki',
    /*
    |--------------------------------------------------------------------------
    | Default page
    |--------------------------------------------------------------------------
    |
    | Here you can set the path used as the default page. If no page has been
    | specified in the url this page will be displayed. Basically this is
    | the homepage of the website.
    |
    */
    'default'           => 'Home',

    /*
    |--------------------------------------------------------------------------
    | Documentation Storage Provider
    |--------------------------------------------------------------------------
    |
    | Here you can set the storage provider used to store the documentation.
    |
    */

    'storage_provider'  => 'local',

    /*
    |--------------------------------------------------------------------------
    | Webhook token
    |--------------------------------------------------------------------------
    |
    | Here you can set the key used by the GitHub webhook service. The value
    | should be set to a random string. It is used to securely sent update
    | notifications from the GitHub servers to this documentation site.
    |
    */

    'webhook_token'     => env('APP_KEY', 'MyWebhookToken'),

    /*
    |--------------------------------------------------------------------------
    | HTML replacements
    |--------------------------------------------------------------------------
    |
    | The generated HTML might not be exactly to your liking. You can customize
    | it by replacing certain elements to make it more easy for your
    | CSS (framework) to digest.
    |
    */

    'html_replacements' => [
        ['search' => '<table>', 'replace' => '<table class="table table-bordered table-striped">']
    ]

];