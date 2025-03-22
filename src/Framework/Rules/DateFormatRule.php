<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class DateFormatRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {   
        ## verify the date if it is in the correct format the greping infos  for the inserted date 
        $parsedDate = date_parse_from_format($params[0], $data[$field]);

        ## if there is no errors and worning in the return table the format s correct
        return $parsedDate['error_count'] === 0 && $parsedDate['warning_count'] === 0 ;
    }


public function getMessage(array $data, string $field, array $params): string
   {
        return "Invalid date";
   }

        
}