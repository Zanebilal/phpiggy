<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class GestOnlyMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {

        ## check if the user loged in and redirect him to the login page to prevent them accessing unautherizes pages
        if(!empty($_SESSION['user'])){
            redirectTo('/');
        }

        $next();

    }
}