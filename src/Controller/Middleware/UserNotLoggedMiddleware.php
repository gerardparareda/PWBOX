<?php

namespace PwBox\Controller\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserNotLoggedMiddleware{
    public function __invoke(Request $request, Response $response, callable $next){
        if(isset($_COOKIE['user_id'])){
            return $response->withStatus(302)->withHeader("Location", "/dashboard");
        }
        return $next($request, $response);
    }
}