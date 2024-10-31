<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap kbp-backend">
	<h1><?php echo __('Klantenvertellen configurator', KBP_THEME); ?></h1>

<?php
$current_fields = get_option('kbp_fields');

if ($current_fields !== false) {

	# Build a selector for the general section
	$general_select = $GLOBALS['KBP_functions']->getArrayFromFields($current_fields['statistieken'], '_');

	# Build a selector for the single reviews
	$loopsingle_select = $GLOBALS['KBP_functions']->getArrayFromFields($current_fields['resultaten'], '_', ':');

}

# Get all the current data
$current_html 	= get_option( 'kbp_html' );
$current_color  = '#000000';
$general 		= '';
$loop_single 	= '';
$loop_review 	= '';

# If we have data
if ($current_html !== false && !empty( $current_html ) ) {

		# Get the data from the option
		# Get the color they picked.
		$current_color 	= $current_html['current_color'];

		# Sanitize the textfields
		$general	 	= stripslashes( $current_html['general'] );
		$loop_single 	= stripslashes( $current_html['loop_single'] );
		$loop_review	= stripslashes( $current_html['loop_review'] );

		# Set the options for these boxes
		$items_per_page = $current_html['items_per_page'];
		$items_on_homepage = $current_html['items_on_homepage'];

		$show_reaction = $current_html['show_reaction'];

}
?>

<?php
if (isset($this->message)):
?>
	<div class="updated fade">
		<p>
			<?php echo $this->message; ?>
		</p>
	</div>
<?php
endif;
if (isset($this->errorMessage)):
?>
	<div class="error fade">
		<p>
			<?php echo $this->errorMessage; ?>
		</p>
	</div>
