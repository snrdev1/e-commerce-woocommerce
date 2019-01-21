<!-- Generate API Key -->
<div class="postbox">
    <h3 class="hndle">
        <span><?php _e('Generate Woocommerce API key', $this->plugin->name); ?></span>
    </h3>

    <div class="inside">
        <p>
            <?php _e( "zubi.ai requires your Woocommerce API key to function properly. If you haven't already, go ahead and generate it ", $this->plugin->name ); ?>
            <a href="<?php get_site_url(); ?>/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys"><?php _e( 'here' ); ?></a>.
        </p>
		

        <u>
            <?php _e( 'The steps to complete are:', $this->plugin->name ); ?>
        </u>
		
		<ul style="margin-top:6px">
           <li>1. Generate the <a href="/wp-admin/admin.php?page=wc-settings&amp;tab=advanced&amp;section=keys">API key</a></li>
            <li>2. Copy/paste the API key <a href="http://portal.zubi.ai" target="_blank">here</a></li>
        </ul>
    </div>
</div>

<!-- GDPR -->
<div class="postbox">
    <h3 class="hndle">
        <span><?php _e( 'GDPR & Data Privacy', $this->plugin->name); ?></span>
    </h3>
    <div class="inside">
        <p><?php _e( 'The zubi.ai tracker use cookies to track visitor behaviour. It is advicable to inform your visitors of any such tracking, may it be from zubi or any other third party solution (such as google analytics, facebook or other.) Read more about the cookies used by zubi.ai ', $this->plugin->name ); ?><a href="http://portal.zubi.ai" target="_blank"><?php _e( 'here', $this->plugin->name ); ?></a>
        </p>
		<p><a href="http://portal.zubi.ai" target="_blank"><?php _e( 'Terms of Service', $this->plugin->name ); ?></a></p>
    </div>
</div>
