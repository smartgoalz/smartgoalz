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

	/* Get current datetime */
	var js_start_time =Date.parse("{{ $timewatch->start_time }}");
	/* Convert datetime string to unix timestamp */
	var js_start_ts = (js_start_time.getTime() / 1000) - (js_start_time.getTimezoneOffset() * 60);

	/* Set current date and time */
	$("[name=current_datetime]").val(Date.now().toString("yyyy-MM-dd HH:mm:ss"));

	/* Update clock every 1 second */
	setInterval(function() {
		/* Get current datetime in unix timestamp format */
		js_current_ts = (Date.now().getTime() / 1000) - (Date.now().getTimezoneOffset() * 60);

		/* Covnert timestamp in seconds to minutes */
		diff_ts = Math.floor((js_current_ts - js_start_ts) / 60);

		$("#timespent").text(js_toDHM(diff_ts));

		$("[name=current_datetime]").val(Date.now().toString("yyyy-MM-dd HH:mm:ss"));
	}, 1000);

	/* Convert to time difference in human readable format */
	function js_toDHM(minutes) {
		if (minutes <= 0) {
			return '';
		}

		dhm = '';

		var balance = minutes;

		/* Calculate days */
		if (minutes > 1440) {
			dhm = Math.floor(balance / 1440) + 'd ';
			balance = Math.balance % 1440;
		}

		/* Calculate hours */
		if (minutes > 60) {
			dhm += Math.floor(balance / 60) + 'h ';
			balance = balance % 60;
		}

		/* Calculate minutes */
		dhm += Math.floor(balance) + 'm';

		return dhm;
	}
});

</script>

@stop

@section('breadcrumb-title', 'Timewatches')

@section('page-title', 'Active Timewatch')

@section('content')

{{ Form::open() }}

{{ Form::openGroup('goal', 'Selected Goal') }}
        {{ Form::text('goal', $goal->title, array('disabled')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('task', 'Selected Task') }}
        {{ Form::text('task', $task->title, array('disabled')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('start', 'Started On') }}
        {{ Form::text('start',
                date_format(date_create_from_format(
                        'Y-m-d H:i:s',
                        $timewatch->start_time
                ), $dateformat_php . ' h:i:s A'), array('disabled')) }}
{{ Form::closeGroup() }}

<div class="form-group">
        <label>Time Elapsed : <span id="timespent"></span></label>
</div>

{{ Form::hidden('current_datetime') }}

{{ Form::submit('Stop Timer', array('class' => 'btn btn-danger btn-lg')) }}
<span class="small-margin"></span>
{{ HTML::linkAction('TimewatchesController@getStart', 'Cancel') }}

{{ Form::close() }}

@stop
