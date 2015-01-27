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
});

</script>

@stop

@section('breadcrumb-title', 'Goals')

@section('page-title', 'Create SMART Goal')

@section('content')

{{ Form::open() }}

{{ Form::openGroup('title', 'Title of the goal') }}
        {{ Form::text('title') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('start_date', 'Start date') }}
        {{ Form::text('start_date') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('due_date', 'Due date') }}
        {{ Form::text('due_date') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('category', 'Category') }}
        {{ Form::select('category', $categories_list) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('difficulty', 'Difficulty level') }}
        {{ Form::select('difficulty', Constants::$difficulties) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('priority', 'Priority') }}
        {{ Form::select('priority', Constants::$priorities) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('reason', 'Reason why you want to achieve this goal') }}
        {{ Form::textarea('reason', null, ['size' => '50x2']) }}
{{ Form::closeGroup() }}

{{ Form::submit('Create') }}
{{ HTML::linkAction('GoalsController@getIndex', 'Cancel') }}

{{ Form::close() }}

@stop
