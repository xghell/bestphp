<?php


namespace Best\Validate\Driver;


use Best\Validate\Contract\Validator;

/**
 * Class Float
 *
 * $options = [min, max]|'floating'|null
 * 
 * @package Best\Validate\Driver
 */
class Floating extends Validator
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
        return '{name} is not a float.';
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
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            return ['name' => $name];
        }

        if (is_array($this->options)) {
            $min = $this->options[0];
            $max = $this->options[1];
            if ($value >= $min && $value <= $max) {
                return true;
            }

            $this->setTemplate('{name} is not between {min} and {max}.');

            return ['name' => $name, 'min' => $min, 'max' => $max];
        }

        return true;
    }
}