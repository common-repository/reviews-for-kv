<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class KlantenvertellenWidget extends WP_Widget {



	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'klantenvertellen',
			'Klantenvertellen',
			array(
				'description' => __('Shows the last review', KBP_THEME)
			)
		);

		require_once(KBP_PATH . 'includes/Carbon.php');
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $wpdb;

		$current_fields = get_option('kbp_fields');

		$result = $GLOBALS['KBP_fetchXML']->get_fields($current_fields['url'], false);

		foreach ($result->children() as $child) {
			if ($child->count()) {
		    	$result_types[$child->getName()] = array();
			} else {
				$result_types[$child->getName()] = $child->__toString();
			}
		}

		$count = 0;

		# Get the max amount of reviews
		$maxReviews = get_option('kbp_html')['items_per_page'];

		# Fix the zeroth index
		$maxReviews = intval($maxReviews) - 1;

	    foreach ($child->children() as $grandchild) {
	    	if($count > $maxReviews) {
	    		break;
	    	}

	    	if ($grandchild->count()) {
	    		$result_types[$child->getName()][$grandchild->getName()][$count] = array();


		    	foreach ($grandchild->children() as $greatgrandchild) {
		    		$result_types[$child->getName()][$grandchild->getName()][$count][$greatgrandchild->attributes()->__toString()] = $greatgrandchild->__toString();
		    	}


			} else {
				$result_types[$child->getName()][$grandchild->getName()] = $grandchild->__toString();
			}

			$count++;
	    }

		# Get the reviews
		$last_reviews = $result_types['resultaten']['resultaat'];

		# Start the output of the widget
		require_once(KBP_PATH . 'templates/start-klantenvertellenwidget.php');

		for( $i=0; $i<$maxReviews+1; $i++ ) {

			$review = $last_reviews[ $i ];
			$noborder = ($i === 0) ? ' no-border' : '';

			require(KBP_PATH . 'templates/klantenvertellenwidget.php');

		}

		require_once(KBP_PATH . 'templates/end-klantenvertellenwidget.php');

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		echo '<p>' . __('This widget shows the latest reviews', KBP_THEME) . '</p>';

	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

	}

}