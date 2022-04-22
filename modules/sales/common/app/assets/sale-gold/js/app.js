var saleEditApplication = (function() {
    var settings = {
        api:          {
            files: '/api/files',
            sales: '/api/sales-gold'
        },
        apiUrlPrefix: '',
        id:           null,
        saveUrl:      ''
    };

    function apiUrl(api, action) {
        action = action || '';
        return settings.apiUrlPrefix + api + action;
    }

    angular
        .module('SaleEditApplication', ['ngFileUpload'])
        .factory('API', ['$http', 'Upload', function($http, Upload) {
            return {
                createSale:     function() {
                    return $http.get(apiUrl(settings.api.sales, '/new'));
                },
                loadSale:       function(id) {
                    return $http.get(apiUrl(settings.api.sales, '/view'), {params: {id: id}});
                },
                uploadDocument: function(document) {
                    return Upload.upload({
                        url:          apiUrl(settings.api.files, '/upload'),
                        sendFieldsAs: 'form',
                        file:         document
                    })
                },
                uploadPreviousDocument: function(document) {
                    return Upload.upload({
                        url:          apiUrl(settings.api.files, '/upload-previous'),
                        sendFieldsAs: 'form',
                        file:         document
                    })
                }
            };
        }])
        .controller('SaleEdit', ['$scope', '$http', 'Upload', 'API',
            function($scope, $http, Upload, API) {
                function Document(document) {
                    document = document || {};
                    var _self = this;

                    this.id = document.id || null;
                    this.name = document.name || '';
                    this.original_name = document.original_name || '';
                    this.uploaded = false;//this.id != null;
                    this.isImage = document.isImage || false;

                    this.upload = function() {
                        if (!this.document || this.document.length == 0) {
                            return;
                        }

                        this.uploading = true;
                        this.uploaded = false;
                        this.errors = [];

                        API.uploadDocument(this.document)
                            .success(function(info) {
                                _self.uploading = false;
                                _self.uploaded = true;
                                _self.id = info.id;
                                _self.name = info.name;
                                _self.original_name = info.original_name;
                                _self.isImage = info.isImage;
                            })
                            .error(function(info) {
                                _self.uploading = false;
                                _self.uploaded = false;
                                _self.errors = info;
                            });
                    };

                    this.uploadPrevious = function() {
                        if (!this.document || this.document.length == 0) {
                            return;
                        }

                        this.uploading = true;
                        this.uploaded = false;
                        this.errors = [];

                        API.uploadPreviousDocument(this.document)
                            .success(function(info) {
                                _self.uploading = false;
                                _self.uploaded = true;
                                _self.id = info.id;
                                _self.name = info.name;
                                _self.original_name = info.original_name;
                                _self.isImage = info.isImage;
                            })
                            .error(function(info) {
                                _self.uploading = false;
                                _self.uploaded = false;
                                _self.errors = info;
                            });
                    };
                }

                function Sale(data) {
                    data = data || {};
                    this.kg = data.kg / 100 || 0;
                    this.previous_kg = data.previous_kg / 100 || 0;
                }

                function Model(data) {
                    data = data || {};
                    var _self = this;

                    this.status = '';
                    this.sale = new Sale(data.sale || []);
                    this.documents = [];
                    this.previous_documents = [];

                    angular.forEach(data.documents || [], function(value) {
                        _self.documents.push(new Document(value));
                    });

                    angular.forEach(data.previous_documents || [], function(value) {
                        _self.previous_documents.push(new Document(value));
                    });

                    this.addDocument = function(document) {
                        this.documents.push(document);
                    };

                    this.removeDocument = function(document) {
                        var index = this.documents.indexOf(document);
                        if (index > -1) {
                            this.documents.splice(index, 1);
                        }
                    };

                    this.addPreviousDocument = function(document) {
                        this.previous_documents.push(document);
                    };

                    this.removePreviousDocument = function(document) {
                        var index = this.previous_documents.indexOf(document);
                        if (index > -1) {
                            this.previous_documents.splice(index, 1);
                        }
                    };
                }

                $scope.model = null;
                $scope.disabled = false;
                $scope.errors = {};

                (settings.id ? API.loadSale(settings.id) : API.createSale())
                    .success(function(sale) {
                        $scope.model = new Model(sale);
                    })
                    .error(function() {
                    });

                $scope.addDocument = function() {
                    var document = new Document();
                    $scope.model.addDocument(document);
                };

                $scope.removeDocument = function(document) {
                    $scope.model.removeDocument(document)
                };

                $scope.addPreviousDocument = function() {
                    var document = new Document();
                    $scope.model.addPreviousDocument(document);
                };

                $scope.removePreviousDocument = function(document) {
                    $scope.model.removePreviousDocument(document)
                };

                $scope.save = function(status) {
                    $scope.disabled = true;
                    $scope.model.status = status;

                    $http.post(apiUrl(settings.api.sales, settings.saveUrl), {model: $scope.model}, {params: {id: settings.id}})
                        .success(function(result) {
                            window.location = result.url;
                        })
                        .error(function(errors) {
                            $scope.errors = errors;
                            $scope.disabled = false;
                        })
                };
            }
        ]);

    return {
        configure: function(config) {
            angular.extend(settings, config);
        }
    };
})();