@extends('layouts.app')
@section('content')
<style>
/* .loading { color: green; }
#loading { display:none; color:green; font-size:20px; } */
</style>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-{{ $color }}">

                <div class="box-header with-border scroll text-center" data-toggle="collapse" href="#collapseRecentServiceRequests">
                    <h3 class="box-title"> {{ $typeTitle }} </h3>
                    <span class="btn-box-tool glyphicon glyphicon-minus pull-right" style="font-size:12px;"></span> 
                </div>
                
                <div id="collapseRecentServiceRequests" class="box-body collapse in table-responsive " role="tabpanel">
                    <!-- <ul class="products-list product-list-in-box" id="uiandli"> -->

                        {{-- @if(count($dataByType) > 0) --}}
                            {{-- @foreach($dataByType as $key => $SingleServiceTypeDetail) --}}

                                <!-- <li class="item"> -->
                                    <!-- <div class="product-img">
                                        <a href="{{-- route('admin.service_requests.show',$SingleServiceTypeDetail->id) --}}" target="_blank">
                                            <span class="product-title"> JW{{-- sprintf("%04d", $SingleServiceTypeDetail->id) --}} </span> 
                                        </a>
                                    </div> -->

                                    <!-- <div class="product-info"> -->
                                        <?php
                                            // $status = '';
                                            // $backgroundColor = '';
                                        ?>
                                        {{-- @if($SingleServiceTypeDetail->status != '') --}}
                                            <?php
                                                // $backgroundColor = $enum_status_color[$SingleServiceTypeDetail->status];
                                                // $status = '( '.$SingleServiceTypeDetail->status.' )';
                                            ?>
                                        {{-- @endif --}}
                                        <!-- <a href="{{-- route('admin.service_requests.show',$SingleServiceTypeDetail->id) --}}" class="product-title" target="_blank">
                                            {{-- $SingleServiceTypeDetail->servicerequest_title --}}

                                            {{-- @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID')) -- }}
                                                <span class="label label-info pull-right"><i class="fa fa-rupee"></i> {{-- $SingleServiceTypeDetail->amount --}}
                                                </span>
                                            {{-- @endif --}}
                                        </a>  -->
                                        <!-- <span class="headerTitle" style="color:{{-- $backgroundColor --}}">
                                            {{-- $status --}} 
                                        </span> -->
                                        <!-- <span class="product-description">
                                            {{-- $SingleServiceTypeDetail->customer_name --}}
                                        </span> -->
                                    <!-- </div>
                                </li> -->
                            {{-- @endforeach --}}
                        {{-- @else --}}
                            <!-- <li class="item">
                                <span class="product-description text-center">
                                    No Requests
                                </span>   
                            </li> -->
                       {{-- @endif --}}

                    <!-- </ul> -->
                    <table  id="" class="table table-bordered table-striped datatable" width="100%">
                        <thead>
                            <tr>
                                <th>@lang('quickadmin.service-request.fields.request-id')</th>
                                <th>@lang('quickadmin.service-request.fields.company')</th>
                                <th>@lang('quickadmin.service-request.fields.customer')</th>
                                <th>@lang('quickadmin.service-request.fields.service-type')</th>
                                <th>@lang('quickadmin.service-request.fields.service-center')</th>
                                <th>@lang('quickadmin.service-request.fields.product')</th>
                                @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))

                                    <th>@lang('quickadmin.service-request.fields.amount')</th>

                                @endif
                                <th>@lang('quickadmin.service-request.fields.created_date')</th>
                                <th>@lang('quickadmin.service-request.fields.status')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($dataByType as $key => $SingleServiceTypeDetail)
                            <?php
                                $status = '';
                                $backgroundColor = '';
                                $company_name = '';
                                $firstname = '';
                                $lastname= '';
                                $service_type= '';
                                $service_centers= '';
                                $product= '';
                                $amount= '';

                                if( $SingleServiceTypeDetail->company != ''){
                                    $company_name = $SingleServiceTypeDetail->company->name;
                                }
                                if( $SingleServiceTypeDetail->customer != ''){
                                    
                                    $firstname = $SingleServiceTypeDetail->customer->firstname;
                                    $lastname= $SingleServiceTypeDetail->customer->lastname;
                                }
                            
                                if( $SingleServiceTypeDetail->service_type != ''){
                                
                                    $service_type= $SingleServiceTypeDetail->service_type;
                                
                                }

                                if( $SingleServiceTypeDetail->service_center != ''){
                                
                                    $service_centers=  $SingleServiceTypeDetail->service_center->name;
                                
                                }
                                if( $SingleServiceTypeDetail->product != ''){
                                
                                    $product= $SingleServiceTypeDetail->product->name;
                                
                                }
                                if($SingleServiceTypeDetail->amount){
                                    $amount= $SingleServiceTypeDetail->amount;
                                }
                            ?>
                            @if($SingleServiceTypeDetail->status != '')
                                <?php
                                    $backgroundColor = $enum_status_color[$SingleServiceTypeDetail->status];
                                    $status = $SingleServiceTypeDetail->status;
                                ?> 
                            @endif
                                <tr>
                                    <td><a href="{{ route('admin.service_requests.show',$SingleServiceTypeDetail->id) }}" target="_blank"> JW{{ sprintf("%04d", $SingleServiceTypeDetail->id) }} </a></td>
                                    <td>{{ ucfirst($company_name) }}</td>
                                    <td>{{ ucfirst($firstname) }} {{ ucfirst($lastname) }}</td>
                                    <td>{{ $service_type }}</td>
                                    <td>{{ $service_centers }}</td>
                                    <td>{{ $product }}</td>
                                    @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))

                                        <td>{{ $amount }}</td>

                                    @endif

                                    <td>{{ date('Y-m-d',strtotime($SingleServiceTypeDetail->created_at)) }}</td>
                                    <td>
                                        <span style="color:{{ $backgroundColor }}">
                                                {{ $status }} 
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- <span id="loading">Loading Please wait...</span> -->
            </div>
        </div>
    </div>

