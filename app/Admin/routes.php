<?php

use Illuminate\Routing\Router;



Route::group([
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ], function ($router) {

            /* @var \Illuminate\Support\Facades\Route $router */
            $router->namespace('\Encore\Admin\Controllers')->group(function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->resource('auth/users', 'UserController')->names('admin.auth.users');
                $router->resource('auth/roles', 'RoleController')->names('admin.auth.roles');
                $router->resource('auth/permissions', 'PermissionController')->names('admin.auth.permissions');
                $router->resource('auth/menu', 'MenuController', ['except' => ['create']])->names('admin.auth.menu');
                $router->resource('auth/logs', 'LogController', ['only' => ['index', 'destroy']])->names('admin.auth.logs');

                $router->post('_handle_form_', 'HandleController@handleForm')->name('admin.handle-form');
                $router->post('_handle_action_', 'HandleController@handleAction')->name('admin.handle-action');
            });

            $authController = config('admin.auth.controller', AuthController::class);

            /* @var \Illuminate\Routing\Router $router */
            $router->get('auth/login', $authController.'@getLogin')->name('admin.login');
            $router->post('auth/login', $authController.'@postLogin');
            $router->get('auth/logout', $authController.'@getLogout')->name('admin.logout');
            $router->get('auth/setting', $authController.'@getSetting')->name('admin.setting');
            $router->put('auth/setting', $authController.'@putSetting');
});
//Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

	//应用管理
	$router->post('app/restore', 'AppController@restore')->name('admin.app.restore');
	$router->get('app/{id}/set', 'AppController@set')->name('admin.app.set');
	$router->resource('app', 'AppController')->names('admin.app');

	//应用分类
	$router->resource('app/{xapp_id}/cate', 'CateController')->names('admin.app.cate');
	$router->get('app/{xapp_id}/cate/{id}/set', 'CateController@set')->name('admin.app.cate.set');


	//应用
    $router->get('xapps/{xapp}', 'XappsController@index')->name('admin.xapps.index');
    $router->get('xapps/{xapp}/create', 'XappsController@create')->name('admin.xapps.create');
    $router->get('xapps/{xapp}/{id}', 'XappsController@show')->name('admin.xapps.show');
    $router->get('xapps/{xapp}/{id}/edit', 'XappsController@edit')->name('admin.xapps.edit');
    $router->post('xapps/{xapp}', 'XappsController@store')->name('admin.xapps.store');
    $router->put('xapps/{xapp}/{id}', 'XappsController@update')->name('admin.xapps.update');
    $router->delete('xapps/{xapp}/{id}', 'XappsController@destroy')->name('admin.xapps.destroy');
	//可考虑patch方法  $router->post('xapps/{xapp}/{id}', 'XappsController@change')->name('admin.xapps.change');
    $router->post('xapps/{xapp}/change', 'XappsController@change')->name('admin.xapps.change');
    $router->post('xapps/{xapp}/restore', 'XappsController@restore')->name('admin.xapps.restore');

	//web中的api
	$router->get('api/get_cate_groups', 'Api\XappController@get_cate_groups')->name('admin.api.get_cate_groups');


    $router->get('/', 'HomeController@index')->name('admin.home');

});




Route::fallback(function () {
    return '404!';//
});