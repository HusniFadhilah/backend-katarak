<?php

namespace App\Rules;

use App\Models\Position;
use Illuminate\Contracts\Validation\Rule;

class InEnum implements Rule
{
    protected $row;
    protected $data;
    protected $attribute;

    /**
     * Create a new rule instance.
     *
     * @param  mixed  $row
     * @param  array  $data
     * @return void
     */
    public function __construct($data, $row = '')
    {
        $this->row = $row;
        $this->data = $data;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        return in_array(trim(strtolower($value)), array_map('strtolower', $this->data));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Isian ' . $this->attribute . ' ' . ($this->row ? 'baris ke-' . $this->row . ' ' : '') . 'harus bernilai ' . implode('/', $this->data)  . '.';
    }
}
