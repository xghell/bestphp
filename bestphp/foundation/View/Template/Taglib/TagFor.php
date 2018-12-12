<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagFor extends Tag
{
    /**
     * Compile the tag '@for()' to '<?php for (): ?>'
     *
     * @param $content
     */
    protected function compileFor(&$content)
    {
        $content = preg_replace('#@for\s*\((.*?)\)#s', '<?php for (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@endfor' to '<?php endfor; ?>'
     *
     * @param $content
     */
    protected function compileEndfor(&$content)
    {
        $content = preg_replace('#@endfor\b#', '<?php endfor; ?>', $content);
    }
}