<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    |
    | Here you can set the repository the site will be mirroring. If
    | 'repository_type' is 'github_wiki' or 'github_repository' this value
    | should be formatted as 'URL_OR_ORGANIZATION/REPOSITORY'; otherwise the
    | full repository URL should be used.
    |
    */

    'repository'        => 'ikkentim/wikiclone',

    /*
    |--------------------------------------------------------------------------
    | Repository type
    |--------------------------------------------------------------------------
    |
    | Here you can set the type of repository the site will be mirroring.
    | Supported values: 'github_wiki', 'github_repository', 'repository'.
    |
    */

    'repository_type'   => 'github_wiki',

    /*
    |--------------------------------------------------------------------------
    | Use tags?
    |--------------------------------------------------------------------------
    |
    | Here you can set whether the documentation for each tag should be
    | downloaded.
    |
    */

    'tags'              => false,

    /*
    |--------------------------------------------------------------------------
    | Default tag
    |--------------------------------------------------------------------------
    |
    | Here you can set the tag of which the documentation is displayed if no
    | tag has been specified in the URL. This value only has effect if 'tags'
    | is set to true.
    |
    */

    'default_tag'       => 'master',

    /*
    |--------------------------------------------------------------------------
    | Edit tag
    |--------------------------------------------------------------------------
    |
    | Here you can set the name of the branch which is editable on GitHub if
    | repository_type is set to github_repository. No edit link will be
    | displayed when:
    | - Documentation is viewed with a different tag or branch name.
    | - This value is null.
    | - repository_type is not set to github_wiki or github_repository.
    |
     */

    'edit_tag'          => 'master',

    /*
    |--------------------------------------------------------------------------
    | Branches whitelist
    |--------------------------------------------------------------------------
    |
    | Here you can set the branches to include when 'tags' is true. If the
    | value is null, all the branches are included.
    |
    */

    'branches_whitelist'         => null,

    /*
    |--------------------------------------------------------------------------
    | Branches blacklist
    |--------------------------------------------------------------------------
    |
    | Here you can set the branches to exclude when 'tags' is true.
    |
    */

    'branches_blacklist'         => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Tags whitelist
    |--------------------------------------------------------------------------
    |
    | Here you can set the tags to include when 'tags' is true. If the
    | value is null, all the tags are included.
    |
    */

    'tags_whitelist'         => null,

    /*
    |--------------------------------------------------------------------------
    | Tags blacklist
    |--------------------------------------------------------------------------
    |
    | Here you can set the tags to exclude when 'tags' is true.
    |
    */

    'tags_blacklist'         => [
        '0.2',
        '0.1',
    ],

    /*
    |--------------------------------------------------------------------------
    | URL prefix
    |--------------------------------------------------------------------------
    |
    | Here you can set the URL prefix used within the routes. If set to /wiki,
    | requests to example.com/wiki/* will route to the documentation served
    | from this mirror. Use null if you wish to manually add the routes.
    |
    */

    'url_prefix'        => '',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Here you can set the middleware the WikiClone routes should use.
    |
    */

    'middleware'        => [
        // 'web'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default page
    |--------------------------------------------------------------------------
    |
    | Here you can set the path used as the default page. If no page has been
    | specified in the url this page will be displayed. This is basically
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

    'storage_provider'  => 'wiki',

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
        '<code>' => '<code class="hljs">'
    ]

];
