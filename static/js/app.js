var app = angular.module('app', ['ngRoute', 'ui.bootstrap']);

app.config(function($routeProvider){
	$routeProvider.
      when('/', {controller:mainCtrl, templateUrl:BASE_URL+'example/template_list'}).
      when('/home', {controller:mainCtrl, templateUrl:BASE_URL+'example/template_list'}).
      otherwise({redirectTo:'/'});
  });
