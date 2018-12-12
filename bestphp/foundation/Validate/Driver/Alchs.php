<?php


namespace Validate\Driver;


use Validate\Contract\Validator;

/**
 * Class Alchs
 *
 * $options = 'alchs'|null
 *
 * @package Validate\Driver
 */
class Alchs extends Validator
{
    /**
     * The message template factory, the return value is automatically passed to $this->template (the binltin message template)
     *
     * Placeholder names MUST be delimited with a single opening brace { and a single closing brace }.
     * There MUST NOT be any whitespace between the delimiters and the placeholder name.
     *
     * @return string  The message template with the delimiter of '{' and '}'
     */
    protected function templateFactory()
    {
        return '{name} is not composed of letter, chinese.';
    }

    /**
     * Check whether the value conform to the rule. If conform, return the value.
     * Or return a context array that corresponding to placeholders in the $this->template
     *
     * @param string $value
     * @param string $name
     * @return mixed  the $value or a context array
     */
    protected function check(string $value, string $name)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}a-zA-z]+$/u', $value)) {
            return true;
        }
        return ['name' => $name];
    }
}