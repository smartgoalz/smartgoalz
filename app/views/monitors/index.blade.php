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

@section('page-title', 'Monitors')

@section('content')

<div class="header-button">
        {{ HTML::linkAction('MonitorsController@getCreate', 'Add Monitor', array(),
                array('class' => 'btn btn-primary')) }}
</div>

@if ($monitors->count() < 1)

<div>No monitors found. Please add a monitor to get started.</div>

@else

<div>
	<table class="table table-hover">
		<thead>
		<tr>
			<th>Title</th>
			<th class="text-center">Type</th>
			<th class="text-center">Min</th>
			<th class="text-center">Max</th>
			<th class="text-center">Min Thres</th>
			<th class="text-center">Max Thres</th>
			<th class="text-center">Lower Better</th>
			<th class="text-center">Units</th>
			<th class="text-center">Frequency</th>
			<th class="text-left">Actions</th>
		</tr>
		</thead>

		<tbody>
                @foreach ($monitors as $monitor)
		<tr>
			<td>{{ HTML::linkAction('MonitorsController@getShow', $monitor->title, $monitor->id) }}</td>

                        <td class="text-center">
                                {{ Constants::$monitor_types[$monitor->type] }}
                        </td>

                        <td class="text-center">
                                {{ $monitor->minimum }}
                        </td>

                        <td class="text-center">
                                {{ $monitor->maximum }}
                        </td>

                        <td class="text-center">
                                {{ $monitor->minimum_threshold }}
                        </td>

                        <td class="text-center">
                                {{ $monitor->maximum_threshold }}
                        </td>

                        <td class="text-center">
                                @if ($monitor->is_lower_better == 0)
                                        No
                                @else
                                        Yes
                                @endif
                        </td>

                        <td class="text-center">
                                {{ $monitor->units }}
                        </td>

                        <td class="text-center">
                                {{ Constants::$monitor_frequencies[$monitor->frequency] }}
                        </td>

			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('MonitorsController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $monitor->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'MonitorsController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($monitor->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the monitor ?'
                                        ))) }}
			</td>
		</tr>
                @endforeach
		</tbody>
	</table>
</div>

@endif

@stop
