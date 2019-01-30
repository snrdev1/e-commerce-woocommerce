<div class="wrap">
	<div class="postbox">
		<div style="margin-top:20px" class="inside">
			<h1 style="text-align:center"><span style="font-size: 2.5em;font-family: roboto;">eCommerce</span> by zubi</h1>
			<p style="text-align:center">WooCommerce Services is almost ready to go! Once you connect Jetpack you'll have access to automated tax calculation.</p>
		</div>
	</div>
	
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

    <div style="padding-top:0" id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
	                <div class="postbox">
	                    <h3 class="hndle"><?php _e( 'Settings', $this->plugin->name ); ?></h3>

	                    <div class="inside">
		                    <form action="options-general.php?page=<?php echo $this->plugin->name; ?>" method="post">
		                    	<p>
		                    		<label style="padding-left:2px" for="ebz_user_key"><strong><?php _e( 'User key', $this->plugin->name ); ?></strong></label><br>
		                    		<input name="ebz_user_key" id="ebz_user_key" class="regular-text" style="font-family:Courier New;" value="<?php echo $this->settings['ebz_user_key']; ?>" placeholder="Insert your ser key here"><br>
		                    		<span style="padding-left:2px" class="description"><?php _e( 'The user key can be retrieved in your zubi.ai account.', $this->plugin->name ); ?></span>
		                    	</p>
		                    	<p>
		                    		<label style="padding-left:2px" for="ebz_store_name"><strong><?php _e( 'Store name', $this->plugin->name ); ?></strong></label><br>
		                    		<input name="ebz_store_name" id="ebz_store_name" class="regular-text" style="font-family:Courier New;" value="<?php echo $this->settings['ebz_store_name']; ?>" placeholder="default_store"><br>
		                    		<span style="padding-left:2px" class="description"><?php _e( 'The store name is used to separate your stores. E.g. MyStore_US', $this->plugin->name ); ?></span>
		                    	</p>
								<p>
		                    		<label style="padding-left:2px;padding-right:10px" for="ebz_is_disabled"><span style="padding-left:2px" class="description"><?php _e( 'Disable all tracking', $this->plugin->name ); ?></span></label>
									<input type="checkbox" name="ebz_is_disabled" id="ebz_is_disabled" value="1" <?php checked(1, $this->settings['ebz_is_disabled'], true); ?> />
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
