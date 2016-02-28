var flowerAccountImporter = angular.module('flowerAccountImporter', ['ngRoute', 'mgo-angular-wizard','ngFileUpload']);

flowerAccountImporter.config(function ($routeProvider) {
    var basePath = rootPath + "bundles/flowerclients/js/angular/importer/";

    /* app */
    $routeProvider.when('/', {templateUrl: basePath + 'view/selectFile.html', controller: 'ImporterController'});

    $routeProvider.otherwise({redirectTo: '/'});

}).run();