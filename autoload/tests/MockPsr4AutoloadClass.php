<?php
/**
 * Created by PhpStorm.
 * User: wangxiguang
 * Date: 2018\\10\\25
 * Time: 19:29
 */

namespace AutoLoad\Tests;

use AutoLoad\Psr4AutoloadClass;

class MockPsr4AutoloadClass extends Psr4AutoloadClass
{
    protected $file = [];

    public function loadClass($class)
    {
        return parent::loadClass($class);
    }

    public function setFiles($file = [])
    {
        $this->file = $file;
    }

    protected function requireFile($file)
    {
        return in_array($file, $this->file);
    }
}