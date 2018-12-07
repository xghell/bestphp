<?php

namespace Best\Autoload;


require BASE_PATH . '/bestphp/Autoload/Loader.php';

$autoloadPath = BASE_PATH . '/autoload';
$composerPath = BASE_PATH . '/vendor/composer';

$loader = new Loader($autoloadPath, $composerPath);

//Case-Sensitive loader name. e.g., 'Psr4', 'Psr0', 'ClassMap', 'Alias', 'File'
$loader->addLoader(['File', 'ClassMap', 'Psr4', 'Psr0', 'Alias']);
$loader->register();
