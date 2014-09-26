var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap',
	'ui.router', 'ngCookies', 'ngSanitize', 'textAngular']);

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
	when('/notes/show/:id', {
		templateUrl: 'frontend/notes/show.html',
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
	when('/journals/show/:id', {
		templateUrl: 'frontend/journals/show.html',
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

goalApp.factory('alertService', function() {
	return {
		alerts : [],
		add : function(message, alertType) {
			if (typeof message === 'object') {
				for (var key in message) {
					var eachmsg = message[key];
					for (var index in eachmsg) {
						this.alerts.push({type: alertType, msg: eachmsg[index]});
					}
				}
			} else {
				this.alerts.push({type: alertType, msg: message});
			}
		},
		clear : function() {
			for (var c = this.alerts.length - 1; c >= 0; c--) {
				this.alerts.splice(c, 1);
			}
		}
	};
});

goalApp.factory('SelectService', function($http, $q, $cookieStore) {

	var categories = function() {
		return $cookieStore.get('categories');
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
		categories: categories(),
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

goalApp.filter('showDate', function() {
	return function(input) {
		if (!input) {
			return '';
		}
		jsdate = new Date(input.replace(/(.+) (.+)/, "$1T$2Z"));
		return jsdate.toString('dd-MMMM-yyyy');
	};
});

/******************* CONTROLLERS *******************/

goalApp.controller('BodyCtrl', function ($scope, $rootScope, $cookieStore) {
	$scope.formdata = [];

	$rootScope.pageTitle = "";
	$rootScope.dateFormat = "dd-MMMM-yyyy";

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

goalApp.controller('ContentCtrl', function ($scope, $rootScope, $cookieStore, alertService) {
	$scope.alerts = alertService.alerts;
	$scope.formdata = [];

	$rootScope.pageTitle = "";

	$scope.alerts = [];

	$scope.clearAlerts = function() {
		alertService.alerts = [];
		$scope.alerts = [];
	}

	$scope.calendar = {
		opened: {},
		dateFormat: $rootScope.dateFormat,
		dateOptions: {},

		open: function($event, which) {
			$event.preventDefault();
			$event.stopPropagation();
			$scope.calendar.opened[which] = true;
		}
	};

	$scope.dateToJS = function(input) {
		if (!input) {
			return new Date();
		}
		// return jsdate.toString($rootScope.dateFormat);
		return new Date(input.replace(/(.+) (.+)/, "$1T$2Z"));
	}

	$scope.dateToSQL = function(input) {
		if (!input) {
			return '';
		}

		return input.toString("yyyy-MM-dd HH:mm:ss");
	}

	$scope.dateToSQLNoTime = function(input) {
		if (!input) {
			return '';
		}

		return input.toString("yyyy-MM-dd 00:00:00");
	}

	/* TODO : Fetch data from server */
	var categories = {
		1: 'Personal',
		2: 'Financial'
	};
	$cookieStore.put('categories', categories);
});

/********************************************************************/
/***************************** DASHBOARD ****************************/
/********************************************************************/

goalApp.controller('DashboardCtrl', function ($scope, $rootScope, $cookieStore) {
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Dashboard";
});

/********************************************************************/
/****************************** GOALS *******************************/
/********************************************************************/

/* Show goals */
goalApp.controller('GoalsIndexCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Goals";

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = SelectService.categories;

	$http.get('api/goals/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.goals = data.data.goals;
		} else {
			$scope.goals = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
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
			alertService.clear();
			/* Send DELETE request to delete the goal */
			$http.delete('api/goals/destroy/' + id).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					alertService.add(data.message, 'success');
				} else {
					alertService.add(data.message, 'danger');
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				alertService.add('Oh snap! Change a few things up and try submitting again.', 'danger');
				$route.reload();
			});
		});
	};
});

/* Add goal */
goalApp.controller('GoalAddCtrl', function ($scope, $rootScope, $http,
	$location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Goal";

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = SelectService.categories;

	$scope.formdata = [];

	$scope.addGoal = function() {
		alertService.clear();

		var data = {
			'goal' : {
				title: $scope.formdata.Title,
				start_date: $scope.dateToSQLNoTime($scope.formdata.Startdate),
				due_date: $scope.dateToSQLNoTime($scope.formdata.Duedate),
				category_id: $scope.formdata.Category,
				difficulty: $scope.formdata.Difficulty,
				priority: $scope.formdata.Priority,
				reason: $scope.formdata.Reason,
			}
		};

		$http.post("api/goals/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/goals');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit goal */
goalApp.controller('GoalEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Goal";

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = SelectService.categories;

	$scope.formdata = [];

	$http.get('api/goals/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Title = data.data.goal.title;
			$scope.formdata.Startdate = $scope.dateToJS(data.data.goal.start_date);
			$scope.formdata.Duedate = $scope.dateToJS(data.data.goal.due_date);
			$scope.formdata.Category = data.data.goal.category_id;
			$scope.formdata.Difficulty = data.data.goal.difficulty;
			$scope.formdata.Priority = data.data.goal.priority;
			$scope.formdata.Reason = data.data.goal.reason;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/goals');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/goals');
	});

	$scope.editGoal = function() {
		alertService.clear();

		var data = {
			'goal' : {
				title: $scope.formdata.Title,
				start_date: $scope.dateToSQLNoTime($scope.formdata.Startdate),
				due_date: $scope.dateToSQLNoTime($scope.formdata.Duedate),
				category_id: $scope.formdata.Category,
				difficulty: $scope.formdata.Difficulty,
				priority: $scope.formdata.Priority,
				reason: $scope.formdata.Reason,
			}
		};

		$http.put("api/goals/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/goals');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

goalApp.controller('GoalManageCtrl', function ($scope, $rootScope, $http,
	$modal, $routeParams, $location, $route, alertService, modalService,
	SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Manage Goal";

	$scope.priorities = SelectService.priorities;
	$scope.difficulties = SelectService.difficulties;
	$scope.categories = SelectService.categories;

	$scope.goal = [];
	$scope.tasks = [];

	$http.get('api/goals/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.goal = data.data.goal;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/goals');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/goals');
	});

	$http.get('api/tasks/index/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.tasks = data.data.tasks;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/goals');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/goals');
	});

	/* Add task action */
	$scope.addTask = function() {
		alertService.clear();

		/* Open modal window */
		var TaskAddModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/add.html',
			controller: TaskAddModalInstanceCtrl,
			scope: $scope,
			resolve: {
				goal: function () {
					return $scope.goal;
				},
				tasks: function () {
					return $scope.tasks;
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
		alertService.clear();

		/* Open modal window */
		var TaskEditModalInstance = $modal.open({
			templateUrl: 'frontend/tasks/edit.html',
			controller: TaskEditModalInstanceCtrl,
			scope: $scope,
			resolve: {
				goal: function () {
					return $scope.goal;
				},
				tasks: function () {
					return $scope.tasks;
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
		alertService.clear();

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
			alertService.clear();
			/* Send DELETE request to delete the task */
			$http.delete('api/tasks/destroy/' + id).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					alertService.add(data.message, 'success');
				} else {
					alertService.add(data.message, 'danger');
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				alertService.add('Oh snap! Change a few things up and try submitting again.', 'danger');
				$route.reload();
			});
		});
	};

	/* Mark task completed action */
	$scope.doneTask = function(id) {
		alertService.clear();

		$http.put('api/tasks/done/' + id).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
			} else {
				alertService.add(data.message, 'danger');
			}
			$route.reload();
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap! Change a few things up and try submitting again.', 'danger');
			$route.reload();
		});
	};
});

/* Task add modal */
var TaskAddModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, goal, tasks)
{
	$scope.alerts = alertService.alerts;
	$scope.goal = goal;
	/* Do a deep copy, else below unshift operation will add to original tasks */
	$scope.tasks = angular.copy(tasks);
	$scope.tasks.unshift({'id': 0, 'title': '(None)'});
	$scope.modalAlerts = [];

	/* Initial values of form items */
	$scope.formdata = [];
	$scope.formdata.Title = '';
	$scope.formdata.Prev = 0;
	$scope.formdata.Startdate = new Date();
	$scope.formdata.Duedate = new Date();
	$scope.formdata.Completed = 0;
	$scope.formdata.Completiondate = new Date();
	$scope.formdata.Notes = '';

	$scope.addTask = function () {
		alertService.clear();

		var data = {
			'task' : {
				goal_id: goal.id,
				title: $scope.formdata.Title,
				prev: $scope.formdata.Prev,
				start_date: $scope.dateToSQLNoTime($scope.formdata.Startdate),
				due_date: $scope.dateToSQLNoTime($scope.formdata.Duedate),
				is_completed: $scope.formdata.Completed,
				completion_date: $scope.dateToSQLNoTime($scope.formdata.Completiondate),
				notes: $scope.formdata.Notes,
			}
		};

		$http.post("api/tasks/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$modalInstance.close();
			} else {
				for (var key in data.message) {
					var eachmsg = data.message[key];
					for (var index in eachmsg) {
						$scope.modalAlerts.push({'msg': eachmsg[index]});
					}
				}
			}
		}).
		error(function (data, status, headers) {
			$scope.modalAlerts.push({'msg' : 'Oh snap! Change a few things up and try submitting again.'});
		});
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};
};

/* Task edit modal */
var TaskEditModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, goal, tasks, id)
{
	$scope.alerts = alertService.alerts;
	$scope.goal = goal;
	/* Do a deep copy, else below unshift operation will add to original tasks */
	$scope.tasks = angular.copy(tasks);
	$scope.tasks.unshift({'id': 0, 'title': '(None)'});
	$scope.id = id;
	$scope.formdata = [];
	$scope.modalAlerts = [];

	/* Locate task */
	var task;
	var prev_id = 0;
	for (var c = 0, len = tasks.length; c < len; c++) {
		if (tasks[c].id == id) {
			task = tasks[c];
			break;
		}
		prev_id = tasks[c].id;
	}

	if (task) {
		$scope.formdata.Title = task.title;
		$scope.formdata.Prev = prev_id;
		$scope.formdata.Startdate = $scope.dateToJS(task.start_date);
		$scope.formdata.Duedate = $scope.dateToJS(task.due_date);
		if (task.is_completed == 1) {
			$scope.formdata.Completed = true;
			$scope.formdata.Completiondate = $scope.dateToJS(task.completion_date);
		} else {
			$scope.formdata.Completed = false;
			$scope.formdata.Completiondate = new Date();
		}
		$scope.formdata.Notes = task.notes;
	} else {
		$scope.formdata = [];
		alertService.add('Task not found.', 'danger');
		$modalInstance.dismiss();
	}

	$scope.editTask = function () {
		alertService.clear();

		var data = {
			'task' : {
				title: $scope.formdata.Title,
				start_date: $scope.dateToSQLNoTime($scope.formdata.Startdate),
				due_date: $scope.dateToSQLNoTime($scope.formdata.Duedate),
				prev: $scope.formdata.Prev,
				is_completed: $scope.formdata.Completed,
				completion_date: $scope.dateToSQLNoTime($scope.formdata.Completiondate),
				notes: $scope.formdata.Notes,
			}
		};

		$http.put("api/tasks/update/" + id, data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$modalInstance.close();
			} else {
				for (var key in data.message) {
					var eachmsg = data.message[key];
					for (var index in eachmsg) {
						$scope.modalAlerts.push({'msg': eachmsg[index]});
					}
				}
			}
		}).
		error(function (data, status, headers) {
			$scope.modalAlerts.push({'msg' : 'Oh snap! Change a few things up and try submitting again.'});
		});
	};

	$scope.cancel = function () {
		$modalInstance.dismiss();
	};
};

