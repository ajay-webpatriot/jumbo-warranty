@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.service-center.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.name')</th>
                            <td field-key='name'>{{ $service_center->name }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.address-1')</th>
                            <td field-key='address_1'>{{ $service_center->address_1 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.location')</th>
                            <td>
                    <strong>{{ $service_center->location_address }}</strong>
                    <div id='location-map' style='width: 600px;height: 300px;' class='map' data-key='location' data-latitude='{{$service_center->location_latitude}}' data-longitude='{{$service_center->location_longitude}}'></div>
                </td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.commission')</th>
                            <td field-key='commission'>{{ $service_center->commission }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.address-2')</th>
                            <td field-key='address_2'>{{ $service_center->address_2 }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.city')</th>
                            <td field-key='city'>{{ $service_center->city }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.state')</th>
                            <td field-key='state'>{{ $service_center->state }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.zipcode')</th>
                            <td field-key='zipcode'>{{ $service_center->zipcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.status')</th>
                            <td field-key='status'>{{ $service_center->status }}</td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    
<li role="presentation" class="active"><a href="#users" aria-controls="users" role="tab" data-toggle="tab">Users</a></li>
<li role="presentation" class=""><a href="#service_request" aria-controls="service_request" role="tab" data-toggle="tab">Service request</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    
<div role="tabpanel" class="tab-pane active" id="users">
<table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('quickadmin.users.fields.role')</th>
                        <th>@lang('quickadmin.users.fields.company')</th>
                        <th>@lang('quickadmin.users.fields.service-center')</th>
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.phone')</th>
                        <th>@lang('quickadmin.users.fields.address-1')</th>
                        <th>@lang('quickadmin.users.fields.address-2')</th>
                        <th>@lang('quickadmin.users.fields.city')</th>
                        <th>@lang('quickadmin.users.fields.state')</th>
                        <th>@lang('quickadmin.users.fields.zipcode')</th>
                        <th>@lang('quickadmin.users.fields.location')</th>
                        <th>@lang('quickadmin.users.fields.email')</th>
                        <th>@lang('quickadmin.users.fields.status')</th>
                                                <th>&nbsp;</th>

        </tr>
    </thead>

    <tbody>
        @if (count($users) > 0)
            @foreach ($users as $user)
                <tr data-entry-id="{{ $user->id }}">
                    <td field-key='role'>{{ $user->role->title or '' }}</td>
                                <td field-key='company'>{{ $user->company->name or '' }}</td>
                                <td field-key='service_center'>{{ $user->service_center->name or '' }}</td>
                                <td field-key='name'>{{ $user->name }}</td>
                                <td field-key='phone'>{{ $user->phone }}</td>
                                <td field-key='address_1'>{{ $user->address_1 }}</td>
                                <td field-key='address_2'>{{ $user->address_2 }}</td>
                                <td field-key='city'>{{ $user->city }}</td>
                                <td field-key='state'>{{ $user->state }}</td>
                                <td field-key='zipcode'>{{ $user->zipcode }}</td>
                                <td field-key='location'>{{ $user->location_address }}</td>
                                <td field-key='email'>{{ $user->email }}</td>
                                <td field-key='status'>{{ $user->status }}</td>
                                                                <td>
                                    @can('user_view')
                                    <a href="{{ route('admin.users.show',[$user->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('user_edit')
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('user_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.users.destroy', $user->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="20">@lang('quickadmin.qa_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
<div role="tabpanel" class="tab-pane " id="service_request">
<table class="table table-bordered table-striped {{ count($service_requests) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('quickadmin.service-request.fields.company')</th>
                        <th>@lang('quickadmin.service-request.fields.service-type')</th>
                        <th>@lang('quickadmin.service-request.fields.service-center')</th>
                        <th>@lang('quickadmin.service-request.fields.technician')</th>
                        <th>@lang('quickadmin.service-request.fields.call-type')</th>
                        <th>@lang('quickadmin.service-request.fields.call-location')</th>
                        <th>@lang('quickadmin.service-request.fields.priority')</th>
                        <th>@lang('quickadmin.service-request.fields.product')</th>
                        <th>@lang('quickadmin.service-request.fields.make')</th>
                        <th>@lang('quickadmin.service-request.fields.model-no')</th>
                        <th>@lang('quickadmin.service-request.fields.is-item-in-warrenty')</th>
                        <th>@lang('quickadmin.service-request.fields.bill-no')</th>
                        <th>@lang('quickadmin.service-request.fields.bill-date')</th>
                        <th>@lang('quickadmin.service-request.fields.serial-no')</th>
                        <th>@lang('quickadmin.service-request.fields.mop')</th>
                        <th>@lang('quickadmin.service-request.fields.purchase-from')</th>
                        <th>@lang('quickadmin.service-request.fields.adavance-amount')</th>
                        <th>@lang('quickadmin.service-request.fields.service-charge')</th>
                        <th>@lang('quickadmin.service-request.fields.service-tag')</th>
                        <th>@lang('quickadmin.service-request.fields.complain-details')</th>
                        <th>@lang('quickadmin.service-request.fields.note')</th>
                        <th>@lang('quickadmin.service-request.fields.completion-date')</th>
                        <th>@lang('quickadmin.service-request.fields.parts')</th>
                        <th>@lang('quickadmin.service-request.fields.additional-charges')</th>
                        <th>@lang('quickadmin.service-request.fields.amount')</th>
                        <th>@lang('quickadmin.service-request.fields.status')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
        </tr>
    </thead>

    <tbody>
        @if (count($service_requests) > 0)
            @foreach ($service_requests as $service_request)
                <tr data-entry-id="{{ $service_request->id }}">
                    <td field-key='company'>{{ $service_request->company->name or '' }}</td>
                                <td field-key='service_type'>{{ $service_request->service_type }}</td>
                                <td field-key='service_center'>{{ $service_request->service_center->name or '' }}</td>
                                <td field-key='technician'>{{ $service_request->technician->name or '' }}</td>
                                <td field-key='call_type'>{{ $service_request->call_type }}</td>
                                <td field-key='call_location'>{{ $service_request->call_location }}</td>
                                <td field-key='priority'>{{ $service_request->priority }}</td>
                                <td field-key='product'>{{ $service_request->product->name or '' }}</td>
                                <td field-key='make'>{{ $service_request->make }}</td>
                                <td field-key='model_no'>{{ $service_request->model_no }}</td>
                                <td field-key='is_item_in_warrenty'>{{ $service_request->is_item_in_warrenty }}</td>
                                <td field-key='bill_no'>{{ $service_request->bill_no }}</td>
                                <td field-key='bill_date'>{{ $service_request->bill_date }}</td>
                                <td field-key='serial_no'>{{ $service_request->serial_no }}</td>
                                <td field-key='mop'>{{ $service_request->mop }}</td>
                                <td field-key='purchase_from'>{{ $service_request->purchase_from }}</td>
                                <td field-key='adavance_amount'>{{ $service_request->adavance_amount }}</td>
                                <td field-key='service_charge'>{{ $service_request->service_charge }}</td>
                                <td field-key='service_tag'>{{ $service_request->service_tag }}</td>
                                <td field-key='complain_details'>{!! $service_request->complain_details !!}</td>
                                <td field-key='note'>{{ $service_request->note }}</td>
                                <td field-key='completion_date'>{{ $service_request->completion_date }}</td>
                                <td field-key='parts'>
                                    @foreach ($service_request->parts as $singleParts)
                                        <span class="label label-info label-many">{{ $singleParts->name }}</span>
                                    @endforeach
                                </td>
                                <td field-key='additional_charges'>{{ $service_request->additional_charges }}</td>
                                <td field-key='amount'>{{ $service_request->amount }}</td>
                                <td field-key='status'>{{ $service_request->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    @can('service_request_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.restore', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                    @can('service_request_delete')
                                                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.perma_del', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                @endcan
                                </td>
                                @else
                                <td>
                                    @can('service_request_view')
                                    <a href="{{ route('admin.service_requests.show',[$service_request->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('service_request_edit')
                                    <a href="{{ route('admin.service_requests.edit',[$service_request->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('service_request_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.service_requests.destroy', $service_request->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="32">@lang('quickadmin.qa_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.service_centers.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a>
        </div>
    </div>
@stop

@section('javascript')
    @parent
   <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
 
    <script>
        function initialize() {
            const maps = document.getElementsByClassName("map");
            for (let i = 0; i < maps.length; i++) {
                const field = maps[i]
                const fieldKey = field.dataset.key;
                const latitude = parseFloat(field.dataset.latitude) || -33.8688;
                const longitude = parseFloat(field.dataset.longitude) || 151.2195;
        
                const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
                    center: {lat: latitude, lng: longitude},
                    zoom: 13
                });
                const marker = new google.maps.Marker({
                    map: map,
                    position: {lat: latitude, lng: longitude},
                });
        
                marker.setVisible(true);
            }    
              
          }
    </script>
@stop
