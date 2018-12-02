<?php

namespace Best;

require __DIR__ . '/../bootstrap/boot.php';

$request = new Request();
$request->withUri('/hello/name/wxg/25');

$router = new Router();
Container::getInstance()->bind('router', $router);
//var_dump(Container::getInstance()->get('router'));
Container::getInstance()->get('router')->group('/hello', function () {
//    Container::getInstance()->get('router')->get('name/{age}', 'index/user/eeeeee/name/{a}')->where('name', '\d+');
    Container::getInstance()->get('router')->get('/name/{a}', 'index/user/kafajjk/name/{a}');
})->where('a', '\d+');

$result = $router->check($request);
var_dump($result);
echo $result->getPathinfo();
