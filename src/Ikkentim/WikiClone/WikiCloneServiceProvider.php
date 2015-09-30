<?php namespace Ikkentim\WikiClone;

use Ikkentim\WikiClone\Http\Middleware\VerifyWebhookToken;
use Ikkentim\WikiClone\Http\Middleware\GollumWebhook;
use Illuminate\Support\ServiceProvider;

class WikiCloneServiceprovider extends ServiceProvider {
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