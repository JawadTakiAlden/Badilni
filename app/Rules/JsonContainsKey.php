<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonContainsKey implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $titleArray = json_decode($value, true);
        if (!isset($titleArray['en'])) {
            $fail('The title must contain an "en" key.');
        }
    }
}
