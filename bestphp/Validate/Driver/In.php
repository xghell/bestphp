<?php


namespace Best\Validate\Driver;


use Best\Contract\Validator\The;
use Best\Validate\Contract\Validator;
use PHPUnit\Framework\Constraint\IsTrue;

/**
 * Class In
 *
 * $options = ['value1', 'value2', ...]
 *
 * @package Best\Validate\Driver
 */
class In extends Validator
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
        return '{name} not in {rule}';
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
        if (in_array($value, $this->options)) {
            return true;
        } else {
            return ['name' => $name, 'rule' => $this->options];
        }
    }
}