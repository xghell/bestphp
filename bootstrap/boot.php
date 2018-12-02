<?php

namespace Best;

//Register autoload mechanism
require __DIR__ . '/../vendor/bestphp/src/library/Loader.php';

$loader = new Loader(__DIR__ . '/../autoload');  //TODO how to use debug model
$loader->addLoader(['Psr4']);
$loader->register();

//Register composer mechanism
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
}
