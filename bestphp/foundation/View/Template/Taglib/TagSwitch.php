<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagSwitch extends Tag
{
    /**
     * Compile the tag '@switch ()' to '<?php switch (): ?>'
     * And trip the space between 'switch' and the first 'case'.
     *
     * @param $content
     */
    protected function compileSwitch(&$content)
    {
        $content = preg_replace('#@switch\s*\((.*?)\).*(?=@case)#s', '<?php switch (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@case ()' to '<?php case (): ?>'
     *
     * @param $content
     */
    protected function compileCase(&$content)
    {
        $content = preg_replace('#@case\s+\((.*?)\)#s', '<?php case ${1} : ?>', $content);
    }

    /**
     * Compile the tag '@default' to '<?php default: ?>'
     *
     * @param $content
     */
    protected function compileDefault(&$content)
    {
        $content = preg_replace('#@default\b#', '<?php default : ?>', $content);
    }

    /**
     * Compile the tag '@endswitch' to '<?php endswitch; ?>'
     *
     * @param $content
     */
    protected function compileEndswitch(&$content)
    {
        $content = preg_replace('#@endswitch\b#', '<?php endswitch; ?>', $content);
    }
}