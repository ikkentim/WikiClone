<?php namespace Ikkentim\WikiClone\Http\Middleware;

use Closure;

class GollumWebhook {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('X-GitHub-Event') !== 'gollum')
        {
            return response('Page not found', 404);
        }

        return $next($request);
    }
}