<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html ng-app="goalApp">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>SMART Goalz | <?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		/* Angular JS version 1.2.10 */
		echo $this->Html->script('angular.min.js');
		echo $this->Html->script('angular-cookies.min.js');
		echo $this->Html->script('angular-route.min.js');
		echo $this->Html->script('angular-resource.min.js');

		/* Angular UI version 0.11.0 */
		echo $this->Html->script('ui-bootstrap-tpls-0.11.0.min.js');

		echo $this->Html->script('angular-ui-router.js');

		/* Bootstrap version 3.1.1 */
		echo $this->Html->css('bootstrap.min.css?'.time());
		echo $this->Html->css('bootstrap-theme.min.css?'.time());

		/* Font awesome version 4.1.0*/
		echo $this->Html->css('font-awesome.min.css?'.time());

		/* Dashboard */
		echo $this->Html->css('dashboard.css?'.time());

		echo $this->Html->script('goal.js?'.time());

		echo $this->Html->css('style.css?'.time());

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>

<body ng-controller="BodyCtrl">

<div id="page-wrapper" ng-class="{'active': toggle}" ng-cloak>

	<!-- Sidebar -->
	<div id="sidebar-wrapper">
		<ul class="sidebar">
			<li class="sidebar-main">
				<a href="#" ng-click="toggleSidebar()">smartgoalz.org<span class="menu-icon glyphicon glyphicon-transfer"></span></a>
			</li>
			<li class="sidebar-title"><span>NAVIGATION</span></li>
			<li class="sidebar-list">
				<a href="#/dashboard">Dashboard <span class="menu-icon fa fa-tachometer"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/show">Goals <span class="menu-icon fa fa-cubes"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#">Categories <span class="menu-icon fa fa-tasks"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/notes">Notes <span class="menu-icon fa fa-edit"></span></a>
			</li>
			<li class="sidebar-list">
				<a href="#/notes">Journal <span class="menu-icon fa fa-list-alt"></span></a>
			</li>
			<li class="sidebar-title separator"><span>QUICK LINKS</span></li>
			<li class="sidebar-list">
				<a href="#" target="_blank">Upcoming Tasks <span class="menu-icon fa fa-calendar"></span></a>
			</li>
		</ul>
		<!-- Sidebar Footer -->
		<div class="sidebar-footer">
			<div class="col-xs-4">
				<a href="https://smartgoalz.org" target="_blank">Home</a>
			</div>
			<div class="col-xs-4">
				<a href="https://github.com/rootls/smartgoalz" target="_blank">Github</a>
			</div>
			<div class="col-xs-4">
				<a href="https://github.com/rootls/smartgoalz/issues">Support</a>
			</div>
		</div>
		<!-- End Sidebar Footer -->
	</div>
	<!-- End Sidebar -->

	<div id="content-wrapper">
		<div class="page-content">

			<!-- Header Bar -->
			<div class="row header">
				<div class="col-xs-12">
					<div class="user pull-right">
						<div class="item dropdown">
							<a href="#" class="dropdown-toggle"><img src="img/avatar.jpg"></a>
							<ul class="dropdown-menu dropdown-menu-right">
								<li class="dropdown-header">Joe Bloggs</li>
								<li class="divider"></li>
								<li class="link"><a href="#">Profile</a></li>
								<li class="link"><a href="#">Menu Item</a></li>
								<li class="link"><a href="#">Menu Item</a></li>
								<li class="divider"></li>
								<li class="link"><a href="#">Logout</a></li>
							</ul>
						</div>
						<div class="item dropdown">
							<a href="#" class="dropdown-toggle"><i class="fa fa-bell-o"></i></a>
							<ul class="dropdown-menu dropdown-menu-right">
								<li class="dropdown-header">Notifications</li>
								<li class="divider"></li>
								<li><a href="#">Server Down!</a></li>
							</ul>
						</div>
					</div>
					<div class="meta">
						<div class="page">{{ pageTitle }}</div>
						<div class="breadcrumb-links">Home / {{ pageTitle }}</div>
					</div>
				</div>
			</div>
			<!-- End Header Bar -->

			<!-- Main Content -->
			<div class="row alerts-container" data-ng-show="alerts.length">
				<div class="col-xs-12">
					<alert data-ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</alert>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<?php echo $this->fetch('content'); ?>
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
