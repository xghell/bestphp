<?php


namespace Best\Driver\Validator;


use Best\Contract\Validator\Validator;

/**
 * Class Int
 *
 * $options = [min, max]|'integer'|null
 *
 * @package Best\Driver\Validator
 */
class Integer extends Validator
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
        return '{name} is not a int.';
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
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            return ['name' => $name];
        }

        if (is_array($this->options)) {
            $options = [
                'min_range' => $this->options[0] ?? null,
                'max_range' => $this->options[1] ?? null
            ];

            if (filter_var($value, FILTER_VALIDATE_INT, ['options' => $options])) {
                return true;
            }

            $this->setTemplate('{name} is not between {min} and {max}.');

            return ['name' => $name, 'min' => $options['min_range'], 'max' => $options['max_range']];
        }

        return true;
    }
}