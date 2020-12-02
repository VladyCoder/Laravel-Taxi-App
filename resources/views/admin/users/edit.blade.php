@extends('admin.layout.base')

@section('title', 'Update User ')

@section('content')

<!-- edit page -->
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    <a href="{{ route('admin.user.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

			<h5 style="margin-bottom: 2em;">Update User</h5>

            <form class="form-horizontal" action="{{route('admin.user.update', $user->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">
				<div class="form-row">
					<div class="form-group col-sm-12 col-md-6">
						<label for="first_name" class="col-xs-12 col-form-label">First Name</label>
						<div class="col-xs-12">
							<input class="form-control" type="text" value="{{ $user->first_name }}" name="first_name" required id="first_name" placeholder="First Name">
						</div>
					</div>
					<div class="form-group col-sm-12 col-md-6">
						<label for="last_name" class="col-xs-12 col-form-label">Last Name</label>
						<div class="col-xs-12">
							<input class="form-control" type="text" value="{{ $user->last_name }}" name="last_name" required id="last_name" placeholder="Last Name">
						</div>
					</div>

					<div class="form-group col-sm-12 col-md-6">
						<label for="email" class="col-xs-12 col-form-label">@lang('admin.email')</label>
						<div class="col-xs-12">
							<input class="form-control" type="email" required name="email" value="{{$user->email}}" id="email" placeholder="Email" disabled>
						</div>
					</div>
					<div class="form-group col-sm-12 col-md-6">
						<label for="mobile" class="col-xs-12 col-form-label">Mobile</label>
						<div class="col-xs-12">
							<input class="form-control" type="text" value="{{ $user->mobile }}" name="mobile" required id="mobile" placeholder="Mobile">
						</div>
					</div>

					<div class="form-group col-sm-12 col-md-6">
						<label for="address" class="col-xs-12 col-form-label">@lang('admin.address')</label>
						<div class="col-xs-12">
							<input class="form-control" type="text" value="{{ $user->address }}" name="address" id="address" placeholder="@lang('admin.address')">
						</div>
					</div>
					<div class="form-group col-sm-12 col-md-6">
						<label for="city" class="col-xs-12 col-form-label">@lang('admin.city')</label>
						<div class="col-xs-12">
							<input class="form-control" type="text" value="{{ $user->city }}" name="city" id="city" placeholder="@lang('admin.city')">
						</div>
					</div>

					<div class="form-group col-sm-12 col-md-6">
						<label for="gender" class="col-xs-12 col-form-label">@lang('admin.gender')</label>
						<div class="col-xs-12">
							<select id="inputState" value="{{$user->gender}}" name="gender" id="gender" class="form-control">
								<option value="Male">@lang('admin.male')</option>
								<option value="Female">@lang('admin.female')</option>
								<option value="Other">@lang('admin.other')</option>
							</select>
						</div>
					</div>
					<div class="form-group col-sm-12 col-md-6">
						<label for="date_birth" class="col-xs-12 col-form-label">@lang('admin.date_birth')</label>
						<div class="col-xs-12">
							<div class="input-group date">
								<input value="{{$user->date_birth}}" class="form-control" type="text" id="date_birth" placeholder="Date" name="date_birth">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-th"></span>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group col-xs-12">
						<label for="picture" class="col-xs-12 col-form-label">Picture</label>
						<div class="col-xs-12">
						@if(isset($user->picture))
							<img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{img($user->picture)}}">
						@endif
							<input type="file" accept="image/*" name="picture" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
						</div>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="zipcode" class="col-xs-12 col-form-label"></label>
					<div class="col-xs-12">
						<button type="submit" class="btn btn-primary">Update User</button>
						<a href="{{route('admin.user.index')}}" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

<script src="{{asset('asset/js/jquery.min.js')}}"></script>
<script src="{{asset('asset/js/bootstrap-datepicker.min.js')}}"></script>
<script src="//maps.googleapis.com/maps/api/js?key={{ Setting::get('map_key') }}&libraries=places&callback=initAutocomplete" async defer></script>
<script>
	$('#date_birth').datepicker({
		format: 'yyyy-mm-dd'
	});

	function initAutocomplete(){
		const searchInput = document.getElementById("address");
		const searchbox = new google.maps.places.SearchBox(searchInput);
	}
</script>

@endsection
