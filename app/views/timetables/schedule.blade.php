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

@section('page-title', 'Schedule')

@section('content')

<div class="header-button">
        {{ HTML::linkAction('TimetablesController@getCreate', 'Add Schedule', array($activity->id),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('TimetablesController@getManage', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

@if ($schedules->count() < 1)
        <div>No schedule found. Please add one to get started.</div>
@else
<div>
<table class="table table-hover">
<thead>
	<tr>
		<th>From</th>
                <th>To</th>
		<th>Days</th>
		<th>Actions</th>
	</tr>
</thead>
<tbody>
        @define $c = 1
        @foreach ($schedules as $schedule)
	<tr>
                <td>{{ date_format(date_create_from_format('H:i:s', $schedule->from_time), 'H:i A') }}</td>
                <td>{{ date_format(date_create_from_format('H:i:s', $schedule->to_time), 'H:i A') }}</td>
                <td>
                @define $days = explode(',', $schedule->days)
                @define $last = count($days) - 1
                @foreach ($days as $index => $day)
                        @if ($index != $last)
                                {{ ucfirst(strtolower($day)) }},
                        @else
                                {{ ucfirst(strtolower($day)) }}
                        @endif
                @endforeach
                </td>
		<td class="text-left">
                        {{ HTML::linkAction('TimetablesController@getEdit', 'Edit', array($activity->id, $schedule->id)) }}
			<span class="small-margin"></span>
                        {{ HTML::decode(HTML::linkAction(
                                'TimetablesController@deleteDestroy',
                                '<span class="glyphicon glyphicon-trash"></span>',
                                array($schedule->id),
                                array(
                                        'class' => '',
                                        'data-method' => 'DELETE',
                                        'data-confirm' => 'Are you sure you want to delete the schedule ?'
                                ))) }}
		</td>
	</tr>
        @define $c = $c + 1
        @endforeach
</tbody>
</table>
</div>
@endif

@stop
