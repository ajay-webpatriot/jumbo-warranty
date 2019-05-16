@extends('layouts.app')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">

                <div class="box-header with-border" data-toggle="collapse" href="#collapseRecentServiceRequests">
                    <h3 class="box-title"> {{ $typeTitle }} </h3>
                    <span class="btn-box-tool glyphicon glyphicon-minus pull-right" style="font-size:12px;"></span> 
                </div>
                
                <div id="collapseRecentServiceRequests" class="box-body collapse in" role="tabpanel">
                    <ul class="products-list product-list-in-box">

                        @if(count($dataByType) > 0)
                            @foreach($dataByType as $key => $SingleServiceTypeDetail)

                                <li class="item">
                                    <div class="product-img">
                                        <a href="{{ route('admin.service_requests.show',$SingleServiceTypeDetail->id) }}" target="_blank">
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
                                        <a href="{{ route('admin.service_requests.show',$SingleServiceTypeDetail->id) }}" class="product-title" target="_blank">
                                            {{ $SingleServiceTypeDetail->servicerequest_title }}

                                            @if(auth()->user()->role_id != config('constants.COMPANY_ADMIN_ROLE_ID') && auth()->user()->role_id != config('constants.COMPANY_USER_ROLE_ID'))
                                                <span class="label label-info pull-right"><i class="fa fa-rupee"></i> {{ $SingleServiceTypeDetail->amount }}
                                                </span>
                                            @endif
                                        </a> 
                                        <span class="headerTitle" style="color:{{ $backgroundColor }}">
                                            {{ $status }} 
                                        </span>
                                        <span class="product-description">
                                            {{ $SingleServiceTypeDetail->customer_name }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="item">
                                <span class="product-description text-center">
                                    No Requests
                                </span>   
                            </li>
                       @endif

                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('javascript')
    @parent
@stop