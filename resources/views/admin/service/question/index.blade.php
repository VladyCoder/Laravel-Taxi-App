@extends('admin.layout.base')

@section('title', 'Service Questions ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode') == 1)
                <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
            @endif
            <h5 class="mb-1">Service Questions</h5>

            <a href="{{ route('admin.service.index') }}" style="margin-left: 1em;margin-top: -30px" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> @lang('admin.back')</a>

            <a href="{{ route('admin.service.question.create', $ServiceType->id) }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>@lang('admin.service.Add_Service_Question')</a>

            <table class="table table-striped table-bordered dataTable" id="table-2">
                <thead>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.service.Question')</th>
                        <th>@lang('admin.service.Answers')</th>
                        {{--<th>@lang('admin.status')</th>--}}
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($ServiceQuestions as $index => $ServiceQuestion)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ServiceQuestion->question }}</td>
                        <!-- <td>{{ $ServiceQuestion->provider_name }}</td> -->
                        <td>
                            {{--@if($ServiceQuestion->deactive_answers() == 0 && $ServiceQuestion->all_answers() != 0)--}}
                                {{--<a class="btn btn-success btn-block" href="{{route('admin.service.question.answer.index', [$ServiceType->id, $ServiceQuestion->id] )}}">{{ $ServiceQuestion->all_answers() }}</a>--}}
                            {{--@else--}}
                                {{--<a class="btn btn-danger btn-block label-right" href="{{route('admin.service.question.answer.index', [$ServiceType->id, $ServiceQuestion->id] )}}">{{ $ServiceQuestion->all_answers() }}<span class="btn-label">{{ $ServiceQuestion->deactive_answers() }}</span></a>--}}
                            {{--@endif--}}
                            <a class="btn btn-success btn-block" href="{{route('admin.service.question.answer.index', [$ServiceType->id, $ServiceQuestion->id] )}}">{{ $ServiceQuestion->all_answers() }}</a>
                        </td>
                        {{--<td>--}}
                            {{--@if($ServiceQuestion->status)--}}
                                {{--<label class="btn btn-block btn-primary">@lang('Enable')</label>--}}
                            {{--@else--}}
                                {{--<label class="btn btn-block btn-warning">@lang('Disable')</label>--}}
                            {{--@endif--}}
                        {{--</td>--}}
                        <td>
                            <form action="{{ route('admin.service.question.destroy', [$ServiceType->id, $ServiceQuestion->id]) }}" method="POST">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                @if( Setting::get('demo_mode') == 0)
                                    <a href="{{ route('admin.service.question.edit', [$ServiceType->id, $ServiceQuestion->id]) }}" class="btn btn-info btn-block">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <button class="btn btn-danger btn-block" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>@lang('admin.id')</th>
                        <th>@lang('admin.service.Question')</th>
                        <th>@lang('admin.service.Answers')</th>
                        {{--<th>@lang('admin.status')</th>--}}
                        <th>@lang('admin.action')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection