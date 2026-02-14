<?php
/**
 * Utility class
 */
class WPSD {
	protected static $_instance = null;
	protected $custom_js        = '/etc/wpsd-custom.js';

	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * Print scripts from /etc/wpsd-custom.js if they exist
	 * @example echo wpsd()->user_js(); 
	 */
	public function user_js() {
		if ( file_exists( $this->custom_js ) ) {
			return sprintf( "\n<script id='user-js'>\n%s</script>\n", file_get_contents( $this->custom_js ) );
		}
	}
}

function wpsd() {
	return WPSD::get_instance();
}
