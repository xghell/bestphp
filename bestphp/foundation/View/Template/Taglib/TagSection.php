<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

/**
 * Class TagSection
 *
 * The following configuration are required in engine configuration.
 *
 * [
 *      'section_path'     => '',
 *      'view_suffix'      => ''
 * ];
 *
 * @package View\Template\Taglib
 */
class TagSection extends Tag
{
    /**
     * Compile the tag '@section()' to file content that section name correspond to
     *
     * @param $content
     */
    protected function compileSection(&$content)
    {
        preg_match_all('#@section\s*\(["\'](.*?)["\']\)#s', $content, $matches);
        $sectionNames = array_combine($matches[0], $matches[1]);
        
        $sectionContents = [];
        foreach ($sectionNames as $match => $sectionName) {
            $sectionPath = rtrim($this->engine->config('section_path'), '/');
            $sectionFile = $sectionPath . '/' . trim($sectionName) . '.' . ltrim($this->engine->config('view_suffix'), '.');

            if (!is_file($sectionFile)) {
                throw new \InvalidArgumentException("The section($sectionFile) not exists");
            }

            $sectionContents[$match] = file_get_contents($sectionFile);
        }
        
        $content = strtr($content, $sectionContents);
    }
}