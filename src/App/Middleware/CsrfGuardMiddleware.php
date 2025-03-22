<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{   
    ## validating the token
    public function process(callable $next)
    {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);
        $validMethods = ['POST', 'PATCH', 'DELETE'];

        if(!in_array($requestMethod, $validMethods)){
            $next();
            return;
        }

        ## check if the token from the submition does not mach the token submited with the forn
        if($_SESSION['token'] !== $_POST['token']){
            redirectTo('/login');
        }

        unset($_SESSION['token']);

        $next();




        
    }
}