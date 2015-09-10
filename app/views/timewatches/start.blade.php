{{--

The MIT License (MIT)

SMARTGoalz - SMART Goals made easier

http://smartgoalz.github.io

Copyright (c) 2015 Prashant Shah <pshah.smartgoalz@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

--}}

@extends('layouts.master')

@section('head')

<script type="text/javascript">

$(document).ready(function() {
        $("#task_id").chained("#goals");
});

</script>

@stop

@section('breadcrumb-title', 'Timewatches')

@section('page-title', 'Track time spent on tasks')

@section('content')

{{ Form::open() }}

<div class="form-group">
<label for="goals">Select Goal</label>
<select id="goals" name="goals" class="form-control">
<option value=""></option>
@foreach ($goals as $goal)
        echo <option value="{{ $goal->id }}">{{ $goal->title }}</option>
@endforeach
</select>
</div>

<div class="form-group">
<label for="task_id">Select Task</label>
<select id="task_id" name="task_id" class="form-control">
<option value=""></option>
@foreach ($goals as $goal)
        @foreach ($goal->tasks as $task)
                echo <option value="{{ $task->id }}" class="{{ $goal->id }}">{{ $task->title }}</option>
        @endforeach
@endforeach
</select>
</div>

<br />

{{ Form::submit('Start Timer', array('class' => 'btn btn-success btn-lg')) }}

{{ Form::close() }}

<br />

<div>
        {{ HTML::linkAction('TimewatchesController@getEdit', 'Manually add timewatch') }}
</div>

@if ($timewatches_active->count() >= 1)

<br />

<div class="widget">

<div class="widget-title">
	<i class="fa fa-clock-o"></i> Active timewatches
	<div class="clearfix"></div>
</div>

<div>
	<table class="table table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Task</th>
				<th>Start time</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
                @define ($i = 1)
                @foreach ($timewatches_active as $timewatch)
		<tr>
                        <td>{{ $i }}</td>
                        <td class="text-left">
                                {{ date_format(date_create_from_format('Y-m-d', $timewatch->date), explode('|', $dateformat)[0]) }}
                        </td>

			<td class="text-left">
                                {{ $timewatch->tasks_title }}
                        </td>

                        <td class="text-left">
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $timewatch->start_time), explode('|', $dateformat)[0] . ' h:i A') }}
                        </td>

			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('TimewatchesController@getStop',
                                        'Stop', $timewatch->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction('TimewatchesController@getShow',
                                        'Show', $timewatch->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction('TimewatchesController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $timewatch->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'TimewatchesController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($timewatch->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the timewatch ?'
                                        ))) }}
			</td>
		</tr>
                @define ($i = $i + 1)
                @endforeach
		</tbody>
	</table>
</div>

</div>
<!-- /.widget -->

@endif

@if ($timewatches->count() >= 1)

<br />

<div class="widget">

<div class="widget-title">
	<i class="fa fa-clock-o"></i> All Timewatches
	<div class="clearfix"></div>
</div>

<div class="">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Date</th>
				<th>Task</th>
				<th>Start time</th>
				<th>Stop time</th>
                                <th>Time spent</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
                @define ($i = 1)
                @foreach ($timewatches as $timewatch)
		<tr>
                        <td>{{ $i }}</td>
                        <td class="text-left">
                                {{ date_format(date_create_from_format('Y-m-d', $timewatch->date), explode('|', $dateformat)[0]) }}
                        </td>

			<td class="text-left">
                                {{ $timewatch->tasks_title }}
                        </td>

                        <td class="text-left">
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $timewatch->start_time), explode('|', $dateformat)[0] . ' h:i A') }}
                        </td>

                        <td class="text-left">
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $timewatch->stop_time), explode('|', $dateformat)[0] . ' h:i A') }}
                        </td>

                        <td class="text-left">
                                {{ toDHM($timewatch->minutes_count) }}
                        </td>

			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('TimewatchesController@getShow',
                                        'Show', $timewatch->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction('TimewatchesController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $timewatch->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'TimewatchesController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($timewatch->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the timewatch ?'
                                        ))) }}
			</td>
		</tr>
                @define ($i = $i + 1)
                @endforeach
		</tbody>
	</table>

</div>

</div>
<!-- /.widget -->

<div class="text-center paginator-padding">
        {{ $timewatches->links() }}
</div>

@endif

@stop
