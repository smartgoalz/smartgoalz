var goalApp = angular.module('goalApp', ['ngResource', 'ngRoute', 'ui.bootstrap']);

goalApp.controller('GoalCtrl', function ($scope) {
});

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
	when('/delete/:id', {
		templateUrl: 'frontend/goals/delete.html',
		controller: 'GoalDeleteCtrl'
	}).
	otherwise({
		redirectTo: '/show'
	});
}]);

function GoalAddCtrl($scope) {

}

function GoalShowCtrl($scope, $http) {
	$http.get('goals.json').
	success(function(data, status, headers, config) {
		$scope.goals = data['goals'];
	}).
	error(function(data, status, headers, config) {
		$scope.goals = [];
	});
}


