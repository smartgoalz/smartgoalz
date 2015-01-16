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

@section('page-title', 'Notes')

@section('content')

<div class="pull-right">
        <form name="searchform" method="GET">
        <input name="search" id="search" class="form-input" value="{{ $search }}"/>
        <input type="submit" class="btn btn-info" value="Search" />
        </form>
</div>

<div class="header-button">
        {{ HTML::linkAction('NotesController@getCreate', 'Add Note', array(),
                array('class' => 'btn btn-primary')) }}
</div>

@if ($search)
<div class="search-title">
        Showing search results for "{{ $search }}"
</div>
@endif

@if ($notes->count() < 1)

<div>No notes found. Please add a note to get started.</div>

@else

<div>
	<table class="table table-hover">
		<thead>
		<tr>
			<th>Note</th>
			<th>Actions</th>
		</tr>
		</thead>

		<tbody>
                @foreach ($notes as $note)
		<tr>
			<td class="text-left">
                                {{ HTML::linkAction('NotesController@getShow', $note->title, $note->id) }}
                                <span class="small-margin"></span>
                                <span class="small-text">created on {{ date_format(date_create_from_format('Y-m-d H:i:s', $note->created_at), explode('|', $dateformat)[0]) }}</span>
                        </td>
			<td class="text-left">
                                {{ HTML::decode(HTML::linkAction('NotesController@getEdit',
                                        '<span class="glyphicon glyphicon-pencil">', $note->id)) }}
				<span class="small-margin"></span>
                                {{ HTML::decode(HTML::linkAction(
                                        'NotesController@deleteDestroy',
                                        '<span class="glyphicon glyphicon-trash"></span>',
                                        array($note->id),
                                        array(
                                                'class' => '',
                                                'data-method' => 'DELETE',
                                                'data-confirm' => 'Are you sure you want to delete the note ?'
                                        ))) }}
			</td>
		</tr>
                @endforeach
		</tbody>
	</table>
</div>

<div class="text-center paginator-padding">
        {{ $notes->links() }}
</div>

@endif

@stop
