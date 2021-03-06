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
       Route::post('/changepassword','ServiceRequestApiController@changepassword');
       Route::post('/setfirebasetoken','ServiceRequestApiController@setfirebasetoken');
       Route::post('/dashboard','ServiceRequestApiController@dashboard');
       Route::post('/getassignedrequestlist','ServiceRequestApiController@getAssignedRequestList');
       Route::post('/gettodayduerequestlist','ServiceRequestApiController@getTodayDueRequestList');
       Route::post('/getoverduerequestlist','ServiceRequestApiController@getOverDueRequestList');
       Route::post('/getresolvedrequestlist','ServiceRequestApiController@getResolvedRequestList');
       Route::post('/setRequestStatus','ServiceRequestApiController@setRequestStatus');
       Route::post('/getRequestDetail','ServiceRequestApiController@getRequestDetail');
       Route::post('/updateRequestDetail','ServiceRequestApiController@updateRequestDetail');
       Route::post('/updateRequestDetail_v2','ServiceRequestApiController@updateRequestDetail_v2');
       Route::post('/getRequestDetail_v2','ServiceRequestApiController@getRequestDetail_v2');
       

});
