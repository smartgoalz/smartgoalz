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

	/* Enable text editor */
	$("#note").jqte();

});

</script>

@stop

@section('breadcrumb-title', 'Notes')

@section('page-title', 'Edit Note')

@section('content')

{{ Form::model($note) }}

{{ Form::openGroup('title', 'Title') }}
        {{ Form::text('title') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('note', 'Note') }}
        {{ Form::textarea('note') }}
{{ Form::closeGroup() }}

{{ Form::openGroup('pin_dashboard') }}
{{ Form::checkbox('pin_dashboard', 1, FALSE, ['label' => 'Pin to dashboard']) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('pin_top') }}
{{ Form::checkbox('pin_top', 1, FALSE, ['label' => 'Pin to top']) }}
{{ Form::closeGroup() }}

{{ Form::submit('Update') }}
{{ HTML::linkAction('NotesController@getIndex', 'Cancel') }}

{{ Form::close() }}

@stop
