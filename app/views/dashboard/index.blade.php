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

@section('page-title', 'Dashboard')

@section('content')

<div class="row">

	<div class="col-lg-12">

		<div class="row">

			<div class="col-lg-3 col-md-6 col-xs-12">
				<div class="widget">
					<div class="widget-body">
						<div class="widget-icon green pull-left">
							<i class="fa fa-cubes"></i>
						</div>
					<div class="widget-content pull-left">
						<div class="title">{{ $dashboard['active_goals'] }}</div>
							<div class="comment">Active Goals</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 col-xs-12">
				<div class="widget">
					<div class="widget-body">
						<div class="widget-icon green pull-left">
							<i class="fa fa-tasks"></i>
						</div>
					<div class="widget-content pull-left">
						<div class="title">{{ $dashboard['pending_tasks'] }}</div>
							<div class="comment">Pending Tasks</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 col-xs-12">
				<div class="widget">
					<div class="widget-body">
						<div class="widget-icon green pull-left">
							<i class="fa fa-bell"></i>
						</div>
					<div class="widget-content pull-left">
						<div class="title">0</div>
							<div class="comment">Upcoming Tasks</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 col-xs-12">
				<div class="widget">
					<div class="widget-body">
						<div class="widget-icon green pull-left">
							<i class="fa fa-clock-o"></i>
						</div>
					<div class="widget-content pull-left">
						<div class="title">{{ $dashboard['active_timewatches'] }}</div>
							<div class="comment">Active Timewatches</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>

		</div>
                <!-- /.row -->

		<div class="row">

			<div class="col-lg-6">
				<div class="widget">
					<div class="widget-header">
						<i class="fa fa-info-circle"></i> Alerts
						<div class="clearfix"></div>
					</div>
					<div class="widget-body">
						<div class="message">

						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="widget">
					<div class="widget-header">
						<i class="fa fa-edit"></i> Notes
						<div class="clearfix"></div>
					</div>
					<div class="widget-body">
						<div class="message">
                                                        @foreach ($dashboard['notes_dashboard'] as $note)
							<div class="widget-item">
								<div class="widget-title">{{ $note->title }}</div>
								<div>{{ $note->note }}</div>
							</div>
                                                        @endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
                <!-- /.row -->

	</div>

</div>
<!-- /.row -->

@stop
