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

});

</script>

@stop

@section('page-title', 'Show Goal')

@section('content')

<div>
	<span class="view-title">Goal : {{ $goal->title }}</span>
</div>

<div class="details">
	<div>Start date : {{ date_format(date_create_from_format('Y-m-d H:i:s', $goal->start_date), explode('|', $dateformat)[0]) }}
		<span class="pull-right">
                        {{ HTML::decode(HTML::linkAction('GoalsController@getEdit',
                                'Edit Goal', $goal->id,
                                array('class' => 'btn btn-success btn-xs'))) }}
		</span>
	</div>
	<div>Due date : {{ date_format(date_create_from_format('Y-m-d H:i:s', $goal->due_date), explode('|', $dateformat)[0]) }}</div>
	<div>Category : {{ $goal->category->title }}</div>
	<div>Difficulty : {{ Constants::$difficulties[$goal->difficulty] }}</div>
	<div>Priority : {{ Constants::$priorities[$goal->priority] }}</div>
	<div>Completed : {{ $goal->task_completed }} / {{ $goal->task_total }}</div>
	<div>Reason : {{ $goal->reason }}</div>
</div>

<div class="view-actions">
        {{ HTML::linkAction('TasksController@getCreate', 'Add Task', array(),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('GoalsController@getIndex', 'Cancel', array(),
                array('class' => 'btn btn-info')) }}
</div>

@stop
