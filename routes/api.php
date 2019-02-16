<?php

Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {

        Route::resource('categories', 'CategoriesController', ['except' => ['create', 'edit']]);

        Route::resource('products', 'ProductsController', ['except' => ['create', 'edit']]);

        Route::resource('product_parts', 'ProductPartsController', ['except' => ['create', 'edit']]);

        Route::resource('companies', 'CompaniesController', ['except' => ['create', 'edit']]);

        Route::resource('service_requests', 'ServiceRequestsController', ['except' => ['create', 'edit']]);

        Route::resource('service_centers', 'ServiceCentersController', ['except' => ['create', 'edit']]);

        Route::resource('manage_charges', 'ManageChargesController', ['except' => ['create', 'edit']]);

        Route::resource('invoices', 'InvoicesController', ['except' => ['create', 'edit']]);

});
Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {
       Route::post('/login','LoginApiController@login');
       Route::post('/forgotpassword','LoginApiController@forgotpassword');
       Route::post('/otp','LoginApiController@verifyotp');
       Route::post('/setpassword','LoginApiController@setpassword');
       Route::post('/dashboard','ServiceRequestApiController@dashboard');
       Route::post('/getassignedrequestlist','ServiceRequestApiController@getAssignedRequestList');
       Route::post('/gettodayduerequestlist','ServiceRequestApiController@getTodayDueRequestList');
       Route::post('/getoverduerequestlist','ServiceRequestApiController@getOverDueRequestList');
       Route::post('/getresolvedrequestlist','ServiceRequestApiController@getResolvedRequestList');

});
