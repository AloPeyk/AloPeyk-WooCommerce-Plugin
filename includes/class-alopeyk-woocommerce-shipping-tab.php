<?php

/**
 * New woocommerce shippingsettings tab for alopeyk 
 *
 * @link       https://alopeyk.com
 * @since      2.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/admin
 * @author     Alopeyk <dev@alopeyk.com>
 */

class Alopeyk_WooCommerce_Shipping_Common_Settings extends WC_Settings_Page
{
	public $form_fields = [];

	public $settings = [];

	public $required_fields = [];

	public $parentId = "";

	private $errors;

	private $helpers;

	private $empty_fields_string;

	private $wrong_key;

	private $api_key, $enabled, $environment, $endpoint_url, $endpoint_api_url, $endpoint_tracking_url, $store_name, $store_phone, $store_lat, $store_lng, $store_address, $store_city, $store_unit, $map_marker, $store_description, $store_number, $title, $cost_type, $static_cost_type, $static_cost_fixed, $static_cost_percentage, $pt_motorbike, $pt_car, $pt_cargo, $pt_cargo_s, $auto_type, $auto_type_static, $status_change, $customer_dashboard, $tehran_timezone, $return_cod, $return_cod_customer, $return_cod_customer_alert;

    /**
	 * @since 2.0.0
	 */
	public function __construct()
	{
		$this->id              = METHOD_ID;
		$this->label           = __('Alopeyk', 'alopeyk-shipping-for-woocommerce');
		$this->required_fields = array('api_key', 'store_name', 'store_phone', 'store_lat', 'store_lng', 'store_address', 'store_city');
		$this->errors          = new WP_Error();
		$this->parentId        = $this->id . '_tab_parent';

		$this->set_helpers();
		$this->init_settings();
		$this->init_form_fields();

		parent::__construct();

		add_action('woocommerce_before_settings_' . $this->id, array($this, 'before_settings'));
		add_action('woocommerce_settings_' . $this->parentId, array($this, 'test'));
	}

	/**
	 * @since 2.0.0
	 */
	public function test()
	{
		echo "<div id='{$this->parentId}'></div>";
	}

	/**
	 * @since 2.0.0
	 */
	public function before_settings()
	{
		$empty_fields = array();
		foreach ($this->required_fields as $required_field) {
			if (isset($this->form_fields[$required_field]) && empty($this->{$required_field})) {
				$empty_fields[] = $this->form_fields[$required_field]['title'];
			}
		}
		if (count($empty_fields)) {
			$this->empty_fields_string = '';
			$empty_fields = array_map(function ($field, $index) use ($empty_fields) {
				$this->empty_fields_string .= ($index == 0 ? '' : ($index == count($empty_fields) - 1 ? ' ' . __('and', 'alopeyk-shipping-for-woocommerce') . ' ' : __(',', 'alopeyk-shipping-for-woocommerce') . ' ')) . '<strong>' . $field . '</strong>';
				return $field;
			}, $empty_fields, array_keys($empty_fields));
			$this->errors->add('missing', sprintf(__('Please fill %s field(s), otherwise Alopeyk shipping method cannot be enabled.', 'alopeyk-shipping-for-woocommerce'), $this->empty_fields_string));
		}
		foreach ($this->errors->get_error_messages() as $error) {
			echo '<div class="error notice below-heading is-dismissible"><p>' . $error . '</p></div>';
		}
		// parent::admin_options();
	}

	/**
	 * @since 2.0.0
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings()
	{
		$fields = $this->form_fields;
		$settings = $this->settings;

		if (is_array($settings) && count($settings)) {
			foreach ($fields as $key => $value) {
				if (isset($settings[$key])) {
					$fields[$key]['value'] = $settings[$key];
				}

				if (is_string($key) && !isset($fields[$key]['id'])) {
					// $fields[$key]['id'] = "alopeyk_woocommerce_shipping_method_" . $key;
					$fields[$key]['id'] = $key;
				}
			}
		}

		return apply_filters('woocommerce_get_settings_' . $this->id, $fields);
	}

	/**
	 * @since 2.0.0
	 */
	public function init_settings()
	{
		$this->settings = get_option($this->get_option_key(), null);
		if (is_array($this->settings) && count($this->settings)) {
			foreach ($this->settings as $key => $setting) {
				$this->{wc_clean($key)} = $setting;
			}
		}
	}

	/**
	 * @since 2.0.0
	 */
	public function get_post_data()
	{
		if (!empty($this->data) && is_array($this->data)) {
			return $this->data;
		}
		return $_POST; // WPCS: CSRF ok, input var ok.
	}

	/**
	 * @since 2.0.0
	 */
	public function get_field_type($field)
	{
		return empty($field['type']) ? 'text' : $field['type'];
	}

	/**
	 * @since 2.0.0
	 */
	public function validate_text_field($key, $value)
	{
		$value = is_null($value) ? '' : $value;
		return wp_kses_post(trim(stripslashes($value)));
	}

	/**
	 * @since 2.0.0
	 */
	public function get_field_value($key, $field, $post_data = array())
	{
		$type      = $this->get_field_type($field);
		$post_data = empty($post_data) ? $_POST : $post_data; // WPCS: CSRF ok, input var ok.
		$value     = isset($post_data[$key]) ? $post_data[$key] : null;

		if (isset($field['sanitize_callback']) && is_callable($field['sanitize_callback'])) {
			return call_user_func($field['sanitize_callback'], $value);
		}

		// Look for a validate_FIELDID_field method for special handling.
		if (is_callable(array($this, 'validate_' . $key . '_field'))) {
			return $this->{'validate_' . $key . '_field'}($key, $value);
		}

		// Look for a validate_FIELDTYPE_field method.
		if (is_callable(array($this, 'validate_' . $type . '_field'))) {
			return $this->{'validate_' . $type . '_field'}($key, $value);
		}

		// Fallback to text.
		return $this->validate_text_field($key, $value);
	}

