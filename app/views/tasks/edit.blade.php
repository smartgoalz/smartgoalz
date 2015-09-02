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
        /* Date picker */
        $("input[name='start_date']").datepicker({
                dateFormat: "{{ explode('|', $dateformat)[1] }}",
		changeMonth: true,
		changeYear: true,
        });
        $("input[name='due_date']").datepicker({
                dateFormat: "{{ explode('|', $dateformat)[1] }}",
		changeMonth: true,
		changeYear: true,
        });
        $("input[name='completion_date']").datepicker({
                dateFormat: "{{ explode('|', $dateformat)[1] }}",
		changeMonth: true,
		changeYear: true,
        });

        /* If Completed checkbox is unset hide completion date */
        $("input[name='is_completed']").click(function() {
                if ($(this).is(':checked')) {
                        $("#completion_date").parent().show();
                } else {
                        $("#completion_date").parent().hide();
                }
        });
        if ($("input[name='is_completed']").is(':checked')) {
                $("#completion_date").parent().show();
        } else {
                $("#completion_date").parent().hide();
        }
});
</script>

@stop

@section('breadcrumb-title', 'Goals')

@section('page-title', 'Edit SMART Task')

@section('content')

<div>
	<span class="view-title">Goal : {{ $goal->title }}</span>
</div>

<br />

{{ Form::model($task) }}

{{ Form::openGroup('title', 'Title of the task') }}
        {{ Form::text('title') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('start_date', 'Start date') }}
        {{ Form::text('start_date', $start_date) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('due_date', 'Due date') }}
        {{ Form::text('due_date', $due_date) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('after_id', 'After Task') }}
        {{ Form::select('after_id', $tasks_list) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('is_completed') }}
{{ Form::checkbox('is_completed', 1, FALSE, ['label' => 'Task is completed']) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('completion_date', 'Completion date') }}
        {{ Form::text('completion_date', $completion_date) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('notes', 'Notes') }}
        {{ Form::textarea('notes', null, ['size' => '50x2']) }}
{{ Form::closeGroup() }}

{{ Form::submit('Update') }}
{{ HTML::linkAction('GoalsController@getShow', 'Cancel', array($goal->id)) }}

{{ Form::close() }}

@stop
