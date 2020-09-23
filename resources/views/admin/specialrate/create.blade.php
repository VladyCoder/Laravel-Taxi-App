@extends('admin.layout.base')

@section('title', 'Add Speical Rate ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="row no-margin">
            <div class="col-md-6">
				<div class="box box-block bg-white">
					<a href="{{ route('admin.specialrate.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

					<h5 style="margin-bottom: 2em;">@lang('admin.specialrate.add_specialrate')</h5>

					<form class="form-horizontal" action="{{route('admin.specialrate.store')}}" method="POST" enctype="multipart/form-data" role="form">
						{{csrf_field()}}
						<button type="submit" disabled style="display: none" aria-hidden="true"></button>
						  
						<div class="form-group row">
							<label for="name" class="col-xs-2 col-form-label">@lang('admin.name')</label>
							<div class="col-xs-10">
								<input class="form-control" autocomplete="off"  type="text" value="{{ old('name') }}" name="name" required id="name" placeholder="Name">
							</div>
						</div>

						<div class="form-group row">
							<label for="description" class="col-xs-2 col-form-label">@lang('admin.specialrate.description')</label>
							<div class="col-xs-10">
								<input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" placeholder="description">
							</div>
						</div>

						<div class="form-group row">
							<label for="source" class="col-xs-2 col-form-label">@lang('admin.specialrate.source')</label>
							<div class="col-xs-10">
								<input type="text" class="form-control" id="origin-input" name="source" value="{{ old('source') }}" placeholder="Enter Pickup location">
							</div>
						</div>

						<div class="form-group row">
							<label for="s_radius" class="col-xs-2 col-form-label">@lang('admin.specialrate.radius')</label>
							<div class="col-xs-8">
								<input class="form-control" type="decimal" step="0.01" value="{{ old('s_radius') }}" name="s_radius" required id="s_radius" placeholder="Radius">
							</div>
							<label for="s_radius" class="col-xs-2 col-form-label">Km</label>
						</div>

						<div class="form-group row">
							<label for="destination" class="col-xs-2 col-form-label">@lang('admin.specialrate.destination')</label>
							<div class="col-xs-10">
								<input type="text" class="form-control" id="destination-input" name="destination" value="{{ old('destination') }}"  placeholder="Enter Drop location" >
							</div>
						</div>

						<div class="form-group row">
							<label for="d_radius" class="col-xs-2 col-form-label">@lang('admin.specialrate.radius')</label>
							<div class="col-xs-8">
								<input class="form-control" type="decimal" step="0.01" value="{{ old('d_radius') }}" name="d_radius" required id="d_radius" placeholder="Radius">
							</div>
							<label for="d_radius" class="col-xs-2 col-form-label">Km</label>
						</div>

						<div class="form-group row">
							<label for="price" class="col-xs-2 col-form-label">@lang('admin.specialrate.price')</label>
							<div class="col-xs-8">
								<input class="form-control" type="decimal" step="0.01" value="{{ old('price') }}" name="price" required id="price" placeholder="Price">
							</div>
							<label for="price" class="col-xs-2 col-form-label">{{currency()}}</label>
						</div>

						<div class="form-group row">
							<label for="status" class="col-xs-2 col-form-label">@lang('admin.specialrate.service_type')</label>
							<div class="col-xs-10">
								<select class="form-control" id="service_type_id" name="service_type_id">
									@foreach($servicetypes as $index => $servicetype)
										<option value="{{$servicetype->id}}" @if(old('service_type') == $servicetype->id) selected @endif>{{$servicetype->name}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<input type="hidden" name="s_latitude" id="origin_latitude">
						<input type="hidden" name="s_longitude" id="origin_longitude">
						<input type="hidden" name="d_latitude" id="destination_latitude">
						<input type="hidden" name="d_longitude" id="destination_longitude">
						<input type="hidden" name="current_longitude" id="long">
						<input type="hidden" name="current_latitude" id="lat">

						<div class="form-group row">
							<label for="status" class="col-xs-2 col-form-label">@lang('admin.status')</label>
							<div class="col-xs-10">
								<select class="form-control" id="status" name="status">
									<option value="ACTIVE">@lang('admin.specialrate.ACTIVE')</option>
									<option value="PENDING">@lang('admin.specialrate.PENDING')</option>
								</select>
							</div>
						</div>
						
						<div class="form-group row">
							<label for="zipcode" class="col-xs-2 col-form-label"></label>
							<div class="col-xs-10">
								<button type="submit" class="btn btn-primary">@lang('admin.specialrate.add_specialrate')</button>
								<a href="{{route('admin.specialrate.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
							</div>
						</div>
					</form>
				</div>
			</div>
            <div class="col-md-6">
                <div class="map-responsive">
                    <div id="map" style="width: 100%; height: 450px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
	<script type="text/javascript">
		var current_latitude = 13.0574400;
		var current_longitude = 80.2482605;
	</script>

	<script type="text/javascript">
		if( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition( success, fail );
		} else {
			console.log('Sorry, your browser does not support geolocation services');
			initPositionMap();
		}

		function success(position)
		{
			document.getElementById('longitude').value = position.coords.longitude;
			document.getElementById('latitude').value = position.coords.latitude

			if(position.coords.longitude != "" && position.coords.latitude != ""){
				current_longitude = position.coords.longitude;
				current_latitude = position.coords.latitude;
			}
			initPositionMap();
		}

		function fail()
		{
			// Could not obtain location
			console.log('unable to get your location');
			initPositionMap();
		}
	</script>

	<script type="text/javascript" src="{{ asset('asset/js/map.js') }}"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initMap" async defer></script>

	<script type="text/javascript">
		function disableEnterKey(e)
		{
			var key;
			if(window.e)
				key = window.e.keyCode; // IE
			else
				key = e.which; // Firefox

			if(key == 13)
				return e.preventDefault();
		}
	</script>
@endsection