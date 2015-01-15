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

@section('page-title', 'Journals')

@section('content')

<div class="header-button">
        {{ HTML::linkAction('JournalsController@getCreate', 'Add Entry', array(),
                array('class' => 'btn btn-primary')) }}
</div>

@if ($journals->count() < 1)

<div>No journal entry found. Please add a journal entry to get started.</div>

@else

<div>
	<table class="table table-hover">
		<thead>
		<tr>
			<th class="col-sm-1 text-left">Date</th>
			<th class="text-left">Entry</th>
			<th class="col-sm-1 text-left">Actions</th>
		</tr>
		</thead>

		<tbody>
                @foreach ($journals as $journal)
		<tr>
                        <td class="text-center">
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $journal->date), explode('|', $dateformat)[0]) }}
                        </td>

			<td>{{ HTML::linkAction('JournalsController@getShow', $journal->title, $journal->id) }}</td>

			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('JournalsController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $journal->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'JournalsController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($journal->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the journal entrys ?'
                                        ))) }}
			</td>
		</tr>
                @endforeach
		</tbody>
	</table>
</div>

@endif

@stop
