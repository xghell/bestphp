<?php

namespace Best;

//Define the application base path(this const is required, because some place depend on the path e.g.,'autoload')
define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/boot/boot.php';

(new App(BASE_PATH))->run()->send();