/********************************************************************/
/***************************** TIMEWATCH ****************************/
/********************************************************************/

/* Start timewatch */
goalApp.controller('TimewatchStartCtrl', function ($scope, $rootScope, $location, $http, $route, $modal, $window, alertService, modalService) {
	$scope.alerts = alertService.alerts;
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
		alertService.alerts = [];

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
				alertService.alerts.push({type: 'success', msg: data['message']['text']});
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
			alertService.alerts = [];
			$http.delete('timewatches/delete/' + id + '.json').
			success(function(data, status, headers, config) {
				if (data['message']['type'] == 'error') {
					alertService.alerts.push({type: 'danger', msg: data['message']['text']});
				} else
				if (data['message']['type'] == 'success') {
					alertService.alerts.push({type: 'success', msg: data['message']['text']});
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				alertService.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
				$route.reload();
			});
		});
	};
});

/* Stop timewatch */
goalApp.controller('TimewatchStopCtrl', function ($scope, $rootScope, $cookieStore, $http, $route, $routeParams, $location, alertService) {
	$scope.alerts = alertService.alerts;
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
			alertService.alerts = [];
			alertService.alerts.push({type: 'danger', msg: 'Oops ! The timewatch for the selected task is already stopped.'});
			$location.path('/timewatch');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.alerts = [];
		alertService.alerts.push({type: 'danger', msg: 'Timewatch not found'});
		$location.path('/timewatch');
	});

	/* Stop timer */
	$scope.stopTimer = function(id) {
		$scope.alerts = [];
		alertService.alerts = [];

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
				alertService.alerts.push({type: 'success', msg: data['message']['text']});
				$location.path('/timewatch');
			}
		}).
		error(function (data, status, headers) {
			$scope.alerts.push({type: 'danger', msg: 'Oh snap! Change a few things up and try submitting again.'});
		});
	}
});

