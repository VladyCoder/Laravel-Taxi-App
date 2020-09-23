@extends('admin.layout.base')

@section('title', 'Add Service Answer ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.service.question.answer.index', [$ServiceType->id, $ServiceQuestion->id]) }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">@lang('admin.service.Add_Service_Answer')</h5>

            <div class="form-group row">
                <label for="name" class="col-xs-2 col-form-label"><strong>@lang('admin.service.Service_Name')</strong></label>
                <div class="col-xs-5">
                    <label for="name" class="col-form-label">{{ $ServiceType->name }}</label>
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="col-xs-2 col-form-label"><strong>@lang('admin.service.Question')</strong></label>
                <div class="col-xs-5">
                    <label for="name" class="col-form-label">{{ $ServiceQuestion->question }}</label>
                </div>
            </div>

            <form class="form-horizontal" action="{{route('admin.service.question.answer.store', [$ServiceType->id, $ServiceQuestion->id])}}" method="POST" enctype="multipart/form-data" role="form">
                {{ csrf_field() }}
                <div class="form-group row">
                    <label for="name" class="col-xs-2 col-form-label">@lang('admin.service.Answer')</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('answer') }}" name="answer" required id="answer" placeholder="Service Answer">
                    </div>
                </div>

                 <div class="form-group row">
                    <label for="calculator" class="col-xs-2 col-form-label">@lang('admin.service.Pricing_Logic')</label>
                    <div class="col-xs-5">
                        <select class="form-control" id="calculator" name="calculator">
                            <option value="MIN">@lang('servicetypes.MIN')</option>
                            <option value="HOUR">@lang('servicetypes.HOUR')</option>
                            <option value="DISTANCE">@lang('servicetypes.DISTANCE')</option>
                            <option value="DISTANCEMIN">@lang('servicetypes.DISTANCEMIN')</option>
                            <option value="DISTANCEHOUR">@lang('servicetypes.DISTANCEHOUR')</option>
                        </select>
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>Price Calculation: <span id="changecal"></span></b></i></span>
                    </div> 
                </div>
 
                <!-- Set Hour Price -->
                <div class="form-group row" id="hour_price">
                    <label for="fixed" class="col-xs-2 col-form-label">@lang('admin.service.hourly_Price') ({{ currency() }})</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('fixed') }}" name="hour"  id="hourly_price" placeholder="Set Hour Price( Only For DISTANCEHOUR )">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>PH (@lang('admin.service.per_hour')), TH (@lang('admin.service.total_hour'))</b></i></span>
                    </div>
                </div>

                <!-- Base fare -->
                <div class="form-group row">
                    <label for="fixed" class="col-xs-2 col-form-label">@lang('admin.service.Base_Price') ({{ currency() }})</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('fixed') }}" name="fixed" required id="fixed" placeholder="Base Price">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>BP (@lang('admin.service.Base_Price'))</b></i></span>
                    </div>
                </div>
                <!-- Base distance -->
                <div class="form-group row">
                    <label for="distance" class="col-xs-2 col-form-label">@lang('admin.service.Base_Distance') ({{ distance() }})</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('distance') }}" name="distance" required id="distance" placeholder="Base Distance">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>BD (@lang('admin.service.Base_Distance')) </b></i></span>
                    </div>
                </div>
                <!-- unit time pricing -->
                <div class="form-group row">
                    <label for="minute" class="col-xs-2 col-form-label">@lang('admin.service.unit_time')</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('minute') }}" name="minute" required id="minute" placeholder="Unit Time Pricing">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>PM (@lang('admin.service.per_minute')), TM(@lang('admin.service.total_minute'))</b></i></span>
                    </div>
                </div>
                <!-- unit distance price -->
                <div class="form-group row">
                    <label for="price" class="col-xs-2 col-form-label">@lang('admin.service.unit')({{ distance() }})</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ old('price') }}" name="price" required id="price" placeholder="Unit Distance Price">
                    </div>
                    <div class="col-xs-5">
                        <span class="showcal"><i><b>P{{Setting::get('distance')}} (@lang('admin.service.per') {{Setting::get('distance')}}), T{{Setting::get('distance')}} (@lang('admin.service.total') {{Setting::get('distance')}})</b></i></span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-xs-2 col-form-label">@lang('admin.service.Description')</label>
                    <div class="col-xs-5">
                        <textarea class="form-control" name="description" id="description" placeholder="Description" rows="4">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{--<div class="form-group row">--}}
                    {{--<label for="description" class="col-xs-2 col-form-label">@lang('admin.status')</label>--}}
                    {{--<div class="col-xs-5">--}}
                        {{--<select class="form-control" id="status" name="status">--}}
                            {{--<option value="1">@lang('admin.enable')</option>--}}
                            {{--<option value="0">@lang('admin.disable')</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group row">
                    <div class="col-xs-10">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-3">
                                <a href="{{ route('admin.service.question.answer.index', [$ServiceType->id, $ServiceQuestion->id]) }}" class="btn btn-danger btn-block">@lang('admin.cancel')</a>
                            </div>
                            <div class="col-xs-12 col-sm-6 offset-md-6 col-md-3">
                                <button type="submit" class="btn btn-primary btn-block">@lang('admin.service.Add_Service_Answer')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var cal='MIN';
    $("#calculator").on('change', function(){       
        cal=$(this).val();
        priceInputs(cal);
    });

    function priceInputs(cal){
        if(cal=='MIN'){
            $("#hourly_price,#distance,#price").attr('value','');
            $("#minute").prop('disabled', false); 
            $("#minute").prop('required', true); 
            $("#hourly_price,#distance,#price").prop('disabled', true);
            $("#hourly_price,#distance,#price").prop('required', false);
            $("#changecal").text('BP + (TM*PM)'); 
        }
        else if(cal=='HOUR'){
            $("#minute,#distance,#price").attr('value',''); 
            $("#hourly_price").prop('disabled', false);
            $("#hourly_price").prop('required', true);
            $("#minute,#distance,#price").prop('disabled', true);
            $("#minute,#distance,#price").prop('required', false);
            $("#changecal").text('BP + (TH*PH)');
        }
        else if(cal=='DISTANCE'){
            $("#minute,#hourly_price").attr('value',''); 
            $("#price,#distance").prop('disabled', false);
            $("#price,#distance").prop('required', true);
            $("#minute,#hourly_price").prop('disabled', true);
            $("#minute,#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T{{Setting::get("distance")}}-BD*P{{Setting::get("distance")}})');
        }
        else if(cal=='DISTANCEMIN'){
            $("#hourly_price").attr('value',''); 
            $("#price,#distance,#minute").prop('disabled', false);
            $("#price,#distance,#minute").prop('required', true);
            $("#hourly_price").prop('disabled', true);
            $("#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T{{Setting::get("distance")}}-BD*P{{Setting::get("distance")}}) + (TM*PM)');
        }
        else if(cal=='DISTANCEHOUR'){
            $("#minute").attr('value',''); 
            $("#price,#distance,#hourly_price").prop('disabled', false);
            $("#price,#distance,#hourly_price").prop('required', true);
            $("#minute").prop('disabled', true);
            $("#minute").prop('required', false);
            $("#changecal").text('BP + ((T{{Setting::get("distance")}}-BD)*P{{Setting::get("distance")}}) + (TH*PH)');
        }
        else{
            $("#minute,#hourly_price").attr('value',''); 
            $("#price,#distance").prop('disabled', false);
            $("#price,#distance").prop('required', true);
            $("#minute,#hourly_price").prop('disabled', true);
            $("#minute,#hourly_price").prop('required', false);
            $("#changecal").text('BP + (T{{Setting::get("distance")}}-BD*P{{Setting::get("distance")}})');
        }
    }
</script>
@endsection