	/**
	 * @since 2.0.0
	 * Save settings.
	 */
	public function save()
	{
		$this->init_settings();
		$post_data = $this->get_post_data();
		$fields = $this->form_fields;

		foreach ($fields as $key => $field) {
			if (!in_array($this->get_field_type($field), ['title', 'sectionend'])) {
				try {
					$value = $this->get_field_value($key, $field, $post_data);
					if ($field['type'] == 'checkbox') {
						$this->settings[$key] = $value ? 'yes' : 'no';
					} else {
						$this->settings[$key] = $value;
					}
					
				} catch (Exception $e) {
					$this->errors->add($key, __($e->getMessage(), 'alopeyk-shipping-for-woocommerce'));
				}
			}
		}
		$api_key                  = $this->get_field_value('api_key',               $fields['api_key'],               $post_data);
		$environment              = $this->get_field_value('environment',           $fields['environment'],           $post_data);
		$endpoint['url']          = $this->get_field_value('endpoint_url',          $fields['endpoint_url'],          $post_data);
		$endpoint['api_url']      = $this->get_field_value('endpoint_api_url',      $fields['endpoint_api_url'],      $post_data);
		$endpoint['tracking_url'] = $this->get_field_value('endpoint_tracking_url', $fields['endpoint_tracking_url'], $post_data);

		if (!(empty($api_key))) {
			if ($this->helpers->authenticate(true, $api_key, true, $environment, $endpoint) && $user_data = $this->helpers->get_user_data()) {
				$phone = isset($fields['store_phone']) ? $this->get_field_value('store_phone', $fields['store_phone'], $post_data) : '';
				$name  = isset($fields['store_name'])  ? $this->get_field_value('store_name',  $fields['store_name'],  $post_data) : '';
				if (empty($phone)) {
					$this->settings['store_phone'] = $user_data->phone;
				}
				if (empty($name)) {
					$this->settings['store_name'] = $user_data->firstname . ' ' . $user_data->lastname;
				}
				$this->settings['wrong_key'] = 'no';
			} else {
				$this->settings['enabled']   = 'no';
				$this->settings['wrong_key'] = 'yes';
			}
		}
		foreach ($this->required_fields as $required_field) {
			if (!isset($this->settings[$required_field]) || empty($this->settings[$required_field])) {
				$this->settings['enabled'] = 'no';
				break;
			}
		}

        update_option($this->get_option_key(), apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings));
        return wp_redirect( (new Alopeyk_WooCommerce_Shipping_Admin())->get_settings_url(), 301 );
	}

	/**
	 * @since 2.0.0
	 */
	public function get_option_key()
	{
		return 'woocommerce_' . $this->id . '_settings';
	}

	/**
	 * @since 2.0.0
	 */
	public function set_helpers()
	{
		$this->helpers = new Alopeyk_WooCommerce_Shipping_Common();
	}

	/**
	 * @since 2.0.0
	 */
	public function init_form_fields()
	{
		$api_user_notice = '';
		$is_api_user = $this->helpers->is_api_user();
		if ($is_api_user !== true) {
			$api_user_notice = $is_api_user;
		}

		$form_fields = array(
			array(
				'title' => __( 'Alopeyk', 'alopeyk-shipping-for-woocommerce' ),
				'id'    => $this->parentId,
				'type'  => 'title',
				'desc'  => __( 'By filling the following fields and checking enabled field, Alopeyk On-demand Delivery will be included in WooCommerce shop shipping methods.', 'alopeyk-shipping-for-woocommerce' )
			),

			'enabled' => array(
				'title'   => __('Enable/Disable', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'enabled',
				'type'    => 'checkbox',
				'desc'    => __('Enable Alopeyk shipping', 'alopeyk-shipping-for-woocommerce'),
				'default' => 'no',
			),
			'api_key' => array(
				'title'       => __('API Key', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'api_key',
				'type'        => 'text',
				'default'     => '',
				'desc'        => $api_user_notice,
				'custom_attributes' => array(
					'required' => 'required'
				),
				'class'       => 'awcshm-ltr'
			),
		);
		$env_option = [];
		foreach ($this->helpers->get_endpoints_pack() as  $key => $urls_pack) {
			$env_option[$key] = __($key, 'alopeyk-shipping-for-woocommerce');
			if ($key == 'production') {
				$production_env = $urls_pack;
			}
		}
		$form_fields['environment'] = array(
			'title'       => __('Environment', 'alopeyk-shipping-for-woocommerce'),
			'type'        => 'select',
			'id' => 'environment',
			'options'     => $env_option,
			'default'     => 'production',
			'desc'        => __('Please select the appropriate plugin environment in consultation with the Alopeyk sales team.', 'alopeyk-shipping-for-woocommerce'),
		);
		$form_fields['endpoint_url'] = array(
			'title'       => __('Endpoint Url', 'alopeyk-shipping-for-woocommerce'),
			'id' => 'endpoint_url',
			'type'        => 'text',
			'default'     => $production_env['url'],
			'class'       => 'awcshm-ltr'
		);
		$form_fields['endpoint_api_url'] = array(
			'title'       => __('Endpoint API Url', 'alopeyk-shipping-for-woocommerce'),
			'id' => 'endpoint_api_url',
			'type'        => 'text',
			'default'     => $production_env['api_url'],
			'class'       => 'awcshm-ltr'
		);
		$form_fields['endpoint_tracking_url'] = array(
			'title'       => __('Endpoint Tracking Url', 'alopeyk-shipping-for-woocommerce'),
			'id' => 'endpoint_tracking_url',
			'type'        => 'text',
			'default'     => $production_env['tracking_url'],
			'class'       => 'awcshm-ltr'
		);
		if (!empty($this->helpers->get_option('api_key')) and $this->wrong_key != 'yes') {
			$form_fields['title'] = array(
				'title'       => __('Method Title', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'title',
				'type'        => 'text',
				'default'     => __('Alopeyk', 'alopeyk-shipping-for-woocommerce'),
				'desc'        => __('This controls the title which the user will see during checkout proccess.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['store_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['store_options_title'] = array(
				'id' => 'store_options_title',
				'title'       => __('Store Details', 'alopeyk-shipping-for-woocommerce'),
				'type'        => 'title',
			);
			$form_fields['store_name'] = array(
				'title'             => __('Store Name', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_name',
				'type'              => 'text',
				'desc'              => __('This is your store\'s name.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_number'] = array(
				'title'             =>  __('Store Number', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_number',
				'type'              => 'text',
				'desc'              => __('This is your store\'s number.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			);
			$form_fields['store_unit'] = array(
				'title'             => __('Store Unit', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_unit',
				'type'              => 'text',
				'desc'              => __('This is your store\'s unit.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'pattern' => '\d*',
				)
			);
			$form_fields['store_phone'] = array(
				'title'             => __('Store Phone', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_phone',
				'type'              => 'text',
				'desc'              => __('This is your store\'s phone.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'required' => 'required',
					'maxlength' => '11',
					'pattern'  => '\d*',
				)
			);
			$form_fields['store_lat'] = array(
				'title'             => __('Store Latitude', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_lat',
				'type'              => 'text',
				'desc'              => __('Latitude for specified store address.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_lng'] = array(
				'title'             => __('Store Longitude', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_lng',
				'type'              => 'text',
				'desc'              => __('Longitude for specified store address.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'required' => 'required'
				)
			);
			$form_fields['store_city'] = array(
				'title'             => __('Store City', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_city',
				'type'              => 'text',
				'class'             => 'disabled hide-parent-row',
				'default'           => '',
				'css'               => 'pointer-events: none;',
				'desc'              => __('This will be automatically fetched when you specify your store location via moving below map marker.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['store_address'] = array(
				'title'             => __('Store Address', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_address',
				'type'              => 'text',
				'class'             => 'disabled hidden',
				'default'           => '',
				'css'               => 'pointer-events: none;',
				'desc_tip'          => __('Please specify the exact address for your stock, because it will be used as origin address. The origin address will later be used for picking the packages by the courier and calculation of dynamic shipping cost.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'data-autocomplete-placeholder' => __('Please enter your address ...', 'alopeyk-shipping-for-woocommerce')
				)
			);
			$form_fields['store_description'] = array(
				'title'       => __('Origin Description', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'store_description',
				'type'        => 'textarea',
				'desc_tip'    => __('This will be used as origin description shown on couriers device. In most cases it consists of store address details.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['map_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['map_options_title'] = array(
				'title' => __('Map Options', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'map_options_title',
				'type'  => 'title',
			);
			$form_fields['map_marker'] = array(
				'title'             => __('Marker Image', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'map_marker',
				'type'              => 'text',
				'class'             => 'input-upload hidden',
				'desc_tip'          => __('You can upload your desired marker image here to be used instead of Cedar\'s default marker image on address maps around your store.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'data-upload-label'   => __('Upload', 'alopeyk-shipping-for-woocommerce'),
					'data-remove-label'   => '<i class="dashicons dashicons-trash"></i>',
				)
			);
			$form_fields['cost_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['cost_options_title'] = array(
				'title' => __('Cost Options', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'cost_options_title',
				'type'  => 'title',
			);
			$form_fields['cost_type'] = array(
				'title'       => __('Cost Type', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'cost_type',
				'type'        => 'select',
				'options'     => array(
					'dynamic' => __('Dynamic Cost', 'alopeyk-shipping-for-woocommerce'),
					'static'  => __('Static Cost', 'alopeyk-shipping-for-woocommerce')
				),
				'default'     => 'dynamic',
				'desc'        => __('This option will specify that whether the shipping cost is a static value or should be fetched from Alopeyk API.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['static_cost_type'] = array(
				'title'       => __('Static Cost Type', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'static_cost_type',
				'type'        => 'select',
				'options'     => array(
					'percentage' => __('Percentage', 'alopeyk-shipping-for-woocommerce'),
					'fixed'      => __('Fixed', 'alopeyk-shipping-for-woocommerce')
				),
				'default'     => 'fixed',
				'desc'        => __('This option will specify that wether the shipping cost is a fixed value or a percentage of cart price.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['static_cost_fixed'] = array(
				'title'       => __('Fixed Cost', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'static_cost_fixed',
				'type'        => 'text',
				'default'     => '0',
				'desc'        => __('This option defines the fixed cost should be added to total cart amount. (IRR)', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['static_cost_percentage'] = array(
				'title'       => __('Percentage Cost', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'static_cost_percentage',
				'type'        => 'text',
				'default'     => '0',
				'desc'        => __('This option defines the percentage of cart amount that should be added to total cart amount.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['transport_types_settings'] = array(
				'title' => __('Transportation Type Settings', 'alopeyk-shipping-for-woocommerce'),
				'id'    => 'transport_types_settings',
				'type'  => 'title',
			);
			foreach ($this->helpers->get_transport_types(false) as  $key => $transport_type) {
				$form_fields['pt_' . $key] = array(
					'title'       => __('Ship via', 'alopeyk-shipping-for-woocommerce') . ' ' . __($transport_type['label'], 'alopeyk-shipping-for-woocommerce'),
					'id' => 'pt_' . $key,
					'type'        => 'checkbox',
					'default'     => 'yes',
					'desc'        => __('Enabled', 'alopeyk-shipping-for-woocommerce'),
					'desc_tip'    => sprintf(__('Total weight of the package should be up to %s kg and its dimensions in centimeters should be up to %s for width, %s for height and %s for length to be allowed to be shipped by this method.', 'alopeyk-shipping-for-woocommerce'), $transport_type['limits']['max_weight'] / 1000, $transport_type['limits']['max_width'], $transport_type['limits']['max_height'], $transport_type['limits']['max_length']),
				);
			}
			$form_fields['auto_type'] = array(
				'title'       => __('Smart Switch', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'auto_type',
				'type'        => 'checkbox',
				'default'     => 'yes',
				'desc'        => __('Show only most optimal shipping method', 'alopeyk-shipping-for-woocommerce'),
				'desc_tip'    => __('If not enabled, all possible shipping methods will be shown in the checkout page.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'data-checkbox-toggle-target' => 'toggler-checkbox-id-auto_type_price',
				),
			);
			$form_fields['auto_type_static'] = array(
				'title'       => __('&nbsp;', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'auto_type_static',
				'type'        => 'checkbox',
				'default'     => 'yes',
				'desc'        => __('Use default price set for choosing most optimal method', 'alopeyk-shipping-for-woocommerce'),
				'desc_tip'    => __('If not enabled, real-time price will be fetched from Alopeyk’s API for each shipping method in order to choose the most optimal one. This may make the process a bit slower.', 'alopeyk-shipping-for-woocommerce'),
				'custom_attributes' => array(
					'data-checkbox-toggle-id' => 'toggler-checkbox-id-auto_type_price',
				),
			);
			$form_fields['order_options_title_spacer'] = array(
				'type'  => 'title',
				'title' => '&nbsp;'
			);
			$form_fields['order_options_title'] = array(
				'title' => __('Order Options', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'order_options_title',
				'type'  => 'title',
			);
			$form_fields['status_change'] = array(
				'title'             => __('Status Change', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'status_change',
				'desc'              => __('Enabled', 'alopeyk-shipping-for-woocommerce'),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'desc_tip'          => __('Check this checkbox only if you want WooCommerce orders\' status to be changed based on changes being made in Alopeyk delivery status.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['customer_dashboard'] = array(
				'title'             => __('Dashboard Tracking', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'customer_dashboard',
				'desc'              => __('Enabled', 'alopeyk-shipping-for-woocommerce'),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'desc_tip'          => __('Check this checkbox only if you want your customers to be able to track their delivering from their account dashboard.', 'alopeyk-shipping-for-woocommerce'),
			);
			$form_fields['tehran_timezone'] = array(
				'title'             => __('Use Tehran TimeZone', 'alopeyk-shipping-for-woocommerce'),
				'id' => 'tehran_timezone',
				'desc'              => __('Enabled', 'alopeyk-shipping-for-woocommerce'),
				'type'              => 'checkbox',
				'default'           => 'yes',
				'desc_tip'          => __('Check this checkbox only if you want to use “Tehran TomeZone” for Alopeyk orders, otherwise default Wordpress timezone will be used.', 'alopeyk-shipping-for-woocommerce'),
			);
			if (is_admin()) {
				$wc_payment_gateways = @WC()->payment_gateways;
				if ($wc_payment_gateways) {
					$gateways = $wc_payment_gateways->get_available_payment_gateways();
					if ($gateways && count($gateways)) {
						$form_fields['payment_options_title_spacer'] = array(
							'type'  => 'title',
							'title' => '&nbsp;'
						);
						$form_fields['payment_options_title'] = array(
							'title' => __('Payment Options', 'alopeyk-shipping-for-woocommerce'),
							'id' => 'payment_options_title',
							'type'  => 'title',
						);
						foreach ($gateways as $gateway) {
							$form_fields['return_' . $gateway->id] = array(
								'title'             => $gateway->title,
								'id' => 'return_' . $gateway->id,
								'desc'              => __('Has return', 'alopeyk-shipping-for-woocommerce'),
								'type'              => 'checkbox',
								'default'           => $gateway->id == 'cod' ? 'yes' : 'no',
								'desc_tip'          => __('Check this checkbox only if you need this payment method to have return trip. For example if want the courier to take the money from the customer after delivering the package and bring it back to your store.', 'alopeyk-shipping-for-woocommerce'),
								'custom_attributes' => array(
									'data-checkbox-toggle-target' => 'toggler-checkbox-id-' . $gateway->id
								)
							);
							$form_fields['return_' . $gateway->id . '_customer'] = array(
								'desc'              => __('Customer should pay for return cost', 'alopeyk-shipping-for-woocommerce'),
								'id' => 'return_' . $gateway->id . '_customer',
								'type'              => 'checkbox',
								'default'           => 'no',
								'desc_tip'          => __('Check this checkbox only if you want customers to pay for return costs whenever this payment method is chosen. If not checked, the cost will be deducted from your Alopeyk account.', 'alopeyk-shipping-for-woocommerce'),
								'custom_attributes' => array(
									'data-checkbox-toggle-id' => 'toggler-checkbox-id-' . $gateway->id,
									'data-checkbox-toggle-target' => 'toggler-checkbox-id-' . $gateway->id . '-child'
								)
							);
							$form_fields['return_' . $gateway->id . '_customer_alert'] = array(
								'desc'              => __('Warn customer about price change', 'alopeyk-shipping-for-woocommerce'),
								'id' => 'return_' . $gateway->id . '_customer_alert',
								'type'              => 'checkbox',
								'default'           => 'yes',
								'desc_tip'          => __('Check this checkbox only if you want inform customers about the return cost that will be added to total price.', 'alopeyk-shipping-for-woocommerce'),
								'custom_attributes' => array(
									'data-checkbox-toggle-id' => 'toggler-checkbox-id-' . $gateway->id . '-child'
								)
							);
						}
					}
				}
			}
		}
		$form_fields[] = array(
			'type' => 'sectionend'
		);

		$this->form_fields = $form_fields;
	}
}