<?php
endif;
?>

    <div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
	        <!-- Content -->
	        <div id="post-body-content">

	        	<p>
					<?php echo __( 'This plugin uses three different shortcodes to display the reviews from klantenvertellen.nl', KBP_THEME ); ?><br/>
					<?php echo __( '"kbp_general" - displays the general statistics', KBP_THEME ); ?><br/>
					<?php echo __( '"kbp_loop"    - displays the reviews from customers', KBP_THEME ); ?><br/>
					<?php echo __( '"kbp_review"  - displays the last few reviews from customer depending on the settings', KBP_THEME ); ?><br/><br/>
					<?php echo __( 'Usage:', KBP_THEME ); ?><br/>
					<?php echo __( 'Place the shortcode in your page and the plugin will do the rest for you.', KBP_THEME ); ?><br/>
					<?php echo __( 'Example: [kbp_general]', KBP_THEME ); ?><br/>
				</p>

				<form method="post">
					<input type="hidden" name="page" value="kbp-configurator"/>

					<h3><?php echo __('Pick a color for the average', KBP_THEME); ?></h3>
					<input type="text" class="color-picker" name="color" value="<?php echo $current_color; ?>">

					<h3><?php echo __('General review data for the "kbp_general" shortcode', KBP_THEME); ?></h3>
					<textarea id="general_text" class="big-textarea" name="general"><?php echo trim( $general ); ?></textarea>
					<br/>

					<label for="general">
						<h4><?php echo __('Add tag to the HTML above', KBP_THEME); ?></h4>
					</label>

					<?php
					if (isset($general_select)):

						echo '<select id="general">';
						echo '<option value="-1">' . __('Select an option', KBP_THEME) . '</option>';

						foreach($general_select as $key => $value):

							echo '<option value='. $value .'>'. $value .'</option>';

						endforeach;

						echo '</select>';

					else:

						echo __('Index all the XML fields first on the settings page', KBP_THEME);

					endif;
					?>
					<br/>
					<br/>

					<h3><?php echo __('Single reviews for the "kbp_loop" shortcode', KBP_THEME); ?></h3>
					<textarea id="loop_single_text" class="big-textarea" name="loopsingle">
						<?php echo trim($loop_single); ?>
					</textarea>
					<br>

					<label for="loop_single">
						<h4><?php echo __('Add tag to the HTML above', KBP_THEME); ?></h4>
					</label>

					<?php
					if ( isset($loopsingle_select) ):

						echo '<select id="loop_single">';
						echo '<option value="-1">' . __('Select an option', KBP_THEME) . '</option>';

						foreach($loopsingle_select as $key => $value):

							echo '<option value='. $value .'>'. $value .'</option>';

						endforeach;

						echo '</select>';

					else:

						echo __('Index all the XML fields first on the settings page', KBP_THEME);

					endif;
					?>
					<br/>

					<?php
					$possible_reactions = get_option('kbp_possible_reactions');
					# Get the reactions field
					$which_reaction = get_option('kbp_real_reaction_field');

					if ( count($possible_reactions) > 0 ):
						?>
						<h4><?php echo __('Show reactions?', KBP_THEME); ?></h4>

						<input type="checkbox" name="show_reaction" <?php echo $show_reaction; ?>>
						<br/>

						<label for="which_reaction">
							<h4><?php echo __('Which field is the reaction field?', KBP_THEME); ?></h4>
						</label>

						<select name="which_reaction">
							<option value=""><?php echo __('Select an option', KBP_THEME); ?></option>
							<?php foreach ($possible_reactions as $key => $value): ?>
								<?php $selected = ($key == $which_reaction) ? ' selected="selected"' : '' ?>
								<option<?php echo $selected; ?> value="<?php echo $key ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
						<br/>
					<?php endif; ?>

					<label for="items_per_page">
						<h4><?php echo __('How many single reviews do you want to show with the "kbp_loop" shortcode', KBP_THEME); ?></h4>
					</label>

					<select name="items_per_page">
						<option value="0"><?php echo __('All reviews', KBP_THEME); ?></option>
						<?php
						for( $i = 1; $i <= 10; $i++ ):
							$selected = '';

							if( isset( $items_per_page ) && $items_per_page == $i ):

								$selected = 'selected';

							endif;

							echo '<option value='. $i . ' ' . $selected .'>'. $i .'</option>';

						endfor; ?>
					</select>
					<br/>
					<br/>

					<h3><?php echo __('Single reviews for the "kbp_review" shortcode', KBP_THEME); ?></h3>
					<textarea id="loop_review_text" class="big-textarea" name="loopreview">
						<?php echo trim($loop_review); ?>
					</textarea>
					<br/>

					<label for="items_per_page">
						<h4><?php echo __('Add tag to the HTML above', KBP_THEME); ?></h4>
					</label>
					<?php if (isset($loopsingle_select)): ?>

						<select id="loop_review">
						<option value="-1"><?php echo __('Select an option', KBP_THEME); ?></option>

						<?php
						foreach($loopsingle_select as $key => $value):

							echo '<option value='. $value .'>'. $value .'</option>';

						endforeach; ?>

						</select>

					<?php else:

						echo __('Index all the XML fields first on the settings page', KBP_THEME);

					endif;
					?>
					<br/>

					<label for="items_on_homepage">
						<h4><?php echo __('How many single reviews do you want to show with the "kbp_review" shortcode', KBP_THEME); ?></h4>
					</label>

					<select name="items_on_homepage">
						<?php
						for($i = 1; $i <= 3; $i++):

							$selected = '';

							if (isset($items_on_homepage) && $items_on_homepage == $i):

								$selected = 'selected';

							endif;

							echo '<option value='. $i . ' ' . $selected .'>'. $i .'</option>';

						endfor;
						?>
					</select>
					<br/>

					<?php wp_nonce_field(KBP_FOLDER, KBP_FOLDER . '_nonce'); ?>
					<p class="submit">
						<input type="submit" class="button-primary" name="submit"
							   value="<?php echo __('Save changes', KBP_THEME) ?>"/>
					</p>
				</form>

			</div>
			<!-- content -->

	        <!-- Sidebar -->
	        <div id="postbox-container-1" class="postbox-container">
	            <br><br>
	            <a href="mailto:support@prodes.nl?SUBJECT=[kvr-free]%20Ik%20heb%20een%20vraag%20over%20de%20plugin">
	           	<?php if (get_locale() === 'nl_NL'): ?>
	           		<img src="<?php echo KBP_DIR . '/library/img/support-NL.jpg'; ?>">
	           	<?php else: ?>
	           		<img src="<?php echo KBP_DIR . '/library/img/support-EN.jpg'; ?>">
	           	<?php endif; ?>
	           	</a>
	        </div>
	        <!-- /postbox-container -->

		</div>

	</div>
</div>