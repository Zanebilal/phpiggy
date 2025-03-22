<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    try {
      $next();
    } catch (ValidationException $e) {

      $oldFormData = $_POST;
      
      $excludedFields = ['password','confirmPassword'];

      ## if both arrays have sumilar keys, those keys excluded from the new array
      ## array_flip function flip the values ('password','confirmPassword')in the excudedFields array to keys
      $formattedFormData = array_diff_key($oldFormData, array_flip($excludedFields));


      ## storing errors in session because whene the user redirected to same page we lose error table,when validation fiels
      $_SESSION['errors'] = $e->errors;

      ## storing formdata in session
      $_SESSION['oldFormData'] = $formattedFormData;

      $referer = $_SERVER['HTTP_REFERER'];
      redirectTo($referer);
    }
  }
}