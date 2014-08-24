var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap', 'ui.router', 'ngCookies']);

/******************* ROUTES *******************/

goalApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider.
	when('/show', {
		templateUrl: 'frontend/goals/show.html',
	}).
	when('/add', {
		templateUrl: 'frontend/goals/add.html',
	}).
	when('/edit/:id', {
		templateUrl: 'frontend/goals/edit.html',
	}).
	when('/manage/:id', {
		templateUrl: 'frontend/goals/manage.html',
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

goalApp.factory('SelectService', function($http, $q) {

	var categories = function() {
		var deferred = $q.defer();
		$http({method : "GET", url: "categories/index.json"}).
		success(function(result) {
		    deferred.resolve(result);
		}).
		error(function(result) {
			/* TODO */
		});
		return deferred.promise;
        };

	var priorities = function() {
		return {1: 'Highest', 2: 'High', 3: 'Medium', 4: 'Low', 5: 'Lowest'};
	};

	var difficulties = function() {
		return {1: 'Very Hard', 2: 'Hard', 3: 'Normal', 4: 'Easy', 5: 'Very Easy'};
	}

	return {
		priorities : priorities(),
		difficulties : difficulties(),
		categories: categories,
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
		/* Create temp objects to work with since we're in a singleton service */
		var tempModalDefaults = {};
		var tempModalOptions = {};

		/* Map angular-ui modal custom defaults to modal defaults defined in service */
		angular.extend(tempModalDefaults, modalDefaults, customModalDefaults);

		/* Map modal.html $scope custom properties to defaults defined in service */
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

/******************* FILTERS *******************/

goalApp.filter('fixTime', function() {
	return function(input) {
		var goodTime = input.replace(/(.+) (.+)/, "$1T$2Z");
		return goodTime;
	};
});

/******************* CONTROLLERS *******************/

goalApp.controller('MainGoalCtrl', function ($scope, $rootScope, $cookieStore) {
	$scope.formdata = [];

	$rootScope.pageTitle = "";

	/* Template function */
	var mobileView = 992;
	$scope.getWidth = function() {
		return window.innerWidth;
	};
	$scope.$watch($scope.getWidth, function(newValue, oldValue) {
		if (newValue >= mobileView) {
			if (angular.isDefined($cookieStore.get('toggle'))) {
				if ($cookieStore.get('toggle') == false) {
					$scope.toggle = false;
				} else {
					$scope.toggle = true;
				}
			} else {
				$scope.toggle = true;
			}
		} else {
		    $scope.toggle = false;
		}
	});
	$scope.toggleSidebar = function() {
		$scope.toggle = ! $scope.toggle;
		$cookieStore.put('toggle', $scope.toggle);
	};
	window.onresize = function() {
		$scope.$apply();
	};
});

goalApp.controller('GoalCtrl', function ($scope, $rootScope, $cookieStore) {
	$scope.formdata = [];

	$rootScope.pageTitle = "";
});

/* Show goals */
goalApp.controller('GoalShowCtrl', function ($scope, $rootScope, $http, $location, $modal, $window, $route, AlertService, modalService, SelectService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = [];

	var categoryPromise = SelectService.categories();
	categoryPromise.then(function(result) {
		$scope.categories = result['categories'];
	});

	$rootScope.pageTitle = "Dashboard";

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

	$scope.clearAlerts = function() {
		AlertService.alerts = [];
		$scope.alerts = [];
	}
});

/* Add goal */
goalApp.controller('GoalAddCtrl', function ($scope, $rootScope, $http, $location, AlertService, SelectService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = [];

	var categoryPromise = SelectService.categories();
	categoryPromise.then(function(result) {
		$scope.categories = result['categories'];
	});

	$scope.formdata = [];

	$rootScope.pageTitle = "Add Goal";

	$scope.addGoal = function() {
		$scope.alerts = [];

		var data = {
			'Goal' : {
				title: $scope.formdata.Title,
				start_date: $scope.formdata.Startdate,
				due_date: $scope.formdata.Duedate,
				category_id: $scope.formdata.Category,
				difficulty: $scope.formdata.Difficulty,
				priority: $scope.formdata.Priority,
				reason: $scope.formdata.Reason,
				is_completed: 0,
				task_total: 0,
				task_completed: 0,
			}
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

/* Edit goal */
goalApp.controller('GoalEditCtrl', function ($scope, $rootScope, $http, $routeParams, $location, AlertService, SelectService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Edit Goal";

	$scope.formdata = [];

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = [];

	var categoryPromise = SelectService.categories();
	categoryPromise.then(function(result) {
		$scope.categories = result['categories'];
	});

	$http.get('goals/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Title = data['goal']['Goal']['title'];
		$scope.formdata.Startdate = data['goal']['Goal']['start_date'];
		$scope.formdata.Duedate = data['goal']['Goal']['due_date'];
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
			'Goal' : {
				title: $scope.formdata.Title,
				start_date: $scope.formdata.Startdate,
				due_date: $scope.formdata.Duedate,
				category_id: $scope.formdata.Category,
				difficulty: $scope.formdata.Difficulty,
				priority: $scope.formdata.Priority,
				reason: $scope.formdata.Reason,
			}
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

goalApp.controller('GoalManageCtrl', function ($scope, $http, $modal, $routeParams, $location, $route, AlertService, modalService, SelectService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = [];
	$scope.goaldata = [];

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;

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

	/* Add task action */
	$scope.addTask = function() {
		AlertService.alerts = [];

		/* Open modal window */
		var addModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/add.html',
			controller: AddModalInstanceCtrl,
			resolve: {
				goaldata: function () {
					return $scope.goaldata;
				}
			}
		});

		addModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Edit task action */
	$scope.editTask = function(id) {
		AlertService.alerts = [];

		/* Open modal window */
		var editModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/edit.html',
			controller: EditModalInstanceCtrl,
			resolve: {
				goaldata: function () {
					return $scope.goaldata;
				},
				id : function () {
					return id;
				}
			}
		});

		editModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Delete task action */
	$scope.deleteTask = function(id) {
		AlertService.alerts = [];

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

	/* Mark task completed action */
	$scope.doneTask = function(id) {
		AlertService.alerts = [];

		$http.post("tasks/done/" + id + ".json").
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
	};
});

/* Task add modal */
var AddModalInstanceCtrl = function ($scope, $modalInstance, $http, AlertService, goaldata) {
	$scope.alerts = [];
	$scope.formdata = [];
	$scope.goaldata = goaldata;

	$scope.submit = function () {
		$scope.alerts = [];

		var data = {
			'Task' : {
				goal_id: goaldata.Goal.id,
				title: $scope.formdata.Title,
				start_date: $scope.formdata.Startdate,
				due_date: $scope.formdata.Duedate,
				prev_id: $scope.formdata.Prev,
				reminder_time: $scope.formdata.Reminder,
				is_completed: $scope.formdata.Completed,
				completion_date: $scope.formdata.Completiondate,
				notes: $scope.formdata.Notes,
			}
		};

		$http.post("tasks/add.json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$modalInstance.close();
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};
};

/* Task edit modal */
var EditModalInstanceCtrl = function ($scope, $modalInstance, $http, AlertService, goaldata, id) {
	$scope.alerts = [];
	$scope.formdata = [];
	$scope.goaldata = goaldata;
	$scope.taskid = id;
	AlertService.alerts = [];

	var found = false;
	var task = [];
	for(var c = 0, len = goaldata.Task.length; c < len; c++) {
		if (goaldata.Task[c].id == id) {
			found = true;
			var task = goaldata.Task[c];
			break;
		}
	}

	if (found) {
			$scope.formdata.Title = task.title;
			$scope.formdata.Startdate = task.start_date;
			$scope.formdata.Duedate = task.due_data;
			$scope.formdata.Prev = task.prev_id;
			$scope.formdata.Reminder = task.reminder_time;
			$scope.formdata.Completed = task.is_completed;
			$scope.formdata.Completiondate = task.completion_date;
			$scope.formdata.Notes = task.notes;
	} else {
		AlertService.alerts.push({type: 'danger', msg: 'Task not found'});
		$modalInstance.dismiss();
	}

	$scope.submit = function () {
		$scope.alerts = [];

		var data = {
			'Task' : {
				goal_id: goaldata.Goal.id,
				title: $scope.formdata.Title,
				start_date: $scope.formdata.Startdate,
				due_date: $scope.formdata.Duedate,
				prev_id: $scope.formdata.Prev,
				reminder_time: $scope.formdata.Reminder,
				is_completed: $scope.formdata.Completed,
				completion_date: $scope.formdata.Completiondate,
				notes: $scope.formdata.Notes,
			}
		};

		$http.post("tasks/edit/" + id + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$modalInstance.close();
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};
};

