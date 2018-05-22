WikiClone
=========
[![Packagist](https://img.shields.io/packagist/dt/ikkentim/wikiclone.svg)](https://packagist.org/packages/ikkentim/wikiclone) [![Packagist](https://img.shields.io/packagist/v/ikkentim/wikiclone.svg)](https://packagist.org/packages/ikkentim/wikiclone)

Mirror your GitHub wiki with ease.

This Laravel 5 packages allows you to easily mirror the documentation on your GitHub wiki.
This package has a simple configuration file and one single customizable view to allow you
to have full control over the presentation of your documentation.

Installation
------------
- Add the package to your installation using composer `composer require ikkentim/wikiclone`.
- Add the service provider to your `config/app.php`. Under `providers` add:

``` php
        Ikkentim\WikiClone\WikiCloneServiceProvider::class,
```

- Publish the assets from this package using the `php artisan vendor:publish` command.
- Open the `config/wikiclone.php` and configure it according to your repository (see below for details)
- Set up a `wiki` storage provider (see below for details)
- You can now edit the `resources/views/vendor/wikiclone/documentation.blade.php` view to your liking.
- Run the command `php artisan wiki:update` to re-fetch the documentation from the GitHub wiki.

Storage
-------
By default the `wiki` storage provider will be used by WikiClone. You can create this storage provider under `disks` in `config/filesystems.php`. For example:

```
'wiki' => [                                                                                                                        
    'driver' => 'local',                                                                                                           
    'root'   => storage_path('wiki'),                                                                                              
],   
```

If you wish to use a different storage provider, set its name to the `storage_provider` value in `config/wikiclone.php`.

Repository Types
----------------
WikiClone supports 3 repository types:

### GitHub Wiki
In order to have WikiClone mirror a GitHub wiki, configure your `wikiclone.php` as follows:
- Set `repository` to the repository in question, e.g.: `"ikkentim/wikiclone"`
- Set `repository_type` to `"github_wiki"`
- Set `tags` to `false`
- Set `edit_tag` to `"master"` if you wish for "Edit this page on GitHub" buttons to appear on the documentation page, otherwise set this value to `null`

In order to automatically update the WikiClone website after each edit, you can configure a webhook:
  - On the repository's page, click on `Settings` > `Webhooks & services` > `Add webhook`
  - Under `Payload URL` enter the URL of your WikiClone website
  - Under `Secret` enter your webhook secret as configured in your wikiclone configuration file. By default this is set to your `APP_KEY` from your `.env` file
  - Under 'Which events would you like to trigger with this webhook?' select `Let me select individual events` and uncheck `Push` and check `Gollum`
  
### GitHub Repository
In order to have WikiClone mirror an entire GitHub repository containing markdown files, configure your `wikiclone.php` as follows:
- Set `repository` to the repository in question, e.g.: `"ikkentim/wikiclone"`
- Set `repository_type` to `"github_repository"`
- Set `tags` to `true` if you wish to host multiple versions of the documenation, otherwise set this value to `false`
- Set `default_tag` to the branch or tag name you wish to be displayed by default (usually `master` or a specific version)
- Set `edit_tag` to the branch for which an "Edit this page on GitHub" button should appear on the documentation page, set this to `null` if no edit button should appear
- Set `default` to the name of the markdown file which should appear as the homepage of the WikiClone site, e.g. `"Home"` if `Home.md` represents the homepage

In order to automatically update the WikiClone website after each edit, you can configure a webhook:
  - On the repository's page, click on `Settings` > `Webhooks & services` > `Add webhook`
  - Under `Payload URL` enter the URL of your WikiClone website
  - Under `Secret` enter your webhook secret as configured in your wikiclone configuration file. By default this is set to your `APP_KEY` from your `.env` file
  - Under 'Which events would you like to trigger with this webhook?' select `Let me select individual events` and check `Pushes`, `Branch or tag creation` and `Branch or tag deletion`
  
### Any Repository
Follow the same steps as for "GitHub Repository", except:
- Set `repository` to the full repository URL, e.g.: `https://mygitserver.com/my/repository.git`
- You cannot add webhooks. You must use `php artisan wiki:update` every time you update the contents of the wiki.

Tags and Branches
-----------------
If `tags` is set to `true` in the configuration file, multiple tags and branches can be mirrored, for example to reflect the documentation of different versions of your product. Please note this feature can only be used for repositories of type `"github_repository"` or `"repository"`.

### Blacklisting
If you wish for certain branches or tags to not be mirrored, you can add them to the `branches_blacklist` or `tags_blacklist` in your configuration file.

### Whitelisting
If you wish for only certain branches or tags to be mirrored. You can set an array as the value of the `branches_whitelist` and `tags_whitelist`:
- If `tags_whitelist` is `null`, all tags are mirrored;
- If `tags_whitelist` is an empty array `[]` no tags are mirrored;
- If `tags_whitelist` is `["0.1"]` only tag `0.1` is mirrored.

The same goes for `branches_whitelist`.

URLs and routes
---------------

### URL prefix
If you host other content on your Laravel web application, you can set a prefix for the routes using the `url_prefix` configuration value. If set to `/wiki`, requests to `https://example.com/wiki/Example-Page` will return the wiki page for `Example Page`.

### Manual routes
If you wish to manually set the routes, set the `url_prefix` configuration value to `null` and add the following routes to your routes file:

```
Route::post("/wiki, WebhookController::class . "@trigger");                
Route::get("/wiki/{path?}", DocumentationController::class . "$dc@index")->where('path', '(.*)');                        
```

### Middleware
If you wish for the WikiClone rotues to be using some middleware, add the middleware names to the `middleware` array in the configuration file.
