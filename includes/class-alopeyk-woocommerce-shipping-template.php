<?php

/**
 * Define the templating functionality.
 *
 * Loads external template parts
 * while passing local data to them.
 *
 * @link       https://alopeyk.com
 * @since      1.0.0
 *
 * @package    Alopeyk_WooCommerce_Shipping
 * @subpackage Alopeyk_WooCommerce_Shipping/includes
 * @author     Alopeyk <dev@alopeyk.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Alopeyk_WooCommerce_Shipping_Template' ) ) {
	return;
}

/**
 * @since 1.0.0
 */
class Alopeyk_WooCommerce_Shipping_Template {

	public $slug = '';
	public $args = array();
	public $vars = array();
	protected $template = null;

	/**
	 * @since 1.0.0
	 * @param string $slug
	 * @param array  $vars
	 * @param array  $args
	 * @param string $scope
	 */
	public function __construct( $slug, array $vars = array(), array $args = array(), $scope = 'admin' ) {

		$args = wp_parse_args( $args, array(
			'cache' => false,
			'dir'   => $scope . '/partials',
		) );
		$this->slug = $slug;
		$this->args = $args;
		$this->set_vars( $vars );

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	public function get_output() {

		if ( false === $this->args['cache'] || ! $output = $this->get_cache() ) {
			ob_start();
			if ( $this->has_template() ) {
				$this->load_template( $this->locate_template() );
			}
			$output = ob_get_clean();
			if ( false !== $this->args['cache'] ) {
				$this->set_cache( $output );
			}
		}
		return $output;

	}

	/**
	 * @since  1.0.0
	 * @return boolean
	 */
	public function has_template() {

		return !! $this->locate_template();

	}

	/**
	 * @since 1.0.0
	 * @param array $vars
	 */
	public function set_vars( array $vars ) {

		$this->vars = array_merge( $this->vars, $vars );

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	protected function locate_template() {

		if ( isset( $this->template ) ) {
			return $this->template;
		}
		$this->template = ALOPEYK_PLUGIN_PATH . "{$this->args['dir']}/{$this->slug}.php";
		if ( 0 !== validate_file( $this->template ) ) {
			$this->template = '';
		}
		return $this->template;

	}

	/**
	 * @since 1.0.0
	 * @param string $template_file
	 */
	protected function load_template( $template_file ) {

		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		if ( 0 !== validate_file( $template_file ) ) {
			return;
		}
		require $template_file;

	}

	/**
	 * @since  1.0.0
	 * @return mixed
	 */
	protected function get_cache() {

		return get_transient( $this->cache_key() );

	}

	/**
	 * @since  1.0.0
	 * @param  $string $output
	 * @return boolean
	 */
	protected function set_cache( $output ) {

		return set_transient( $this->cache_key(), $output, $this->args['cache'] );

	}

	/**
	 * @since  1.0.0
	 * @return string
	 */
	protected function cache_key() {

		return 'part_' . md5( $this->locate_template() . '/' . wp_json_encode( $this->args ) );

	}


}

/**
 * @since  1.0.0
 * @param  string  $slug
 * @param  array   $vars
 * @param  boolean $echo
 * @param  string  $scope
 * @param  array   $args
 * @return string
 */
function alopeyk_get_local_template_part( $slug, array $vars = array(), $echo = false, $scope = 'admin', array $args = array() ) {

	$template = new Alopeyk_WooCommerce_Shipping_Template( $slug, $vars, $args, $scope );

	$output = $template->get_output();
	if ( $echo ) {
		echo esc_html($output);
	} else {
		return $output;
	}

}