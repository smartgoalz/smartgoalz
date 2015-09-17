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

@section('breadcrumb-title', 'Goals')

@section('page-title', 'SMART Goals')

@section('content')

<div class="header-button">
        {{ HTML::linkAction('GoalsController@getCreate', 'Add Goal', array(),
                array('class' => 'btn btn-primary')) }}
</div>

@if ($goals->count() < 1)

<div>No goals found. Please add a goal to get started.</div>

@else

<div>
	<table class="table table-hover">
		<thead>
		<tr>
			<th>Goals</th>
			<th class="text-center">Completed</th>
			<th class="text-center">Category</th>
			<th class="text-center">Difficulty</th>
			<th class="text-center">Priority</th>
			<th class="text-center">Due date</th>
			<th class="text-left">Actions</th>
		</tr>
		</thead>

		<tbody>
                @foreach ($goals as $goal)
		<tr>
			<td>{{ HTML::linkAction('GoalsController@getShow', $goal->title, $goal->id) }}</td>

                        @if ($goal->is_completed == 1)
			<td class="text-center"><span class="glyphicon glyphicon-ok"></span></td>
                        @else
			<td class="text-center">{{ $goal->task_completed }} / {{ $goal->task_total }}</td>
                        @endif

                        <td class="text-center">
                                {{ $goal->category->title }}
                        </td>

                        <td class="text-center">
                                {{ Constants::$difficulties[$goal->difficulty] }}
                        </td>
                        <td class="text-center">
                                {{ Constants::$priorities[$goal->priority] }}
                        </td>
                        <td class="text-center">
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $goal->due_date), $dateformat_php) }}
                        </td>
			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('GoalsController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $goal->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'GoalsController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($goal->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the goal ?'
                                        ))) }}
			</td>
		</tr>
                @endforeach
		</tbody>
	</table>
</div>

@endif

@stop
