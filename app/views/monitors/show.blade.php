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

@section('breadcrumb-title', 'Monitors')

@section('page-title', 'Monitor')

@section('content')

<div>
	<span class="view-title">{{ $monitor->title }}</span>
</div>

<div class="details">
	<div>Type : {{ Constants::$monitor_types[$monitor->type] }}
		<span class="pull-right">
                        {{ HTML::decode(HTML::linkAction('MonitorsController@getEdit',
                                'Edit Monitor', $monitor->id,
                                array('class' => 'btn btn-success btn-xs'))) }}
		</span>
	</div>
	<div>Minimum : {{ $monitor->minimum }}</div>
	<div>Maximum : {{ $monitor->maximum }}</div>
	<div>Minimum Threshold : {{ $monitor->minimum_threshold }}</div>
	<div>Maximum Threshold : {{ $monitor->maximum_threshold }}</div>
	<div>
		Is Lower Better :
		@if ($monitor->is_lower_better == 1)
			Yes
		@else
			No
		@endif
	</div>
	<div>Units : {{ $monitor->units }}</div>
	<div>Frequency : {{ Constants::$monitor_frequencies[$monitor->frequency] }}</div>
</div>

<div class="view-actions">
        {{ HTML::linkAction('MonitorvaluesController@getCreate', 'Add Value', array($monitor->id),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('MonitorsController@getIndex', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

@if ($monitorvalues->count() < 1)
<div class="view-content">
	<div>No values found. Please add a value to get started.</div>
</div>
@else
<div class="view-content">
	<table class="table table-hover">
		<thead>
			<tr>
				<th class="text-left">Date</th>
				<th class="text-center">Value</th>
				<th class="text-center">Actions</th>
			</tr>
		</thead>
		<tbody>
                        @foreach ($monitorvalues as $monitorvalue)
			<tr>
				<td class="text-left">
					{{ date_format(date_create_from_format('Y-m-d H:i:s', $monitorvalue->date), explode('|', $dateformat)[0] . ' H:i A') }}
				</td>
				<td class="text-center">{{ $monitorvalue->value }}</td>
				<td class="text-center">
		                        {{ HTML::decode(HTML::linkAction('MonitorvaluesController@getEdit',
		                                '<i class="glyphicon glyphicon-pencil"></i>',
						array($monitor->id, $monitorvalue->id))) }}
					<span class="small-margin"></span>
	                                {{ HTML::decode(HTML::linkAction(
	                                        'MonitorvaluesController@deleteDestroy',
	                                        '<i class="glyphicon glyphicon-trash"></i>',
	                                        array($monitorvalue->id),
	                                        array(
	                                                'class' => '',
	                                                'data-method' => 'DELETE',
	                                                'data-confirm' => 'Are you sure you want to delete the value ?'
	                                        ))) }}
				</td>
			</tr>
                        @endforeach
		</tbody>
	</table>
</div>

<div class="text-center paginator-padding">
        {{ $monitorvalues->links() }}
</div>

@endif

@stop
