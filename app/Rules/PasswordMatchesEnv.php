<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordMatchesEnv implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // return $value === env('PASSWORD', 'S1@pundip$#@!');
        return $value === env('PASSWORD', 'mysecretpassword');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Password untuk membuka asesmen ini tidak cocok';
    }
}
