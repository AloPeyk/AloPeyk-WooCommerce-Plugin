(function ( $j ) {

	'use strict';

	var alopeyk = alopeyk || { wcshm : {} };
	alopeyk.wcshm.public = {};



	/*==========================================
	=            Defining Variables            =
	==========================================*/

	alopeyk.wcshm.public.vars = {

		common : {

			info               : window.awcshm,
			activeClass        : 'active',
			loadingClass       : 'loading',
			disabledClass      : 'disabled',
			alopeykPrefix      : 'awcshm-',
			paymentMethodInput : 'input[name="payment_method"]',
			responseDataCity   : '',
			selectedCityType   : '',

		},

		maps   : {
			
			selector                                   : '.map-canvas',
			mapContainerClass                          : 'map-container',
			destinationLatInput                        : '#destination_latitude, #_shipping_address_latitude',
			destinationLngInput                        : '#destination_longitude, #_shipping_address_longitude',
			destinationAddressInput                    : '#destination_address, #_shipping_address_location',
			destinationNumberInput                     : '#destination_number, #_shipping_address_number',
			destinationUnitInput                       : '#destination_unit, #_shipping_address_unit',
			shippingAddress1Input                      : '#shipping_address_1, #_shipping_address_1',
			shippingAddress2Input                      : '#shipping_address_2, #_shipping_address_2',
			billingAddress1Input                       : '#billing_address_1, #_billing_address_1',
			billingAddress2Input                       : '#billing_address_2, #_billing_address_2',
			billing_address_1_field                    : '#billing_address_1_field',
			addressDetailsFields                       : '#awcshm-address-details',
			billing_country                            : '#billing_country',
			shipping_country                           : '#shipping_country',
			billing_city                               : '#billing_city',
			shipping_city                              : '#shipping_city',
			billing_state                              : '#billing_state',
			shipping_state                             : '#shipping_state',
			select2Class                               : '.select2-selection',
			awcshmLoadingField                         : 'awcshm-loading-field',
			woocommerce_checkout_class                 : '.woocommerce-checkout',
			wc_add_alopeyk                             : 'awcshm-map-visible',
			shipToDifferentAddressInput                : 'input[name="ship_to_different_address"]',
			editAddressButton                          : 'a.edit_address:eq(1)',
			destinationLocatorMapId                    : 'destination-locator-map',
			destinationLocatorMapClass                 : 'map-canvas destination-locator-map',
			destinationLocatorCtaClass                 : 'destination-locator-cta',
			mapMarkerIconClass                         : 'map-marker-icon',
			destinationLocatorInputWrapperId           : 'destination-locator-input-wrapper',
			destinationLocatorInputWrapperClass        : 'destination-locator-input-wrapper',
			destinationLocatorAutocompleteResultsClass : 'destination-locator-autocomplete-results',
			destinationLocatorAutocompleteResultClass  : 'destination-locator-autocomplete-result',
			destinationLocatorHiddenableInput          : '.hide-parent-row',
			autocompletePlaceholderDataAttr            : 'autocomplete-placeholder',
			autocompleteKeyupTimeout                   : null,
			positionKeyupTimeout                       : null,
			autoCompleteKeyupDelay                     : 500,
			positionKeyupDelay                         : 500,
			defaultZoom                                : 15,
			maxZoom                                    : 17,
			defaultCenter                              : {
				lat : 35.744989,
				lng : 51.375284
			},
			cedarMapJsLib                              : 'https://api.cedarmaps.com/cedarmaps.js/v1.8.0/cedarmaps.js',
			cedarMapCssLib                             : 'https://api.cedarmaps.com/cedarmaps.js/v1.8.0/cedarmaps.css',

		},

	};



	/*==================================
	=            Prototypes            =
	==================================*/




	/*==========================================
	=            Defining Functions            =
	==========================================*/

	alopeyk.wcshm.public.fn = {

		addPrefix : function ( classes ) {
			
			var prefixedClasses = [],
				classesArray    = classes.split ( ' ' );

			for ( var i = 0; i < classesArray.length; i++ ) {
				prefixedClasses.push ( alopeyk.wcshm.public.vars.common.alopeykPrefix + classesArray[ i ] )
			}

			return prefixedClasses.join ( ' ' );

		},
		
		translate : function ( term ) {

			var translation = alopeyk.wcshm.public.vars.common.info.translations[ term ];
			return translation ? translation : term;

		},

		injectScript : function ( src, callback ) {

			if ( ! $j( '[src="' + src + '"]' ).length ) {

				var s, t;

				s        = document.createElement ( 'script' );
				s.type   = 'text/javascript';
				s.async  = true;
				s.src    = src;
				s.onload = callback ? callback : false;
				t        = document.getElementsByTagName ( 'script' )[ 0 ];

				t.parentNode.insertBefore ( s, t );

			}

		},
		
		injectStylesheet : function ( href ) {

			if ( ! $j( '[href="' + href + '"]' ).length ) {

				var s, t;

				s       = document.createElement ( 'link' );
				s.type  = 'text/css';
				s.rel   = "stylesheet";
				s.href  = href;
				t       = document.getElementsByTagName ( 'link' )[ 0 ];

				t.parentNode.insertBefore ( s, t );

			}

		},

		loadCedarMaps : function () {

			window[ 'alopeykHandleMapsPublic' ] = alopeyk.wcshm.public.fn.handleMaps;

			if ( typeof alopeykHandleMapsPublic === 'function' ) {

				if ( typeof window.L != 'undefined' ) {

					window.cedarMapIsLoading = false;
					alopeyk.wcshm.public.fn.handleMaps();

				} else if ( window.cedarMapIsLoading ) {

					alopeyk.wcshm.public.vars.loadingMapInterval = setInterval ( function () {

						if ( window.L ) {

							window.cedarMapIsLoading = false;
							clearInterval ( alopeyk.wcshm.public.vars.loadingMapInterval );
							alopeyk.wcshm.public.fn.handleMaps();

						}

					}, 500 );

				} else {

					window.cedarMapIsLoading = true;
					alopeyk.wcshm.public.fn.injectScript ( alopeyk.wcshm.public.vars.maps.cedarMapJsLib, function () {

						alopeyk.wcshm.public.fn.injectScript ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.leaflet_gesture_handling.js, alopeykHandleMapsPublic );

					});
					alopeyk.wcshm.public.fn.injectStylesheet ( alopeyk.wcshm.public.vars.maps.cedarMapCssLib );
					alopeyk.wcshm.public.fn.injectStylesheet ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.leaflet_gesture_handling.css );

				}

			}

		},

		handleMaps : function () {

			$j( document ).trigger ( 'alopeyk:public:map:loaded' );

		},

		initMaps : function () {

			$j( document ).on ( 'alopeyk:public:map:loaded', function () {

				var destinationLatInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
					destinationLngInput         = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput ),
					destinationAddressInput     = $j( alopeyk.wcshm.public.vars.maps.destinationAddressInput ),
					destinationNumberInput      = $j( alopeyk.wcshm.public.vars.maps.destinationNumberInput ),
					destinationUnitInput        = $j( alopeyk.wcshm.public.vars.maps.destinationUnitInput ),
					shippingAddress1Input       = $j( alopeyk.wcshm.public.vars.maps.shippingAddress1Input ),
					shippingAddress2Input       = $j( alopeyk.wcshm.public.vars.maps.shippingAddress2Input ),
					billingAddress1Input        = $j( alopeyk.wcshm.public.vars.maps.billingAddress1Input ),
					billingAddress2Input        = $j( alopeyk.wcshm.public.vars.maps.billingAddress2Input ),
					billing_address_1_field     = $j( alopeyk.wcshm.public.vars.maps.billing_address_1_field ),
					addressDetailsFields        = $j( alopeyk.wcshm.public.vars.maps.addressDetailsFields ),
					shipToDifferentAddressInput = $j( alopeyk.wcshm.public.vars.maps.shipToDifferentAddressInput ),
					editAddressButton           = $j( alopeyk.wcshm.public.vars.maps.editAddressButton ),
					billing_country             = alopeyk.wcshm.public.vars.maps.billing_country,
					shipping_country            = alopeyk.wcshm.public.vars.maps.shipping_country,
					billing_city                = alopeyk.wcshm.public.vars.maps.billing_city,
					shipping_city               = alopeyk.wcshm.public.vars.maps.shipping_city,
					billing_state               = alopeyk.wcshm.public.vars.maps.billing_state,
					shipping_state              = alopeyk.wcshm.public.vars.maps.shipping_state,
					select2Class                = alopeyk.wcshm.public.vars.maps.select2Class,
					awcshmLoadingField          = alopeyk.wcshm.public.vars.maps.awcshmLoadingField,
					woocommerce_checkout_class  = alopeyk.wcshm.public.vars.maps.woocommerce_checkout_class,
					wc_add_alopeyk              = alopeyk.wcshm.public.vars.maps.wc_add_alopeyk;

				if ( destinationLatInput.length && destinationLngInput.length && destinationAddressInput.length ) {
					alopeyk.wcshm.public.fn.initDestinationLocator( destinationLatInput, destinationLngInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shippingAddress1Input, shippingAddress2Input, billingAddress1Input, billingAddress2Input, billing_address_1_field, shipToDifferentAddressInput, editAddressButton, addressDetailsFields, billing_country, shipping_country, billing_city, shipping_city, billing_state, shipping_state, woocommerce_checkout_class, wc_add_alopeyk, select2Class, awcshmLoadingField );
				}

			});

			alopeyk.wcshm.public.fn.loadCedarMaps();

		},

		setCurrentGeolocation : function ( forceRelocation ) {

			var destinationMap      = alopeyk.wcshm.public.vars.common.destinationMap,
				map                 = destinationMap ? destinationMap.mapObject : null,
				destinationLatInput = $j( alopeyk.wcshm.public.vars.maps.destinationLatInput ),
				destinationLngInput = $j( alopeyk.wcshm.public.vars.maps.destinationLngInput );

			if ( navigator.geolocation && map && destinationLatInput.length && destinationLngInput.length && ( forceRelocation || ( ! destinationLatInput.val().length && ! destinationLngInput.val().length ) || ( destinationLatInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lat && destinationngInput.val() == alopeyk.wcshm.public.vars.maps.defaultCenter.lng ) ) ) {

				navigator.geolocation.getCurrentPosition ( function ( position ) {

					if ( alopeyk.wcshm.public.vars.maps.fetchAddressConnection ) {
						alopeyk.wcshm.public.vars.maps.fetchAddressConnection.abort();
					}

					map.setView ({

						lat : position.coords.latitude,
						lng : position.coords.longitude

					});

					alopeyk.wcshm.public.vars.maps.defaultCenter.lat = position.coords.latitude;
					alopeyk.wcshm.public.vars.maps.defaultCenter.lng = position.coords.longitude;

					destinationLatInput.val ( position.coords.latitude );
					destinationLngInput.val ( position.coords.longitude ).trigger ( 'change' );

				});

			}

		},

		initDestinationLocator : function ( destinationLatInput, destinationLngInput, destinationAddressInput, destinationNumberInput, destinationUnitInput, shippingAddress1Input, shippingAddress2Input, billingAddress1Input, billingAddress2Input, billing_address_1_field, shipToDifferentAddressInput, editAddressButton, addressDetailsFields, billing_country, shipping_country, billing_city, shipping_city, billing_state, shipping_state, woocommerce_checkout_class, wc_add_alopeyk, select2Class, awcshmLoadingField ) {

			var initialize = function () {

				var mapMarkerImageUrl  = alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.marker,

					mapCanvas = $j( '<div/>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorMapId ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorMapClass ),

					}),

					destinationAutocompleteInput = destinationAddressInput.clone().attr ({

						id             : destinationAddressInput.attr ( 'id' ) + '_autocomplete',
						class          : destinationAddressInput.attr ( 'class' ),
						placeholder    : destinationAddressInput.data ( alopeyk.wcshm.public.vars.maps.autocompletePlaceholderDataAttr ),
						name           : '',
						style          : '',
						type           : 'text',
						spellcheck     : 'false',
						autocapitalize : 'off',
						autocorrect    : 'off',
						autocomplete   : 'off'

					}).removeClass ( alopeyk.wcshm.public.vars.common.disabledClass ),

					destinationAutocompleteInputWrapper = $j( '<div>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorInputWrapperId ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorInputWrapperClass ),

					}),

					autoCompleteList = $j( '<ul/>' ).attr ({

						id    : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass ),
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultsClass ),

					}),

					mapContainer = $j( '<div/>' ).attr ({

						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.mapContainerClass ),

					}),

					destinationLocatorCta = $j( '<button/>' ).attr ({

						type  : 'button',
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorCtaClass ),

					}),

					markerImage = $j( '<img/>' ).attr ({

						src   : mapMarkerImageUrl,
						class : alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.mapMarkerIconClass ),

					});

				destinationAutocompleteInputWrapper
				.insertAfter ( destinationAddressInput )
				.append ( destinationAutocompleteInput )
				.append ( autoCompleteList );

				mapCanvas.insertAfter( destinationAutocompleteInputWrapper );
				mapContainer.insertAfter( mapCanvas );

				mapContainer
				.append( mapCanvas )
				.append( destinationAutocompleteInputWrapper )
				.append( destinationLocatorCta )
				.prepend( markerImage );

				L.cedarmaps.accessToken = alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.api_key;

				var mapOptions = {

						zoom    : alopeyk.wcshm.public.vars.maps.defaultZoom,
						maxZoom : alopeyk.wcshm.public.vars.maps.maxZoom,
						center  : [

							destinationLatInput.val().length ? parseFloat( destinationLatInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lat,
							destinationLngInput.val().length ? parseFloat( destinationLngInput.val() ) : alopeyk.wcshm.public.vars.maps.defaultCenter.lng,

						],
						zoomControl     : false,
						gestureHandling : true,
						gestureHandlingText: {

							touch: alopeyk.wcshm.public.fn.translate ( 'Use two fingers to move the map' ),
							scroll: alopeyk.wcshm.public.fn.translate ( 'Use ctrl + scroll to zoom the map' ),
							scrollMac: alopeyk.wcshm.public.fn.translate ( 'Use ⌘ + scroll to zoom the map' ),
							
						}

					},

					map = L.cedarmaps.map ( mapCanvas.get ( 0 ), alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.map.api_url.replace ( '{{TOKEN}}', L.cedarmaps.accessToken ), mapOptions ),

					setActiveAutocompleteItem = function ( index ) {

						var results     = autoCompleteList.children(),
							activeIndex = index == results.length ? 0 : ( index < 0 ? results.length - 1 : index ),
							activeClass = alopeyk.wcshm.public.vars.common.activeClass;

						results.removeClass ( activeClass ).eq ( activeIndex ).addClass ( activeClass );

					},

					fetchAddressFromLocation = function () {

						if ( alopeyk.wcshm.public.vars.maps.fetchAddressConnection ) {

							alopeyk.wcshm.public.vars.maps.fetchAddressConnection.abort();

						}

						destinationAutocompleteInputWrapper.addClass ( alopeyk.wcshm.public.vars.common.loadingClass );

						alopeyk.wcshm.public.vars.maps.fetchAddressConnection = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

							nonce        : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
							action       : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
							request      : 'get_address',
							authenticate : true,
							lat          : map.getCenter().lat,
							lng          : map.getCenter().lng,

						}, function ( response ) {

							if ( response ) {

								autoCompleteList.empty();
								destinationAutocompleteInput.val ( response.data.address );

								var selectedCityType     = billing_city,
									selectedStateType    = billing_state;

								if ( shipToDifferentAddressInput.prop ( 'checked' ) ) {

									selectedCityType     = shipping_city;
									selectedStateType    = shipping_state;

								}

								var cityValue     = cityProvinceValue( selectedCityType , response.data.city     ),
									provinceValue = cityProvinceValue( selectedStateType, response.data.province );

								if ( cityValue.length > 0 ) {

									$j( selectedCityType ).val( cityValue ).triggerHandler ( 'change' );

								} else {

									$j( selectedStateType ).val( provinceValue ).trigger ( 'change' );
									alopeyk.wcshm.public.vars.common.responseDataCity = response.data.city;
									alopeyk.wcshm.public.vars.common.selectedCityType = selectedCityType;

								}

								if ( $j( '.' + wc_add_alopeyk ).length ) {

									var address = response.success ? response.data.address : '';
									destinationAddressInput.val ( address );

									shippingAddress1Input.val ( address );
									shippingAddress2Input.val ( '' );

									if ( shipToDifferentAddressInput.length && ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

										if ( billingAddress1Input.length )
											billingAddress1Input.val ( address );

										if ( billingAddress2Input.length )
											billingAddress2Input.val ( '' );

									}

								}
								destinationAddressInput.trigger ( 'change' );

							}

						}).always ( function () {

							destinationAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.public.vars.common.loadingClass );

						});

					},

					saveData = function ( update ) {

						if ( alopeyk.wcshm.public.vars.updateShipingMethod ) {

							alopeyk.wcshm.public.vars.updateShipingMethod.abort();

						}

						destinationAddressInput.parents ( 'form' )
						.find ( 'button, input[type="button"], input[type="submit"]' )
						.filter ( function () {

							return ! $j( this ).is ( ':disabled' );

						})
						.prop ( 'disabled', true )
						.data ( 'alopeyk-disable', true );

						alopeyk.wcshm.public.vars.updateShipingMethod = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

							nonce          : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
							action         : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
							request        : 'set_session',
							authenticate   : true,
							lat            : map.getCenter().lat,
							lng            : map.getCenter().lng,
							address        : destinationAddressInput.val(),
							number         : destinationNumberInput.val(),
							unit           : destinationUnitInput.val(),
							payment_method : $j( paymentMethodInputSelector + ':checked' ).val(),

						}, function ( response ) {
							
							if ( response && response.success && update ) {

								$j( 'body' ).trigger ( 'update_checkout' );

							}

							destinationAddressInput.parents ( 'form' )
							.find ( 'button, input[type="button"], input[type="submit"]' )
							.filter ( function () {

								return $j( this ).data ( 'alopeyk-disable' );

							})
							.prop ( 'disabled', false );

						});

					},

					preLoadCities = function () {

						$j(  billing_city ).parent().find( select2Class ).addClass( awcshmLoadingField );
						$j( shipping_city ).parent().find( select2Class ).addClass( awcshmLoadingField );
						getIrCity ( 'billing' );
						getIrCity ( 'shipping' );
						if ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.config.map_always_visible == 'yes' ) {

							var selectedCountryType = billing_country;

							if ( shipToDifferentAddressInput.prop ( 'checked' ) ) {

								selectedCountryType = shipping_country;

							}
							
							if ( $j( selectedCountryType ).val() == 'IR' ) {

								$j( woocommerce_checkout_class ).addClass ( wc_add_alopeyk );
								fetchAddressFromLocation();

							}

						}

					},
					cityProvinceValue = function ( type, cityProvince ) {

						var valueOne = $j( type + ' option[value="' + cityProvince + '"]' ).val(),
							valueTwo = $j( type + ' option' ).filter(function () { return $j(this).html() == cityProvince; }).val();

						if ( valueOne && valueOne.length > 0  ) {

							return valueOne;

						} else if ( valueTwo && valueTwo.length > 0  ) {

							return valueTwo;

						} else {

							return '';

						}

					},
					getIrCity = function ( type ) {

						var city_type = '#' + type + '_city',
							country_type = '#' + type + '_country';
						$j( city_type ).prop( 'disabled', true ).parent().find( select2Class ).addClass( awcshmLoadingField );
						if ( $j( country_type ).val() == 'IR' ) {

							$j( city_type ).empty();
							$j( city_type ).append($j('<option>', {
								value: '',
								text : '',
							}));

							alopeyk.wcshm.public.vars.maps.fetchIRCities = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

								nonce          : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
								action         : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
								request        : 'get_iran_cities',
								selected_state : $j('#' + type + '_state option:selected').text(),

							}, function ( response ) {

								if ( response ) {

									$j( city_type ).prop( 'disabled', false ).parent().find( select2Class ).removeClass( awcshmLoadingField );
									$j.each( response.data.cities, function ( i, item ) {

										if ( item !== "null" ) {
											var if_selected = undefined;
											if ( type == 'billing' ) {

												if ( item.name == response.data.pre_billing_city ) {

													if_selected = 'selected';

												}

											} else if ( type == 'shipping' ) {

												if ( item.name == response.data.pre_shipping_city ) {

													if_selected = 'selected';

												}

											}
											$j( city_type ).append($j('<option>', {
												value: item.name,
												text : item.name,
												attr: {
													cedar_center: item.location.center,
													selected: if_selected,
												}
											}));
										}

									});

									var cityValue = '';
									if ( alopeyk.wcshm.public.vars.common.selectedCityType != '' && alopeyk.wcshm.public.vars.common.selectedCityType == city_type ) {

										cityValue = cityProvinceValue( city_type, alopeyk.wcshm.public.vars.common.responseDataCity );
										if ( cityValue.length > 0 ) {

											$j( city_type ).val( cityValue );

										}

									}
									alopeyk.wcshm.public.vars.common.selectedCityType = '';
									$j( city_type ).trigger ( 'change' );

								}

							});

						} else {

							$j( city_type ).prop( 'disabled', false ).parent().find( select2Class ).removeClass( awcshmLoadingField );

						}

					},
					changeMapLatlng = function ( type ) {

						var city_type = '#' + type + '_city',
							country_type = '#' + type + '_country';

						if ( $j( country_type ).val () == 'IR' ) {

							var selected = $j( city_type ).find ( 'option:selected' );
							if ( selected.text().length > 0 ) {

								var latlng = selected.attr ( 'cedar_center' );
								latlng = latlng.split (',');
								if ( alopeyk.wcshm.public.vars.common.responseDataCity != selected.val() ){

									if ( latlng && latlng.length > 0 ) {

										destinationLatInput.val ( latlng[0] );
										destinationLngInput.val ( latlng[1] ).trigger ( 'change' );
										$j( woocommerce_checkout_class ).removeClass ( wc_add_alopeyk );

									}

								}

								if ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.config.map_always_visible == 'yes' ) {

									$j( woocommerce_checkout_class ).addClass ( wc_add_alopeyk );

								} else {

									$j( woocommerce_checkout_class ).removeClass ( wc_add_alopeyk );
									if ( alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.config.intercity == 'no' ) {

										var selected_city = selected.val();
										$j( woocommerce_checkout_class ).addClass ( wc_add_alopeyk );
										if ( selected_city != alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.config.store_city ) {

											$j( woocommerce_checkout_class ).removeClass ( wc_add_alopeyk );

										}

									} else {

										alopeyk.wcshm.public.vars.maps.autocompleteConnection = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

											nonce           : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
											action          : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
											request         : 'get_address',
											authenticate    : true,
											lat             : latlng[0],
											lng             : latlng[1],

										}, function ( response ) {

											if ( response.success ) {

												$j( woocommerce_checkout_class ).addClass ( wc_add_alopeyk );

											} else {

												$j( woocommerce_checkout_class ).removeClass ( wc_add_alopeyk );

											}

										});

									}

								}

							}

						} else {

							$j( woocommerce_checkout_class ).removeClass ( wc_add_alopeyk );

						}

					},

					paymentMethodInputSelector = alopeyk.wcshm.public.vars.common.paymentMethodInput;
					preLoadCities();

				

				$j( document ).on ( 'change', billing_state, function ( event ) {

					getIrCity ( 'billing' );

				}).on ( 'change', shipping_state, function ( event ) {

					getIrCity ( 'shipping' );

				}).on ( 'change', billing_city, function ( event ) {

					if ( ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

						changeMapLatlng ( 'billing' );

					}

				}).on ( 'change', shipping_city, function ( event ) {

					if ( shipToDifferentAddressInput.prop ( 'checked' ) ) {

						changeMapLatlng ( 'shipping' );

					}

				});

				L.control.zoom ({

					position : 'bottomright'
					
				})
				.addTo ( map );

				alopeyk.wcshm.public.vars.common.destinationMap = {

					mapObject : map

				};

				if ( alopeyk.wcshm.public.vars.common.info.woocommerce.checkout ) {
					
					$j( document ).on ( 'change', paymentMethodInputSelector + ', #' + destinationAddressInput.attr ( 'id' ) , function () {
						saveData ( true );
					});

					$j.merge ( destinationNumberInput, destinationUnitInput ).on ( 'change paste keyup input propertychange', function () {

						if ( alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout ) {
							clearTimeout(  alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout );
						}

						alopeyk.wcshm.public.vars.maps.addressDetailKeyupTimeout = setTimeout ( function () {
							saveData ( false );
						}, alopeyk.wcshm.public.vars.maps.autoCompleteKeyupDelay );

					});

				}

				map.on ( 'move dragend zoomend', function () {

					destinationLatInput.val ( map.getCenter().lat );
					destinationLngInput.val ( map.getCenter().lng ).trigger ( 'change' );

				});

				$j.merge ( destinationLatInput, destinationLngInput ).on ( 'change paste keyup input propertychange', function () {

					if ( alopeyk.wcshm.public.vars.maps.latitudeValue != destinationLatInput.val() || alopeyk.wcshm.public.vars.maps.longitudeValue != destinationLngInput.val()  ) {

						alopeyk.wcshm.public.vars.maps.latitudeValue = destinationLatInput.val();
						alopeyk.wcshm.public.vars.maps.longitudeValue = destinationLngInput.val();

						if ( alopeyk.wcshm.public.vars.maps.positionKeyupTimeout ) {

							clearTimeout(  alopeyk.wcshm.public.vars.maps.positionKeyupTimeout );

						}

						alopeyk.wcshm.public.vars.maps.positionKeyupTimeout = setTimeout ( function () {

							var location = { lat : parseFloat ( destinationLatInput.val() ), lng : parseFloat ( destinationLngInput.val() ) };
							map.setView ( location );
							fetchAddressFromLocation();

						}, alopeyk.wcshm.public.vars.maps.positionKeyupDelay );

					}

				});

				shipToDifferentAddressInput.on ( 'change', function () {

					var checked = $j( this ).prop ( 'checked' ),
						display = checked ? '' : 'none';

					if ( ! checked ) {

						billingAddress1Input.val ( shippingAddress1Input.val() );
						billingAddress2Input.val ( shippingAddress2Input.val() );
						changeMapLatlng ( 'billing' );

					} else {

						changeMapLatlng ( 'shipping' );

					}

				}).trigger ( 'change' );

				destinationAutocompleteInput.on ({

					'keydown' : function ( e ) {

						var activeItem  = autoCompleteList.children ( '.' + alopeyk.wcshm.public.vars.common.activeClass ),
							activeIndex = activeItem.length ? activeItem.index() : -1;

						switch ( e.which ) {

							case 13:
								e.preventDefault();
								if ( activeItem ) {
									activeItem.trigger ( 'click' );
								}
								break;
							case 27:
								destinationAutocompleteInput.blur();
								break;
							case 38:
								e.preventDefault();
								setActiveAutocompleteItem( activeIndex - 1 );
								break;
							case 40:
								e.preventDefault();
								setActiveAutocompleteItem( activeIndex + 1 );
								break;

						}

					},
					'change paste keyup input propertychange' : function ( e ) {

						if ( alopeyk.wcshm.public.vars.maps.autocompleteInputValue != destinationAutocompleteInput.val()  ) {

							autoCompleteList.empty();
							alopeyk.wcshm.public.vars.maps.autocompleteInputValue = destinationAutocompleteInput.val();

							if ( alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout ) {
								clearTimeout(  alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout );
							}

							alopeyk.wcshm.public.vars.maps.autocompleteKeyupTimeout = setTimeout ( function () {

								if ( alopeyk.wcshm.public.vars.maps.autocompleteConnection ) {
									alopeyk.wcshm.public.vars.maps.autocompleteConnection.abort();
								}

								destinationAutocompleteInputWrapper.addClass ( alopeyk.wcshm.public.vars.common.loadingClass );
								alopeyk.wcshm.public.vars.maps.autocompleteConnection = $j.post ( alopeyk.wcshm.public.vars.common.info.ajaxOptions.url, {

									nonce        : alopeyk.wcshm.public.vars.common.info.ajaxOptions.nonce,
									action       : alopeyk.wcshm.public.vars.common.info.alopeyk.wcshm.id,
									request      : 'suggest_address',
									authenticate : true,
									input        : destinationAutocompleteInput.val(),
									lat          : map.getCenter().lat,
									lng          : map.getCenter().lng,

								}, function ( response ) {

									if ( response && response.success && response.data.length ) {

										destinationAutocompleteInput.focus();
										
										for ( var i = 0; i < response.data.length; i++ ) {

											var itemLat      = parseFloat( response.data[i].lat ),
												itemLng      = parseFloat( response.data[i].lng ),
												itemLocation = { lat : itemLat, lng : itemLng },
												itemAddress  = response.data[i].address,
												itemCity     = response.data[i].city,

												resultItem   = $j( '<li>' )
												.addClass( alopeyk.wcshm.public.fn.addPrefix ( alopeyk.wcshm.public.vars.maps.destinationLocatorAutocompleteResultClass ) )
												.attr ( 'title', itemAddress )
												.data ({
													city     : itemCity,
													address  : itemAddress,
													location : itemLocation
												})
												.text ( itemAddress )
												.on ({

													'click' : function () {

														var city     = $j( this ).data ( 'city' ),
															address  = $j( this ).data ( 'address' ),
															location = $j( this ).data ( 'location' );

														destinationAutocompleteInput.blur();
														destinationAutocompleteInput.val ( address );
														destinationAddressInput.val ( address );
														map.setView ( location );
														map.setZoom ( alopeyk.wcshm.public.vars.maps.defaultZoom );
														destinationLatInput.val ( location.lat );
														destinationLngInput.val ( location.lng );
														alopeyk.wcshm.public.vars.maps.autocompleteInputValue = address;

														shippingAddress1Input.val ( address );
														shippingAddress2Input.val ( '' );

														if ( shipToDifferentAddressInput.length && ! shipToDifferentAddressInput.prop ( 'checked' ) ) {

															if ( billingAddress1Input.length )
																billingAddress1Input.val ( address );

															if ( billingAddress2Input.length )
																billingAddress2Input.val ( '' );

														}

														fetchAddressFromLocation();

													},

													'hover' : function () {

														setActiveAutocompleteItem ( $j( this ).index() );

													}

												});

											autoCompleteList.append ( resultItem );

										}

									}

								}).always ( function () {

									destinationAutocompleteInputWrapper.removeClass ( alopeyk.wcshm.public.vars.common.loadingClass );

								});

							}, alopeyk.wcshm.public.vars.maps.autoCompleteKeyupDelay );

						}

					}

				});

				destinationLocatorCta.on ( 'click', function () {

					alopeyk.wcshm.public.fn.setCurrentGeolocation ( true );

				});

				alopeyk.wcshm.public.fn.setCurrentGeolocation();

			};

			if ( editAddressButton.length && editAddressButton.is ( ':visible' ) ) {

				editAddressButton.on ( 'click', function () {

					initialize();
					
				});

			} else {

				initialize();

			}

		},

		init : function () {

			

		},

		

	};



	/*=========================================
	=            Calling Functions            =
	=========================================*/

	/**
	 *
	 * Registered Events
	 *
	 **/

	$j( document ).on ({

		'ready' : function ( e ) {

			alopeyk.wcshm.public.fn.init();

		}

	});

	$j( window ).on ({

		'load' : function ( e ) {

			alopeyk.wcshm.public.fn.initMaps();

		},

	});

})( jQuery );