/* Edit timewatch */
goalApp.controller('TimewatchEditCtrl', function ($scope, $rootScope, $cookieStore, $http, $route, $routeParams, $location, alertService) {
	$scope.alerts = alertService.alerts;
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
		alertService.alerts = [];
		alertService.alerts.push({type: 'danger', msg: 'Timewatch not found'});
		$location.path('/timewatch');
	});

	/* Stop timer */
	$scope.editTimewatch = function() {
		$scope.alerts = [];
		alertService.alerts = [];

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
				alertService.alerts.push({type: 'success', msg: data['message']['text']});
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
goalApp.controller('NotesIndexCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Notes";
	$scope.notes = [];

	/* Fetch all notes */
	$http.get('api/notes/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.notes = data.data.notes;
		} else {
			$scope.notes = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
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
			alertService.clear();
			/* Send DELETE request to delete the note */
			$http.delete('api/notes/destroy/' + id).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					alertService.add(data.message, 'success');
				} else {
					alertService.add(data.message, 'danger');
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				alertService.add('Oh snap! Change a few things up and try submitting again.', 'danger');
				$route.reload();
			});
		});
	};
});

/* Show note */
goalApp.controller('NoteShowCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, $sce)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Show Note";
	$scope.journal = [];

	$http.get('api/notes/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.note = data.data.note;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/notes');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/journals');
	});

	$scope.skipFilter = function(value) {
		return $sce.trustAsHtml(value);
	};
});

