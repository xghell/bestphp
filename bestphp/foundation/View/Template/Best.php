<?php


namespace View\Template;


use View\Template\Taglib\Contract\Tag;

/**
 * Class Best
 *
 * The following configuration is required in the view configuration
 *
 * [
 *      'var_begin'      => '{{',
 *      'var_end'        => '}}',
 *      'block_begin'    => '[[',
 *      'block_end'      => ']]',
 *      'section_path'   => '../template/index/section',
 *      'component_path' => '../template/component',
 *      'view_suffix'    => 'html',
 *      'cache_suffix'   => 'php',
 *      'view_path'      => '../template/index/index',
 *      'compiled_path'  => '../runtime/view',
 * ];
 *
 * @package View\Template
 */
class Best
{
    /**
     * @var array The instance of view
     */
    private $view;

    /**
     * @var
     */
    private $taglib;

    /**
     * Best constructor.
     *
     * @param $view     The instance of view
     */
    public function __construct($view)
    {
        $this->view = $view;

        $files = array_slice(scandir(__DIR__ . '/Taglib'), 3);
        $tags = [];
        //Transform the tag file name to tag class name
        array_map(function ($file) use (&$tags) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $tag = '\\View\\Template\\Taglib\\' . $filename;
            $tags[$filename] = $tag;
        }, $files);

        $this->addTag($tags);
    }

    /**
     * Add the tag to taglib
     *
     * @param $name    The tag name
     * @param Tag $tag  The tag class
     * @param bool $prepend     Determine if overwrite existed tag
     */
    public function addTag($name, $tag = null, bool $prepend = false)
    {
        if (is_array($name)) {
            if ($prepend) {
                $this->taglib = $name + ($this->taglib ?? []);
            } else {
                $this->taglib = ($this->taglib ?? []) + $name;
            }
        } elseif ($prepend || !isset($this->taglib[$name])) {
            $this->taglib[$name] = $tag;
        }
    }

    /**
     * Get configuration
     *
     * @param $name
     * @return mixed
     */
    public function config($name)
    {
        return $this->view->config($name) ?? null;
    }

    /**
     * Compile the content with the tag class of taglib
     *
     * @param string $content
     * @return string
     */
    public function compile(string $content)
    {
        return array_reduce($this->taglib, function ($carry, $item) {
            return call_user_func([new $item($this), 'compile'], $carry);
        }, $content);
    }
}