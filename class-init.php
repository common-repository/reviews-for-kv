<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if(!class_exists('KBP_init')) {

	class KBP_init {

		function __construct() {

			# Add our actions
			add_action('wp_enqueue_scripts',	array($this, 'load_scripts'));

		}

		# Load our scripts
		function load_scripts() {

			wp_enqueue_style('kbp_style', KBP_DIR . 'library/css/klantvertellen.css');

		}

	}

}