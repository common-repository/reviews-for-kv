<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'KBP_shortcode' ) ) {

	class KBP_shortcode {

		function __construct() {

			# Add our shortcodes
			add_shortcode('kbp_general', 	array($this, 'do_kbp_general'));
			add_shortcode('kbp_loop', 		array($this, 'do_kbp_loop'));
			add_shortcode('kbp_review', 	array($this, 'do_kbp_review'));

		}


		# This function shows the general averages
		function do_kbp_general() {

			# Get our current fields from the database
			$current_fields = get_option('kbp_fields');
			# Get our HTML from the database
			$current_html = get_option('kbp_html');

			# Check if we have something to display
			if ($current_html === false || $current_fields === false) {

				# If we don't have any data to display yet, tell them.
				echo __('Get the XML fields from the URL and create your HTML for the shortcodes in the configurator.', KBP_THEME);
				return;

			}

			# Get the XML data
			$result_types = $GLOBALS['KBP_functions']->getXmlData();

			# Get the general data
			$general_array = $GLOBALS['KBP_functions']->getArrayFromFields($current_fields['statistieken'], '_');
			# Only use the general data for this shortcode
			$data = $result_types['statistieken'];

			# Get the HTML for this shortcode
			$general_html = $current_html['general'];

			# Start the HTML output with the style
			$result = $this->outputCustomStyle();

			# Create our modified HTML from the HTML, fields and results
			$result .= $GLOBALS['KBP_functions']->changeTagsToHTML($general_html, $general_array, $data);

			# Replace empty values with predefined surroundings
			preg_match_all("/%.*?%/", $result, $matches);

			foreach($matches[0] as $empty_value) {

				$tag = str_replace('%', '', $empty_value);
				$result = str_replace($empty_value, sprintf(__('No %s available', KBP_THEME), $tag), $result);

			}

			# Return our HTML
			return htmlspecialchars_decode( $result );

		}


		# This function shows the individual reviews
		function do_kbp_loop() {

			# Get our current fields from the database
			$current_fields = get_option('kbp_fields');
			# Get our HTML from the database
			$current_html = get_option('kbp_html');

			# Check if we have something to display
			if ($current_html === false || $current_fields === false) {

				# If we don't have any data to display yet, tell them.
				echo __('Get the XML fields from the URL and create your HTML for the shortcodes in the configurator.', KBP_THEME);
				return;

			}

			# Check if we need to show reactions from the company
			$show_reactions = ($current_html['show_reaction'] == 'checked="checked"') ? true : false;

			# Get the XML data
			$result_types = $GLOBALS['KBP_functions']->getXmlData();

			# Get the data for the results
			$loop_array = $GLOBALS['KBP_functions']->getArrayFromFields($current_fields['resultaten'], '_', ':');
			# Only get our loop results
			$data = $result_types['resultaten'];

			# Get the HTML for the loop
			$loop_html = $current_html['loop_single'];

			# Get the reaction field
			$reactions_field = get_option('kbp_real_reaction_field');;

			# init style string
			$results = $this->outputCustomStyle();

			# Initialize our count
			$count = 0;

			foreach( $data['resultaat'] as $index => $value ) {

				# Check if we need to show more reviews
				if ( $current_html['items_per_page'] > 0 && ( $count > $current_html['items_per_page'] ) ) {
					# Stop showing reviews.
					break;

				}

				$count++;

				$results .= $GLOBALS['KBP_functions']->changeTagsToHTML($loop_html, $loop_array, $value, '_', ':' );

				if( $show_reactions ) {
					# If we should show reactions, and we have a reaction, show it
					if( isset( $value[$reactions_field] ) ) {

						$results .= '<div class="klantenvertellen-loop-reaction">' . $value[$reactions_field] . '</div>';

					}
				}

			}

			# Replace empty values with predefined surroundings
			preg_match_all("/%.*?%/", $results, $matches);

			foreach($matches[0] as $empty_value) {

				$tag = str_replace('%', '', $empty_value);
				$results = str_replace($empty_value, sprintf(__('No %s available', KBP_THEME), $tag), $results);

			}

			return htmlspecialchars_decode( $results );

		}


		# This function shows individual reviews for use on a homepage
		function do_kbp_review() {

			# Get our current fields from the database
			$current_fields = get_option('kbp_fields');
			# Get our HTML from the database
			$current_html = get_option('kbp_html');

			# Check if we have something to display
			if ($current_html === false || $current_fields === false) {

				# If we don't have any data to display yet, tell them.
				echo __('Get the XML fields from the URL and create your HTML for the shortcodes in the configurator.', KBP_THEME);
				return;

			}

			# Get XML data
			$result_types = $GLOBALS['KBP_functions']->getXmlData();

			# Get our fields
			$loop_array = $GLOBALS['KBP_functions']->getArrayFromFields($current_fields['resultaten'], '_', ':');
			# Get the result data
			$data = $result_types['resultaten'];

			# Get the HTML for the review
			$loop_html = $current_html['loop_review'];

			# initialize empty string
			$results = $this->outputCustomStyle();

			# Loop through the amount of reviews we want to show
			for ($i = 0; isset($data['resultaat'][$i]) && $i < $current_html['items_on_homepage']; $i++) {
				# Create a surround div which displays the correct size for the review
				$results .= '<div class="kbp-column-'. 12 / $current_html['items_on_homepage'] .'">';

				# Create the HTML for this review
				$results .= $GLOBALS['KBP_functions']->changeTagsToHTML($loop_html, $loop_array, $data['resultaat'][$i], '_', ':' );

				# Close the div
				$results .= '</div>';
			}

			# Replace empty values with predefined surroundings
			preg_match_all("/%.*?%/", $results, $matches);

			foreach($matches[0] as $empty_value) {

				$tag = str_replace('%', '', $empty_value);
				$results = str_replace($empty_value, sprintf(__('No %s available', KBP_THEME), $tag), $results);

			}

			return htmlspecialchars_decode( $results );

		}

		function outputCustomStyle() {

			# Get our options from the database
			$current_options = get_option('kbp_html');

			# Pick our color
			$current_color = $current_options['current_color'];

			# If there is no color, output a default that is black.
			if ( empty( $current_color ) ) {

				$current_color = '#000000';

			}

			return '<style>
						 span.klantenvertellen-big-average,
						 span.klantenvertellen-review-big-average {
						 	color: '. $current_color .';
						 }
					</style>';


		}

	}
}