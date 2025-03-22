<?php

declare(strict_types=1);

namespace Framework;

use Framework\Contracts\RuleInterface;
use Framework\Exceptions\ValidationException;

class Validator
{
  private array $rules = [];

  public function add(string $alias, RuleInterface $rule)
  {
    $this->rules[$alias] = $rule;
  }

  public function validate(array $formData, array $fields)
  {
    $errors = [];

    foreach ($fields as $fieldName => $rules) {
      foreach ($rules as $rule) {

        $ruleParams = [];

        ## check if rule has parameters
        if(str_contains($rule,':')){

          ## extruct those parameters
          [$rule, $ruleParams] = explode(':',$rule); ## parameters appear after the colone chartacter

          ##converte parameters into array (ruleParams are strings)
          $ruleParams =explode(',',$ruleParams);
          
        }
        
        $ruleValidator = $this->rules[$rule];
        
        
        if ($ruleValidator->validate($formData, $fieldName, $ruleParams)) {
          continue;
        }
        
        
        $errors[$fieldName][] = $ruleValidator->getMessage(
          $formData,
          $fieldName,
          $ruleParams
        );
      }
    }

    if (count($errors)) {
      throw new ValidationException($errors);
    }
  }
}