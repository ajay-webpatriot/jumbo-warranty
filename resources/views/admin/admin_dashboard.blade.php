@extends('layouts.app')
@section('content')
    <style>
        .boxfont
        {
            font-size: 12px !important;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <section class="content-header">
                <h1>
                    Dashboard
                </h1>
                <ol class="breadcrumb">
                    <li><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">@lang('quickadmin.qa_dashboard')</li>
                </ol>
            </section>

            <section class="content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">

                            <div class="box-header with-border">
                                <h3 class="box-title">Monthly Recap Report</h3>
                            </div>

                            <div class="box-body">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <!-- <label>Date range:</label> -->
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" class="form-control pull-right" id="dateRangeFilter">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-6 col-xs-12">
                                                <div class="form-group">
                                                    <!-- <label>Company</label> -->
                                                    <select class="form-control select2" id="companyDropdown" style="width: 100%;">

                                                        <option selected="selected" value="all">All Compaines</option>

                                                        @foreach($CompaninesName as $CompanyKey => $SingleCompanyName)
                                                            <option value="{{$SingleCompanyName->CompanyId}}">{{$SingleCompanyName->CompanyName}}</option>
                                                            
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-6 col-xs-12 margin-bottom">
                                               
                                                <!-- <label></label> -->
                                                <button type="button" onclick="changeDateCompanyGetCount()" class="btn btn-block btn-primary">Filters</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-9">
                                        <div class="row">
                                            <!-- <div class="col-md-12">
                                                <div class="row"> -->

                                                    <!-- Total PENDING complain -->
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="info-box bg-aqua">
                                                            <span class="info-box-icon"><i class="ion ion-ios-gear-outline"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text boxfont">TOTAL INSTALLATION REQUESTS</span>
                                                                <span class="info-box-number" id="installationToday">{{-- $installationToday --}}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Total PENDING installation -->
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="info-box bg-red">
                                                            <span class="info-box-icon"><i class="fa fa-tv"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text boxfont">TOTAL SERVICE REQUESTS</span>
                                                                <span class="info-box-number" id="repairToday">{{-- $repairToday --}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- </div>
                                            </div> -->
                                        </div>

                                        <div class="row">
                                            <!-- <div class="col-md-12">
                                                <div class="row"> -->

                                                    <!-- Total solved installation -->
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="info-box bg-green">
                                                            <span class="info-box-icon"><i class="fa fa-tv"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text boxfont">TOTAL DELAYED REQUESTS</span>
                                                                <span class="info-box-number" id="delayedRequest">{{-- $delayedRequest --}}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                
                                                    <!-- Total solved complain -->
                                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                                        <div class="info-box bg-yellow">
                                                            <span class="info-box-icon"><i class="ion ion-ios-gear-outline"></i></span>
                                                            <div class="info-box-content">
                                                                <span class="info-box-text boxfont">TOTAL REQUESTS CLOSED</span>
                                                                <span class="info-box-number" id="closededRequest">{{-- $closededRequest --}}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <!-- </div>
                                            </div> -->
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
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
                        <div class="box box-primary">

                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Service Requests</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
                                </div>
                            </div>
                            
                            <div class="box-body">
                                <ul class="products-list product-list-in-box">

                                    @foreach($ServiceTypeDetails as $key => $SingleServiceTypeDetail)
                                        <li class="item">
                                                <a href="{{route('admin.service_requests.show',$SingleServiceTypeDetail->id)}}" class="product-title">
                                                    {{$SingleServiceTypeDetail->servicerequest_title}}
                                                    <span class="label label-info pull-right"><i class="fa fa-rupee"></i> {{$SingleServiceTypeDetail->amount}}
                                                    </span>
                                                </a>
                                                
                                                <span class="product-description">
                                                    {{$SingleServiceTypeDetail->customer_name}}
                                                </span>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                           
                            <div class="box-footer text-center">
                                <a href="{{ route('admin.service_requests.index') }}" class="uppercase">View All Products</a>
                            </div>
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
    </script>
@stop