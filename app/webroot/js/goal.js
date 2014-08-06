var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap']);

/******************* ROUTES *******************/

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
	when('/manage/:id', {
		templateUrl: 'frontend/goals/manage.html',
		controller: 'GoalManageCtrl'
	}).
	otherwise({
		redirectTo: '/show'
	});
}]);

/******************* SERVICES *******************/

goalApp.factory('AlertService', function() {
	return {
		alerts : []
	};
});

angular.module('goalApp').service('modalService', ['$modal', function ($modal) {
	var modalDefaults = {
		backdrop: true,
		keyboard: true,
		modalFade: true,
		templateUrl: 'frontend/partials/modal.html'
	};
	var modalOptions = {
		closeButtonText: 'Close',
		actionButtonText: 'OK',
		headerText: 'Proceed?',
		bodyText: 'Perform this action?'
	};

	this.showModal = function (customModalDefaults, customModalOptions) {
		if (!customModalDefaults)
			customModalDefaults = {};
		customModalDefaults.backdrop = 'static';
		return this.show(customModalDefaults, customModalOptions);
	};

	this.show = function (customModalDefaults, customModalOptions) {
		//Create temp objects to work with since we're in a singleton service
		var tempModalDefaults = {};
		var tempModalOptions = {};

		//Map angular-ui modal custom defaults to modal defaults defined in service
		angular.extend(tempModalDefaults, modalDefaults, customModalDefaults);

		//Map modal.html $scope custom properties to defaults defined in service
		angular.extend(tempModalOptions, modalOptions, customModalOptions);

		if (!tempModalDefaults.controller) {
			tempModalDefaults.controller = function ($scope, $modalInstance) {
				$scope.modalOptions = tempModalOptions;
				$scope.modalOptions.ok = function (result) {
					$modalInstance.close(result);
				};
				$scope.modalOptions.close = function (result) {
					$modalInstance.dismiss('cancel');
				};
			}
		}
		return $modal.open(tempModalDefaults).result;
	};
}]);

/******************* CONTROLLERS *******************/

goalApp.controller('GoalCtrl', function ($scope) {
	$scope.formdata = [];
});

goalApp.controller('GoalShowCtrl', function ($scope, $http, $location, $modal, $window, $route, AlertService, modalService) {
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


	/* Delete goal action */
	$scope.deleteGoal = function(id) {
		/* Open modal window */
		var modalDefaults = {
			backdrop: true,
			keyboard: true,
			modalFade: true,
			templateUrl: 'frontend/partials/confirm.html'
		};

		var modalOptions = {
			closeButtonText: 'No',
			actionButtonText: 'Yes',
			headerText: 'Please confirm',
			bodyText: 'Are you sure you want to delete the goal ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			AlertService.alerts = [];
			$http.delete('goals/delete/' + id + '.json').
			success(function(data, status, headers, config) {
				if (data['message']['type'] == 'error') {
					AlertService.alerts.push({type: 'danger', msg: data['message']['text']});
				} else
				if (data['message']['type'] == 'success') {
					AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				AlertService.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
				$route.reload();
			});
		});
	};

	/* Manage goal action */
	$scope.manageGoal = function(id) {
		$location.path('/manage/' + id);
	}
});

goalApp.controller('GoalAddCtrl', function ($scope, $http, $location, AlertService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = [];

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

	$scope.formdata = [];

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

goalApp.controller('GoalManageCtrl', function ($scope, $http, $modal, $routeParams, $location, $route, AlertService, modalService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = [];
	$scope.goaldata = [];

	$http.get('goals/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.goaldata = data['goal'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Goal not found'});
		$location.path('/show');
	});

	/* Edit goal action */
	$scope.editGoal = function(id) {
		$location.path('/edit/' + id);
	}

	/* Edit task action */
	$scope.editTask = function() {
		/* Open modal window */
		var modalDefaults = {
			backdrop: true,
			keyboard: true,
			modalFade: true,
			templateUrl: 'frontend/tasks/add.html'
		};

		var modalOptions = {
			closeButtonText: 'Cancel',
			actionButtonText: 'Submit',
			headerText: 'Edit a Task',
			bodyText: ''
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			// todo
		});
	};

	/* Add task action */
	$scope.addTask = function() {
		/* Open modal window */
		var modalDefaults = {
			backdrop: true,
			keyboard: true,
			modalFade: true,
			templateUrl: 'frontend/tasks/add.html'
		};

		var modalOptions = {
			closeButtonText: 'Cancel',
			actionButtonText: 'Submit',
			headerText: 'Add Task',
			bodyText: ''
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) { console.log($scope.formdata); return;
			var data = {
				goal_id: $scope.goaldata.Goal.id,
				parent_id: 1,
				title: $scope.formdata.Title,
				start_date: $scope.formdata.Startdate,
				end_date: $scope.formdata.Enddate,
				reminder_time: $scope.formdata.Reminder,
				is_completed: $scope.formdata.Completed,
				completion_date: $scope.formdata.Completiondate,
				notes: $scope.formdata.Notes,
			};

			$http.post("tasks/add.json", data).
			success(function (data, status, headers) {
				if (data['message']['type'] == 'error') {
					AlertService.alerts.push({type: 'danger', msg: data['message']['text']});
				}
				if (data['message']['type'] == 'success') {
					AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				}
				$route.reload();
			}).
			error(function (data, status, headers) {
				AlertService.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
				$route.reload();
			});
		});
	};

	/* Delete task action */
	$scope.deleteTask = function(id) {
		/* Open modal window */
		var modalDefaults = {
			backdrop: true,
			keyboard: true,
			modalFade: true,
			templateUrl: 'frontend/partials/confirm.html'
		};

		var modalOptions = {
			closeButtonText: 'No',
			actionButtonText: 'Yes',
			headerText: 'Please confirm',
			bodyText: 'Are you sure you want to delete the task ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			AlertService.alerts = [];
			$http.delete('tasks/delete/' + id + '.json').
			success(function(data, status, headers, config) {
				if (data['message']['type'] == 'error') {
					AlertService.alerts.push({type: 'danger', msg: data['message']['text']});
				} else
				if (data['message']['type'] == 'success') {
					AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				AlertService.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
				$route.reload();
			});
		});
	};
});

