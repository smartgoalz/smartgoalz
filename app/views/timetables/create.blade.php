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
        $('#from_time').timepicker({
                defaultTime: '00:00 AM',
                minuteStep: 5,
        });
        $('#to_time').timepicker({
                defaultTime: '00:00 AM',
                minuteStep: 5,
        });
});

</script>

@stop

@section('breadcrumb-title', 'Timetables')

@section('page-title', 'Create Activity Schedule')

@section('content')

{{ Form::open() }}

<div class="form-group timepicker-width">
        <label for="from_time">From Time</label>
        <div class="input-group bootstrap-timepicker">
                <input id="from_time" type="text" class="form-control" name="from_time">
                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
        </div>
</div>

<div class="form-group timepicker-width">
        <label for="to_time">To Time</label>
        <div class="input-group bootstrap-timepicker">
                <input id="to_time" type="text" class="form-control" name="to_time">
                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
        </div>
</div>

<div class="form-group">
        <label for="days[]">Days</label>
        <select name="days[]" class="selectpicker form-control" multiple title='Please select'>
        @foreach ($days as $row => $day)
                <option value="{{ $row }}">
                        {{ $day }}
                </option>
        @endforeach
        </select>
</div>

{{ Form::submit('Create') }}
{{ HTML::linkAction('TimetablesController@getSchedule', 'Cancel', array($activity->id)) }}

{{ Form::close() }}

@stop
