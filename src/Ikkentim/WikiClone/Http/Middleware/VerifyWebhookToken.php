<?php namespace Ikkentim\WikiClone\Http\Middleware;

use Closure;

class VerifyWebhookToken {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $signature = explode('=', $request->header('X-Hub-Signature'));
        if (count($signature) != 2)
        {
            return response('Unauthorized action. (no signature)', 401);
        }
        if ($signature[0] !== 'sha1')
        {
            return response('Unauthorized action. (invalid signature hash type)', 401);
        }
        $hash = hash_hmac('sha1', file_get_contents('php://input'), config('wikiclone.webhook_token'));
        if ($hash != $signature[1])
        {
            return response('Unauthorized action. (invalid signature hash)', 401);
        }

        return $next($request);
    }
}