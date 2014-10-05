var userApp = angular.module('userApp', ['ngResource', 'ngRoute', 'ui.bootstrap',
	'ui.router', 'ngCookies']);

/******************* ROUTES *******************/

userApp.config(['$routeProvider', function($routeProvider) {
	$routeProvider.
	when('/login', {
		templateUrl: 'frontend/users/login.html',
	}).
	when('/logout', {
		templateUrl: 'frontend/users/logout.html',
	}).
	when('/register', {
		templateUrl: 'frontend/users/register.html',
	}).
	when('/forgot', {
		templateUrl: 'frontend/users/forgot.html',
	}).
	otherwise({
		redirectTo: '/login'
	});
}]);

/******************* SERVICES *******************/

userApp.factory('alertService', function() {
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

/******************* CONTROLLERS *******************/

userApp.controller('BodyCtrl', function ($scope, $rootScope, $cookieStore)
{

});

userApp.controller('ContentCtrl', function ($scope, $rootScope, $cookieStore, alertService)
{

});

userApp.controller('UserLoginCtrl', function ($scope, $rootScope, $http,
	$cookieStore, $window, alertService)
{
	$scope.alerts = alertService.alerts;
	$scope.formdata = [];

	/* Initial data */
	$cookieStore.put('logged_in', false);

	$scope.login = function() {
		alertService.clear();

		/* Google for "AngularJS browser autofill workaround" */
		var data = {
			'user' : {
				username: document.getElementById('inputUsername').value,
				password: document.getElementById('inputPassword').value,
			}
		};
		if ($scope.formdata.RememberMe == true) {
			data.user.remember_me = true;
		} else {
			data.user.remember_me = false;
		}

		$http.post("api/users/login", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				$cookieStore.put('logged_in', true);
				$window.location.href = 'index.html';
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});

userApp.controller('UserLogoutCtrl', function ($scope, $rootScope, $http,
	$cookieStore, alertService)
{
	$http.get("api/users/logout").
	success(function (data, status, headers) {
		$cookieStore.put('logged_in', false);
	}).
	error(function (data, status, headers) {
		alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
	});
});

userApp.controller('UserRegisterCtrl', function ($scope, $rootScope, $http,
	$cookieStore, $location, alertService)
{
	$scope.alerts = alertService.alerts;
	$scope.formdata = [];

	/* Initial data */
	$cookieStore.put('logged_in', false);

	$scope.register = function() {
		alertService.clear();

		var data = {
			'user' : {
				username: $scope.formdata.Username,
				password: $scope.formdata.Password,
				email: $scope.formdata.Email,
			}
		};

		$http.post("api/users/register", data).
		success(function (data, status, headers) {
			if (data.status == 'success') {
				alertService.add(data.message, 'success');
				$location.path('/login');
			} else {
				alertService.add(data.message, 'danger');
			}
		}).
		error(function (data, status, headers) {
			alertService.add('Oh snap ! Something went wrong, please try again.', 'danger');
		});
	}
});
