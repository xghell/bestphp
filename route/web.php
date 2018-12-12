<?php

Route::get('index/index/{name}', function ($name) {
    return $name;
});
