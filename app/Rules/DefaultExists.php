<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DefaultExists implements Rule
{
    protected $row;
    protected $data;
    protected $model;
    protected $name;
    protected $alias;
    protected $isCode;

    /**
     * Create a new rule instance.
     *
     * @param  mixed  $row
     * @param  mixed  $data
     * @param  mixed  $model
     * @param  string  $alias
     * @param  string  $name
     * @param  bool  $isCode
     * @return void
     */
    public function __construct($row, $data, $model, $alias, $name = 'name', $isCode = true)
    {
        $this->row = $row;
        $this->data = $data;
        $this->model = $model;
        $this->name = $name;
        $this->alias = $alias;
        $this->isCode = $isCode;
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
        $model = $this->model;
        if ($value) {
            if (is_numeric($value)) {
                $result = $model->where('id', $value)->first();
            } else {
                if ($this->isCode)
                    $result = $model->where('code', $value)->orWhere($this->name, $value)->first();
                else
                    $result = $model->where($this->name, $value)->first();
            }

            if (!$result)
                return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Isian ' . $this->alias . ' baris ke-' . $this->row . ' yaitu "' . $this->data . '" tidak ditemukan di database.';
    }
}
