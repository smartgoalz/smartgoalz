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

/* Function to mark the task as completed */
function taskDone(id) {
	js_current_ts = (Date.now().getTime() / 1000) - (Date.now().getTimezoneOffset() * 60);
	js_current_ts = Math.floor(js_current_ts);
	window.location = "{{ action('TasksController@getDone') }}/" + id + "/" + js_current_ts;
}

$(document).ready(function() {

});

</script>

@stop

@section('breadcrumb-title', 'Goals')

@section('page-title', 'Goal')

@section('content')

<div>
	<span class="view-title">{{ $goal->title }}</span>
</div>

<div class="details">
	<div>Start date : {{ date_format(date_create_from_format('Y-m-d H:i:s', $goal->start_date), $dateformat_php) }}
		<span class="pull-right">
                        {{ HTML::decode(HTML::linkAction('GoalsController@getEdit',
                                'Edit Goal', $goal->id,
                                array('class' => 'btn btn-success btn-xs'))) }}
		</span>
	</div>
	<div>Due date : {{ date_format(date_create_from_format('Y-m-d H:i:s', $goal->due_date), $dateformat_php) }}</div>
	<div>Category : {{ $goal->category->title }}</div>
	<div>Difficulty : {{ Constants::$difficulties[$goal->difficulty] }}</div>
	<div>Priority : {{ Constants::$priorities[$goal->priority] }}</div>
	<div>Completed : {{ $goal->task_completed }} / {{ $goal->task_total }}</div>
	<div>Reason : {{ $goal->reason }}</div>
</div>

<div class="view-actions">
        {{ HTML::linkAction('TasksController@getCreate', 'Add Task', array($goal->id),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('GoalsController@getIndex', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

@if ($tasks->count() < 1)
<div class="view-content">
	<div>No tasks found. Please add a task to get started.</div>
</div>
@else
<div class="view-content">
	<table class="table table-hover">
		<thead>
			<tr>
				<th class="text-left">#</th>
				<th class="text-left">Task</th>
				<th class="text-center">Start date</th>
				<th class="text-center">Due date</th>
				<th class="text-center">Timewatch</th>
				<th class="text-center">Completed</th>
				<th class="text-center">Actions</th>
			</tr>
		</thead>
		<tbody>
                        @foreach ($tasks as $task)
			<tr>
				<td class="text-left">{{ $task->id }}</td>
				<td class="text-left">{{ $task->title }}</td>
				<td class="text-center">
					{{ date_format(date_create_from_format('Y-m-d H:i:s', $task->start_date), $dateformat_php) }}
				</td>
				<td class="text-center">
					{{ date_format(date_create_from_format('Y-m-d H:i:s', $task->due_date), $dateformat_php) }}
				</td>
				<td class="text-center">
					{{ toDHM($task->total_minutes($task->id)) }}
				</td>
				<td class="text-center">
				@if ($task->is_completed == 1)
					<span class="glyphicon glyphicon-ok"></span>
				@else
					{{ link_to("#", "Done", array("onclick" => "taskDone($task->id)", "class" => "btn-sm btn-default")) }}
				@endif
				</td>
				<td class="text-center">
		                        {{ HTML::decode(HTML::linkAction('TasksController@getEdit',
		                                '<i class="glyphicon glyphicon-pencil"></i>', array($task->id))) }}
					<span class="small-margin"></span>
	                                {{ HTML::decode(HTML::linkAction(
	                                        'TasksController@deleteDestroy',
	                                        '<i class="glyphicon glyphicon-trash"></i>',
	                                        array($task->id),
	                                        array(
	                                                'class' => '',
	                                                'data-method' => 'DELETE',
	                                                'data-confirm' => 'Are you sure you want to delete the task ?'
	                                        ))) }}
				</td>
			</tr>
                        @endforeach
		</tbody>
	</table>
</div>
@endif

@stop
