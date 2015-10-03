<?php
Route::post(config('wikiclone.url_prefix'), '\Ikkentim\WikiClone\Http\Controllers\WebhookController@trigger');
Route::get(config('wikiclone.url_prefix') . '/{page?}', '\Ikkentim\WikiClone\Http\Controllers\DocumentationController@index')
    ->where('page', '(.*)');