angular.module('flowerAccountImporter').controller("ImporterController", function ($scope, $routeParams, $http, Upload, $timeout) {

    $scope.doneUploading = false;
    $scope.doneSelectColumns = false;
    $scope.doneImporting = false;
    $scope.filename = '';

    $scope.colsFile = null;
    $scope.availableAccounts = [];
    $scope.accountSel = null;
    $scope.colsEntity = ['name', 'phone', 'address', 'cuit'];
    $scope.colDef = {
        'name': '',
        'phone': '',
        'address': '',
        'cuit': ''
    };

    $scope.uploadFiles = function (file) {
        $scope.f = file;
        if (file && !file.$error) {
            file.upload = Upload.upload({
                url: rootPath + 'account/upload',
                file: file
            });

            file.upload.then(function (response) {
                $timeout(function () {
                    file.result = response.data;
                    $scope.doneUploading = true;
                    $scope.colsFile = response.data.first_row;
                    $scope.filename = response.data.filename;
                });
            }, function (response) {
                if (response.status > 0) {
                    $scope.errorMsg = response.status + ': ' + response.data;
                }
            });

            file.upload.progress(function (evt) {
                file.progress = Math.min(100, parseInt(100.0 *
                        evt.loaded / evt.total));
            });
        }
    };

    $scope.finishImport = function () {

        $http.post(rootPath + 'account/import/launch', {
            "filename": $scope.filename,
            "col_def": $scope.colDef
        }).then(function(){
            window.location.href = importsUrl;
        });
    };

});
