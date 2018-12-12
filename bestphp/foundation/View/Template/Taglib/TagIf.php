<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagIf extends Tag
{
    /**
     * Compile the tag '@if ()' to '<?php if () : ?>'
     *
     * @param $content
     */
    protected function compileIf(&$content)
    {
        $content = preg_replace('#@if\s*\((.*?)\)#s', '<?php if (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@elseif ()' to '<?php elseif (): ?>'
     *
     * @param $content
     */
    protected function compileElseif(&$content)
    {
        $content = preg_replace('#@elseif\s*\((.*?)\)#s', '<?php elseif (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@else' to '<?php else: ?>'
     * @param $content
     */
    protected function compileElse(&$content)
    {
        $content = preg_replace('#@else\b#', '<?php else : ?>', $content);
    }

    /**
     * Compile the tag '@endif' to '<?php endif; ?>'
     *
     * @param $content
     */
    protected function compileEndif(&$content)
    {
        $content = preg_replace('#@endif\b#', '<?php endif; ?>', $content);
    }
}