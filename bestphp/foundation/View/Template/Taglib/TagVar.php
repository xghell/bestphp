<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

/**
 * Class TagVar
 *
 * The following configuration are required in the engine configuration.
 *
 * [
 *      'var_begin'     => '{{',
 *      'var_end'       => '}}'
 * ];
 *
 * @package View\Template\Taglib
 */
class TagVar extends Tag
{
    /**
     * Compile the tag '{{$var}}' to '<?php echo $var; ?>'
     * @param $content
     */
    protected function compileVar(&$content)
    {
        $pattern = '#' . preg_quote($this->engine->config('var_begin')) . '(.*?)' . preg_quote($this->engine->config('var_end')) . '#s';
        $content = preg_replace($pattern, '<?php echo ${1}; ?>', $content);
    }
}