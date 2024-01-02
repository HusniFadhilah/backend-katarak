<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Rule;

class UniqueRowRule implements Rule
{
    private $table;
    private $number;
    private $niceName;
    private $categoryField;
    private $categoryValue;
    private $id;

    public function __construct($table = '', $niceName = '', $id = '', $categoryField = '', $categoryValue = '')
    {
        $this->table = $table;
        $this->niceName = $niceName;
        $this->categoryField = $categoryField;
        $this->categoryValue = $categoryValue;
        $this->id = $id;
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
        $this->number = (explode('.', $attribute)[1]) + 1;
        $field  = explode('.', $attribute)[2];
        $result = DB::table($this->table)->select(DB::raw(1));
        $result->where($field, $value);
        if ($this->id)
            $result->where('id', '<>', $this->id);
        if ($this->categoryValue && $this->categoryField)
            $result->where($this->categoryField, $this->categoryValue);
        $result = $result->first();

        return empty($result); // edited here
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->niceName . ' baris ke-' . $this->number . ' sudah pernah digunakan sebelumnya';
    }
}
