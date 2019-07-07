<?php

namespace App\Http\Middleware;

use Closure;

class SimpleAuth
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->authService->auth($request->headers->get('WWW-Authenticate'));

            return $next($request);
        } catch (\LogicException $e) {
            return response()->json([ "error" => $e->getMessage() ], 403);
        } catch (\Exception $e) {
            return response()->json([ "error" => 'something went wrong' ], 403);
        }
    }
}
