var saleEditApplication = (function() {
	var settings = {
		api:          {
			products:   '/api/products',
			types:      '/api/types',
			categories: '/api/categories',
			files:      '/api/files',
			sales:      '/api/sales2',
			brands:     '/api/brands'
		},
		apiUrlPrefix: '',
		id:           null,
		saveUrl:      '',
		promotion_type: '',
		promotion_id: 0,
		dealer_id:    0
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
				loadProduct:    function(id) {
					return $http.get(apiUrl(settings.api.products, '/view'), {params: {id: id}});
				},
				loadProducts:   function(type_id, category_id) {
					return $http.get(apiUrl(settings.api.products), {
						params: {
							typeId:      type_id,
							categoryId:  category_id,
							promotionId: settings.promotion_id
						}
					});
				},
				loadCategories: function(type_id) {
					return $http.get(apiUrl(settings.api.categories), {
						params: {
							typeId:      type_id,
							promotionId: settings.promotion_id
						}
					});
				},
				loadTypes:      function() {
					return $http.get(apiUrl(settings.api.types), {params: {promotionId: settings.promotion_id}});
				},
				loadBrands:     function() {
					return $http.get(apiUrl(settings.api.brands), {params: {promotionId: settings.promotion_id}});
				},
				uploadDocument: function(document) {
					return Upload.upload({
						url:          apiUrl(settings.api.files, '/upload'),
						sendFieldsAs: 'form',
						file:         document
					})
				}
			};
		}])

		.controller('SaleEdit', ['$scope', '$http', 'Upload', 'API',
			function($scope, $http, Upload, API) {
				function BrandPosition(brandPosition) {
					brandPosition = brandPosition || {};
					var brands = [];
					var _self = this;

					this.brand_id = brandPosition.brand_id || null;
					this.kg = brandPosition.kg || null;
					this.rub = brandPosition.rub || null;

					if (this.brand_id) {
						_self.kg = brandPosition.kg / 100;
					}
				}

				function Position(position) {

					var products = [],
						categories = [],
						types = [],
						product = {},
						_self = this;

					position = position || {};

					this.id = position.id;
					this.type_id = null;
					this.category_id = position.category_id || null;
					this.product_id = position.product_id || null;
					this.kg = position.kg || null;

					this.getProduct = function() {
						return product;
					};

					this.loadCategories = function() {
						return API.loadCategories(_self.type_id)
							.then(function(data) {
								_self.categories = data.data;
							});
					};

					this.loadProducts = function() {
						return API.loadProducts(_self.type_id, _self.category_id)
							.then(function(data) {
								_self.products = data.data;
							});
					};

					this.loadProduct = function() {
						return API.loadProduct(_self.product_id)
							.then(function(data) {
								_self.product = data.data;
								_self.product_id = _self.product.id;
							});
					};

					this.changeType = function() {
						_self.products = [];
						_self.loadCategories(_self.type_id);
					};

					this.changeCategory = function() {
						_self.products = [];
						_self.loadProducts(_self.type_id, _self.category_id);
					};

					this.changeProduct = function() {
						_self.loadProduct();
					};

					if (this.product_id) {
						this.loadProduct()
							.then(function() {
								_self.type_id = _self.product.type_id;
								_self.category_id = _self.product.category_id;
								_self.product_id = _self.product.id;
								_self.kg = position.kg / 100;
								_self.loadCategories();
								_self.loadProducts();
							});
					}
				}

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
							.then(function(info) {
								_self.uploading = false;
								_self.uploaded = true;
								_self.id = info.data.id;
								_self.name = info.data.name;
								_self.original_name = info.data.original_name;
								_self.isImage = info.data.isImage;
							})
							.catch(function(info) {
								_self.uploading = false;
								_self.uploaded = false;
								_self.errors = info.data;
							});
					};
				}

				function Sale(data) {
					data = data || {};

					this.sold_on_local = data.sold_on_local || '';
				}

				function Model(data) {
					data = data || {};
					var _self = this;

					this.status = '';
					this.sale = new Sale(data.sale || []);
					this.positions = [];
					this.brandPositions = [];
					this.documents = [];

					angular.forEach(data.positions || [], function(value) {
						_self.positions.push(new Position(value));
					});

					angular.forEach(data.brandPositions || [], function(value) {
						_self.brandPositions.push(new BrandPosition(value));
					});

					angular.forEach(data.documents || [], function(value) {
						_self.documents.push(new Document(value));
					});

					this.addPosition = function(position) {
						this.positions.push(position);
					};

					this.removePosition = function(position) {
						var index = this.positions.indexOf(position);
						if (index > -1) {
							this.positions.splice(index, 1);
						}
					};

					this.addBrandPosition = function(brandPosition) {
						this.brandPositions.push(brandPosition);
					};

					this.removeBrandPosition = function(brandPosition) {
						var index = this.brandPositions.indexOf(brandPosition);
						if (index > -1) {
							this.brandPositions.splice(index, 1);
						}
					};

					this.addDocument = function(document) {
						this.documents.push(document);
					};

					this.removeDocument = function(document) {
						var index = this.documents.indexOf(document);
						if (index > -1) {
							this.documents.splice(index, 1);
						}
					}
				}

				$scope.model = null;
				$scope.type = settings.promotion_type;
				$scope.categories = [];
				$scope.disabled = false;
				$scope.errors = {};
				$scope.positionTypeOptions = [
					{type: 'kg', name: 'кг'},
					{type: 'packing', name: 'фасовка'}
				];

				(settings.id ? API.loadSale(settings.id) : API.createSale())
					.then(function(sale) {
						API
							.loadTypes()
							.then(function(data) {
								$scope.types = data.data;
							})
							.then(function() {
								$scope.model = new Model(sale);
								if ($scope.model.brandPositions.length) {
									$scope.type = 'brands';
									for (var i = 0; i < $scope.model.brandPositions.length; i++) {
										if ($scope.model.brandPositions[i].rub) {
											$scope.type = 'brandsRub';
										}
									}
								}
							});

						API
							.loadBrands()
							.then(function(data) {
								$scope.brands = data.data;
							});
					})
					.catch(function() {
					});

				$scope.addPosition = function() {
					var position = new Position();
					$scope.model.addPosition(position);
				};

				$scope.removePosition = function(position) {
					$scope.model.removePosition(position)
				};

				$scope.addBrandPosition = function() {
					var brandPosition = new BrandPosition();
					$scope.model.addBrandPosition(brandPosition);
				};

				$scope.removeBrandPosition = function(brandPosition) {
					$scope.model.removeBrandPosition(brandPosition)
				};
				$scope.addDocument = function() {
					var document = new Document();
					$scope.model.addDocument(document);
				};

				$scope.removeDocument = function(document) {
					$scope.model.removeDocument(document)
				};

				$scope.save = function(status) {
					$scope.disabled = true;
					$scope.model.status = status;

					$http.post(apiUrl(settings.api.sales, settings.saveUrl), {model: $scope.model}, {params: {id: settings.id}})
						.then(function(result) {
							window.location = result.data.url;
						})
						.catch(function(errors) {
							$scope.errors = errors.data;
							$scope.disabled = false;
						})
				};

				$scope.typeDefault = function() {
					$scope.model.brandPositions = [];
				};

				$scope.typeBrands = function() {
					$scope.model.brandPositions = [];
					$scope.model.positions = [];
				};

				$scope.typeBrandsRub = function() {
					$scope.model.brandPositions = [];
					$scope.model.positions = [];
				};
			}
		]);

	return {
		configure: function(config) {
			angular.extend(settings, config);
		}
	};
})();