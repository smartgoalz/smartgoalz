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
        /* Date and time picker */
        $('#dob').datepicker({
                dateFormat: $("#dateformat").val().split('|')[1],
                changeMonth: true,
                changeYear: true,
                minDate: new Date(1900, 1 - 1, 1),
                maxDate: "-1D",
        });

	$("#dateformat").change(function() {
                /* On change update the date format in the datepicker */
		$("#dob").datepicker("option", {
                        dateFormat: $("#dateformat").val().split('|')[1],
                        changeMonth: true,
                        changeYear: true,
                        minDate: new Date(1900, 1 - 1, 1),
                        maxDate: "-1D",
                });

	});
});

</script>

@stop

@section('breadcrumb-title', 'User Profile')

@section('page-title', 'Edit Profile')

@section('content')

{{ Form::model($user) }}

{{ Form::openGroup('fullname', 'Fullname') }}
        {{ Form::text('fullname') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('email', 'Email') }}
        {{ Form::text('email') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('gender', 'Gender') }}
        {{ Form::select('gender', $gender_options) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('dateformat', 'Date format') }}
        {{ Form::select('dateformat', $dateformat_options, $dateformat) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('dob', 'Date of birth') }}
        {{ Form::text('dob', $dob) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('timezone', 'Timezone') }}
        {{ Form::select('timezone', $timezone_options) }}
{{ Form::closeGroup() }}

{{ Form::submit('Submit') }}
{{ HTML::linkAction('UsersController@getProfile', 'Cancel') }}

{{ Form::close() }}


@stop
