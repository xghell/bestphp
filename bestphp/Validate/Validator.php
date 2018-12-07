<?php


namespace Best\Validate;


class Validator
{
    /**
     * The status of validator. True if all of data is validate, or False
     *
     * @var string
     */
    protected $status;

    /**
     * The error message array.
     * While the key is the field of validated data, The value is a error message corresponding to the key
     *
     * @var array
     */
    protected $messages;

    /**
     * The validate rules customized by user. The format follows:
     * $rules = [
     *      'field1' => [
     *          'validatorName1' => [ The validator options array ],
     *          'validatorName2' => [ The validator options array ]
     *      ],
     *     'field2' => [
     *          'validatorName1' => [ The validator options array ],
     *          'validatorName2' => [ The validator options array ]
     *      ]
     * ]
     *
     * @example
     * $rules = [
     *     'age' => [
     *          'required' => 'required',
     *          'between' => [18,35]
     *     ],
     *     'gender' => [
     *          'in' => ['male', 'female']
     *     ]
     * ];
     * @var array
     */
    protected $rules = [];

    /**
     * The error prompt customized by user. The prompt will overwrite the builtin error message. The format follows:
     * $prompts = [
     *      'fieldName.validatorName' => 'error message.'
     * ]
     *
     * @example
     * $prompts = [
     *      'age.required' => 'The age is required.'
     * ]
     *
     * @var array
     */
    protected $prompts = [];

    /**
     * Get error message
     *
     * @param string $field
     * @return mixed
     */
    public function getMessage(string $field = '')
    {
        if ('' === trim($field)) {
            return $this->messages;
        }

        return $this->messages[$field] ?? null;
    }

    /**
     * Validate whether all of the '$data' value is valid. Return True if success, or return False
     *
     * @param array $data
     * @param bool $validateAll   Quit validating or not, if arbitrary field failed. QUIT if False, NOT if True
     * @return bool
     */
    public function validate(array $data, bool $validateAll = false)
    {
        $rules = $this->getValidRules($data);
        
        foreach ($rules as $field => $validators) {
            foreach ($validators as $name => $options) {
                //Get a custom prompt message
                $prompt = $this->getPrompt($field, $name);
                //Get a validator instance by validator name
                $validator = $this->getValidator($name, $options, $prompt);

                if (!$validator->validate($data[$field], $field)) {
                    $this->messages[$field] = $validator->getMessage();

                    $this->status = false;
                    break;
                }
            }

            if (!$validateAll && $this->status === false) {
                break;
            }
        }
        return $this->status ?? true;
    }

    /**
     * Get valid rules
     *
     * @param array $data
     * @return array
     */
    protected function getValidRules(array $data)
    {
        return array_intersect_key($this->rules, $data);
    }
    
    /**
     * Get validator instance by validator name
     *
     * @param $name  Case-insensitive
     * @param $options
     * @param $message
     * @return mixed
     */
    protected function getValidator($name, $options, $message)
    {
        $name = strtolower($name);
        $validator = '\\Best\\Driver\\Validator\\' . ucfirst($name);
        return new $validator($options, $message);
    }

    /**
     * Get error prompt customized bu user. The prompt will overwrite the builtin error message.
     *
     * @param $field
     * @param $validator
     * @return string
     */
    protected function getPrompt($field, $validator)
    {
        $prompt = $field . '.' . $validator;
        return $this->prompts[$prompt] ?? '';
    }
}