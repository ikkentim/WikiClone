<?php namespace Ikkentim\WikiClone\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;

class WebhookController extends Controller {

    function __construct()
    {
        $this->middleware('verify.webhook');
        $this->middleware('gollum.webhook');
    }

    public function trigger()
    {
        if (strtolower(Input::get('repository.full_name')) === strtolower(config('wikiclone.repository')))
        {
            Artisan::call('wiki:update');
        }
        else
        {
            return response('Unauthorized action. (wrong repository)', 401);
        }
    }
}