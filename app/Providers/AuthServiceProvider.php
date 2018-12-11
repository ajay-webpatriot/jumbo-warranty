<?php

namespace App\Providers;

use App\Role;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $user = \Auth::user();

        
        // Auth gates for: User management
        Gate::define('user_management_access', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Roles
        Gate::define('role_access', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_create', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_edit', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_view', function ($user) {
            return in_array($user->role_id, [1]);
        });
        Gate::define('role_delete', function ($user) {
            return in_array($user->role_id, [1]);
        });

        // Auth gates for: Users
        Gate::define('user_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 7]);
        });
        Gate::define('user_create', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 7]);
        });
        Gate::define('user_edit', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 7]);
        });
        Gate::define('user_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 7]);
        });
        Gate::define('user_delete', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 7]);
        });

        // Auth gates for: Product management
        Gate::define('product_management_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4]);
        });

        // Auth gates for: Categories
        Gate::define('category_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('category_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('category_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('category_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('category_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Products
        Gate::define('product_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('product_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Product parts
        Gate::define('product_part_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_part_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_part_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('product_part_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('product_part_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Company management
        Gate::define('company_management_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4]);
        });

        // Auth gates for: Company
        Gate::define('company_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('company_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('company_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('company_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('company_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Customers
        Gate::define('customer_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('customer_create', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('customer_edit', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('customer_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('customer_delete', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });

        // Auth gates for: Assign product
        Gate::define('assign_product_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_product_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_product_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_product_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('assign_product_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Assign parts
        Gate::define('assign_part_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_part_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_part_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('assign_part_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('assign_part_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Service request
        Gate::define('service_request_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('service_request_create', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });
        Gate::define('service_request_edit', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('service_request_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('service_request_delete', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 7]);
        });

        // Auth gates for: Service request log
        Gate::define('service_request_log_access', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });

        // Auth gates for: Service center management
        Gate::define('service_center_management_access', function ($user) {
            return in_array($user->role_id, [1, 3, 5, 6]);
        });

        // Auth gates for: Service center
        Gate::define('service_center_access', function ($user) {
            return in_array($user->role_id, [1, 3, 5]);
        });
        Gate::define('service_center_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('service_center_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('service_center_view', function ($user) {
            return in_array($user->role_id, [1, 3, 5]);
        });
        Gate::define('service_center_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Manage charges
        Gate::define('manage_charge_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('manage_charge_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('manage_charge_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('manage_charge_view', function ($user) {
            return in_array($user->role_id, [1, 3, 4, 5, 6, 7]);
        });
        Gate::define('manage_charge_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

        // Auth gates for: Invoices
        Gate::define('invoice_access', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('invoice_create', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('invoice_edit', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('invoice_view', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });
        Gate::define('invoice_delete', function ($user) {
            return in_array($user->role_id, [1, 3]);
        });

    }
}
