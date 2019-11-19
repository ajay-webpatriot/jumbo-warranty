@extends('layouts.app')
@section('content')
    <style>
        .boxfont
        {
            font-size: 12px !important;
            
        }
        .info-box-text{
            white-space: normal;
            /* word-wrap: break-word; */
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <section class="">
                <ol class="breadcrumb">
                    <li><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">@lang('quickadmin.qa_dashboard')</li>
                </ol>
            </section>
            
            <section class="">
            <!-- <div class="row">
            <div class="col-md-12"> -->
                <div class="box">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <div class="box"> -->

                                <div class="box-header with-border">
                                    <h3 class="box-title">@lang('quickadmin.qa_dashboard')</h3>
                                </div>

                                <div class="box-body">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-6 col-xs-12">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" class="form-control pull-right" id="dateRangeFilter">
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.SERVICE_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.TECHNICIAN_ROLE_ID'))
                                                
                                                    <div class="col-md-12 col-sm-6 col-xs-12">
                                                        <div class="form-group">
                                                            <select class="form-control select2" id="companyDropdown" style="width: 100%;">

                                                                <option selected="selected" value="all">All Compaines</option>

                                                                @foreach($CompaninesName as $CompanyKey => $SingleCompanyName)
                                                                    <option value="{{$SingleCompanyName->CompanyId}}">{{$SingleCompanyName->CompanyName}}</option>
                                                                    
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-12 col-sm-6 col-xs-12 margin-bottom">
                                                    <button type="button" onclick="changeDateCompanyGetCount()" class="btn btn-block btn-primary">Filters</button>
                                                </div>

                                            </div>
                                        </div>
                                        
                                        <div class="col-md-9">
                                            <div class="row">
                                                
                                                <!-- Total PENDING complain -->
                                                {!! Form::open(['method' => 'POST', 'route' => ['admin.request_list'],'id' => 'installation_today','class' => 'requestlistform']) !!}
                                                
                                                    <!-- <div class="col-md-6 col-sm-6 col-xs-12" onclick="getRequestList('installation_today')" style="cursor: pointer;" id="installationTodays">
                                                        <div class="info-box bg-aqua">
                                                            <span class="info-box-icon"><i class="ion ion-ios-gear-outline"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text boxfont">TOTAL INSTALLATION REQUESTS</span>
                                                                <span class="info-box-number" id="installationToday">{{-- $installationToday --}}</span>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="small-box bg-aqua">
                                                            <div class="inner">
                                                                <h3 id="installationToday"></h3>

                                                                <p>TOTAL INSTALLATION REQUESTS</p>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="ion ion-stats-bars"></i>
                                                            </div>
                                                            <a href="javascript:void(0);" class="small-box-footer" onclick="getRequestList('installation_today')">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                        </div>
                                                    </div>

                                                    {{ Form::hidden('startDate', '', array('id' => 'startDate_installation_today')) }}
                                                    {{ Form::hidden('endDate', '', array('id' => 'endDate_installation_today')) }}
                                                    {{ Form::hidden('SelectedCompanyId', '', array('id' => 'SelectedCompanyId_installation_today')) }}
                                                    {{ Form::hidden('type', '', array('id' => 'type_installation_today')) }}

                                                {!! Form::close() !!}

                                                <!-- Total PENDING installation -->
                                                {!! Form::open(['method' => 'POST', 'route' => ['admin.request_list'],'id' => 'repair_today','class' => 'requestlistform']) !!}

                                                <!-- <div class="col-md-6 col-sm-6 col-xs-12" onclick="getRequestList('repair_today')" style="cursor: pointer;" id="repairTodays">
                                                    <div class="info-box bg-red">
                                                        <span class="info-box-icon"><i class="fa fa-tv"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text boxfont">TOTAL SERVICE REQUESTS</span>
                                                            <span class="info-box-number" id="repairToday">{{-- $repairToday --}}</span>
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="small-box bg-red">
                                                        <div class="inner">
                                                            <h3 id="repairToday"></h3>

                                                            <p>TOTAL SERVICE REQUESTS</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="ion ion-stats-bars"></i>
                                                        </div>
                                                        <a href="javascript:void(0);" class="small-box-footer" onclick="getRequestList('repair_today')">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>

                                                {{ Form::hidden('startDate', '', array('id' => 'startDate_repair_today')) }}
                                                {{ Form::hidden('endDate', '', array('id' => 'endDate_repair_today')) }}
                                                {{ Form::hidden('SelectedCompanyId', '', array('id' => 'SelectedCompanyId_repair_today')) }}
                                                {{ Form::hidden('type', '', array('id' => 'type_repair_today')) }}

                                                {!! Form::close() !!}
                                            </div>

                                            <div class="row">

                                                <!-- Total solved complain --> 
                                                {!! Form::open(['method' => 'POST', 'route' => ['admin.request_list'],'id' => 'closed_request','class' => 'requestlistform']) !!}

                                                <!-- <div class="col-md-6 col-sm-6 col-xs-12" onclick="getRequestList('closed_request')" style="cursor: pointer;" id="closededRequests">
                                                    <div class="info-box bg-yellow">
                                                        <span class="info-box-icon"><i class="ion ion-ios-gear-outline"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text boxfont">TOTAL REQUESTS CLOSED BY TODAY</span>
                                                            <span class="info-box-number" id="closededRequest">{{-- $closededRequest --}}</span>
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="small-box bg-yellow">
                                                        <div class="inner">
                                                            <h3 id="closededRequest"></h3>

                                                            <p>TOTAL REQUESTS CLOSED TILL NOW</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="ion ion-stats-bars"></i>
                                                        </div>
                                                        <a href="javascript:void(0);" class="small-box-footer" onclick="getRequestList('closed_request')">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>

                                                {{ Form::hidden('startDate', '', array('id' => 'startDate_closed_request')) }}
                                                {{ Form::hidden('endDate', '', array('id' => 'endDate_closed_request')) }}
                                                {{ Form::hidden('SelectedCompanyId', '', array('id' => 'SelectedCompanyId_closed_request')) }}
                                                {{ Form::hidden('type', '', array('id' => 'type_closed_request')) }}

                                                {!! Form::close() !!}

                                                <!-- Total solved installation -->
                                                {!! Form::open(['method' => 'POST', 'route' => ['admin.request_list'],'id' => 'delayed_request','class' => 'requestlistform']) !!}

                                                <!-- <div class="col-md-6 col-sm-6 col-xs-12" onclick="getRequestList('delayed_request')" style="cursor: pointer;" id="delayedRequests">
                                                    <div class="info-box bg-green">
                                                        <span class="info-box-icon"><i class="fa fa-tv"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text boxfont">TOTAL DELAYED REQUESTS FROM TODAY</span>
                                                            <span class="info-box-number" id="delayedRequest">{{-- $delayedRequest --}}</span>
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="small-box bg-green">
                                                        <div class="inner">
                                                            <h3 id="delayedRequest"></h3>

                                                            <p>TOTAL DELAYED REQUESTS</p>
                                                        </div>
                                                        <div class="icon">
                                                            <i class="ion ion-stats-bars"></i>
                                                        </div>
                                                        <a href="javascript:void(0);" class="small-box-footer" onclick="getRequestList('delayed_request')">More info <i class="fa fa-arrow-circle-right"></i></a>
                                                    </div>
                                                </div>

                                                {{ Form::hidden('startDate', '', array('id' => 'startDate_delayed_request')) }}
                                                {{ Form::hidden('endDate', '', array('id' => 'endDate_delayed_request')) }}
                                                {{ Form::hidden('SelectedCompanyId', '', array('id' => 'SelectedCompanyId_delayed_request')) }}
                                                {{ Form::hidden('type', '', array('id' => 'type_delayed_request')) }}

                                                {!! Form::close() !!}
                                                
                                            </div>
                                        </div>
                                        {{ Form::hidden('color', '', array('id' => 'storeHiddenColor')) }}
                                    </div>
                                </div>

                                <!-- <div class="" id="Addloader" style="display:none;">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div> -->

                            <!-- </div>  box-->

                        </div>
                    </div>

                    <div class="row" style="display:none;" id="ListView">
                        <div class="col-md-12">
                            <div class="box" id="boxcolor">
                                <div id="requestlistHtml">

                                

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="" id="Addloader" style="display:none;">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
               
                <div class="row">

                    <!-- Total PENDING complain -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text boxfont">TOTAL PENDING COMPLAINS</span>
                                <span class="info-box-number">{{$PendingComplainCount}}</span>
                            </div>
                        </div>
                    </div>
                       
                    <!-- Total PENDING installation -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-tv"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text boxfont">TOTAL PENDING INSTALLATION</span>
                                <span class="info-box-number">{{$PendingInstallationCount}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix visible-sm-block"></div>

                    <!-- Total solved installation -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-tv"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text boxfont">TOTAL SOLVED INSTALLATION</span>
                                <span class="info-box-number">{{$SolvedInstallationCount}}</span>
                            </div>
                        </div>
                    </div>
                        
                    <!-- Total solved complain -->
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-gear-outline"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text boxfont">TOTAL SOLVED COMPLAINS</span>
                                <span class="info-box-number">{{$SolvedComplainCount}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="box box-primary"> 

                            <div class="box-header with-border" data-toggle="collapse" href="#collapseRecentServiceRequests">
                                <h3 class="box-title">Recent Service Requests</h3>
                                    <span class="btn-box-tool glyphicon glyphicon-minus pull-right" style="font-size:12px;"></span>
                            </div>
                            
                            <div id="collapseRecentServiceRequests" class="box-body collapse in" role="tabpanel">
                                <ul class="products-list product-list-in-box">

                                    @if(!empty($ServiceTypeDetails) && count($ServiceTypeDetails) > 0)
                                        @foreach($ServiceTypeDetails as $key => $SingleServiceTypeDetail)
                                            <li class="item">
                                                <div class="product-img">
                                                    <a href="{{route('admin.service_requests.show',$SingleServiceTypeDetail->id)}}">
                                                        <span class="product-title"> JW{{ sprintf("%04d", $SingleServiceTypeDetail->id) }} </span> 
                                                    </a>
                                                </div>
                                                <div class="product-info">
                                                    <?php
                                                        $status = '';
                                                        $backgroundColor = '';
                                                    ?>
                                                    @if($SingleServiceTypeDetail->status != '')
                                                        <?php
                                                            $backgroundColor = $enum_status_color[$SingleServiceTypeDetail->status];
                                                            $status = '( '.$SingleServiceTypeDetail->status.' )';
                                                        ?>
                                                    @endif
                                                    <a href="{{route('admin.service_requests.show',$SingleServiceTypeDetail->id)}}" class="product-title">
                                                        {{$SingleServiceTypeDetail->servicerequest_title}}

                                                        @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                                                            <span class="pull-right"><i class="fa fa-rupee"></i> {{$SingleServiceTypeDetail->amount}}
                                                            </span>
                                                        @endif
                                                    </a> 
                                                    <span class="headerTitle" style="color:{{$backgroundColor}}">
                                                        {{$status}}
                                                    </span>
                                                    <span class="headerTitle">
                                                        ( {{date('d/m/Y',strtotime($SingleServiceTypeDetail->created_at))}} )
                                                    </span>
                                                    <span class="product-description">
                                                        {{$SingleServiceTypeDetail->customer_name}}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="item">
                                            <span class="product-description text-center">
                                                No Request Available
                                            </span>   
                                        </li>
                                    @endif

                                </ul>
                            </div>
                            @if(!empty($ServiceTypeDetails) && count($ServiceTypeDetails) > 0)
                            <div class="box-footer text-center">
                                <a href="{{ route('admin.service_requests.index') }}" class="uppercase">View All Service requests</a>
                            </div>
                            @endif 
                        </div> -->

                        <div class="box box-info">
                            <div class="box-header with-border">
                              <h3 class="box-title">Recent Service Requests</h3>

                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                              </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="">
                              <div class="table-responsive">
                                <table class="table no-margin recent-service-request table-striped table-hover table-bordered table-responsive">
                                  <thead>
                                    <tr>
                                        <th>@lang('quickadmin.service-request.fields.request-id')</th>
                                        <th>@lang('quickadmin.service-request.fields.title')</th>
                                        <th>@lang('quickadmin.service-request.fields.customer')</th>
                                        @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))

                                            <th>@lang('quickadmin.service-request.fields.amount')</th>

                                        @endif
                                        @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                                            <th>@lang('quickadmin.service-request.fields.created_by')</th>
                                        @endif
                                        <th>@lang('quickadmin.service-request.fields.created_date')</th>
                                        <th>@lang('quickadmin.service-request.fields.status')</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @if(!empty($ServiceTypeDetails) && count($ServiceTypeDetails) > 0)
                                        @foreach($ServiceTypeDetails as $key => $SingleServiceTypeDetail)
                                        <?php
                                            $createdByName = '-';
                                            if($SingleServiceTypeDetail->createdbyName != ''){
                                                $createdByName = $SingleServiceTypeDetail->createdbyName;
                                            }
                                        ?>
                                        <tr>
                                            <td align="center">
                                                <a href="{{route('admin.service_requests.show',$SingleServiceTypeDetail->id)}}">
                                                        <span class="product-title"> JW{{ sprintf("%04d", $SingleServiceTypeDetail->id) }} </span> 
                                                    </a>
                                            </td>
                                            <td>
                                                <a href="{{route('admin.service_requests.show',$SingleServiceTypeDetail->id)}}" class="product-title">
                                                        {{$SingleServiceTypeDetail->servicerequest_title}}

                                                        <!-- <span style="margin:auto; display:table;">{{$status}}</span> -->
                                                </a>
                                            </td>
                                            <td>{{ $SingleServiceTypeDetail->customer_name}}</td>
                                            @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                                                <td align="right" nowrap><i class="fa fa-rupee"></i> <?php echo number_format($SingleServiceTypeDetail->amount, 2);?>
                                                </td>
                                            @endif
                                            @if(auth()->user()->role_id != config('constants.SERVICE_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.TECHNICIAN_ROLE_ID'))
                                                <td align="left">{{ $createdByName }} </td>
                                            @endif
                                            <td align="center">
                                            {{date('d/m/Y',strtotime($SingleServiceTypeDetail->created_at))}}
                                            </td>
                                            <?php
                                                $status = '';
                                                $backgroundColor = '';
                                            ?>
                                            @if($SingleServiceTypeDetail->status != '')
                                                <?php
                                                    $backgroundColor = $enum_status_color[$SingleServiceTypeDetail->status];
                                                    $status = $SingleServiceTypeDetail->status;
                                                ?>
                                            @endif
                                            <td align="center">
                                                <span class="headerTitle" style="color:{{$backgroundColor}}">
                                                    {{$status}}
                                                </span>
                                                @if($SingleServiceTypeDetail->is_reopen == 1)
                                                    <span class="label label-primary paddingMarginLeftLabel">Re-opened</span>
                                                @endif
                                            </td>
                                            
                                        </tr>
                                        @endforeach
                                    @endif
                                  </tbody>
                                </table>
                              </div>
                              <!-- /.table-responsive -->
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer clearfix" style="">
                            
                                @if(auth()->user()->role_id == config('constants.SUPER_ADMIN_ROLE_ID') || auth()->user()->role_id == config('constants.ADMIN_ROLE_ID'))

                                    <a href="{{ route('admin.service_requests.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>

                                @endif

                                @if(!empty($ServiceTypeDetails) && count($ServiceTypeDetails) > 0)
                                    <a href="{{ route('admin.service_requests.index') }}" class="btn btn-sm btn-default btn-flat pull-right">View All Service requests</a>
                                @endif
                            </div>
                            <!-- /.box-footer -->
                        </div>
                    
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('javascript')
    @parent

    <script src="{{ url('adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(function(){
            $('#dateRangeFilter').daterangepicker({
                opens: 'right',
                locale: {
                    format: "{{ config('app.date_format_moment') }}"
                }
            });
            
        });
        $(document).ready(function(){
            changeDateCompanyGetCount();
        });
        // $('#dateRangeFilter').change(function() {
        //     changeDateCompanyGetCount();
        // });

        // $('#companyDropdown').change(function() {
        //     changeDateCompanyGetCount();
        // });
        
        function changeDateCompanyGetCount() {

            $('#ListView').hide();
            
            var date = $('#dateRangeFilter').val();
            var SelectedCompanyId = $('select#companyDropdown option:selected').val();
            
            var dates = date.split(" - ");
            var startDate = dates[0];
            var endDate = dates[1];

            $.ajax({
                type:'GET',
                url:APP_URL+'/admin/dashboard',
                data:{
                    'startDate':startDate,
                    'endDate':endDate,
                    'SelectedCompanyId':SelectedCompanyId
                },
                dataType: "json",
                success:function(data) {
                    $("#installationToday").html(data.installationToday);
                    $("#repairToday").html(data.repairToday);
                    $("#delayedRequest").html(data.delayedRequest);
                    $("#closededRequest").html(data.closededRequest);
                }
            });
        }

        function getRequestList(type) {
            
            var date = $('#dateRangeFilter').val();
            var SelectedCompanyId = $('select#companyDropdown option:selected').val();
            
            var dates = date.split(" - ");
            var startDate = dates[0];
            var endDate = dates[1];
           
            $('#startDate_'+type).val(startDate);
            $('#endDate_'+type).val(endDate);
            $('#SelectedCompanyId_'+type).val(SelectedCompanyId);
            $('#type_'+type).val(type);

            $('#'+type).submit();
        }

        $('.requestlistform').on('submit', function(e){
            var color = $('#storeHiddenColor').val();
            $('#boxcolor').removeClass('box-'+color);
            $('#Addloader').removeAttr('style');
            $('#Addloader').addClass('overlay');

            e.preventDefault(); 
            var formData = $(this).serializeArray();

            $.ajax({
                type:'POST',
                url:APP_URL+'/admin/dashboard/listdata',
                data:{
                    "formData":formData,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success:function(data) {
                    console.log(data.color);
                    $('#storeHiddenColor').val(data.color);
                    $('#boxcolor').addClass('box-'+data.color);
                    $('#Addloader').hide();
                    $('#Addloader').removeClass('overlay');
                    $('#ListView').removeAttr('style');
                    $('#requestlistHtml').html(data.html);
                    $('.datatable').dataTable();
                    // $("#installationToday").html(data.installationToday);
                    // $("#repairToday").html(data.repairToday);
                    // $("#delayedRequest").html(data.delayedRequest);
                    // $("#closededRequest").html(data.closededRequest);
                }
            });
            
        });
        

    </script>
@stop