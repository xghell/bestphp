<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagWhile extends Tag
{
    /**
     * Compile the tag '@while()' to '<?php while () : ?>'
     *
     * @param $content
     */
    protected function compileWhile(&$content)
    {
        $content = preg_replace('#@while\s*\((.*?)\)#s', '<?php while (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@endwhile' to '<?php endwhile; ?>'
     *
     * @param $content
     */
    protected function compileEndwhile(&$content)
    {
        $content = preg_replace('#@endwhile\b#', '<?php endwhile; ?>', $content);
    }
}