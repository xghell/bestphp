<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

class TagForeach extends Tag
{
    /**
     * Compile the tag '@foreach ()' to '<?php foreach () : ?>'
     *
     * @param $content
     */
    protected function compileForeach(&$content)
    {
        $content = preg_replace('#@foreach\s*\((.*?)\)#s', '<?php foreach (${1}) : ?>', $content);
    }

    /**
     * Compile the tag '@endforeach' to '<?php endforeach; ?>'
     *
     * @param $content
     */
    protected function compileEndforeach(&$content)
    {
        $content = preg_replace('#@endforeach\b#', '<?php endforeach; ?>', $content);
    }
}