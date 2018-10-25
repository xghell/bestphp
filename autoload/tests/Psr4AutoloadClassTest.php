<?php
/**
 * Created by PhpStorm.
 * User: wangxiguang
 * Date: 2018\\10\\25
 * Time: 17:01
 */

namespace AutoLoad\Tests;

require __DIR__ . '/../Psr4AutoloadClass.php';
require __DIR__ . '/MockPsr4AutoloadClass.php';

use PHPUnit\Framework\TestCase;

class Psr4AutoloadClassTest extends TestCase
{
    protected $loader;

    protected function setUp()
    {
        $this->loader = new MockPsr4AutoloadClass;

        $this->loader->setFiles(array(
            '\\vendor\\foo.bar\\src\\ClassName.php',
            '\\vendor\\foo.bar\\src\\DoomClassName.php',
            '\\vendor\\foo.bar\\tests\\ClassNameTest.php',
            '\\vendor\\foo.bardoom\\src\\ClassName.php',
            '\\vendor\\foo.bar.baz.dib\\src\\ClassName.php',
            '\\vendor\\foo.bar.baz.dib.zim.gir\\src\\ClassName.php',
        ));

        $this->loader->addNamespace(
            'Foo\Bar',
            '\\vendor\\foo.bar\\src'
        );

        $this->loader->addNamespace(
            'Foo\Bar',
            '\\vendor\\foo.bar\\tests'
        );

        $this->loader->addNamespace(
            'Foo\BarDoom',
            '\\vendor\\foo.bardoom\\src'
        );

        $this->loader->addNamespace(
            'Foo\Bar\Baz\Dib',
            '\\vendor\\foo.bar.baz.dib\\src'
        );

        $this->loader->addNamespace(
            'Foo\Bar\Baz\Dib\Zim\Gir',
            '\\vendor\\foo.bar.baz.dib.zim.gir\\src'
        );
    }

    public function testExistingFile()
    {
        $actual_path = $this->loader->loadClass('\\Foo\\Bar\\ClassName');
        $expected_path = '\\vendor\\foo.bar\\src\\ClassName.php';
        self::assertSame($expected_path, $actual_path, '载入文件失败！' . $actual_path);
    }

    public function testMissingFile()
    {
        $actual = $this->loader->loadClass('No_Vendor\No_Package\NoClass');
        self::assertFalse($actual);
    }

    public function testDeepFile()
    {
        $actual = $this->loader->loadClass('Foo\Bar\Baz\Dib\Zim\Gir\ClassName');
        $expect = '\\vendor\\foo.bar.baz.dib.zim.gir\\src\\ClassName.php';
        self::assertSame($expect, $actual);
    }

    public function testConfusion()
    {
        $actual = $this->loader->loadClass('Foo\Bar\DoomClassName');
        $expect = '\\vendor\\foo.bar\\src\\DoomClassName.php';
        self::assertSame($expect, $actual);

        $actual = $this->loader->loadClass('Foo\BarDoom\ClassName');
        $expect = '\\vendor\\foo.bardoom\\src\\ClassName.php';
        self::assertSame($expect, $actual);
    }



}