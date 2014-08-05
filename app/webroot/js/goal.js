var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap']);

goalApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider.
	when('/show', {
		templateUrl: 'frontend/goals/show.html',
		controller: 'GoalShowCtrl'
	}).
	when('/add', {
		templateUrl: 'frontend/goals/add.html',
		controller: 'GoalAddCtrl'
	}).
	when('/edit/:id', {
		templateUrl: 'frontend/goals/edit.html',
		controller: 'GoalEditCtrl'
	}).
	otherwise({
		redirectTo: '/show'
	});
}]);

goalApp.factory('AlertService', function() {
	return {
		alerts : []
	};
});

goalApp.controller('GoalCtrl', function ($scope) {
	$scope.formdata = [];
});

goalApp.controller('GoalShowCtrl', function ($scope, $http, $window, AlertService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$http.get('goals.json').
	success(function(data, status, headers, config) {
		$scope.goals = data['goals'];
	}).
	error(function(data, status, headers, config) {
		$scope.goals = [];
	});

	/* Goal delete action */
	$scope.deleteGoal = function(id) {
		AlertService.alerts = [];
		var deleteConfirm = $window.confirm('Are you sure you want to delete the goal and all its task ?');   
		if (deleteConfirm) {
			$http.delete('goals/delete/' + id + '.json').
			success(function(data, status, headers, config) {
				if (data['message']['type'] == 'error') {
					AlertService.alerts.push({type: 'danger', msg: data['message']['text']});
				} else
				if (data['message']['type'] == 'success') {
					AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				}
			}).
			error(function(data, status, headers, config) {
				AlertService.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
			});
		}
	}
});

goalApp.controller('GoalAddCtrl', function ($scope, $http, $location, AlertService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = []

	$scope.addGoal = function() {
		$scope.alerts = [];

		var data = {
			title: $scope.formdata.Title,
			start_date: $scope.formdata.Startdate,
			end_date: $scope.formdata.Enddate,
			category_id: $scope.formdata.Category,
			difficulty: $scope.formdata.Difficulty,
			priority: $scope.formdata.Priority,
			reason: $scope.formdata.Reason,
		};

		$http.post("goals/add.json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/show');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

goalApp.controller('GoalEditCtrl', function ($scope, $http, $routeParams, $location, AlertService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = []

	$http.get('goals/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Title = data['goal']['Goal']['title'];
		$scope.formdata.Startdate = data['goal']['Goal']['start_date'];
		$scope.formdata.Enddate = data['goal']['Goal']['end_date'];
		$scope.formdata.Category = data['goal']['Goal']['category_id'];
		$scope.formdata.Difficulty = data['goal']['Goal']['difficulty'];
		$scope.formdata.Priority = data['goal']['Goal']['priority'];
		$scope.formdata.Reason = data['goal']['Goal']['reason'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Goal not found'});
		$location.path('/show');
	});

	$scope.editGoal = function() {
		$scope.alerts = [];

		var data = {
			title: $scope.formdata.Title,
			start_date: $scope.formdata.Startdate,
			end_date: $scope.formdata.Enddate,
			category_id: $scope.formdata.Category,
			difficulty: $scope.formdata.Difficulty,
			priority: $scope.formdata.Priority,
			reason: $scope.formdata.Reason,
		};

		$http.post("goals/edit/" +  + $routeParams['id'] + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/show');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

