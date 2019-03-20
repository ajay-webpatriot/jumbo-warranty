@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="{{ $request->segment(1) == 'home' ? 'active' : '' }}">
                <a href="{{ route('admin.index') }}">
                    <i class="fa fa-dashboard"></i>
                    <span class="title">@lang('quickadmin.qa_dashboard')</span>
                </a>
            </li>
            <!-- can('service_request_access') -->
            @can('manageServiceRequest')
            <li>
                <a href="{{ route('admin.service_requests.index') }}">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.service-request.title')</span>
                </a>
            </li>
            @endcan
            <!-- endcan -->
            @can('user_management_access')
            <!-- can('manageUser') -->
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
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.roles.title')</span>
                        </a>
                    </li>
                    @endcan
                    
                    <!-- can('user_access') -->
                    
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.users.title')</span>
                        </a>
                    </li>
                    
                    <!-- endcan -->
                    
                </ul>
            </li>@endcan
            <!-- can('product_management_access') -->
            @can('manageCategory','manageProduct','manageParts')
                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                    <li class="treeview">
                        <a href="#">
                            <i class="fa fa-product-hunt"></i>
                            <span>@lang('quickadmin.product-management.title')</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <!-- can('category_access') -->
                            @can('manageCategory')
                            <li>
                                <a href="{{ route('admin.categories.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('quickadmin.categories.title')</span>
                                </a>
                            </li>
                            @endcan
                            <!-- endcan -->
                            
                            <!-- can('product_access') -->
                            @can('manageProduct')
                            <li>
                                <a href="{{ route('admin.products.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('quickadmin.products.title')</span>
                                </a>
                            </li>
                            @endcan
                            <!-- endcan -->
                            
                            <!-- can('product_part_access') -->
                            @can('manageParts')
                            <li>
                                <a href="{{ route('admin.product_parts.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('quickadmin.product-parts.title')</span>
                                </a>
                            </li>
                            @endcan
                            <!-- endcan -->
                            
                        </ul>
                    </li>
                @endif
            @endcan
            
            @can('manageCompany')
            <!-- can('company_management_access') -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-building"></i>
                    <span>@lang('quickadmin.company-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <!-- can('company_access') -->
                    @can('manageCompany')

                        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                            <li>
                                <a href="{{ route('admin.companies.index') }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('quickadmin.company.title')</span>
                                </a>
                            </li>
                        @elseif(auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID'))
                            <li>
                                <a href="{{ route('admin.companies.show',[auth()->user()->company_id]) }}">
                                    <i class="fa fa-circle-o"></i>
                                    <span>@lang('quickadmin.company.company-info')</span>
                                </a>
                            </li>
                        @endif
                        <!-- endcan -->
                        
                        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                        <li>
                            <a href="{{ route('admin.company_admins.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('quickadmin.company-admins.menu-title')</span>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.COMPANY_ADMIN_ROLE_ID'))
                        <li>
                            <a href="{{ route('admin.company_users.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('quickadmin.company-users.menu-title')</span>
                            </a>
                        </li>
                        @endif

                        
                        <!-- can('customer_access') -->
                        <!-- can('manageCompany') -->
                        <li>
                            <a href="{{ route('admin.customers.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('quickadmin.customers.title')</span>
                            </a>
                        </li>
                        <!-- endcan -->
                        
                        <!-- can('assign_product_access') -->
                        <li>
                            <a href="{{ route('admin.assign_products.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('quickadmin.assign-product.title')</span>
                            </a>
                        </li>
                        <!-- endcan -->
                        
                        <!-- can('assign_part_access') -->
                        <li>
                            <a href="{{ route('admin.assign_parts.index') }}">
                                <i class="fa fa-circle-o"></i>
                                <span>@lang('quickadmin.assign-parts.title')</span>
                            </a>
                        </li>
                    @endcan
                    <!-- endcan -->
                    
                    
                    
                    <!-- can('service_request_log_access') -->
                    <!-- can('manageServiceRequestLog')
                    <li>
                        <a href="{{ route('admin.service_request_logs.index') }}">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.service-request-log.title')</span>
                        </a>
                    </li>
                    endcan -->
                    <!-- endcan -->
                    
                </ul>
            </li>@endcan
            
            @can('manageServiceCenter')
            <!-- can('service_center_management_access') -->
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.service-center-management.title')</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <!-- can('service_center_access') -->
                    @if(auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID')
                        || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                    <li>
                        <!-- service center admin and technician can only view details -->
                        <a href="{{ route('admin.service_centers.show',[auth()->user()->service_center_id]) }}">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.service-center.service-center-info')</span>
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('admin.service_centers.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.service-center.title')</span>
                        </a>
                    </li>
                    @endif
                    <!-- endcan -->
                    @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                    || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                    <li>
                        <a href="{{ route('admin.service_center_admins.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.service-center-admin.title')</span>
                        </a>
                    </li>
                    @endif

                    @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                    || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID')
                    || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID'))
                    <li>
                        <a href="{{ route('admin.technicians.index') }}">
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.users.technicianTitle')</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>@endcan
            
            @can('manage_charge_access')
            <li>
                <a href="{{ route('admin.manage_charges.index') }}">
                    <i class="fa fa-gears"></i>
                    <span>@lang('quickadmin.manage-charges.title')</span>
                </a>
            </li>@endcan
            
            @can('manageInvoices')
            <!-- can('invoice_access') -->
                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID')
                || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))
                    <li>
                        <a href="{{ route('admin.invoices.index') }}" target="_blank">
                            <i class="fa fa-gears"></i>
                            <span>@lang('quickadmin.invoices.title')</span>
                        </a>
                    </li>
                @endif
            @endcan
            
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
                            <i class="fa fa-circle-o"></i>
                            <span>@lang('quickadmin.permissions.title')</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

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

