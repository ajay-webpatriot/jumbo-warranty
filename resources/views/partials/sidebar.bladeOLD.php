@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">

             

            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ url('/') }}">
                    <i class="fa fa-wrench"></i>
                    <span class="title">@lang('quickadmin.qa_dashboard')</span>
                </a>
            </li>
            @can('permission_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>@lang('quickadmin.permission-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                   @can('permission_access')
                    <li>
                        <a href="{{ route('admin.permissions.index') }}">
                            <i class="fa fa-briefcase"></i>
                            <span>@lang('quickadmin.permissions.title')</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan
            @can('user_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>@lang('quickadmin.user-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('role_access')
                    <li>
                        <a href="{{ route('admin.roles.index') }}">
                            <i class="fa fa-briefcase"></i>
                            <span>@lang('quickadmin.roles.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('user_access')
                    @if(auth()->user()->role_id ==  $_ENV['COMPANY_ADMIN_ROLE_ID'])
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-user"></i>
                            <span>@lang('quickadmin.users.companyUserTitle')</span>
                        </a>
                    </li>
                    @elseif(auth()->user()->role_id == $_ENV['SERVICE_ADMIN_ROLE_ID'])
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-user"></i>
                            <span>@lang('quickadmin.users.technicianTitle')</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-user"></i>
                            <span>@lang('quickadmin.users.title')</span>
                        </a>
                    </li>
                    @endif
                    
                    @endcan
                    
                </ul>
            </li>@endcan
            
            @can('product_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-product-hunt"></i>
                    <span>@lang('quickadmin.product-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('category_access')
                    <li>
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.categories.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('product_access')
                    <li>
                        <a href="{{ route('admin.products.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.products.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('product_part_access')
                    <li>
                        <a href="{{ route('admin.product_parts.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.product-parts.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>@endcan
            
            @can('company_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.company-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('company_access')
                    <li>
                        <a href="{{ route('admin.companies.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.company.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('customer_access')
                    <li>
                        <a href="{{ route('admin.customers.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.customers.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('assign_product_access')
                    <li>
                        <a href="{{ route('admin.assign_products.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.assign-product.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('assign_part_access')
                    <li>
                        <a href="{{ route('admin.assign_parts.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.assign-parts.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('service_request_access')
                    <li>
                        <a href="{{ route('admin.service_requests.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.service-request.title')</span>
                        </a>
                    </li>@endcan
                    
                    @can('service_request_log_access')
                    <li>
                        <a href="{{ route('admin.service_request_logs.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.service-request-log.title')</span>
                        </a>
                    </li>@endcan
                    
                </ul>
            </li>@endcan
            
            @can('service_center_management_access')
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.service-center-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    @can('service_center_access')
                    <li>
                        <a href="{{ route('admin.service_centers.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.service-center.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                </ul>
            </li>@endcan
            
            @can('manage_charge_access')
            <li>
                <a href="{{ route('admin.manage_charges.index') }}">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.manage-charges.title')</span>
                </a>
            </li>@endcan
            
            @can('invoice_access')
            <li>
                <a href="{{ route('admin.invoices.index') }}">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.invoices.title')</span>
                </a>
            </li>@endcan
            

            

            



            <li class="{{ $request->segment(1) == 'change_password' ? 'active' : '' }}">
                <a href="{{ route('auth.change_password') }}">
                    <i class="fa fa-key"></i>
                    <span class="title">@lang('quickadmin.qa_change_password')</span>
                </a>
            </li>

            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="fa fa-arrow-left"></i>
                    <span class="title">@lang('quickadmin.qa_logout')</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

