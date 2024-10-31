<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'KBP_admin' ) ) {

	# This class contains the admin part of the KBP plugin
	class KBP_admin {
		protected $message;
		protected $errorMessage;

		# Let's fire up the class
		function __construct() {

			add_action( 'admin_menu',			 array( $this, 'add_KBP_admin_menu' ), 5 );
			add_action( 'admin_init',			 array( $this, 'KBP_register_settings' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 15 );
			add_filter( 'plugin_action_links_' . KBP_BASENAME, array( $this, 'add_action_links' ) );

		}


		# Register settings
		function KBP_register_settings() {

			register_setting(KBP_FOLDER, 'kbp_html');
			register_setting(KBP_FOLDER, 'kbp_real_reaction_field', 'trim');

		}



		# Add the menu option
		function add_KBP_admin_menu() {

			add_menu_page( 'Klantenvertellen ' . __( 'by Prodes', KBP_THEME ), 'Klantenvertellen', 'manage_options', 'kbp-configurator', array($this, 'load_configurator_page'), 'dashicons-groups' );
			add_submenu_page( 'kbp-configurator', '', __( 'Settings', KBP_THEME ), 'manage_options', 'kbp-settings', array($this, 'load_settings_page') );

			global $submenu;

			# Change our name in the menu
			if ( isset( $submenu[ 'kbp-configurator' ] ) ) {
				$submenu[ 'kbp-configurator' ][ 0 ][ 0 ] = __( 'Configurator', KBP_THEME );
			}

		}


		# Manage options
		function load_configurator_page() {

			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			# Handle the post
			if( isset( $_POST[ 'submit' ] ) ) {

				# Check nonce
				if( isset( $_POST[ KBP_FOLDER . '_nonce' ] ) ) {

					if ( isset( $_POST['general'] ) && isset( $_POST[ 'loopsingle' ] ) && isset( $_POST[ 'loopreview' ] ) && isset( $_POST[ 'items_per_page' ] ) ) {

						# Get the selected color
						$current_color = sanitize_text_field( $_POST[ 'color' ] );

						# Check if our field has 7 characters (#000000)
						if ( strlen( $current_color ) > 7 ) {
							$current_color = substr( $current_color, 0, 7 );
						}

						# Sanitize all input
						$general = esc_html( stripslashes( $_POST[ 'general' ] ) );
						$loop_single = esc_html( stripslashes( $_POST[ 'loopsingle' ] ) );
						$loop_review = esc_html( stripslashes( $_POST[ 'loopreview' ] ) );

						# This is from a select input
						$items_per_page = intval( $_POST[ 'items_per_page' ] );
						$items_on_homepage = intval( $_POST[ 'items_on_homepage' ] );

						# Store the reaction if there is a reaction for this XML
						if ( isset( $_POST[ 'show_reaction' ] ) && isset( $_POST[ 'which_reaction' ] ) ) {

							# Checkbox
							$show_reaction = ( $_POST[ 'show_reaction' ] == "on" ) ? 'checked="checked"' : '';
							# Store the reaction field
							$which_reaction = sanitize_text_field( $_POST[ 'which_reaction' ] );

						} else {

							# Set them to be empty if it was not sent
							$show_reaction = '';
							$which_reaction = '';

						}


						# Store the options for that XML in the database
						$placeholder = array();

						# This is our escaped color field
						$placeholder['current_color'] = $current_color;
						# This is our escaped HTML
						$placeholder['general'] = $general;
						$placeholder['loop_single'] = $loop_single;
						$placeholder['loop_review'] = $loop_review;
						# Don't allow more than 10 ever
						$placeholder['items_per_page'] = ($items_per_page > 10) ? 10 : $items_per_page;
						# Don't allow more than 3 ever
						$placeholder['items_on_homepage'] = ($items_on_homepage > 3) ? 3 : $items_on_homepage;
						# This is always safe
						$placeholder['show_reaction'] = $show_reaction;

						# Update our data in the database
						update_option('kbp_html', $placeholder);

						# Set the reaction field in the database
						update_option('kbp_real_reaction_field', $which_reaction);

						$this->message = __( 'Saved changes', KBP_THEME );

					}

				} elseif( !wp_verify_nonce( $_POST[ KBP_FOLDER . '_nonce' ], KBP_FOLDER ) ) {

					# Invalid nonce
					$this->errorMessage = __( 'Settings NOT saved due to a failed security check.', KBP_THEME );

				} else {

					# Something else went wrong, let's not save
					$this->errorMessage = __( 'Settings NOT saved due to a failed security check.', KBP_THEME );

				}
			}

			require_once( KBP_PATH . 'admin/pages/kbp-configurator.php' );

		}


		function load_settings_page() {

			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			}

			# Handle the post
			if( isset($_POST['submit']) ) {

				# Check nonce
				if ( isset( $_POST[ KBP_FOLDER . '_nonce' ] ) && isset($_POST[ 'url' ] ) ) {

					# Save it
					$url = $_POST['url'];

					# Escape the URL, only allow HTTP(S) connections
					$esc_url = esc_url( $url , array( 'http', 'https' ) );

					# Check if it was a valid URL
					if (empty($esc_url)) {

						# Empty string, so not a valid URL.
						$result = false;

					} else {

						# Get the fields from the XML file, if the XML file is invalid, we will get false back.
						$result = $GLOBALS['KBP_fetchXML']->get_fields($esc_url, true);

					}

					if ($result === false) {

						$this->errorMessage = __('Settings NOT saved due to an invalid XML file. Please try again with the correct URL.', KBP_THEME);

					} else {

						# Initialize the placeholder
						$result_types = array();

						# Setup some defaults
						$result_types["date"] = new DateTime();
						$result_types["american_date"] = '%american_date%';
						$result_types["url"] = sanitize_text_field($esc_url);

						# Initialize a placeholder for the reactions
						$possible_reactions = array();

						# Get the children from the XML object
						$children = $result->children();

						# This loops through all fields in the XML to make sure we get all fields.
						foreach ( $children as $child ) {

							$grandchildren = $child->children();

							$result_types[$child->getName()] = array();

							$child_name = (string)$child->getName();

							foreach ( $grandchildren as $grandchild ) {
								$grandchild_name = $grandchild->getName();

								if ( !isset( $result_types[ $child->getName() ][ $grandchild->getName() ] ) ) {

									$result_types[$child_name][$grandchild_name] = array();

								}


								foreach ( $grandchild->children() as $greatgrandchild ) {
									$arr = $greatgrandchild->attributes();

									if ( empty($arr) ) {

										$greatgrandchild_name = (string)$greatgrandchild->getName();

										$possible_reactions[] = $greatgrandchild_name;

										if ( !in_array( $greatgrandchild_name, $result_types[ $child->getName() ][ $grandchild->getName() ] ) ) {

											$result_types[ $child->getName() ][ $grandchild->getName() ][] = $greatgrandchild_name;

										}

									} else {

										$greatgrandchild_name = (string)$greatgrandchild->attributes();

										if ( !in_array( $greatgrandchild->attributes(), $result_types[ $child->getName() ][ $grandchild->getName() ] ) ) {

											$result_types[ $child->getName() ][ $grandchild->getName() ][] = $greatgrandchild_name;

										}

									}

								}

							}

						}

						# Initialize a placeholder
						$normalized_possible_reactions = array();
						# Loop through the reaction possibilities to display a 'possible' reaction field for the user to choose from later.
						foreach ( $possible_reactions as $key => $value ) {

							# If there is no key, it might be a reaction field
							if ( !array_key_exists( $value, $normalized_possible_reactions ) ) {

								$normalized_possible_reactions[ $value ] = $value;

							}

						}

						# Store the options for that customer in the database
						update_option( 'kbp_fields', $result_types );
						update_option( 'kbp_possible_reactions', $normalized_possible_reactions );
						# Remove the kbp_xml_data option to force a refresh of our cached data
						delete_option( 'kbp_xml_data' );

						$this->message = sprintf( __( '%d fields of Klantenvertellen have been indexed', KBP_THEME ), count( $result_types, COUNT_RECURSIVE ) );

					}

				} elseif( !wp_verify_nonce( $_POST[KBP_FOLDER . '_nonce'], KBP_FOLDER ) ) {

						# Invalid nonce
						$this->errorMessage = __( 'Settings NOT saved due to a failed security check.', KBP_THEME );

				} else {

					# Well, something went wrong.
					$this->errorMessage = __( 'Settings NOT saved due to a failed security check.', KBP_THEME );

				}

			}

			require_once( KBP_PATH . 'admin/pages/kbp-settings.php' );

		}


		function load_scripts() {

			# Load our CSS
			wp_enqueue_style('kbp_style', KBP_DIR . 'library/css/klantvertellen.css' );

			# Load our JS
			wp_enqueue_script('kbp_general', KBP_DIR . 'library/js/kbp-general.js', array( 'jquery', 'wp-color-picker' ) );

			# Load the Wordpress Colorpicker
			wp_enqueue_style('wp-color-picker');

		}

		function add_action_links ( $links ) {

			# Set up some custom links for our plugin
			$mylinks = array(
				'configurator' 	=> '<a href="' . admin_url( 'admin.php?page=kbp-configurator' ) . '">'. __('Configurator', KBP_THEME) .'</a>',
				'settings' 		=> '<a href="' . admin_url( 'admin.php?page=kbp-settings' ) . '">'. __('Settings', KBP_THEME) .'</a>',
			);

			# Merge our links with the custom links
			return array_merge( $links, $mylinks );
		}


	}

}