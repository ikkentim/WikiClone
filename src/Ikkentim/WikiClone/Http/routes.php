<?php

Route::post('/', '\Ikkentim\WikiClone\Http\Controllers\WebhookController@trigger');
Route::get('/{page?}', '\Ikkentim\WikiClone\Http\Controllers\DocumentationController@index')
    ->where('page', '(.*)');