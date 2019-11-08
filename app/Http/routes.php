<?php

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


$api = app('Dingo\Api\Routing\Router');


//Public Routes - Open to the World
$api->version('v1', function ($api) {
    $api->group([], function ($api) {
        
        $api->post('/authenticate', 'App\Http\Controllers\AuthenticateController@backend');

        //Members        
        $api->get('/members/{id}/password/{value}','App\Http\Controllers\MembersController@checkCurrentPassword');
        $api->post('/members/password-change','App\Http\Controllers\MembersController@passwordChange'); //UNTESTED
        $api->post('/members/password-reset','App\Http\Controllers\MembersController@passwordReset'); //UNTESTED        
        $api->get('/members', 'App\Http\Controllers\MembersController@find');
        $api->get('/members/{id}','App\Http\Controllers\MembersController@get');        

        //GroupsInvitations
        $api->get('/groupsInvitations/{id}/accept/{token}','App\Http\Controllers\GroupsInvitationsController@accept');
      
    });
});

//Members Routes - Open to all authenticated members.
$api->version('v1', function ($api) {
    //$api->group(['middleware' => ['api.auth'], 'providers' => 'jwt'], function ($api) {
    $api->group([], function ($api) { //replace this soon... for now just have it be open
        
        //Members       
        $api->post('/logout', 'App\Http\Controllers\MembersController@logout');        
        $api->get('/me', 'App\Http\Controllers\MembersController@me');
        $api->put('/members/{id}','App\Http\Controllers\MembersController@update');
        $api->put('/members/{id}/set-custom-password','App\Http\Controllers\MembersController@setCustomPassword'); //for first time password set, which is used to complete account setup.
        $api->delete('/members/{id}','App\Http\Controllers\MembersController@delete');        

        //Groups
        $api->get('/groups','App\Http\Controllers\GroupsController@find');
        $api->post('/groups','App\Http\Controllers\GroupsController@create');
        $api->get('/groups/{id}','App\Http\Controllers\GroupsController@get');     
        $api->delete('/groups/{id}','App\Http\Controllers\GroupsController@delete');
        $api->put('/groups/{id}','App\Http\Controllers\GroupsController@update');

        //GroupsInvitations
        $api->get('/groups/{id}/invitations','App\Http\Controllers\GroupsInvitationsController@find');
        $api->post('/groupsInvitations','App\Http\Controllers\GroupsInvitationsController@create');
        $api->get('/groupsInvitations/{id}','App\Http\Controllers\GroupsInvitationsController@get');
        $api->delete('/groupsInvitations/{id}','App\Http\Controllers\GroupsInvitationsController@delete');
        $api->put('/groupsInvitations/{id}','App\Http\Controllers\GroupsInvitationsController@update');

        //Verbs
        $api->get('/verbs','App\Http\Controllers\VerbsController@find');
        $api->get('/verbs/{spanish}','App\Http\Controllers\VerbsController@get');
        $api->get('/questions','App\Http\Controllers\VerbsController@get_questions');
        
    });
});

//SuperAdmin routes
$api->version('v1', function ($api) {
    $api->group(['middleware' => ['api.auth','superadmin'], 'providers' => 'jwt'], function ($api) {
        $api->post('/members','App\Http\Controllers\MembersController@create');    
        $api->put('/members/{id}/restore','App\Http\Controllers\MembersController@restore');
        $api->put('/groups/{id}/restore','App\Http\Controllers\GroupsController@restore');
        $api->put('/groupsInvitations/{id}/restore','App\Http\Controllers\GroupsInvitationsController@restore');        
    });
});