<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">

	<h1><?php echo __('Klantenvertellen by Prodes plugin settings', KBP_THEME); ?></h1>


<?php
if (isset($this->message)) {
	?>
	<div class="updated fade">
		<p>
			<?php echo $this->message; ?>
		</p>
	</div>
	<?php
}
if (isset($this->errorMessage)) {
	?>
	<div class="error fade">
		<p>
			<?php echo $this->errorMessage; ?>
		</p>
	</div>
	<?php
}


?>

    <div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
	        <!-- Content -->
	        <div id="post-body-content">
    			<?php
				# Check if we were supplied with a (new) url
				$current_fields = get_option('kbp_fields');

				if ($current_fields !== false):
					echo '<p class="kbp-settings">';
					echo sprintf(__('Klantenvertellen fields were updated on: %s', KBP_THEME), $current_fields["date"]->format('H:i:s d-m-Y')) .'<br/>';
					echo sprintf(__('%d fields of Klantenvertellen have been indexed', KBP_THEME), count($current_fields, COUNT_RECURSIVE)) . '<br/>';
					echo sprintf(__('The current URL for the Klantenvertellen XML is: <a href=%s>%s</a>', KBP_THEME), $current_fields['url'], $current_fields['url']) . '<br/>';
					echo '</p>';
				endif;
				?>

				<form method="post">

					<input type="hidden" name="page" value="kbp-settings"/>
					<input type="text" class="xml-url" name="url" placeholder="<?php echo __('Enter the URL of the XML here', KBP_THEME); ?>"/>

					<br/>
					<?php wp_nonce_field(KBP_FOLDER, KBP_FOLDER . '_nonce'); ?>
					<p class="submit">
						<input type="submit" class="button-primary" name="submit"
							   value="<?php echo __('Index fields', KBP_THEME) ?>"/>
					</p>
				</form>
			</div>
			<!-- end of content -->

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

