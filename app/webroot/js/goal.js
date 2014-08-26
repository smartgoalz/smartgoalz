var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap', 'ui.router', 'ngCookies']);

/******************* ROUTES *******************/

goalApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider.
	when('/dashboard', {
		templateUrl: 'frontend/dashboard/dashboard.html',
	}).
	when('/goals', {
		templateUrl: 'frontend/goals/index.html',
	}).
	when('/goals/add', {
		templateUrl: 'frontend/goals/add.html',
	}).
	when('/goals/edit/:id', {
		templateUrl: 'frontend/goals/edit.html',
	}).
	when('/goals/manage/:id', {
		templateUrl: 'frontend/goals/manage.html',
	}).
	when('/timewatch', {
		templateUrl: 'frontend/timewatch/start.html',
	}).
	when('/timewatch/stop/:id', {
		templateUrl: 'frontend/timewatch/stop.html',
	}).
	when('/timewatch/edit/:id', {
		templateUrl: 'frontend/timewatch/edit.html',
	}).
	when('/notes', {
		templateUrl: 'frontend/notes/index.html',
	}).
	when('/notes/view/:id', {
		templateUrl: 'frontend/notes/view.html',
	}).
	when('/notes/add', {
		templateUrl: 'frontend/notes/add.html',
	}).
	when('/notes/edit/:id', {
		templateUrl: 'frontend/notes/edit.html',
	}).
	when('/journals', {
		templateUrl: 'frontend/journals/index.html',
	}).
	when('/journals/view/:id', {
		templateUrl: 'frontend/journals/view.html',
	}).
	when('/journals/add', {
		templateUrl: 'frontend/journals/add.html',
	}).
	when('/journals/edit/:id', {
		templateUrl: 'frontend/journals/edit.html',
	}).
	otherwise({
		redirectTo: '/dashboard'
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

	var reminders = function() {
		return {1: 'Before due date', 2: 'Weekly', 3: 'Daily', 4: 'Never'};
	};

	return {
		priorities : priorities(),
		difficulties : difficulties(),
		reminders : reminders(),
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

goalApp.controller('BodyCtrl', function ($scope, $rootScope, $cookieStore) {
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

goalApp.controller('ContentCtrl', function ($scope, $rootScope, $cookieStore, AlertService) {
	$scope.formdata = [];

	$rootScope.pageTitle = "";

	$scope.clearAlerts = function() {
		AlertService.alerts = [];
		$scope.alerts = [];
	}
});

/* Show goals */
goalApp.controller('GoalsIndexCtrl', function ($scope, $rootScope, $http, $location, $modal, $window, $route, AlertService, modalService, SelectService) {
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

	$rootScope.pageTitle = "Goals";

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
				$location.path('/goals');
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
		$location.path('/goals');
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
				$location.path('/goals');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

goalApp.controller('GoalManageCtrl', function ($scope, $rootScope, $http, $modal, $routeParams, $location, $route, AlertService, modalService, SelectService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Manage Goal";

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
		$location.path('/goals');
	});

	/* Edit goal action */
	$scope.editGoal = function(id) {
		$location.path('/edit/' + id);
	}

	/* Add task action */
	$scope.addTask = function() {
		AlertService.alerts = [];

		/* Open modal window */
		var TaskAddModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/add.html',
			controller: TaskAddModalInstanceCtrl,
			resolve: {
				goaldata: function () {
					return $scope.goaldata;
				}
			}
		});

		TaskAddModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Edit task action */
	$scope.editTask = function(id) {
		AlertService.alerts = [];

		/* Open modal window */
		var TaskEditModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/edit.html',
			controller: TaskEditModalInstanceCtrl,
			resolve: {
				goaldata: function () {
					return $scope.goaldata;
				},
				id : function () {
					return id;
				}
			}
		});

		TaskEditModalInstance.result.then(function (result) {
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
var TaskAddModalInstanceCtrl = function ($scope, $modalInstance, $http, AlertService, SelectService, goaldata) {
	$scope.alerts = [];
	$scope.formdata = [];
	$scope.goaldata = goaldata;

	$scope.reminders = SelectService.reminders;

	$scope.addTask = function () {
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
var TaskEditModalInstanceCtrl = function ($scope, $modalInstance, $http, AlertService, SelectService, goaldata, id) {
	$scope.alerts = [];
	$scope.formdata = [];
	$scope.goaldata = goaldata;
	$scope.taskid = id;
	AlertService.alerts = [];

	$scope.reminders = SelectService.reminders;

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

	$scope.editTask = function () {
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


/********************************************************************/
/***************************** DASHBOARD ****************************/
/********************************************************************/

goalApp.controller('DashboardCtrl', function ($scope, $rootScope, $cookieStore) {
	$scope.formdata = [];

	$rootScope.pageTitle = "Dashboard";
});

/********************************************************************/
/***************************** TIMEWATCH ****************************/
/********************************************************************/

/* Start timewatch */
goalApp.controller('TimewatchStartCtrl', function ($scope, $rootScope, $location, $http, $route, $modal, $window, AlertService, modalService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Timewatch";

	$scope.formdata = [];
	$scope.goals = [];
	$scope.tasks = [];

	$scope.timewatches = [];

	/* Get current timewatches */
	$http.get('timewatches.json').
	success(function(data, status, headers, config) {
		$scope.timewatches = data['timewatches'];
	}).
	error(function(data, status, headers, config) {
		$scope.timewatches = [];
	});

	$http.get('goals.json').
	success(function(data, status, headers, config) {
		$scope.goals = data['goals'];
	}).
	error(function(data, status, headers, config) {
		$scope.goals = [];
	});

	$scope.$watch('formdata.Goal', function(newVal) {
		if (newVal) {
			$http.get('goals/' + newVal + '.json').
			success(function(data, status, headers, config) {
				$scope.tasks = data['goal']['Task'];
			}).
			error(function(data, status, headers, config) {
				$scope.tasks = [];
			});
		}
	});

	/* Start timer */
	$scope.startTimer = function(id) {
		$scope.alerts = [];
		AlertService.alerts = [];

		var data = {
			'Timewatch' : {
				task_id: id,
				start_time: '2014-08-01 00:00:00',
				end_time: '2014-08-01 00:00:00',
			}
		};

		$http.post("timewatches/start.json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/timewatch/stop/' + data['message']['id']);
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}

	/* Delete timewatch action */
	$scope.deleteTimewatch = function(id) {
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
			bodyText: 'Are you sure you want to delete the timewatch ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			AlertService.alerts = [];
			$http.delete('timewatches/delete/' + id + '.json').
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

/* Stop timewatch */
goalApp.controller('TimewatchStopCtrl', function ($scope, $rootScope, $cookieStore, $http, $route, $routeParams, $location, AlertService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.timewatch = [];

	$rootScope.pageTitle = "Timewatch";

	$http.get('timewatches/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Goal = data['timewatch']['Goal']['title'];
		$scope.formdata.Task = data['timewatch']['Task']['title'];
		$scope.formdata.Starttime = data['timewatch']['Timewatch']['start_time'];
		$scope.formdata.IsStopped = !data['timewatch']['Timewatch']['is_active'];
		if ($scope.formdata.IsStopped) {
			$scope.formdata.Endtime = data['timewatch']['Timewatch']['end_time'];
		} else {
			$scope.formdata.Endtime = "";
		}
		if ($scope.formdata.IsStopped == true) {
			AlertService.alerts = [];
			AlertService.alerts.push({type: 'danger', msg: 'Oops ! The timewatch for the selected task is already stopped.'});
			$location.path('/timewatch');
		}
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts = [];
		AlertService.alerts.push({type: 'danger', msg: 'Timewatch not found'});
		$location.path('/timewatch');
	});

	/* Stop timer */
	$scope.stopTimer = function(id) {
		$scope.alerts = [];
		AlertService.alerts = [];

		var data = {
			'Timewatch' : {
				id: id,
				end_time: '2014-08-01 00:00:00',
			}
		};

		$http.post("timewatches/stop/" + id + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/timewatch');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/* Edit timewatch */
goalApp.controller('TimewatchEditCtrl', function ($scope, $rootScope, $cookieStore, $http, $route, $routeParams, $location, AlertService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.timewatch = [];

	$rootScope.pageTitle = "Edit Timewatch";

	$http.get('timewatches/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Goal = data['timewatch']['Goal']['title'];
		$scope.formdata.Task = data['timewatch']['Task']['title'];
		$scope.formdata.Starttime = data['timewatch']['Timewatch']['start_time'];
		$scope.formdata.IsStopped = !data['timewatch']['Timewatch']['is_active'];
		if ($scope.formdata.IsStopped) {
			$scope.formdata.Endtime = data['timewatch']['Timewatch']['end_time'];
		} else {
			$scope.formdata.Endtime = "";
		}
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts = [];
		AlertService.alerts.push({type: 'danger', msg: 'Timewatch not found'});
		$location.path('/timewatch');
	});

	/* Stop timer */
	$scope.editTimewatch = function() {
		$scope.alerts = [];
		AlertService.alerts = [];

		var data = {
			'Timewatch' : {
				start_time: $scope.formdata.Starttime,
				end_time: $scope.formdata.Endtime,
				is_active: !$scope.formdata.IsStopped,
			}
		};

		$http.post("timewatches/edit/" + $routeParams['id'] + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/timewatch');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/********************************************************************/
/******************************* NOTES ******************************/
/********************************************************************/

/* Show notes */
goalApp.controller('NotesIndexCtrl', function ($scope, $rootScope, $http, $location, $modal, $window, $route, AlertService, modalService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Notes";

	$http.get('notes.json').
	success(function(data, status, headers, config) {
		$scope.notes = data['notes'];
	}).
	error(function(data, status, headers, config) {
		$scope.notes = [];
	});

	/* Delete note action */
	$scope.deleteNote = function(id) {
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
			bodyText: 'Are you sure you want to delete the note ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			AlertService.alerts = [];
			$http.delete('notes/delete/' + id + '.json').
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

/* View note */
goalApp.controller('NoteViewCtrl', function ($scope, $rootScope, $http, $modal, $routeParams, $location, $route, AlertService, modalService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.note = [];

	$rootScope.pageTitle = "View Note";

	$http.get('notes/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.note = data['note'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Note not found'});
		$location.path('/notes');
	});
});

/* Add note */
goalApp.controller('NoteAddCtrl', function ($scope, $rootScope, $http, $location, AlertService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = [];

	$rootScope.pageTitle = "Add Note";

	$scope.addNote = function() {
		$scope.alerts = [];

		var data = {
			'Note' : {
				title: $scope.formdata.Title,
				note: $scope.formdata.Note,
				pin_dashboard: $scope.formdata.PinDashboard,
				pin_top: $scope.formdata.PinTop,
			}
		};

		$http.post("notes/add.json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/notes');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/* Edit note */
goalApp.controller('NoteEditCtrl', function ($scope, $rootScope, $http, $routeParams, $location, AlertService, SelectService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Edit Note";

	$scope.formdata = [];

	$http.get('notes/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Title = data['note']['Note']['title'];
		$scope.formdata.Note = data['note']['Note']['note'];
		$scope.formdata.PinDashboard = data['note']['Note']['pin_dashboard'];
		$scope.formdata.PinTop = data['note']['Note']['pin_top'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Note not found'});
		$location.path('/notes');
	});

	$scope.editNote = function() {
		$scope.alerts = [];

		var data = {
			'Note' : {
				title: $scope.formdata.Title,
				note: $scope.formdata.Note,
				pin_dashboard: $scope.formdata.PinDashboard,
				pin_top: $scope.formdata.PinTop,
			}
		};

		$http.post("notes/edit/" +  + $routeParams['id'] + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/notes');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/********************************************************************/
/***************************** JOURNAL ******************************/
/********************************************************************/

/* Show journal */
goalApp.controller('JournalsIndexCtrl', function ($scope, $rootScope, $http, $location, $modal, $window, $route, AlertService, modalService) {
	$scope.alerts = AlertService.alerts;
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Journal";

	$http.get('journals.json').
	success(function(data, status, headers, config) {
		$scope.journals = data['journals'];
	}).
	error(function(data, status, headers, config) {
		$scope.journals = [];
	});

	/* Delete journal entry action */
	$scope.deleteJournal = function(id) {
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
			bodyText: 'Are you sure you want to delete the journal entry ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			AlertService.alerts = [];
			$http.delete('journals/delete/' + id + '.json').
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

/* View journal entry */
goalApp.controller('JournalViewCtrl', function ($scope, $rootScope, $http, $modal, $routeParams, $location, $route, AlertService, modalService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.note = [];

	$rootScope.pageTitle = "View Journal Entry";

	$http.get('journals/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.journal = data['journal'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Journal entry not found'});
		$location.path('/journals');
	});
});

/* Add journal entry */
goalApp.controller('JournalAddCtrl', function ($scope, $rootScope, $http, $location, AlertService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$scope.formdata = [];

	$rootScope.pageTitle = "Add Journal Entry";

	$scope.addJournal = function() {
		$scope.alerts = [];

		var data = {
			'Journal' : {
				title: $scope.formdata.Title,
				entry: $scope.formdata.Entry,
				entrydate: $scope.formdata.Entrydate,
			}
		};

		$http.post("journals/add.json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/journals');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/* Edit journal entry */
goalApp.controller('JournalEditCtrl', function ($scope, $rootScope, $http, $routeParams, $location, AlertService, SelectService) {
	AlertService.alerts = [];
	$scope.closeAlert = function(index) {
		$scope.alerts.splice(index, 1);
	};

	$rootScope.pageTitle = "Edit Journal Entry";

	$scope.formdata = [];

	$http.get('journals/' + $routeParams['id'] + '.json').
	success(function(data, status, headers, config) {
		$scope.formdata.Title = data['journal']['Journal']['title'];
		$scope.formdata.Entry = data['journal']['Journal']['entry'];
		$scope.formdata.Entrydate = data['journal']['Journal']['entrydate'];
	}).
	error(function(data, status, headers, config) {
		AlertService.alerts.push({type: 'danger', msg: 'Journal Entry not found'});
		$location.path('/journals');
	});

	$scope.editJournal = function() {
		$scope.alerts = [];

		var data = {
			'Journal' : {
				title: $scope.formdata.Title,
				entry: $scope.formdata.Entry,
				entrydate: $scope.formdata.Entrydate,
			}
		};

		$http.post("journals/edit/" +  + $routeParams['id'] + ".json", data).
		success(function (data, status, headers) {
			if (data['message']['type'] == 'error') {
				$scope.alerts.push({type: 'danger', msg: data['message']['text']});
			}
			if (data['message']['type'] == 'success') {
				AlertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/journals');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});
