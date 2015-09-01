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

@extends('layouts.setup')

@section('head')

<script type="text/javascript">

$(document).ready(function() {

});

</script>

@stop

@section('content')

	<h3>SMARTGoalz Installation</h3>

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

		{{ Form::openGroup('dbname', 'Database name') }}
			{{ Form::text('dbname') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('dbhost', 'Database host') }}
			{{ Form::text('dbhost') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('dbport', 'Database port') }}
			{{ Form::text('dbport') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('dbusername', 'Database username') }}
			{{ Form::text('dbusername') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('dbpassword', 'Database password') }}
			{{ Form::password('dbpassword') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('username', 'Administrator username') }}
			{{ Form::text('username') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('password', 'Administrator password') }}
			{{ Form::password('password') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('confirmpassword', 'Confirm password') }}
			{{ Form::password('confirmpassword') }}
		{{ Form::closeGroup() }}

		{{ Form::openGroup('email', 'Administrator Email') }}
			{{ Form::text('email') }}
		{{ Form::closeGroup() }}

		{{ Form::submit('Install') }}

		{{ Form::close() }}
	</div>


@stop