@endsection
@section('javascript')
    @parent
    <script>
    // $(function() {
    //     loadResults(0);
    //     $(window).scroll(function() {
            
    //         if($("#loading").css('display') == 'none') {
    //             // console.log($(this).scrollTop());
                
    //             // if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
    //             if($(this).scrollTop() > "200"){
    //                 var limitStart = $("#uiandli li").length;
    //                 loadResults(limitStart)
    //                 console.log(limitStart);
    //             }
    //         }
    //     });
    // });
    // function loadResults(limitStart) {
    //     $("#loading").show();
    //     $.ajax({
    //         url:APP_URL+'/admin/dashboard/listdata',
    //         type: "POST",
    //         // dataType: "json",
    //         data: {
    //             'limitStart': limitStart,
    //             'type':"{{ $type }}",
    //             'companyId':"{{ $companyId }}",
    //             'startDate':"{{ $startDate }}",
    //             'endDate':"{{ $endDate }}",
    //             'todayDate':"{{ $todayDate }}",
    //             "_token": "{{ csrf_token() }}",
    //         },
    //         success: function(data) {                
    //             $.each(data.dataByType, function(key, value) {
    //                 console.log(key);
    //                 console.log('<br>==============================<br>');
    //                 console.log(value.id);
    //                 var url = '{{ route("admin.service_requests.show", ":id") }}';
    //                 url = url.replace(':id', value.id);

    //                 var productId = addLeadingZeros(value.id,4);
    //                 // "<li id='"+key.id+"'>"+value.title+"<img src='"+value.image+"' width='200px' height='200px'></li>"

    //                 $("#uiandli").append('<li class="item"><div class="product-img"><a href="'+url+'" target="_blank"><span class="product-title"> JW'+productId+'</span></a></div></li>');
    //             });
    //             $("#loading").hide();
    //         }
    //     });
    // }
    // function addLeadingZeros (n, length)
    // {
    //     var str = (n > 0 ? n : -n) + "";
    //     var zeros = "";
    //     for (var i = length - str.length; i > 0; i--)
    //         zeros += "0";
    //     zeros += str;
    //     return n >= 0 ? zeros : "-" + zeros;
    // }

    </script>
@stop