<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

## flashing is a concept in programming where data is deleted after a single request (in this case errers should only appeare once)
class FlashMiddleware implements MiddlewareInterface
{
    ## injecting errors into a template
    public function __construct(private TemplateEngine $view)
    {
    }
    public function process(callable $next)
    {   
        ## add data (errors in this case) whatever template gets rendred or needs them
        $this->view->addGlobal('errors', $_SESSION['errors'] ?? []); ## if a page dont have errors the value will be an empty array

        ## destroy the errors messeges after they been sent to the template to avoid senting errors to all pages
        unset($_SESSION['errors']);

        ## add the submition data to a session to avoid losing it whene fiels validation (avoiding submiting the correct data again)
        $this->view->addGlobal('oldFormData', $_SESSION['oldFormData'] ?? []);

        unset($_SESSION['oldFormData']);

        $next();
    }
}