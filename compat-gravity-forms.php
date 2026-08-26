<?php
/**
 * Plugin Name: Bojaco Gravity Forms Compatibility
 * Description: Checks for any namespaced 'add_custom_css_classes' filter on 'gform_submit_button' and replaces it with 'bojaco\custom_btn_class'.
 * Version: 1.0.0
 * Author: Niklas
 *
 * @package Bojaco_Compat
 */

namespace bojaco;

/**
 * Filter the gravity forms submit button to add CSS classes.
 *
 * @param string $button The HTML markup of the submit button.
 * @return string The updated HTML markup of the submit button.
 */
function custom_btn_class( $button ) {
	$fragment = \WP_HTML_Processor::create_fragment( $button );
	$fragment->next_token();
	$fragment->add_class( 'btn' );

	return $fragment->get_updated_html();
}

/**
 * Replace any registered 'add_custom_css_classes' filter on 'gform_submit_button'
 * with the one from this file ('bojaco\custom_btn_class').
 */
function replace_gravity_forms_custom_css_filter() {
	global $wp_filter;

	if ( ! isset( $wp_filter['gform_submit_button'] ) ) {
		return;
	}

	$wp_hook    = $wp_filter['gform_submit_button'];
	$to_replace = array();

	foreach ( $wp_hook->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $idx => $callback_data ) {
			$callback = $callback_data['function'];

			// Check if callback is a string and matches the pattern *add_custom_css_classes.
			if ( is_string( $callback ) && preg_match( '/(^|\\\\)add_custom_css_classes$/', $callback ) ) {
				$to_replace[] = array(
					'callback' => $callback,
					'priority' => $priority,
				);
			}
		}
	}

	foreach ( $to_replace as $item ) {
		remove_filter( 'gform_submit_button', $item['callback'], $item['priority'] );
		add_filter( 'gform_submit_button', 'bojaco\custom_btn_class', $item['priority'], 1 );
	}
}

// Run on 'init' and 'wp_loaded' to replace as early as possible after registration.
add_action( 'init', 'bojaco\replace_gravity_forms_custom_css_filter', 9999 );
add_action( 'wp_loaded', 'bojaco\replace_gravity_forms_custom_css_filter', 9999 );

/**
 * Filter callback to also perform the replacement on the fly when the filter runs,
 * catching any late-registered filters.
 *
 * @param string $button The HTML markup of the submit button.
 * @return string The HTML markup of the submit button.
 */
function replace_gravity_forms_custom_css_filter_filter( $button ) {
	replace_gravity_forms_custom_css_filter();
	return $button;
}
add_filter( 'gform_submit_button', 'bojaco\replace_gravity_forms_custom_css_filter_filter', -9999, 1 );
