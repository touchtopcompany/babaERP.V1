<?php

namespace Modules\Subdomain\Rules;

use Illuminate\Contracts\Validation\Rule;

class WordCountRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $attribute;
    private int $expected;

    public function __construct(int $expected)
    {
        $this->expected = $expected;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;
        $trimmed = trim($value);
        $numWords = count(explode(' ', $trimmed));
        return $numWords <= $this->expected;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The ' . strtolower($this->attribute) . ' field must not exceed more than ' . $this->expected . ' word';
    }
}
