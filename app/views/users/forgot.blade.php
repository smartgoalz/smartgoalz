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

@extends('layouts.user')

@section('head')

<script type="text/javascript">

$(document).ready(function() {

});

</script>

@stop

@section('content')

<div class="container">

<div id="login-box" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">

<div class="panel panel-info" >

        <div class="panel-heading">
                <span class="panel-title">Forgot Password</span>
                <span class="pull-right panel-link">
                        {{ HTML::linkAction('UsersController@getLogin', 'Sign In') }}
                </span>
        </div>
        <!-- /.panel-heading -->

        <div class="panel-body">

	<!-- Alerts -->
	<div class="alerts">
                @if (Session::has('alert-success'))
			<div class="alert alert-success">
			{{ Session::get('alert-success') }}
			</div>
                @endif
                @if (Session::has('alert-danger'))
			<div class="alert alert-danger">
			{{ Session::get('alert-danger') }}
			</div>
                @endif
	</div>
        <!-- /.alerts -->

        <div>
                {{ Form::open() }}

                {{ Form::openGroup('input', 'Username or Email') }}
                        {{ Form::text('input') }}
                {{ Form::closeGroup() }}

                {{ Form::submit('Submit') }}

                {{ Form::close() }}
        </div>

        </div>
        <!-- /.panel-body -->

</div>
<!-- /.panel -->

</div>
<!-- /.login-box -->

</div>
<!-- /.container -->

@stop
