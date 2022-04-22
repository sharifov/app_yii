console.log(phoneValidation);

var phoneValidation = (function () {
    var settings = {
        generateTokenUrl: '/identity/api/registration-validation/generate-token',
        validateTokenUrl: '/identity/api/registration-validation/validate-token'
    };

    var app = angular

            .module('PhoneValidation', [])

            .controller('Validation', ['$scope', '$http',
                function ($scope, $http) {
                    $scope.phone = '';
                    $scope.token = '';
                    $scope.tokenRequested = false;
                    $scope.errors = {};
                    $scope.disabled = false;

                    // Small hack for work with phone
                    $('#phone-mobile').change(function () {
                        $scope.phone = $(this).val();
                    });

                    $scope.generateToken = function () {
                        $scope.disabled = true;
                        $scope.errors = {};
                        $http.post(settings.generateTokenUrl, {phoneMobileLocal: $scope.phone})
                            .then(function () {
                                $scope.token = '';
                                $scope.tokenRequested = true;
                                $scope.disabled = false;
                            })
                            .catch(function (errors) {
                                $scope.disabled = false;
                                $scope.tokenRequested = false;
                                $scope.errors = errors.data;
                            });
                    };

                    $scope.validateToken = function () {
                        $scope.disabled = true;
                        $scope.errors = {};
                        $http.post(settings.validateTokenUrl, {phoneMobileLocal: $scope.phone, token: $scope.token})
                            .then(function (result) {
                                window.location = result.data.url;
                            })
                            .catch(function (errors) {
                                $scope.disabled = false;
                                $scope.tokenRequested = true;
                                $scope.errors = errors.data;
                            });
                    };

                    $scope.reenterPhone = function () {
                        $scope.tokenRequested = false;
                        $scope.phone = '';
                        $scope.errors = {};
                        $scope.token = '';
                    }
                }
            ])

            .directive('inputMask', function () {
                return {
                    restrict: 'A',
                    link: function (scope, el, attrs) {
                        function update(val) {
                            scope.$eval(attrs.ngModel + "='" + val + "'");
                        }
                        var options = scope.$eval(attrs.inputMask);
                        options['onKeyPress'] = function() {
                            update($(this).val());
                        };
                        $(el).inputmask(options);
                        $(el).on('change', function () {
                            update($(this).val());
                        });
                    }
                };
            })
        ;

    return {
        settings: settings
    };
})();