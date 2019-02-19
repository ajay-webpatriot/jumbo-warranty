@extends('layouts.app')

@section('content')
    <!-- <h3 class="page-title">@lang('quickadmin.service-center.title')</h3> -->

    <div class="panel panel-default">
        <div class="panel-heading headerTitle">
            @lang('quickadmin.service-center.formTitle')
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
                            <th>@lang('quickadmin.service-center.fields.commission')</th>
                            <td field-key='commission'>{{ $service_center->commission }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.address-1')</th>
                            <td field-key='address_1'>{{ $service_center->address_1 }}</td>
                        </tr>
                        <!-- <tr>
                            <th>@lang('quickadmin.service-center.fields.location')</th>
                            <td>
                    <strong>{{ $service_center->location_address }}</strong>
                    <div id='location-map' style='width: 600px;height: 300px;' class='map' data-key='location' data-latitude='{{$service_center->location_latitude}}' data-longitude='{{$service_center->location_longitude}}'></div>
                </td>
                        </tr> -->
                        
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
                            <th>@lang('quickadmin.service-center.fields.supported-zipcode')</th>
                            <td field-key='zipcode'>{{ $service_center->supported_zipcode }}</td>
                        </tr>
                        <tr>
                            <th>@lang('quickadmin.service-center.fields.status')</th>
                            <td field-key='status'>{{ $service_center->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- <p>&nbsp;</p> -->

            <!-- <a href="{{ route('admin.service_centers.index') }}" class="btn btn-default">@lang('quickadmin.qa_back_to_list')</a> -->
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
