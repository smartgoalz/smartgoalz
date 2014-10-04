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
	when('/goals/show/:id', {
		templateUrl: 'frontend/goals/show.html',
	}).
	when('/timewatches', {
		templateUrl: 'frontend/timewatches/start.html',
	}).
	when('/timewatches/stop/:id', {
		templateUrl: 'frontend/timewatches/stop.html',
	}).
	when('/timewatches/add', {
		templateUrl: 'frontend/timewatches/add.html',
	}).
	when('/timewatches/edit/:id', {
		templateUrl: 'frontend/timewatches/edit.html',
	}).
	when('/timetables', {
		templateUrl: 'frontend/timetables/index.html',
	}).
	when('/timetables/activities/add', {
		templateUrl: 'frontend/timetables/activities/add.html',
	}).
	when('/timetables/activities/edit/:id', {
		templateUrl: 'frontend/timetables/activities/edit.html',
	}).
	when('/timetables/schedule/:id', {
		templateUrl: 'frontend/timetables/schedule.html',
	}).
	when('/timetables/edit/:id', {
		templateUrl: 'frontend/timetables/edit.html',
	}).
	when('/timetables/manage', {
		templateUrl: 'frontend/timetables/manage.html',
	}).
	when('/monitors', {
		templateUrl: 'frontend/monitors/index.html',
	}).
	when('/monitors/show/:id', {
		templateUrl: 'frontend/monitors/show.html',
	}).
	when('/monitors/add', {
		templateUrl: 'frontend/monitors/add.html',
	}).
	when('/monitors/edit/:id', {
		templateUrl: 'frontend/monitors/edit.html',
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
	when('/users/profile', {
		templateUrl: 'frontend/users/profile.html',
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

	var monitortypes = function() {
		return {'INT': 'Integer', 'FLOAT': 'Decimal Number',
			'CHAR': 'Character', 'BOOL': 'True / False'};
	}

	var monitorfrequencies = function() {
		return {'DAILY': 'Daily', 'WEEKLY': 'Weekly', 'MONTHLY': 'Monthly',
			'QUATERLY': 'Quaterly', 'YEARLY': 'Yearly'};
	}

	return {
		priorities : priorities(),
		difficulties : difficulties(),
		categories: categories(),
		monitortypes: monitortypes(),
		monitorfrequencies: monitorfrequencies(),
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

goalApp.filter('showTime', function() {
	return function(input) {
		if (!input) {
			return '';
		}
		inputArr = input.split(':');
		jsdate = new Date('2000', '01', '01', inputArr[0], inputArr[1], 0);
		return jsdate.toString('hh:mm tt');
	};
});


/******************* CONTROLLERS *******************/

goalApp.controller('BodyCtrl', function ($scope, $rootScope, $cookieStore,
	$window, alertService)
{
	/* Check if user is logged in */
	if ($cookieStore.get('logged_in') == false) {
		$window.location.href = 'user.html';
	}

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

	$scope.clearAlerts = function() {
		alertService.clear();
	}
});

goalApp.controller('ContentCtrl', function ($scope, $rootScope, $cookieStore, alertService) {
	$scope.alerts = alertService.alerts;
	$scope.formdata = [];

	$rootScope.pageTitle = "";

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
		var outputStr = input.replace(/-/g, ' ');
		outputStr = outputStr.replace(/:/g, ' ');
		outputArr = outputStr.split(' ');
		/* Since JS month starts from 0 - 11 */
		outputArr[1] = outputArr[1] - 1;
		return new Date(outputArr[0], outputArr[1], outputArr[2],
			outputArr[3], outputArr[4], outputArr[5]);
	}

	$scope.timeToJS = function(input) {
		if (!input) {
			return new Date();
		}
		var outputStr = input.replace(/:/g, ' ');
		outputArr = outputStr.split(' ');
		return new Date('2000', '0', '1',
			outputArr[0], outputArr[1], outputArr[2]);
	}


	$scope.dateToSQL = function(input) {
		if (!input) {
			return '';
		}

		return input.toString("yyyy-MM-dd HH:mm:00");
	}

	$scope.toSQLTime = function(input) {
		if (!input) {
			return '';
		}

		return input.toString("h:mm:00");
	}

	$scope.dateToSQLNoTime = function(input) {
		if (!input) {
			return '';
		}

		return input.toString("yyyy-MM-dd 00:00:00");
	}

	$scope.datetimeToScreen = function(input) {
		if (!input) {
			return '';
		}
		inputToDate = $scope.dateToJS(input);
		return inputToDate.toString("dd-MMMM-yyyy h:mm:ss tt");
	}

	$scope.mergeDateTime = function(inputDate, inputTime) {
		var outputDatetime = new Date(
			inputDate.getFullYear(),
			inputDate.getMonth(),
			inputDate.getDate(),
			inputTime.getHours(),
			inputTime.getMinutes(),
			inputTime.getSeconds()
		);
		return outputDatetime;
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

goalApp.controller('DashboardCtrl', function ($scope, $rootScope,
	$cookieStore, alertService)
{
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
			bodyText: 'Are you sure you want to delete the goal ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

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

goalApp.controller('GoalShowCtrl', function ($scope, $rootScope, $http,
	$modal, $routeParams, $location, $route, alertService, modalService,
	SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Show Goal";

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


/********************************************************************/
/****************************** TASKS *******************************/
/********************************************************************/

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
	$scope.formdata.PrevID = 0;
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
				prev_id: $scope.formdata.PrevID,
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
		$scope.formdata.PrevID = prev_id;
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
				prev_id: $scope.formdata.PrevID,
				start_date: $scope.dateToSQLNoTime($scope.formdata.Startdate),
				due_date: $scope.dateToSQLNoTime($scope.formdata.Duedate),
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
goalApp.controller('TimewatchStartCtrl', function ($scope, $rootScope, $location,
	$http, $route, $modal, $window, alertService, modalService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Timewatch";

	$scope.formdata = [];
	$scope.goals = [];
	$scope.tasks = [];

	$scope.timewatches = [];
	$scope.active_timewatches = [];

	/* Get active timewatches */
	$http.get('api/timewatches/active').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.active_timewatches = data.data.active_timewatches;
		} else {
			alertService.add(data.message, 'danger');
			$scope.active_timewatches = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.active_timewatches = [];
	});

	/* Get all timewatches */
	$http.get('api/timewatches/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.timewatches = data.data.timewatches;
		} else {
			alertService.add(data.message, 'danger');
			$scope.timewatches = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.timewatches = [];
	});

	/* Get list of goals and tasks */
	$http.get('api/goals/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.goals = data.data.goals;
		} else {
			alertService.add(data.message, 'danger');
			$scope.goals = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.goals = [];
	});
	$scope.$watch('formdata.Goal', function(newVal) {
		if (newVal) {
			$http.get('api/tasks/index/' + newVal).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					$scope.tasks = data.data.tasks;
				} else {
					$scope.tasks = [];
				}
			}).
			error(function(data, status, headers, config) {
				$scope.tasks = [];
			});
		}
	});

	/* Start timer */
	$scope.startTimer = function(goal_id, task_id) {
		alertService.clear();

		var data = {
			'timewatch' : {
				goal_id: goal_id,
				task_id: task_id,
				start_time: $scope.dateToSQL(new Date()),
			}
		};

		$http.post("api/timewatches/start", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timewatches/stop/' + data.data.id);
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}

	/* Delete timewatch action */
	$scope.deleteTimewatch = function(id) {
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
			bodyText: 'Are you sure you want to delete the timewatch ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

			$http.delete('api/timewatches/destroy/' + id).
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

/* Stop timewatch */
goalApp.controller('TimewatchStopCtrl', function ($scope, $rootScope,
	$cookieStore, $http, $route, $routeParams, $location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Timewatch";

	$http.get('api/timewatches/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.GoalTitle = data.data.goal.title;
			$scope.formdata.TaskTitle = data.data.task.title;
			$scope.formdata.Starttime = $scope.datetimeToScreen(data.data.timewatch.start_time);
			if (data.data.timewatch.is_active == 0) {
				alertService.clear();
				alertService.add('Oops ! The timewatch for the selected task is already stopped.', 'danger');
				$location.path('/timewatches');
			}
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/timewatches');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/timewatches');
	});

	/* Stop timer */
	$scope.stopTimer = function() {
		alertService.clear();

		var data = {
			'timewatch' : {
				stop_time: $scope.dateToSQL(new Date()),
			}
		};

		$http.put("api/timewatches/stop/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timewatches');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Add timewatch */
goalApp.controller('TimewatchAddCtrl', function ($scope, $rootScope,
	$cookieStore, $http, $route, $routeParams, $location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Timewatch";

	$scope.formdata = [];
	$scope.goals = [];
	$scope.tasks = [];

	/* Intial data */
	$scope.formdata.Startdate = new Date();
	$scope.formdata.Stopdate = new Date();
	$scope.formdata.Starttime = new Date();
	$scope.formdata.Stoptime = new Date();
	$scope.formdata.IsStopped = false;

	/* Get list of goals and tasks */
	$http.get('api/goals/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.goals = data.data.goals;
		} else {
			alertService.add(data.message, 'danger');
			$scope.goals = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.goals = [];
	});
	$scope.$watch('formdata.Goal', function(newVal) {
		if (newVal) {
			$http.get('api/tasks/index/' + newVal).
			success(function(data, status, headers, config) {
				if (data.status == 'success') {
					$scope.tasks = data.data.tasks;
				} else {
					$scope.tasks = [];
				}
			}).
			error(function(data, status, headers, config) {
				$scope.tasks = [];
			});
		}
	});

	/* Add timewatch */
	$scope.addTimewatch = function(goal_id, task_id) {
		alertService.clear();

		var data = {
			'timewatch' : {
				goal_id: goal_id,
				task_id: task_id,
				start_time: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Startdate, $scope.formdata.Starttime)
				),
				stop_time: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Stopdate, $scope.formdata.Stoptime)
				),
				is_active: !$scope.formdata.IsStopped,
			}
		};

		$http.post("api/timewatches/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timewatches');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit timewatch */
goalApp.controller('TimewatchEditCtrl', function ($scope, $rootScope,
	$cookieStore, $http, $route, $routeParams, $location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Timewatch";

	$scope.formdata = [];
	$scope.goals = [];
	$scope.tasks = [];

	$http.get('api/timewatches/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.GoalTitle = data.data.goal.title;
			$scope.formdata.TaskTitle = data.data.task.title;
			$scope.formdata.Startdate = $scope.dateToJS(data.data.timewatch.start_time);
			$scope.formdata.Starttime = $scope.dateToJS(data.data.timewatch.start_time);
			if (data.data.timewatch.is_active == 0) {
				$scope.formdata.IsStopped = true;
				$scope.formdata.Stopdate = $scope.dateToJS(data.data.timewatch.stop_time);
				$scope.formdata.Stoptime = $scope.dateToJS(data.data.timewatch.stop_time);
			} else {
				$scope.formdata.IsStopped = false;
				$scope.formdata.Stopdate = new Date();
				$scope.formdata.Stoptime = new Date();
			}
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/timewatches');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/timewatches');
	});

	/* Stop timer */
	$scope.editTimewatch = function() {
		alertService.clear();

		var data = {
			'timewatch' : {
				start_time: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Startdate, $scope.formdata.Starttime)
				),
				stop_time: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Stopdate, $scope.formdata.Stoptime)
				),
				is_active: !$scope.formdata.IsStopped,
			}
		};

		$http.put("api/timewatches/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timewatches');
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
			bodyText: 'Are you sure you want to delete the note ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

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

/* Index journal */
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
			bodyText: 'Are you sure you want to delete the journal entry ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

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

/********************************************************************/
/**************************** MONITORS ******************************/
/********************************************************************/

/* Index monitors */
goalApp.controller('MonitorsIndexCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Monitors";

	$scope.monitortypes = SelectService.monitortypes;
	$scope.monitorfrequencies = SelectService.monitorfrequencies;
	$scope.monitors = [];

	/* Fetch all monitors */
	$http.get('api/monitors/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.monitors = data.data.monitors;
		} else {
			$scope.monitors = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.monitors = [];
	});

	/* Delete monitor action */
	$scope.deleteMonitor = function(id) {
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
			bodyText: 'Are you sure you want to delete the monitor ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

			$http.delete('api/monitors/destroy/' + id).
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

/* Show monitor */
goalApp.controller('MonitorShowCtrl', function ($scope, $rootScope, $http,
	$modal, $routeParams, $location, $route, alertService, modalService,
	SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Show Monitor";

	$scope.monitortypes = SelectService.monitortypes;
	$scope.monitorfrequencies = SelectService.monitorfrequencies;

	$scope.monitor = [];

	$http.get('api/monitors/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.monitor = data.data.monitor;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/monitors');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/monitors');
	});

	$http.get('api/monitorvalues/index/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.monitorvalues = data.data.monitorvalues;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/monitors');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/monitors');
	});

	/* Add value action */
	$scope.addMonitorvalue = function() {
		alertService.clear();

		/* Open modal window */
		var MonitorvalueAddModalInstance = $modal.open({
			templateUrl: 'frontend/monitors/values/add.html',
			controller: MonitorvalueAddModalInstanceCtrl,
			scope: $scope,
			resolve: {
				monitor: function () {
					return $scope.monitor;
				}
			}
		});

		MonitorvalueAddModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Edit value action */
	$scope.editMonitorvalue = function(id) {
		alertService.clear();

		/* Open modal window */
		var MonitorvalueEditModalInstance = $modal.open({
			templateUrl: 'frontend/monitors/values/edit.html',
			controller: MonitorvalueEditModalInstanceCtrl,
			scope: $scope,
			resolve: {
				monitor: function () {
					return $scope.monitor;
				},
				id: function () {
					return id;
				},
			}
		});

		MonitorvalueEditModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Delete value action */
	$scope.deleteMonitorvalue = function(id) {
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
			bodyText: 'Are you sure you want to delete the value ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

			$http.delete('api/monitorvalues/destroy/' + id).
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

/* Add monitor */
goalApp.controller('MonitorAddCtrl', function ($scope, $rootScope, $http,
	$location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Monitor";

	$scope.monitortypes = SelectService.monitortypes;
	$scope.monitorfrequencies = SelectService.monitorfrequencies;

	$scope.formdata = [];

	$scope.addMonitor = function() {
		alertService.clear();

		var data = {
			'monitor' : {
				title: $scope.formdata.Title,
				type: $scope.formdata.Type,
				minimum: $scope.formdata.Minimum,
				maximum: $scope.formdata.Minimum,
				minimum_threshold: $scope.formdata.MinimumThreshold,
				maximum_threshold: $scope.formdata.MaximumThreshold,
				units: $scope.formdata.Units,
				frequency: $scope.formdata.Frequency,
				description: $scope.formdata.Description,
			}
		};
		if ($scope.formdata.LowerBetter == true)
			data.monitor.is_lower_better = 1;
		else
			data.monitor.is_lower_better = 0;

		$http.post("api/monitors/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/monitors');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit monitor */
goalApp.controller('MonitorEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Monitor";

	$scope.monitortypes = SelectService.monitortypes;
	$scope.monitorfrequencies = SelectService.monitorfrequencies;

	$scope.formdata = [];

	$http.get('api/monitors/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Title = data.data.monitor.title;
			$scope.formdata.Type = data.data.monitor.type;
			$scope.formdata.Minimum = data.data.monitor.minimum;
			$scope.formdata.Maximum = data.data.monitor.maximum;
			$scope.formdata.MinimumThreshold = data.data.monitor.minimum_threshold;
			$scope.formdata.MaximumThreshold = data.data.monitor.maximum_threshold;
			if (data.data.monitor.is_lower_better == 0)
				$scope.formdata.LowerBetter = false;
			else
				$scope.formdata.LowerBetter = true;
			$scope.formdata.Units = data.data.monitor.units;
			$scope.formdata.Frequency = data.data.monitor.frequency;
			$scope.formdata.Description = data.data.monitor.description;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/monitors');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/monitors');
	});

	$scope.editMonitor = function() {
		alertService.clear();

		var data = {
			'monitor' : {
				title: $scope.formdata.Title,
				type: $scope.formdata.Type,
				minimum: $scope.formdata.Minimum,
				maximum: $scope.formdata.Maximum,
				minimum_threshold: $scope.formdata.MinimumThreshold,
				maximum_threshold: $scope.formdata.MaximumThreshold,
				units: $scope.formdata.Units,
				frequency: $scope.formdata.Frequency,
				description: $scope.formdata.Description,
			}
		};
		if ($scope.formdata.LowerBetter == true)
			data.monitor.is_lower_better = 1;
		else
			data.monitor.is_lower_better = 0;

		$http.put("api/monitors/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/monitors');
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
/********************** MONITORS VALUES *****************************/
/********************************************************************/

/* Monitorvalue add modal */
var MonitorvalueAddModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, monitor)
{
	$scope.alerts = alertService.alerts;
	$scope.monitor = monitor;
	$scope.modalAlerts = [];

	/* Initial values of form items */
	$scope.formdata = [];
	$scope.formdata.Value = '';
	$scope.formdata.Date = new Date();
	$scope.formdata.Valuetime = new Date();

	$scope.addMonitorvalue = function () {
		alertService.clear();

		var data = {
			'monitorvalue' : {
				monitor_id: monitor.id,
				value: $scope.formdata.Value,
				date: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Date, $scope.formdata.Valuetime)
				),
			}
		};

		$http.post("api/monitorvalues/create", data).
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

/* Monitorvalue edit modal */
var MonitorvalueEditModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, monitor, id)
{
	$scope.alerts = alertService.alerts;
	$scope.monitor = monitor;
	$scope.modalAlerts = [];

	$http.get('api/monitorvalues/show/' + id).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Value = data.data.monitorvalue.value;
			$scope.formdata.Date = $scope.dateToJS(data.data.monitorvalue.date);
			$scope.formdata.Valuetime = $scope.dateToJS(data.data.monitorvalue.date);
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/monitorvalues');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/monitorvalues');
	});

	$scope.editMonitorvalue = function () {
		alertService.clear();

		var data = {
			'monitorvalue' : {
				monitor_id: monitor.id,
				value: $scope.formdata.Value,
				date: $scope.dateToSQL(
					$scope.mergeDateTime($scope.formdata.Date, $scope.formdata.Valuetime)
				),
			}
		};

		$http.put("api/monitorvalues/update/" + id, data).
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
/************************** TIME TABLES *****************************/
/********************************************************************/

/* Index timetables */
goalApp.controller('TimetablesIndexCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Timetable";

	$scope.activities = [];

	var curTime = new Date();
	$scope.curTime = curTime.toString("dddd dd-MMMM-yyyy h:mm:ss tt");
	var curTimestamp = Math.floor(curTime.getTime() / 1000);

	/* Fetch today schedule */
	$http.get('api/activities/today/' + curTimestamp).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.activities = data.data.activities;
		} else {
			$scope.activities = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.activities = [];
	});
});

/* Manage timetables */
goalApp.controller('TimetableManageCtrl', function ($scope, $rootScope, $http,
	$location, $modal, $window, $route, alertService, modalService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Manage Timetable";

	$scope.activities = [];
	$scope.allschedules = [];

	/* Initial data */
	$scope.allschedules = [];
	$scope.allschedules.sunday = [];
	$scope.allschedules.monday = [];
	$scope.allschedules.tuesday = [];
	$scope.allschedules.wednesday = [];
	$scope.allschedules.thursday = [];
	$scope.allschedules.friday = [];
	$scope.allschedules.saturday = [];

	/* Fetch all activities */
	$http.get('api/activities/index').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.activities = data.data.activities;
		} else {
			alertService.add(data.message, 'danger');
			$scope.activities = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.activities = [];
	});

	/* Fetch all timetable entries */
	$http.get('api/activities/timetable').
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			allschedules = data.data.allschedules;
			console.log(allschedules);
			for (var c = 0; c < allschedules.length; c++) {
				if (allschedules[c].days.indexOf("SUNDAY") != -1) {
					$scope.allschedules.sunday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("MONDAY") != -1) {
					$scope.allschedules.monday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("TUESDAY") != -1) {
					$scope.allschedules.tuesday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("WEDNESDAY") != -1) {
					$scope.allschedules.wednesday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("THURSDAY") != -1) {
					$scope.allschedules.thursday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("FRIDAY") != -1) {
					$scope.allschedules.friday.push(allschedules[c]);
				}
				if (allschedules[c].days.indexOf("SATURDAY") != -1) {
					$scope.allschedules.saturday.push(allschedules[c]);
				}
			}
		} else {
			/* TODO : ERROR */
			$scope.allschedules = [];
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$scope.allschedules = [];
	});

	/* Delete activity */
	$scope.deleteActivity = function(id) {
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
			bodyText: 'Are you sure you want to delete the activity for all days ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

			$http.delete('api/activities/destroy/' + id).
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


/* Timetable schedule */
goalApp.controller('TimetableScheduleCtrl', function ($scope, $rootScope, $http,
	$modal, $routeParams, $location, $route, alertService, modalService,
	alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Schedule";

	$scope.weekdays = SelectService.weekdays;

	$scope.activity = [];
	$scope.timetables = [];

	$http.get('api/timetables/schedule/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.activity = data.data.activity;
			$scope.timetables = data.data.timetables;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/timetables/manage');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/timetables/manage');
	});

	/* Add schedule action */
	$scope.addSchedule = function() {
		alertService.clear();

		/* Open modal window */
		var ScheduleAddModalInstance = $modal.open({
			templateUrl: 'frontend/timetables/add.html',
			controller: ScheduleAddModalInstanceCtrl,
			scope: $scope,
			resolve: {
				activity: function () {
					return $scope.activity;
				}
			}
		});

		ScheduleAddModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Edit schedule action */
	$scope.editSchedule = function(id) {
		alertService.clear();

		/* Open modal window */
		var ScheduleEditModalInstance = $modal.open({
			templateUrl: 'frontend/timetables/edit.html',
			controller: ScheduleEditModalInstanceCtrl,
			scope: $scope,
			resolve: {
				activity: function () {
					return $scope.activity;
				},
				timetables: function () {
					return $scope.timetables;
				},
				id : function () {
					return id;
				}
			}
		});

		ScheduleEditModalInstance.result.then(function (result) {
			$route.reload();
		}, function () {
		});
	};

	/* Delete schedule action */
	$scope.deleteSchedule = function(id) {
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
			bodyText: 'Are you sure you want to delete the schedule ?'
		};

		modalService.showModal(modalDefaults, modalOptions).then(function (result) {
			alertService.clear();

			$http.delete('api/timetables/destroy/' + id).
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

/* Schedule add modal */
var ScheduleAddModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, activity)
{
	$scope.alerts = alertService.alerts;

	$scope.formdata = [];
	$scope.activity = activity;
	$scope.modalAlerts = [];

	/* Initial values of form items */
	$scope.formdata.FromTime = new Date();
	$scope.formdata.ToTime = new Date();
	$scope.formdata.Days = [];
	$scope.formdata.Days.all = false;
	$scope.formdata.Days.sunday = false;
	$scope.formdata.Days.monday = false;
	$scope.formdata.Days.tuesday = false;
	$scope.formdata.Days.wednesday = false;
	$scope.formdata.Days.thursday = false;
	$scope.formdata.Days.friday = false;
	$scope.formdata.Days.saturday = false;

	$scope.addSchedule = function () {
		alertService.clear();

		var data = {
			'timetable' : {
				activity_id: activity.id,
				from_time: $scope.toSQLTime($scope.formdata.FromTime),
				to_time: $scope.toSQLTime($scope.formdata.ToTime),
			}
		};
		data.timetable.days = '';
		if ($scope.formdata.Days.all == true) {
			data.timetable.days = 'SUNDAY,MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY';
		} else {
			daysArr = [];
			if ($scope.formdata.Days.sunday == true)
				daysArr.push('SUNDAY');
			if ($scope.formdata.Days.monday == true)
				daysArr.push('MONDAY');
			if ($scope.formdata.Days.tuesday == true)
				daysArr.push('TUESDAY');
			if ($scope.formdata.Days.wednesday == true)
				daysArr.push('WEDNESDAY');
			if ($scope.formdata.Days.thursday == true)
				daysArr.push('THURSDAY');
			if ($scope.formdata.Days.friday == true)
				daysArr.push('FRIDAY');
			if ($scope.formdata.Days.saturday == true)
				daysArr.push('SATURDAY');
			data.timetable.days = daysArr.join(',');
		}

		$http.post("api/timetables/create", data).
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

/* Schedule edit modal */
var ScheduleEditModalInstanceCtrl = function ($scope, $rootScope, $modalInstance,
	$http, alertService, activity, timetables, id)
{
	$scope.alerts = alertService.alerts;

	$scope.formdata = [];
	$scope.activity = activity;
	$scope.modalAlerts = [];

	/* Initial values of form items */
	$scope.formdata.FromTime = new Date();
	$scope.formdata.ToTime = new Date();
	$scope.formdata.Days = [];
	$scope.formdata.Days.all = false;
	$scope.formdata.Days.sunday = false;
	$scope.formdata.Days.monday = false;
	$scope.formdata.Days.tuesday = false;
	$scope.formdata.Days.wednesday = false;
	$scope.formdata.Days.thursday = false;
	$scope.formdata.Days.friday = false;
	$scope.formdata.Days.saturday = false;

	/* Locate schedule */
	var timetable;
	for (var c = 0, len = timetables.length; c < len; c++) {
		if (timetables[c].id == id) {
			timetable = timetables[c];
			break;
		}
	}

	if (timetable) {
		$scope.formdata.FromTime = $scope.timeToJS(timetable.from_time);
		$scope.formdata.ToTime = $scope.timeToJS(timetable.to_time);
		$scope.formdata.Days = [];
		if (timetable.days == 'SUNDAY,MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY') {
			$scope.formdata.Days.all = true;
		} else {
			if (timetable.days.indexOf("SUNDAY") != -1) {
				$scope.formdata.Days.sunday = true;
			}
			if (timetable.days.indexOf("MONDAY") != -1) {
				$scope.formdata.Days.monday = true;
			}
			if (timetable.days.indexOf("TUESDAY") != -1) {
				$scope.formdata.Days.tuesday = true;
			}
			if (timetable.days.indexOf("WEDNESDAY") != -1) {
				$scope.formdata.Days.wednesday = true;
			}
			if (timetable.days.indexOf("THURSDAY") != -1) {
				$scope.formdata.Days.thursday = true;
			}
			if (timetable.days.indexOf("FRIDAY") != -1) {
				$scope.formdata.Days.friday = true;
			}
			if (timetable.days.indexOf("SATURDAY") != -1) {
				$scope.formdata.Days.saturday = true;
			}
		}
	} else {
		$scope.formdata = [];
		alertService.add('Schedule not found.', 'danger');
		$modalInstance.dismiss();
	}

	$scope.editSchedule = function () {
		alertService.clear();

		var data = {
			'timetable' : {
				from_time: $scope.toSQLTime($scope.formdata.FromTime),
				to_time: $scope.toSQLTime($scope.formdata.ToTime),
			}
		};
		data.timetable.days = '';
		if ($scope.formdata.Days.all == true) {
			data.timetable.days = 'SUNDAY,MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY';
		} else {
			daysArr = [];
			if ($scope.formdata.Days.sunday == true)
				daysArr.push('SUNDAY');
			if ($scope.formdata.Days.monday == true)
				daysArr.push('MONDAY');
			if ($scope.formdata.Days.tuesday == true)
				daysArr.push('TUESDAY');
			if ($scope.formdata.Days.wednesday == true)
				daysArr.push('WEDNESDAY');
			if ($scope.formdata.Days.thursday == true)
				daysArr.push('THURSDAY');
			if ($scope.formdata.Days.friday == true)
				daysArr.push('FRIDAY');
			if ($scope.formdata.Days.saturday == true)
				daysArr.push('SATURDAY');
			data.timetable.days = daysArr.join(',');
		}

		$http.put("api/timetables/update/" + id, data).
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
/************************** ACTIVITIES ******************************/
/********************************************************************/

/* Add activity */
goalApp.controller('ActivityAddCtrl', function ($scope, $rootScope, $http,
	$location, alertService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Add Activity";

	$scope.formdata = [];

	$scope.addActivity = function() {
		alertService.clear();

		var data = {
			'activity' : {
				name: $scope.formdata.Name,
			}
		};

		$http.post("api/activities/create", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timetables/manage');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

/* Edit activity */
goalApp.controller('ActivityEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Activity";

	$scope.formdata = [];

	$http.get('api/activities/show/' + $routeParams['id']).
	success(function(data, status, headers, config) {
		if (data.status == 'success') {
			$scope.formdata.Name = data.data.activity.name;
		} else {
			alertService.add(data.message, 'danger');
			$location.path('/timetables/manage');
		}
	}).
	error(function(data, status, headers, config) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		$location.path('/timetables/manage');
	});

	$scope.editActivity = function() {
		alertService.clear();

		var data = {
			'activity' : {
				name: $scope.formdata.Name,
			}
		};

		$http.put("api/activities/update/" + $routeParams['id'], data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/timetables/manage');
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
/*************************** PROFILE ********************************/
/********************************************************************/

/* Edit activity */
goalApp.controller('ProfileEditCtrl', function ($scope, $rootScope, $http,
	$routeParams, $location, alertService, SelectService)
{
	$scope.alerts = alertService.alerts;
	$rootScope.pageTitle = "Edit Profile";

	$scope.formdata = [];
});
