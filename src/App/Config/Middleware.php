<?php

declare(strict_types=1);

namespace App\config;

use Framework\App;
use App\Middleware\{TemplateDataMiddleware,ValidationExceptionMiddleware,SessionMiddleware,FlashMiddleware,
                    CsrfTokenMiddleware, CsrfGuardMiddleware};


function registerMiddleware(App $app)
{

    $app->addMiddleware(CsrfGuardMiddleware::class);
    $app->addMiddleware(CsrfTokenMiddleware::class);
    $app->addMiddleware(TemplateDataMiddleware::class);
    $app->addMiddleware(ValidationExceptionMiddleware::class);
    $app->addMiddleware(FlashMiddleware::class);
    $app->addMiddleware(SessionMiddleware ::class); /*the order is metter the session must be enabled 
    to make the exception class capble of storing errors and avoiding losing it ,(the last middleware gets excuted first) */
    
}