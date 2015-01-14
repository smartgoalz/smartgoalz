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

<!doctype html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>SMARTGoalz</title>

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- jQuery & jQuery UI -->
        {{ HTML::style('css/jquery-ui.min.css') }}
        {{ HTML::style('css/jquery-ui.structure.min.css') }}
        {{ HTML::style('css/jquery-ui.theme.min.css') }}
	{{ HTML::style('css/jquery-ui-timepicker-addon.css') }}
        {{ HTML::script('js/jquery-1.11.1.min.js') }}
        {{ HTML::script('js/jquery-ui.min.js') }}

        <!-- jQuery Plugins -->
        {{ HTML::script('js/jquery-ui-timepicker-addon.js') }}
        {{ HTML::style('css/jquery-ui-timepicker-addon.css') }}

        <!-- Bootstrap -->
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/bootstrap-theme.min.css') }}
        {{ HTML::script('js/bootstrap.min.js') }}

        <!-- Bootstrap Select -->
        {{ HTML::style('css/bootstrap-select.min.css') }}
        {{ HTML::script('js/bootstrap-select.min.js') }}

	<!-- Chart.js -->
        {{ HTML::script('js/Chart.min.js') }}

	<!-- jQuery scripting adapter for links -->
        {{ HTML::script('js/rails.js') }}

        <!-- Custom CSS -->
        @define $time = rand(0, 1000)

        {{ HTML::style('css/userstyle.css?' . $time) }}

        @yield('head')
</head>

<body>

<div id="wrapper">

<div class="container-fluid">

<div id="page-wrapper">

	<!-- Content -->
	<div class="row">
		<div class="col-lg-12">
			<div class="page-content">
				@yield('content')
			</div>
		</div>
	</div>
        <!-- /.row -->

</div><!-- /page-wrapper -->

</div><!-- /container-fluid -->

</div><!-- /wrapper -->

</body>

</html>
