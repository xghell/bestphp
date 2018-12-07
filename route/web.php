<?php

Route::get('index/index/{name}', function ($name) {
    return json_encode([1,2,3]);
});
