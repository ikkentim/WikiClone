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
- Open the `config/wikiclone.php` configuration file and edit the `repository` value to the github repository you intend to mirror.
- Configure a webhook on the GitHub repository you're mirroring. 
  - On the repository's page, click on `Settings` > `Webhooks & services` > `Add webhook`
  - Under `Payload URL` enter the URL to the route to `WebhookController@trigger` you've added to your routes file a few steps back.
  - Under `Secret` enter your webhook secret as configured in your wikiclone configuration file. By default this is set to your `APP_KEY` from your `.env` file.
  - Under 'Which events would you like to trigger with this webhook?' select `Let me select individual events` and uncheck `Push` and check `Gollum`.
- You can now edit the `resources/views/vendor/wikiclone/documentation.blade.php` view to your liking.
- *(optional)* Run the command `php artisan wiki:update` to re-fetch the documentation from the GitHub wiki.