/* Add note */
goalApp.controller('NoteAddCtrl', function ($scope, $rootScope, $http,
	$location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Note";
	$scope.formdata = [];

	$scope.addNote = function() {
		alertService.clear();

		var data = {
			'note' : {
				title: $scope.formdata.Title,
				note: $scope.formdata.Note,
				pin_dashboard: $scope.formdata.PinDashboard,
				pin_top: $scope.formdata.PinTop,
			}
		};
		if ($scope.formdata.PinDashboard == 1)
			data.note.pin_dashboard = true;
		else
			data.note.pin_dashboard = false;
		if ($scope.formdata.PinTop == 1)
			data.note.pin_top = true;
		else
			data.note.pin_top = false;

		$http.post("api/notes/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/notes');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit note */
goalApp.controller('NoteEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Note";
	$scope.formdata = [];

	$http.get('api/notes/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Title = data.data.note.title;
			$scope.formdata.Note = data.data.note.note;
			if (data.data.note.pin_dashboard == 1)
				$scope.formdata.PinDashboard = true;
			else
				$scope.formdata.PinDashboard = false;
			if (data.data.note.pin_top == 1)
				$scope.formdata.PinTop = true;
			else
				$scope.formdata.PinTop = false;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/notes');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/notes');
	});

	$scope.editNote = function() {
		alertService.clear();

		var data = {
			'note' : {
				title: $scope.formdata.Title,
				note: $scope.formdata.Note,
				pin_dashboard: $scope.formdata.PinDashboard,
				pin_top: $scope.formdata.PinTop,
			}
		};

		$http.put("api/notes/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/notes');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/********************************************************************/
/***************************** JOURNAL ******************************/
/********************************************************************/

/* Show journal */
goalApp.controller('JournalsIndexCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Journal";
	$scope.journals = [];

	/* Fetch all journal entries */
	$http.get('api/journals/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.journals = data.data.journals;
		} else {
			$scope.journals = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
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
			alertService.clear();
			/* Send DELETE request to delete the journal entry */
			$http.delete('api/journals/destroy/' + id).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					alertService.add(data.message, 'success');
				} else {
					alertService.add(data.message, 'danger');
				}
				$route.reload();
			}).
			error(function(data, status, headers, config) {
				alertService.add('Oh snap! Change a few things up and try submitting again.', 'danger');
				$route.reload();
			});
		});
	};
});

/* Show journal entry */
goalApp.controller('JournalShowCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, $sce)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Show Journal Entry";
	$scope.journal = [];

	$http.get('api/journals/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.journal = data.data.journal;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/journals');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/journals');
	});

	$scope.skipFilter = function(value) {
		return $sce.trustAsHtml(value);
	};
});

/* Add journal entry */
goalApp.controller('JournalAddCtrl', function ($scope, $rootScope, $http,
	$location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Journal Entry";
	$scope.formdata = [];
	$scope.formdata.Date = new Date();

	$scope.addJournal = function() {
		alertService.clear();

		var data = {
			'journal' : {
				title: $scope.formdata.Title,
				entry: $scope.formdata.Entry,
				date: $scope.dateToSQLNoTime($scope.formdata.Date),
			}
		};

		$http.post("api/journals/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/journals');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit journal entry */
goalApp.controller('JournalEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Journal Entry";
	$scope.formdata = [];

	$http.get('api/journals/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Title = data.data.journal.title;
			$scope.formdata.Entry = data.data.journal.entry;
			$scope.formdata.Date = $scope.dateToJS(data.data.journal.date);
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/journals');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/journals');
	});

	$scope.editJournal = function() {
		alertService.clear();

		var data = {
			'journal' : {
				title: $scope.formdata.Title,
				entry: $scope.formdata.Entry,
				date: $scope.dateToSQLNoTime($scope.formdata.Date),
			}
		};

		$http.put("api/journals/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/journals');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});
