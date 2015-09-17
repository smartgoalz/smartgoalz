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
        $("input[name='date']").datetimepicker({
                dateFormat: "{{ $dateformat_cal }}",
                timeFormat: "hh:mm TT",
		changeMonth: true,
		changeYear: true,
	});

	/* Enable text editor */
	$("#entry").jqte();
});

</script>

@stop

@section('breadcrumb-title', 'Journals')

@section('page-title', 'Create Journal Entry')

@section('content')

{{ Form::open() }}

{{ Form::openGroup('title', 'Title') }}
        {{ Form::text('title') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('date', 'Date') }}
        {{ Form::text('date') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('entry', 'Entry') }}
        {{ Form::textarea('entry') }}
{{ Form::closeGroup() }}

{{ Form::submit('Create') }}
{{ HTML::linkAction('JournalsController@getIndex', 'Cancel') }}

{{ Form::close() }}

@stop
