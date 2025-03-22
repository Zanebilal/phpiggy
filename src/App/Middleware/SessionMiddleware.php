<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use App\Exceptions\SessionException;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {
        ## chek if session has already started (if true we can not activate a new one)
        if(session_status()===PHP_SESSION_ACTIVE){
            throw new SessionException("Sesiion already active");
        }        
        
        /*check if data has already sent to the browser 
        (if true we can not activate a new one because by sending content ,
        headers can not be configured [session uses headers] so session can not be activated)*/
        if(headers_sent($filename, $line)){
            ## this arg give us the exact location where th content was rendered eliar
            throw new SessionException("Header already sent. Consider enabling output buffering . data outputted from{$filename} - line: {$line}");
        }
        
        ## to prevent session Hijacking
        session_set_cookie_params([

            ## prevent cookies from being sent on unsecure connections
            'secure' => $_ENV['APP_ENV'] === "producation",

            ## prevent javascript from accessing the cookie
            'httponly' => true,

            ## allow cookies to be accesebale to our site (if user visite our site from external link the cookie will not be set)
            'samesite' => 'lax'
        ]);
    
        session_start();

        $next();

        session_write_close();

    }
}