<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'   => 'tools',
    'auth_app' => '扩展工具'
], function () {
    Route::group([
        'auth_group' => '菜单管理'
    ], function () {
        Route::manage(\Modules\Tools\Admin\Menu::class)->only(['index', 'data', 'page', 'save', 'del'])->make();
    });
    Route::group([
        'auth_group' => '菜单内容管理'
    ], function () {
        Route::manage(\Modules\Tools\Admin\MenuItems::class)->only(['index', 'data', 'page', 'save', 'del'])->prefix('menuItems-{menu}')->make();
    });

    Route::group([
        'auth_group' => '链接管理'
    ], function () {
        Route::manage(\Modules\Tools\Admin\Url::class)->only(['index', 'data'])->make();
    });

    Route::group([
        'auth_group' => '自定义页面'
    ], function () {
        Route::manage(\Modules\Tools\Admin\Page::class)->only(['index', 'data', 'page', 'save', 'del'])->make();
    });

    Route::group([
        'auth_tags' => '内容标签'
    ], function () {
        Route::get('tags', ['uses' => 'Modules\Tools\Admin\Tags@index', 'desc' => '列表'])->name('admin.tools.tags');
        Route::post('tags/del/{id?}', ['uses' => 'Modules\Tools\Admin\Tags@del', 'desc' => '删除'])->name('admin.tools.tags.del');
        Route::get('tags/empty', ['uses' => 'Modules\Tools\Admin\Tags@empty', 'desc' => '清理'])->name('admin.tools.tags.empty');
    });
    Route::group([
        'auth_group' => '模板标记'
    ], function () {
        Route::manage(\Modules\Tools\Admin\Mark::class)->make();
    });
    // Generate Route Make
});
