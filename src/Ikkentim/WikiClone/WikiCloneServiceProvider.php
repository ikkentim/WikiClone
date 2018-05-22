<?php namespace Ikkentim\WikiClone;

use Ikkentim\WikiClone\Http\Controllers\DocumentationController;
use Ikkentim\WikiClone\Http\Controllers\WebhookController;
use Ikkentim\WikiClone\Http\Middleware\VerifyWebhookToken;
use Ikkentim\WikiClone\Http\Middleware\GollumWebhook;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WikiCloneServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $router = $this->app['router'];

        $router->middleware('verify.webhook', VerifyWebhookToken::class);
        $router->middleware('gollum.webhook', GollumWebhook::class);

        if (!$this->app->routesAreCached())
        {
            if(config('wikiclone.url_prefix') !== null) {
                $middleware = config('wikiclone.middleware');

                if($middleware == null) {
                    $middleware = [];
                }
                Route::group(['middleware' => $middleware], function () {
                    $prefix = config('wikiclone.url_prefix');
                    $wc = WebhookController::class;
                    $dc = DocumentationController::class;

                    Route::post($prefix, "$wc@trigger");
                    Route::get("$prefix/{path?}", "$dc@index")
                        ->where('path', '(.*)');
                });
            }
        }

        $this->loadViewsFrom(__DIR__ . '/../..' . '/views', 'wikiclone');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDocsUpdater();

        $this->mergeConfigFrom(
            __DIR__ . '/../..' . '/config/wikiclone.php', 'wikiclone'
        );

        $this->publishes([
                             __DIR__ . '/../..' . '/views' => base_path('resources/views/vendor/wikiclone'),
                         ], 'views');
        $this->publishes([
                             __DIR__ . '/../..' . '/config/wikiclone.php' => config_path('wikiclone.php'),
                         ], 'config');
    }

    /**
     * Register the docs:update command.
     */
    private function registerDocsUpdater()
    {
        $this->app->singleton('command.wikiclone.update', function ($app)
        {
            return $app['Ikkentim\WikiClone\Console\Commands\WikiUpdateCommand'];
        });
        $this->commands('command.wikiclone.update');
    }
}
