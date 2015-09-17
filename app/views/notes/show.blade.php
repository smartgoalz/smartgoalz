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

@section('breadcrumb-title', 'Notes')

@section('page-title', 'Note')

@section('content')

<div>
	<span class="view-title">{{ $note->title }}</span>
        <div class="small-text">Created on {{ date_format(date_create_from_format('Y-m-d H:i:s', $note->created_at), $dateformat_php) }}</div>
</div>

<div class="view-content">
        {{ $note->note }}
</div>

<div class="view-content-footer">
	@if ($note->pin_dashboard)
		<div><i class="fa fa-check-square-o"></i> Pinned to Dashboard</div>
	@endif
	@if ($note->pin_top)
		<div><i class="fa fa-check-square-o"></i> Pinned to Top</div>
	@endif
</div>

<div class="view-actions">
        {{ HTML::linkAction('NotesController@getEdit', 'Edit', array($note->id),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('NotesController@getIndex', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

@stop
