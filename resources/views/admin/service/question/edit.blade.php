@extends('admin.layout.base')

@section('title', 'Update Service Question ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.service.question.index', $ServiceType->id) }}" class="btn btn-primary pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">@lang('admin.service.Update_Service_Question')</h5>

            <div class="form-group row">
                <label for="name" class="col-xs-12 col-form-label"><strong>@lang('admin.service.Service_Name')</strong></label>
                <div class="col-xs-5">
                    <label for="name" class="col-form-label">{{ $ServiceType->name }}</label>
                </div>
            </div>

            {{--<form class="form-horizontal" action="{{ route('admin.service.question.update', [$ServiceType->id, $ServiceQuestion->id] )}}" method="POST" enctype="multipart/form-data" role="form">--}}
            <form class="form-horizontal" action="{{ route('admin.service.question.update', [$ServiceType->id, $ServiceQuestion->id] )}}" method="POST" enctype="multipart/form-data" role="form">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PATCH">
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.service.Question')</label>
                    <div class="col-xs-5">
                        <input class="form-control" type="text" value="{{ $ServiceQuestion->question }}" name="question" required id="question" placeholder="Service Question">
                    </div>
                </div>

               <!--  <div class="form-group row">
                    <label for="provider_name" class="col-xs-12 col-form-label">@lang('admin.service.Provider_Name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $ServiceQuestion->provider_name }}" name="provider_name" required id="provider_name" placeholder="Provider Name">
                    </div>
                </div> -->

                <div class="form-group row">
                    <label for="description" class="col-xs-12 col-form-label">@lang('admin.service.Description')</label>
                    <div class="col-xs-5">
                        <textarea class="form-control" name="description" id="description" placeholder="Description" rows="4">{{ $ServiceQuestion->description }}</textarea>
                    </div>
                </div>

                {{--<div class="form-group row">--}}
                    {{--<label for="description" class="col-xs-12 col-form-label">@lang('admin.status')</label>--}}
                    {{--<div class="col-xs-5">--}}
                        {{--<select class="form-control" id="status" name="status">--}}
                            {{--<option value="1" @if($ServiceQuestion->status) selected @endif>@lang('admin.enable')</option>--}}
                            {{--<option value="0" @if(!$ServiceQuestion->status) selected @endif>@lang('admin.disable')</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                {{--</div>--}}
                
                <div class="form-group row">
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <a href="{{route('admin.service.question.index', $ServiceType->id)}}" class="btn btn-danger btn-block">@lang('admin.cancel')</a>
                    </div>
                    <div class="col-xs-12 col-sm-6 offset-md-6 col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">@lang('admin.service.Update_Service_Question')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection