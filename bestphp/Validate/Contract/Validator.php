<?php


namespace Best\Validate\Contract;


abstract class Validator
{
    /**
     * The error message
     *
     * @var string
     */
    private $message;

    /**
     * The builtin message template with the delimiter of '{' and '}'
     *
     * @var string
     */
    private $template;

    /**
     * The validation options
     *
     * @var mixed
     */
    private $options;

    /**
     * ValidatorInterface constructor.
     *
     * @param $options
     * @param string $message  The customized error message
     */
    public function __construct($options = null, string $message = '')
    {
        $this->options = $options;
        if ('' !== trim($message)) {
            $this->message = $message;
        } else {
            $this->setTemplate($this->templateFactory());
        }
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Validate whether the value conform to the rule. If conform, return the value. Or return false
     *
     * @param string $value  The value to be check
     * @param string $name  The name of value
     * @return bool
     */
    public function validate(string $value, string $name = ''){
        $result = $this->check($value, $name);
        if (is_array($result)) {
            $this->messageFactory($result);
            return false;
        }
        return true;
    }


    /**
     * The message template factory, the return value is automatically passed to $this->template (the binltin message template)
     *
     * Placeholder names MUST be delimited with a single opening brace { and a single closing brace }.
     * There MUST NOT be any whitespace between the delimiters and the placeholder name.
     *
     * @return string  The message template with the delimiter of '{' and '}'
     */
    abstract protected function templateFactory();

    /**
     * Check whether the value conform to the rule. If conform, return the value.
     * Or return a context array that corresponding to placeholders in the $this->template
     *
     * @param string $value
     * @param string $name
     * @return true|array  True or a context array
     */
    abstract protected function check(string $value, string $name);

    /**
     * The message factory.
     *
     * If $this->message is null, this method will parse $this->template,
     * and the return value is automatically passed to $this->message (the error message)
     *
     * The template MAY contain placeholders which implementors MAY replace with values from the context array.
     * Placeholder names MUST correspond to keys in the context array.
     * Placeholder names MUST be delimited with a single opening brace { and a single closing brace }.
     * There MUST NOT be any whitespace between the delimiters and the placeholder name.
     *
     * @param array $context
     * @return string
     */
    protected function messageFactory(array $context)
    {
        if (is_null($this->message)) {
            $replace = [];
            foreach ($context as $field => $value) {
                $search = '{' . $field . '}';
                $replace[$search] = json_encode($value);
            }
            $this->message = strtr($this->template, $replace);
        }
    }

    /**
     * Set template
     *
     * @param $template
     */
    protected function setTemplate($template)
    {
        $this->template = $template;
    }
}