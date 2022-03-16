<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/users'], function ($router) {
    $router->post('login', 'User\AuthController@login');
    $router->post('refresh', 'User\AuthController@refresh');
	$router->get('logout', 'User\AuthController@logout');
    $router->get('getprofile', 'User\UserController@getProfile');
    $router->post('profile/update', 'User\UserController@profile_update');

    //Password Reset
    #$router->post('password/create', 'User\PasswordResetController@create');

});

$router->group(['prefix' => 'api/merchants'], function ($router) {
    $router->post('login', 'Merchant\AuthController@login');
    $router->post('refresh', 'Merchant\AuthController@refresh');
	$router->get('logout', 'Merchant\AuthController@logout');
    $router->get('getprofile', 'Merchant\UserMerchantController@getProfile');
    $router->post('profile/update', 'Merchant\UserMerchantController@profile_update');

    //Settings
    $router->get('apikey', 'Merchant\SettingController@apikey');

    //Password Reset
    $router->post('password/create', 'Merchant\PasswordResetController@create');

    //Customer
    $router->get('customers', 'Merchant\CustomerController@index');
    $router->post('customers', 'Merchant\CustomerController@store');
    $router->get('customers/{id}', 'Merchant\CustomerController@show');
    $router->put('customers/{id}', 'Merchant\CustomerController@update');
    $router->delete('customers/{id}', 'Merchant\CustomerController@destroy');
});

$router->group(['prefix' => 'api'], function ($router) {
    //Master Data
    // $router->get('states', 'All\StateController@index');
    // $router->get('classes', 'All\ClassController@index');
    // $router->get('subjects', 'All\SubjectController@index');
});
