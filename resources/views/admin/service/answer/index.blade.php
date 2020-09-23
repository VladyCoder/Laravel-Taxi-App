@extends('admin.layout.base')

@section('title', 'Service Answers ')

@section('content')
    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                @if(Setting::get('demo_mode') == 1)
                    <div class="col-md-12" style="height:50px;color:red;">
                        ** Demo Mode : @lang('admin.demomode')
                    </div>
                @endif
                <h5 class="mb-1">Service Answers</h5>

                <a href="{{ route('admin.service.question.index', $ServiceType->id) }}" style="margin-left: 1em;margin-top: -30px" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> @lang('admin.back')</a>

                <a href="{{ route('admin.service.question.answer.create', [$ServiceType->id, $ServiceQuestion->id]) }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>@lang('admin.service.Add_Service_Answer')</a>

                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.service.Answer')</th>
                            <th>@lang('admin.service.Base_Price')</th>
                            <th>@lang('admin.service.Base_Distance')</th>
                            <th>@lang('admin.service.Distance_Price')</th>
                            <th>@lang('admin.service.Time_Price')</th>
                            <th>@lang('admin.service.hourly_Price')</th>
                            <th>@lang('admin.service.Price_Calculation')</th>
                            {{--<th>@lang('admin.status')</th>--}}
                            <th>@lang('admin.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ServiceAnswers as $index => $ServiceAnswer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ServiceAnswer->answer }}</td>
                            <td>{{ currency($ServiceAnswer->fixed) }}</td>
                            <td>{{ distance($ServiceAnswer->distance) }}</td>
                            <td>{{ currency($ServiceAnswer->price) }}</td>
                            <td>{{ currency($ServiceAnswer->minute) }}</td>
                            @if($ServiceAnswer->calculator == 'DISTANCEHOUR')
                                <td>{{ currency($ServiceAnswer->hour) }}</td>
                            @else
                                <td>No Hour Price</td>
                            @endif
                            <td>@lang('servicetypes.'.$ServiceAnswer->calculator)</td>
                            {{--<td>--}}
                                {{--@if($ServiceAnswer->status)--}}
                                    {{--<label class="btn btn-block btn-primary">@lang('Enable')</label>--}}
                                {{--@else--}}
                                    {{--<label class="btn btn-block btn-warning">@lang('Disable')</label>--}}
                                {{--@endif--}}
                            {{--</td>--}}
                            <td>
                                <form action="{{ route('admin.service.question.answer.destroy', [$ServiceType->id, $ServiceQuestion->id, $ServiceAnswer->id]) }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    @if( Setting::get('demo_mode') == 0)
                                        <a href="{{ route('admin.service.question.answer.edit', [$ServiceType->id, $ServiceQuestion->id, $ServiceAnswer->id]) }}" class="btn btn-info btn-block">
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
                            <th>@lang('admin.service.Answer')</th>
                            <th>@lang('admin.service.Base_Price')</th>
                            <th>@lang('admin.service.Base_Distance')</th>
                            <th>@lang('admin.service.Distance_Price')</th>
                            <th>@lang('admin.service.Time_Price')</th>
                            <th>@lang('admin.service.hourly_Price')</th>
                            <th>@lang('admin.service.Price_Calculation')</th>
                            {{--<th>@lang('admin.status')</th>--}}
                            <th>@lang('admin.action')</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection