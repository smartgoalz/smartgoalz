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
        $("#task_id").chained("#goals");
});

</script>

@stop

@section('breadcrumb-title', 'Timewatches')

@section('page-title', 'Timewatch')

@section('content')

{{ Form::open() }}

{{ Form::openGroup('goal', 'Selected Goal') }}
        {{ Form::text('goal', $goal->title, array('disabled')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('task', 'Selected Task') }}
        {{ Form::text('task', $task->title, array('disabled')) }}
{{ Form::closeGroup() }}

{{ Form::openGroup('start', 'Started On') }}
        {{ Form::text('start',
                date_format(date_create_from_format(
                        'Y-m-d H:i:s',
                        $timewatch->start_time
                ), explode('|', $dateformat)[0] . ' H:i:s'), array('disabled')) }}
{{ Form::closeGroup() }}

@if ($timewatch->is_active == 0)
<div class="form-group">
        <label>Status : Stopped</label>
</div>
{{ Form::openGroup('start', 'Stopped On') }}
        {{ Form::text('start',
                date_format(date_create_from_format(
                        'Y-m-d H:i:s',
                        $timewatch->stop_time
                ), explode('|', $dateformat)[0] . ' H:i:s'), array('disabled')) }}
{{ Form::closeGroup() }}
<div class="form-group">
        <label>Time Elapsed : TODO Hours</label>
</div>
@else
<div class="form-group">
        <label>Status : Active</label>
</div>
<div class="form-group">
        <label>Time Elapsed : TODO Hours</label>
</div>
@endif

<div class="view-actions">

@if ($timewatch->is_active == 1)
        {{ HTML::linkAction('TimewatchesController@getStop', 'Stop', array($timewatch->id),
                array('class' => 'btn btn-danger')) }}
        <span class="small-margin"></span>
@endif

        {{ HTML::linkAction('TimewatchesController@getEdit', 'Edit', array($timewatch->id),
                array('class' => 'btn btn-primary')) }}
        <span class="small-margin"></span>
        {{ HTML::linkAction('TimewatchesController@getIndex', 'Back', array(),
                array('class' => 'btn btn-info')) }}
</div>

@stop
