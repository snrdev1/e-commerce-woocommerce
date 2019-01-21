<div class="wrap">
    <h2><?php echo $this->plugin->displayName; ?> &raquo; <?php _e( 'Settings', $this->plugin->name ); ?></h2>

    <?php
    if ( isset( $this->message ) ) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>
        <?php
    }
    if ( isset( $this->errorMessage ) ) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>
        <?php
    }
    ?>

    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
	                <div class="postbox">
	                    <h3 class="hndle"><?php _e( 'Settings', $this->plugin->name ); ?></h3>

	                    <div class="inside">
		                    <form action="options-general.php?page=<?php echo $this->plugin->name; ?>" method="post">
		                    	<p>
		                    		<label for="ebz_user_key"><strong><?php _e( 'User key', $this->plugin->name ); ?></strong></label>
		                    		<textarea name="ebz_user_key" id="ebz_user_key" class="widefat" rows="8" style="font-family:Courier New;"><?php echo $this->settings['ebz_user_key']; ?></textarea>
		                    		<?php _e( 'The user key can be retrieved in your zubi.ai account.', $this->plugin->name ); ?>
		                    	</p>
		                    	<p>
		                    		<label for="ebz_store_name"><strong><?php _e( 'Store name', $this->plugin->name ); ?></strong></label>
		                    		<textarea name="ebz_store_name" id="ebz_store_name" class="widefat" rows="8" style="font-family:Courier New;"><?php echo $this->settings['ebz_store_name']; ?></textarea>
		                    		<?php _e( 'If you have multiple stores, the store name is used to separate them. E.g. MyZubiStore_US', $this->plugin->name ); ?>
		                    	</p>
		                    	<?php wp_nonce_field( $this->plugin->name, $this->plugin->name.'_nonce' ); ?>
		                    	<p>
									<input name="submit" type="submit" name="Submit" class="button button-primary" value="<?php _e( 'Save', $this->plugin->name ); ?>" />
								</p>
						    </form>
	                    </div>
	                </div>
	                <!-- /postbox -->
				</div>
				<!-- /normal-sortables -->
    		</div>
    		<!-- /post-body-content -->

    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once( $this->plugin->folder . '/views/sidebar.php' ); ?>
    		</div>
    		<!-- /postbox-container -->
    	</div>
	</div>
</div>
