'use strict';


myApp.config(function($stateProvider, $urlRouterProvider) {
  //
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  //
  // Now set up the states
  $stateProvider
    .state('mainview', {
      url: "/mainview",
      templateUrl: "partials/mainview.html",
      controller: function($scope){
        
      }
    })
     .state('login', {
      url: "/",
        templateUrl: "partials/login.html",
        controller: function($scope, $http) {
          $scope.array_users = [];

          $http({
            method:'GET',
            url:"js/content.json"
          }).success(function(data){
           
            $scope.array_users = data.Users;
       
          }); 

          $scope.login = function(){
            angular.forEach($scope.array_users, function(value, keys){
              if(value.username == $scope.username && value.password == $scope.password){
                //$data.setSession(true);

                window.location = "http://localhost/html/#/mainview";
              }else{
                $scope.error_message = "Username or password mistake";
              }
            });

          }


        }
      })
    });