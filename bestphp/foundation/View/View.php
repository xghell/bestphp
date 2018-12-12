<?php


namespace Best\View;


class View
{
    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var array The following are required.
     *
     * $config = [
     *      'engine'         => 'Best',
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
     * ]
     */
    private $config = [];

    /**
     * View constructor.
     *
     * @param array $config
     * @param bool $isDebug
     */
    public function __construct(array $config, bool $isDebug = false)
    {
        $this->config = $config;
        $this->isDebug = $isDebug;
    }

    /**
     * Render the view
     *
     * @param string $view
     * @param array $vars
     * @return false|string
     */
    public function fetch(string $view, $vars = [])
    {
        $viewFile = rtrim($this->config['view_path'], '/') . '/' . $view . '.' . ltrim($this->config['view_suffix'], '.');
        $compiledFile = rtrim($this->config['compiled_path'], '/') . '/' . md5($view) . '.' . ltrim($this->config['cache_suffix'], '.');

        $engine = '\\View\\Template\\' . $this->config['engine'];

        if ($this->valid($viewFile, $compiledFile)) {
            return $this->includeFile($compiledFile, $vars);
        } else {
            $content = file_get_contents($viewFile);

            $compiledContent = (new $engine($this))->compile($content);

            file_put_contents($compiledFile, $compiledContent);

            return $this->includeFile($compiledFile, $vars);
        }
    }

    public function config($name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * Isolate the variables scope.
     *
     * @param $file
     * @param array $vars
     * @return false|string
     */
    protected function includeFile($file, array $vars)
    {
        ob_start();
        ob_implicit_flush(0);

        extract($vars, EXTR_OVERWRITE);
        include $file;

        return ob_get_clean();
    }

    /**
     * Check if the view cache file is expired
     *
     * @param $viewFile
     * @param $compiledFile
     * @return bool
     */
    protected function valid($viewFile, $compiledFile)
    {
        if (!is_file($viewFile)) {
            throw new \InvalidArgumentException("The view file($viewFile) not exists.");
        }

        if (is_file($compiledFile) && $this->isDebug) {
            return true;
        }

        return false;
    }
}