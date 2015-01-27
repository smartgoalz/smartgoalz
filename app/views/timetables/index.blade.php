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

@section('breadcrumb-title', 'Timetables')

@section('page-title', 'Timetable for Today')

@section('content')

<div>
	<div>Current time : </div>
</div>

<br />

<div class="header-button">
        {{ HTML::linkAction('TimetablesController@getManage', 'Manage Timetable', array(),
                array('class' => 'btn btn-primary')) }}
</div>

<div>
        @if ($schedules->count() < 1)
                <div>No schedule found. Please add one to get started.</div>
        @else
	<table class="table table-hover">
	<thead>
		<tr>
			<th>#</th>
			<th>Activity</th>
                        <th>From time</th>
                        <th>To time</th>
		</tr>
	</thead>
	<tbody>
                @define $c = 1
		@foreach ($schedules as $schedule)
		<tr>
			<td class="text-left">{{ $c }}</td>
			<td>{{ $schedule->activities_name }}</td>
                        <td>{{ date_format(date_create_from_format('H:i:s', $schedule->from_time), 'H:i A') }}</td>
                        <td>{{ date_format(date_create_from_format('H:i:s', $schedule->to_time), 'H:i A') }}</td>
		</tr>
                @define $c = $c + 1
                @endforeach
	</tbody>
	</table>
        @endif
</div>

@stop
