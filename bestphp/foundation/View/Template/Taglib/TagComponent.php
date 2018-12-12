<?php


namespace View\Template\Taglib;


use View\Template\Taglib\Contract\Tag;

/**
 * Class TagComponent
 *
 * The following configuration are required in engine configuration.
 *
 * [
 *      'block_begin'    => '[[',
 *      'block_end'      => ']]',
 *      'component_path' => '',
 *      'view_suffix'    => 'html'
 * ];
 *
 * @package View\Template\Taglib
 */
class TagComponent extends Tag
{
    /**
     * Compile the tag '@component('name', ['varname' => 'value'])' to file content that section name correspond to
     *
     * @param $content
     */
    protected function compileComponent(&$content)
    {
        preg_match_all('#@component\s*\((.*?)(?:\[(.*?)\])?\)#s', $content, $matches);
        //Array that composed if the '@component()' part of the content.
        $searches = [];
        //Component name
        $names = [];
        //The key/value array to replace variables in the component file
        $vars = [];

        //Get $searches
        array_map(function ($search) use (&$searches) {
            $searches[] = trim($search, ' ,\'"');
        }, $matches[0]);

        //Get $names
        array_map(function ($name) use (&$names) {
            $names[] = trim($name, ' ,\'"');
        }, $matches[1]);

        //Get $vars
        array_map(function ($componentVar) use (&$vars) {
            $varItems = [];
            array_map(function ($varString) use (&$varItems) {
                if ('' !== trim($varString)) {
                    [$key, $value] = explode('=>', $varString, 2);
                    $key = $this->engine->config('block_begin') . '$' . trim(trim($key), ' \'"') . $this->engine->config('block_end');
                    $value = trim(trim($value), ' \'"');
                    $varItems[$key] = $value;
                }
            }, explode(',', $componentVar));
            $vars[] = $varItems;
        }, $matches[2]);

        //Template replacement. Replace the variables in the component file with $vars
        //and replace @component() in the content with component file.
        $count = count($searches);
        $replaceParis = [];
        for ($i = 0; $i < $count; ++$i) {
            $search = $searches[$i];
            $name = $names[$i];
            $var = $vars[$i];
            $componentPath = rtrim($this->engine->config('component_path'), '/');
            $componentFile = $componentPath . '/' . $name . '.' . trim($this->engine->config('view_suffix'), '.');

            if (!is_file($componentFile)) {
                throw new \InvalidArgumentException("The section($componentFile) not exists");
            }
            
            $componentContent = file_get_contents($componentFile);

            $componentContent = strtr($componentContent, $var);
            $replaceParis[$search] = $componentContent;
        }
        $content = strtr($content, $replaceParis);
    }
}