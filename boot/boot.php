<?php

namespace Best\Autoload;


require BASE_PATH . '/bestphp/foundation/Autoload/Loader.php';

$autoloadPath = BASE_PATH . '/autoload';
$composerPath = BASE_PATH . '/vendor/composer';

$loader = new Loader($autoloadPath, $composerPath, false);

//Case-Sensitive loader name. e.g., 'Psr4', 'Psr0', 'ClassMap', 'Alias', 'File'
$loader->addLoader(['File', 'Psr4', 'Psr0', 'Alias']);
$loader->register();
