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

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>SMARTGoalz</title>
	<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">

	<link rel="shortcut icon" type="image/png" href="favicon.ico" />

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

        <!-- Date.js -->
        {{ HTML::script('js/date.js') }}

	<!-- jQuery scripting adapter for links -->
        {{ HTML::script('js/rails.js') }}

        <!-- Custom CSS -->
        @define $time = rand(0, 1000)

        {{ HTML::style('css/style.css?' . $time) }}
        {{ HTML::style('css/dashboard.css?' . $time) }}
</head>

<script type="text/javascript">

$(document).ready(function() {

});

</script>

<body>

<!-- Page Wrapper -->
<div id="page-wrapper">

	<!-- Sidebar -->
	<div id="sidebar-wrapper">

		<!-- Sidebar Navigation -->
		<ul class="sidebar">
			<li class="sidebar-main">
				<a href="#" ng-click="toggleSidebar()">smartgoalz.org<span class="menu-icon glyphicon glyphicon-transfer"></span></a>
			</li>
			<li class="sidebar-title"><span>NAVIGATION</span></li>

	                <li class="sidebar-list">
				{{ HTML::decode(HTML::linkAction(
	                        'DashboardController@getIndex',
	                        'Dashboard <i class="menu-icon fa fa-tachometer"></i>')) }}
			</li>

			<li class="sidebar-list">
				<a href="#/goals" ng-click="clearAlerts()">Goals <span class="menu-icon fa fa-cubes"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/timewatches" ng-click="clearAlerts()">Timewatch <span class="menu-icon fa fa-clock-o"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/timetables" ng-click="clearAlerts()">Daily Timetable <span class="menu-icon fa fa-coffee"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/monitors" ng-click="clearAlerts()">Monitor <span class="menu-icon fa fa-signal"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/notes" ng-click="clearAlerts()">Notes <span class="menu-icon fa fa-edit"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/journals" ng-click="clearAlerts()">Journal <span class="menu-icon fa fa-list-alt"></span></a>
			</li>
			<li class="sidebar-title separator"><span>QUICK LINKS</span></li>
			<li class="sidebar-list">
				<a href="#" ng-click="clearAlerts()">Upcoming Tasks <span class="menu-icon fa fa-info-circle"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/calendar" ng-click="clearAlerts()">Calendar <span class="menu-icon fa fa-calendar"></span></a>
			</li>
		</ul>
		<!-- End Sidebar Navigation -->

		<!-- Sidebar Footer -->
		<div class="sidebar-footer">
			<div class="col-xs-4">
				<a href="https://smartgoalz.github.io" target="_blank">Home</a>
			</div>
			<div class="col-xs-4">
				<a href="https://github.com/smartgoalz/smartgoalz" target="_blank">Github</a>
			</div>
			<div class="col-xs-4">
				<a href="https://github.com/smartgoalz/smartgoalz/issues">Support</a>
			</div>
		</div>
		<!-- End Sidebar Footer -->

	</div>
	<!-- End Sidebar -->

	<!-- Content Wrapper -->
	<div id="content-wrapper">

		<!-- Page Content -->
		<div class="page-content">

			<!-- Header Bar -->
			<div class="row header">
				<div class="col-xs-12">
					<div class="user pull-right">
						<div class="item">
							<a href="user.html#/logout"><i class="fa fa-sign-out fa-fw"></i>Logout</a>
						</div>
						<div class="item">
							<a href="#/users/profile"><i class="fa fa-gears fa-fw"></i> Profile</a>
						</div>
					</div>
					<div class="meta">
						<div class="page">@yield('page-title')</div>
						<div class="breadcrumb-links">Home / @yield('page-title')</div>
					</div>
				</div>
			</div>
			<!-- End Header Bar -->

                        <!-- Alerts -->
                        <div class="row">
                                <div class="col-lg-12">
                                        @if (Session::has('alert-success'))
                                                <div class="alert alert-success alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                {{ Session::get('alert-success') }}
                                                </div>
                                        @endif
                                        @if (Session::has('alert-danger'))
                                                <div class="alert alert-danger alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                {{ Session::get('alert-danger') }}
                                                </div>
                                        @endif
                                </div>
                        </div>
                        <!-- /.row -->

			<div class="row">
				<div class="col-xs-12">
					<div>
				@yield('content')
					</div>
				</div>
			</div>
			<!-- End Main Content -->

		</div>
		<!-- End Page Content -->

	</div>
	<!-- End Content Wrapper -->

</div>
<!-- End Page Wrapper -->

</body>

</html>
