<?php
Route::get('/', function () { return redirect('/admin/home'); });

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('auth.login');
$this->post('login', 'Auth\LoginController@login')->name('auth.login');
$this->post('logout', 'Auth\LoginController@logout')->name('auth.logout');

// Change Password Routes...
$this->get('change_password', 'Auth\ChangePasswordController@showChangePasswordForm')->name('auth.change_password');
$this->patch('change_password', 'Auth\ChangePasswordController@changePassword')->name('auth.change_password');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('auth.password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('auth.password.reset');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset')->name('auth.password.reset');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/home', 'HomeController@index');

    // permission routes
    Route::resource('permissions', 'Admin\PermissionsController');
    
    Route::resource('roles', 'Admin\RolesController');
    Route::post('roles_mass_destroy', ['uses' => 'Admin\RolesController@massDestroy', 'as' => 'roles.mass_destroy']);
    Route::resource('users', 'Admin\UsersController');
    Route::post('users_mass_destroy', ['uses' => 'Admin\UsersController@massDestroy', 'as' => 'users.mass_destroy']);

    Route::resource('service_center_admins', 'Admin\ServiceCenterAdminsController');
    Route::post('service_center_admin_mass_destroy', ['uses' => 'Admin\ServiceCenterAdminsController@massDestroy', 'as' => 'service_center_admins.mass_destroy']);

    Route::resource('company_admins', 'Admin\CompanyAdminsController');
    Route::post('company_admins_mass_destroy', ['uses' => 'Admin\CompanyAdminsController@massDestroy', 'as' => 'company_admins.mass_destroy']);

    Route::resource('company_users', 'Admin\CompanyUsersController');
    Route::post('company_admin_mass_destroy', ['uses' => 'Admin\CompanyUsersController@massDestroy', 'as' => 'company_users.mass_destroy']);

    Route::resource('technicians', 'Admin\TechniciansController');
    Route::post('technicians_mass_destroy', ['uses' => 'Admin\TechniciansController@massDestroy', 'as' => 'technicians.mass_destroy']);

    Route::resource('categories', 'Admin\CategoriesController');
    Route::post('categories_mass_destroy', ['uses' => 'Admin\CategoriesController@massDestroy', 'as' => 'categories.mass_destroy']);
    Route::post('categories_restore/{id}', ['uses' => 'Admin\CategoriesController@restore', 'as' => 'categories.restore']);
    Route::delete('categories_perma_del/{id}', ['uses' => 'Admin\CategoriesController@perma_del', 'as' => 'categories.perma_del']);
    Route::resource('products', 'Admin\ProductsController');
    Route::post('products_mass_destroy', ['uses' => 'Admin\ProductsController@massDestroy', 'as' => 'products.mass_destroy']);
    Route::post('products_restore/{id}', ['uses' => 'Admin\ProductsController@restore', 'as' => 'products.restore']);
    Route::delete('products_perma_del/{id}', ['uses' => 'Admin\ProductsController@perma_del', 'as' => 'products.perma_del']);
    Route::resource('product_parts', 'Admin\ProductPartsController');
    Route::post('product_parts_mass_destroy', ['uses' => 'Admin\ProductPartsController@massDestroy', 'as' => 'product_parts.mass_destroy']);
    Route::post('product_parts_restore/{id}', ['uses' => 'Admin\ProductPartsController@restore', 'as' => 'product_parts.restore']);
    Route::delete('product_parts_perma_del/{id}', ['uses' => 'Admin\ProductPartsController@perma_del', 'as' => 'product_parts.perma_del']);
    Route::resource('companies', 'Admin\CompaniesController');
    Route::post('companies_mass_destroy', ['uses' => 'Admin\CompaniesController@massDestroy', 'as' => 'companies.mass_destroy']);
    Route::post('companies_restore/{id}', ['uses' => 'Admin\CompaniesController@restore', 'as' => 'companies.restore']);
    Route::delete('companies_perma_del/{id}', ['uses' => 'Admin\CompaniesController@perma_del', 'as' => 'companies.perma_del']);
    Route::resource('customers', 'Admin\CustomersController');
    Route::post('customers_mass_destroy', ['uses' => 'Admin\CustomersController@massDestroy', 'as' => 'customers.mass_destroy']);
    Route::post('customers_restore/{id}', ['uses' => 'Admin\CustomersController@restore', 'as' => 'customers.restore']);
    Route::delete('customers_perma_del/{id}', ['uses' => 'Admin\CustomersController@perma_del', 'as' => 'customers.perma_del']);
    Route::resource('assign_products', 'Admin\AssignProductsController');
    Route::post('assign_products_mass_destroy', ['uses' => 'Admin\AssignProductsController@massDestroy', 'as' => 'assign_products.mass_destroy']);
    Route::post('assign_products_restore/{id}', ['uses' => 'Admin\AssignProductsController@restore', 'as' => 'assign_products.restore']);
    Route::delete('assign_products_perma_del/{id}', ['uses' => 'Admin\AssignProductsController@perma_del', 'as' => 'assign_products.perma_del']);
    Route::resource('assign_parts', 'Admin\AssignPartsController');
    Route::post('assign_parts_mass_destroy', ['uses' => 'Admin\AssignPartsController@massDestroy', 'as' => 'assign_parts.mass_destroy']);
    Route::post('assign_parts_restore/{id}', ['uses' => 'Admin\AssignPartsController@restore', 'as' => 'assign_parts.restore']);
    Route::delete('assign_parts_perma_del/{id}', ['uses' => 'Admin\AssignPartsController@perma_del', 'as' => 'assign_parts.perma_del']);
    Route::resource('service_requests', 'Admin\ServiceRequestsController');
    Route::post('service_requests_mass_destroy', ['uses' => 'Admin\ServiceRequestsController@massDestroy', 'as' => 'service_requests.mass_destroy']);
    Route::get('service_request_invoice/{id}', ['uses' => 'Admin\ServiceRequestsController@createReceiptPDF', 'as' => 'service_request.invoice']);

    Route::post('service_requests_restore/{id}', ['uses' => 'Admin\ServiceRequestsController@restore', 'as' => 'service_requests.restore']);
    Route::delete('service_requests_perma_del/{id}', ['uses' => 'Admin\ServiceRequestsController@perma_del', 'as' => 'service_requests.perma_del']);
    Route::resource('service_request_logs', 'Admin\ServiceRequestLogsController');
    Route::resource('service_centers', 'Admin\ServiceCentersController');
    Route::post('service_centers_mass_destroy', ['uses' => 'Admin\ServiceCentersController@massDestroy', 'as' => 'service_centers.mass_destroy']);
    Route::post('service_centers_restore/{id}', ['uses' => 'Admin\ServiceCentersController@restore', 'as' => 'service_centers.restore']);
    Route::delete('service_centers_perma_del/{id}', ['uses' => 'Admin\ServiceCentersController@perma_del', 'as' => 'service_centers.perma_del']);
    Route::resource('manage_charges', 'Admin\ManageChargesController');
    Route::post('manage_charges_mass_destroy', ['uses' => 'Admin\ManageChargesController@massDestroy', 'as' => 'manage_charges.mass_destroy']);
    Route::post('manage_charges_restore/{id}', ['uses' => 'Admin\ManageChargesController@restore', 'as' => 'manage_charges.restore']);
    Route::delete('manage_charges_perma_del/{id}', ['uses' => 'Admin\ManageChargesController@perma_del', 'as' => 'manage_charges.perma_del']);
    Route::resource('invoices', 'Admin\InvoicesController');
    Route::post('invoices_mass_destroy', ['uses' => 'Admin\InvoicesController@massDestroy', 'as' => 'invoices.mass_destroy']);
    Route::post('invoices_restore/{id}', ['uses' => 'Admin\InvoicesController@restore', 'as' => 'invoices.restore']);
    Route::delete('invoices_perma_del/{id}', ['uses' => 'Admin\InvoicesController@perma_del', 'as' => 'invoices.perma_del']);

    Route::post('service_requests_logs_mass_destroy', ['uses' => 'Admin\ServiceRequestLogsController@massDestroy', 'as' => 'service_requests_logs.mass_destroy']);
    
    // ajax routes
    Route::get('/getCharge','Admin\ServiceRequestsController@requestCharge');
    Route::get('/getCompanyDetails','Admin\ServiceRequestsController@getCompanyDetails');

    Route::get('/getTechnicians','Admin\ServiceRequestsController@getTechnicians');
    Route::get('/getCustomerAddress','Admin\ServiceRequestsController@getCustomerAddress');
    Route::get('/getSuggestedServiceCenter','Admin\ServiceRequestsController@getSuggestedServiceCenter');
    Route::get('/getTransporationCharge','Admin\ServiceRequestsController@getTransporationCharge');

    //service request ajax
    Route::post('/DataTableServiceRequestAjax','Admin\ServiceRequestsController@DataTableServiceRequestAjax');

    Route::get('/clearRequestFilterAjax','Admin\ServiceRequestsController@clearRequestFilterAjax');
    
});
  