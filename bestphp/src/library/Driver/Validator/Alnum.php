<?php


namespace Best\Driver\Validator;


use Best\Contract\Validator\Validator;

/**
 * Class Alphanum
 * 
 * $options = 'alphanum'|null
 * 
 * @package Best\Driver\Validator
 */
class Alnum extends Validator
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
        return '{name} is not composed of letter, number.';
    }

    /**
     * Check whether the value conform to the rule. If conform, return the value.
     * Or return a context array that corresponding to placeholders in the $this->template
     *
     * @param string $value
     * @param string $name
     * @return true|array  True or a context array
     */
    protected function check(string $value, string $name)
    {
        if (ctype_alnum($value)) {
            return true;
        }
        return ['name' => $name];
    }
}