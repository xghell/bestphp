<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagJump extends Tag
{
    /**
     * Compile the tag '@break' to '<?php break; ?>'
     *
     * @param $content
     */
    protected function compileBreak(&$content)
    {
        $content = preg_replace('#@break\b#', '<?php break; ?>', $content);
    }

    /**
     * Compile the tag '@continue' to '<?php continue; ?>'
     *
     * @param $content
     */
    protected function compileContinue(&$content)
    {
        $content = preg_replace('#@continue\b#', '<?php continue; ?>', $content);
    }

    /**
     * @param $content
     */
    protected function compileReturn(&$content)
    {
        $content = preg_replace('#@return\b#', '<?php return; ?>', $content);
    }
}