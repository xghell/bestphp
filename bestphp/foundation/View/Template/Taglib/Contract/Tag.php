<?php


namespace View\Template\Taglib\Contract;


/**
 * Class Tag
 *
 * The method that starts with 'compile' will be called by method 'compile' automatically.
 * That is to say, If you want to compile a tag,
 * you ONLY need define the method name start with 'compile' like 'compileIf', 'compileElseif','compileEndif'
 * ATTENTION: The uer-define method MUST be reference arguments.
 *
 * @example The user-defined function
 *
 * Compile the tag '@if ()' to '<?php if (): ?>'
 *
 * protected function compileIf(&$content)
 * {
 *      $content = preg_replace('#@if\s*\((.*?)\)#s', '<?php if (${1}): ?>', $content);
 * }
 *
 * @package View\Template\Taglib\Contract
 */
abstract class Tag
{
    /**
     * @var array
     */
    protected $engine;

    /**
     * Tag constructor.
     *
     * @param array $engine   The engine MUST implement the method 'config' to get configuration
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    /**
     * The compile the tag in the content.
     * This method will automatically call the user-defined method that starts with 'compile', e.g., 'compileIf','compileEndif'
     *
     * @param $content
     * @return mixed
     */
    public function compile($content)
    {
        $methods = get_class_methods(static::class);
        $methods =  preg_grep('#^compile.+#s', $methods);

        array_walk($methods, function (&$value) {
            $value = [$this, $value];
        });

        array_map(function ($method) use (&$content) {
            $method($content);
        }, $methods);

        return $content;
    }
}