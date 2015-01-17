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

        /* Date picker */
        $("input[name='start_time']").datetimepicker({
                timeFormat: "hh:mm TT",
                dateFormat: "{{ explode('|', $dateformat)[1] }}",
		changeMonth: true,
		changeYear: true,
        });
        $("input[name='stop_time']").datetimepicker({
                dateFormat: "{{ explode('|', $dateformat)[1] }}",
                timeFormat: "hh:mm TT",
		changeMonth: true,
		changeYear: true,
        });

        /* If Completed checkbox is unset hide completion date */
        $("input[name='is_active']").click(function() {
                if ($(this).is(':checked')) {
                        $("#stop_time").parent().hide();
                } else {
                        $("#stop_time").parent().show();
                }
        });
        if ($("input[name='is_active']").is(':checked')) {
                $("#stop_time").parent().hide();
        } else {
                $("#stop_time").parent().show();
        }
});

</script>

@stop

@section('page-title', 'Edit Timewatches')

@section('content')

{{ Form::open() }}

<div class="form-group">
<label for="goals">Select Goal</label>
<select id="goals" name="goals" class="form-control">
<option value=""></option>
@foreach ($goals as $goal)
        @if (isset($timewatch_task) && ($goal->id == $timewatch_task->goal_id))
                echo <option value="{{ $goal->id }}" SELECTED>{{ $goal->title }}</option>
        @else
                echo <option value="{{ $goal->id }}">{{ $goal->title }}</option>
        @endif
@endforeach
</select>
</div>

<div class="form-group">
<label for="task_id">Select Task</label>
<select id="task_id" name="task_id" class="form-control">
<option value=""></option>
@foreach ($goals as $goal)
        @foreach ($goal->tasks as $task)
                @if (isset($timewatch_task) && ($task->id == $timewatch_task->id))
                        echo <option value="{{ $task->id }}" class="{{ $goal->id }}" SELECTED>{{ $task->title }}</option>
                @else
                        echo <option value="{{ $task->id }}" class="{{ $goal->id }}">{{ $task->title }}</option>
                @endif
        @endforeach
@endforeach
</select>
</div>

{{ Form::openGroup('start_time', 'Start Time') }}
        {{ Form::text('start_time', $start_time) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('is_active') }}
{{ Form::checkbox('is_active', 1, 'Active', $active) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('stop_time', 'Stop Time') }}
        {{ Form::text('stop_time', $stop_time) }}
{{ Form::closeGroup() }}

{{ Form::submit('Submit') }}
{{ HTML::linkAction('TimewatchesController@getStart', 'Cancel') }}

{{ Form::close() }}

@stop
