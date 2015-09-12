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
        $(function() {
                $("#tabs").tabs();
        });
});

</script>

@stop

@section('breadcrumb-title', 'Timetables')

@section('page-title', 'Manage Timetable')

@section('content')

<div class="header-button">
        {{ HTML::linkAction('ActivitiesController@getCreate', 'Add Activity', array(),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('TimetablesController@getIndex', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

<div id="tabs">
        <ul>
                <li><a href="#ALL">All Activities</a></li>
                <li><a href="#SUNDAY">Sunday</a></li>
                <li><a href="#MONDAY">Monday</a></li>
                <li><a href="#TUESDAY">Tuesday</a></li>
                <li><a href="#WEDNESDAY">Wednesday</a></li>
                <li><a href="#THURSDAY">Thursday</a></li>
                <li><a href="#FRIDAY">Friday</a></li>
                <li><a href="#SATURDAY">Saturday</a></li>
        </ul>

        <div id="ALL">
		<div class="timetable-day">All Activities</div>
                @if ($activities->count() < 1)
                        <div>No activities found. Please add one to get started.</div>
                @else
		<div>
		<table class="table table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Activity</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
                        @define $c = 1
                        @foreach ($activities as $activity)
			<tr>
				<td class="text-left">{{ $c }}</td>
				<td>{{ $activity->title }}</td>
				<td class="text-left">
                                        {{ HTML::linkAction('TimetablesController@getSchedule', 'Schedule', array($activity->id)) }}
					<span class="small-margin"></span>
                                        {{ HTML::linkAction('ActivitiesController@getEdit', 'Edit', array($activity->id)) }}
					<span class="small-margin"></span>
                                        {{ HTML::decode(HTML::linkAction(
                                                'ActivitiesController@deleteDestroy',
                                                '<span class="glyphicon glyphicon-trash"></span>',
                                                array($activity->id),
                                                array(
                                                        'class' => '',
                                                        'data-method' => 'DELETE',
                                                        'data-confirm' => 'Are you sure you want to delete the activity ?'
                                                ))) }}
				</td>
			</tr>
                        @define $c = $c + 1
                        @endforeach
		</tbody>
		</table>
		</div>
                @endif
	</div>

        @define $weekdays = array('SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY');
        @foreach ($weekdays as $weekday)
        <div id="{{ $weekday }}">
		<div class="timetable-day">Timetable for {{ ucfirst(strtolower($weekday)) }}</div>
                @if (!isset($timetable[$weekday]))
                        <div>No schedule found. Please add one to get started.</div>
                @else
		<div>
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
                        @foreach ($timetable[$weekday] as $activity)
			<tr>
				<td class="text-left">{{ $c }}</td>
				<td>{{ $activity['title'] }}</td>
                                <td>{{ date_format(date_create_from_format('H:i:s', $activity['from_time']), 'H:i A') }}</td>
                                <td>{{ date_format(date_create_from_format('H:i:s', $activity['to_time']), 'H:i A') }}</td>
			</tr>
                        @define $c = $c + 1
                        @endforeach
		</tbody>
		</table>
		</div>
                @endif
	</div>
        @endforeach
</div>

@stop
