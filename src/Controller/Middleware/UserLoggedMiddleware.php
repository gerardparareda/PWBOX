<?php

namespace PwBox\Controller\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;

class UserLoggedMiddleware{
    public function __invoke(Request $request, Response $response, callable $next){
        if(!isset($_COOKIE['user_id'])){

            if ($request->getUri()->getPath() == '/profile'){
                return $response->withStatus(302)->withHeader("Location", "/logiasdasdasdn");
            } else {
                return $response->withStatus(302)->withHeader("Location", "/login");

            }
        }
        return $next($request, $response);
    }
}