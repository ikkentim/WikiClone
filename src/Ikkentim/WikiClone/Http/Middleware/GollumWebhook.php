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
        $event = $request->header('X-GitHub-Event');
        if ($event !== 'gollum' && $event != 'push' && $event != 'create' && $event != 'delete')
        {
            return response('Page not found', 404);
        }

        return $next($request);
    }
}
