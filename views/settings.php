<div class="wrap">
    <!-- <h2><?php echo $this->plugin->displayName; ?> &raquo; <?php _e( 'Settings', $this->plugin->name ); ?></h2> -->
	
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
	                    <h3 class="hndle"><?php _e( 'eCommerce by zubi', $this->plugin->name ); ?></h3>

	                    <div class="inside">
		                    <form action="options-general.php?page=<?php echo $this->plugin->name; ?>" method="post">
		                    	<p>
		                    		<label for="ebz_user_key"><strong><?php _e( 'User key', $this->plugin->name ); ?></strong></label><br>
		                    		<input name="ebz_user_key" id="ebz_user_key" class="regular-text" style="font-family:Courier New;" value="<?php echo $this->settings['ebz_user_key']; ?>" placeholder="Insert your ser key here"><br>
		                    		<?php _e( 'The user key can be retrieved in your zubi.ai account.', $this->plugin->name ); ?>
		                    	</p>
		                    	<p>
		                    		<label for="ebz_store_name"><strong><?php _e( 'Store name', $this->plugin->name ); ?></strong></label><br>
		                    		<input name="ebz_store_name" id="ebz_store_name" class="regular-text" style="font-family:Courier New;" value="<?php echo $this->settings['ebz_store_name']; ?>" placeholder="default_store"><br>
		                    		<?php _e( 'The store name is used to separate your stores. E.g. MyStore_US', $this->plugin->name ); ?>
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
