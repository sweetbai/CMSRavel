<?php

Route::group([
    'name' => '公共模块'
], function () {
    Route::get('/', ['uses' => 'Modules\Common\Web\Index@index', 'desc' => '首页'])->name('web.index');
});

// 省市区街道数据