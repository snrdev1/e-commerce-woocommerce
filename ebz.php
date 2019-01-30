<?php
/**
* Plugin Name: eCommerce by zubi
* Plugin URI: http://www.zubi.ai/
* Version: 1.0.0
* Author: zubi.ai
* Author URI: http://www.zubi.ai/
* Description: Allows you to integrate with the zubi platform for eCommerce.
* Copyright 2019 ZubiLabs AB
* License: GPL3
*/
if (!defined('ABSPATH'))
{
    exit;
} // Exit if accessed directly
/*if (!class_exists('WooCommerce'))
{
    exit;
}// Exit if WooCommerce is not active
*/

class eCommerceByZubi {
	/**
	* Constructor
	*/
	public function __construct() {
		// Plugin Details
        $this->plugin               = new stdClass;
        $this->plugin->name         = 'ecommerce-by-zubi'; // Plugin Folder
        $this->plugin->displayName  = 'eCommerce by zubi'; // Plugin Name
        $this->plugin->version      = '1.0.0';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
        $this->plugin->db_welcome_dismissed_key = $this->plugin->name . '_welcome_dismissed_key';
		// Hooks
		add_action( 'admin_init', array( &$this, 'registerSettings' ) );
        add_action( 'admin_menu', array( &$this, 'adminPanelsAndMetaBoxes' ) );
        add_action( 'admin_notices', array( &$this, 'dashboardNotices' ) );
        add_action( 'wp_ajax_' . $this->plugin->name . '_dismiss_dashboard_notices', array( &$this, 'dismissDashboardNotices' ) );
        // Frontend Hooks
        add_action( 'wp_head', array( &$this, 'allPages' ) );
		add_action( 'wp_footer', array( &$this, 'trigger_for_ajax_add_to_cart' ) );
		add_action( 'woocommerce_before_single_product', array( &$this, 'productPages' ) );
		add_action( 'woocommerce_after_cart', array( &$this, 'custom_remove_from_cart') );
		add_action( 'woocommerce_thankyou', array( &$this, 'customReadOrder' ) );
		add_action( 'woocommerce_add_to_cart', array( &$this, 'custom_add_to_cart') );
		add_action( 'wp_ajax_nopriv_zl_ajax_product', array( &$this, 'zl_ajax_get_product'));
		add_action( 'wp_ajax_zl_ajax_product', array( &$this, 'zl_ajax_get_product'));
		add_action( 'woocommerce_cart_item_restored', array( &$this, 'custom_restore_cart'));
		add_action( 'woocommerce_before_cart_item_quantity_zero', array( &$this, 'custom_zero_qty_cart') ); 
        add_action( 'woocommerce_after_cart_item_quantity_update', array( &$this, 'custom_qty_update_cart') ); 
		
	}
	
	function zl_ajax_get_product(){
		$num = $_GET['pid'];
		$num = str_replace('"', "", $num);
		$num = stripslashes($num);
		$product = wc_get_product( $num );
		echo $product;
		exit();
	}
	
    /**
     * Show relevant notices for the plugin
     */
    function dashboardNotices() {
        global $pagenow;
        if ( !get_option( $this->plugin->db_welcome_dismissed_key ) ) {
        	if ( ! ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'ecommerce-by-zubi' ) ) {
	            $setting_page = admin_url( 'options-general.php?page=' . $this->plugin->name );
	            // load the notices view
                include_once( $this->plugin->folder . '/views/dashboard-notices.php' );
        	}
        }
    }
    /**
     * Dismiss the welcome notice for the plugin
     */
    function dismissDashboardNotices() {
    	check_ajax_referer( $this->plugin->name . '-nonce', 'nonce' );
        // user has dismissed the welcome notice
        update_option( $this->plugin->db_welcome_dismissed_key, 1 );
        exit;
    }
	/**
	* Register Settings
	*/
	function registerSettings() {
		register_setting( $this->plugin->name, 'ebz_user_key', 'trim' );
		register_setting( $this->plugin->name, 'ebz_store_name', 'trim' );
	}
	/**
    * Register the plugin settings panel
    */
    function adminPanelsAndMetaBoxes() {
    	add_submenu_page( 'options-general.php', $this->plugin->displayName, $this->plugin->displayName, 'manage_options', $this->plugin->name, array( &$this, 'adminPanel' ) );
	}
    /**
    * Output the Administration Panel
    * Save POSTed data from the Administration Panel into a WordPress option
    */
    function adminPanel() {
		// only admin user can access this page
		if ( !current_user_can( 'administrator' ) ) {
			echo '<p>' . __( 'Sorry, you are not allowed to access this page.', $this->plugin->name ) . '</p>';
			return;
		}
    	// Save Settings
        if ( isset( $_REQUEST['submit'] ) ) {
        	// Check nonce
			if ( !isset( $_REQUEST[$this->plugin->name.'_nonce'] ) ) {
	        	// Missing nonce
	        	$this->errorMessage = __( 'nonce field is missing. Settings NOT saved.', $this->plugin->name );
        	} elseif ( !wp_verify_nonce( $_REQUEST[$this->plugin->name.'_nonce'], $this->plugin->name ) ) {
	        	// Invalid nonce
	        	$this->errorMessage = __( 'Invalid nonce specified. Settings NOT saved.', $this->plugin->name );
        	} else {
	        	// Save
				// $_REQUEST has already been slashed by wp_magic_quotes in wp-settings
				// so do nothing before saving
	    		update_option( 'ebz_user_key', $_REQUEST['ebz_user_key'] );
	    		update_option( 'ebz_store_name', $_REQUEST['ebz_store_name'] );
	    		update_option( $this->plugin->db_welcome_dismissed_key, 1 );
				$this->message = __( 'Settings Saved.', $this->plugin->name );
			}
        }
        // Get latest settings
        $this->settings = array(
			'ebz_user_key' => esc_html( wp_unslash( get_option( 'ebz_user_key' ) ) ),
			'ebz_store_name' => esc_html( wp_unslash( get_option( 'ebz_store_name' ) ) ),
        );
    	// Load Settings Form
        include_once( WP_PLUGIN_DIR . '/' . $this->plugin->name . '/views/settings.php' );
    }
    /**
	* Loads plugin textdomain
	*/
	function loadLanguageFiles() {
		load_plugin_textdomain( $this->plugin->name, false, $this->plugin->name . '/languages/' );
	}
	function custom_zero_qty_cart( $cart_item_key, $cart ){
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		$qty = $cart->cart_contents[$cart_item_key]["quantity"];
		$product = wc_get_product( $product_id );
		wc_enqueue_js("var cur = '" . $currency . "';");
		wc_enqueue_js('(function($){
			window.zubitracker(
				"addEnhancedEcommerceProductContext",
				"' . $product->get_id() . '","' . $product->get_name() . '","","","","",
				"' . $product->get_price() . '","' . $qty . '","","",cur
			);
			window.zubitracker("trackEnhancedEcommerceAction","remove");
		})(jQuery);');
	}
	function custom_qty_update_cart( $cart_item_key, $quantity, $old_quantity, $cart ){
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		$qty = $quantity - $old_quantity;
		$label = 'add';
		if ($quantity == $old_quantity) {
			return;
		}
		if ($quantity < $old_quantity) {
			$qty = $old_quantity - $quantity;
			$label = 'remove';
		}	
		$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		$product = wc_get_product( $product_id );
		wc_enqueue_js("var cur = '" . $currency . "';");
		wc_enqueue_js('(function($){
			window.zubitracker(
				"addEnhancedEcommerceProductContext",
				"' . $product->get_id() . '","' . $product->get_name() . '","","","","",
				"' . $product->get_price() . '","' . $qty . '","","",cur
			);
			window.zubitracker("trackEnhancedEcommerceAction","' . $label . '");
		})(jQuery);');
	}
	
		//add_action( 'woocommerce_before_cart_item_quantity_zero', $cart_item_key, $this );
        //add_action( 'woocommerce_after_cart_item_quantity_update', $cart_item_key, $quantity, $old_quantity, $this );
	
	function custom_add_to_cart( $cart_item_key, $cart ){
		//return if not product page       
        if (!is_single())
            return;
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		$qty = $cart->cart_contents[$cart_item_key]["quantity"];
		$product = wc_get_product( $product_id );
		wc_enqueue_js("var cur = '" . $currency . "';");
		wc_enqueue_js('(function($){
			window.zubitracker(
				"addEnhancedEcommerceProductContext",
				"' . $product->get_id() . '","' . $product->get_name() . '","","","","",
				"' . $product->get_price() . '","' . $qty . '","","",cur
			);
			window.zubitracker("trackEnhancedEcommerceAction","add");
		})(jQuery);');
	}
	function custom_restore_cart( $cart_item_key, $cart ){
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		$qty = $cart->cart_contents[$cart_item_key]["quantity"];
		$product = wc_get_product( $product_id );
		wc_enqueue_js("var cur = '" . $currency . "';");
		wc_enqueue_js('(function($){
			window.zubitracker(
				"addEnhancedEcommerceProductContext",
				"' . $product->get_id() . '","' . $product->get_name() . '","","","","",
				"' . $product->get_price() . '","' . $qty . '","","",cur
			);
			window.zubitracker("trackEnhancedEcommerceAction","add");
		})(jQuery);');
	}
	function custom_remove_from_cart(){
		global $woocommerce;
        $cart_items = array();
        foreach ($woocommerce->cart->cart_contents as $key => $item) {
            $product = wc_get_product($item["product_id"]);
            $removeURL = html_entity_decode(wc_get_cart_remove_url($key)); 
            $cart_items[$removeURL] = array(
            	"pid" => esc_html($product->get_id()),
                "pna" => html_entity_decode($product->get_name()),
                "ppr" => esc_html($product->get_price()),
                "pqt"=>$woocommerce->cart->cart_contents[$key]["quantity"]
            );
        }
		wc_enqueue_js("var ci = " . json_encode($cart_items) . ";");
		wc_enqueue_js("var cur = '" . get_woocommerce_currency() . "';");
		wc_enqueue_js('(function($){
			$("a[href*=\"?remove_item\"]").click(function(){
            	var rl=jQuery(this).attr("href");
				window.zubitracker(
					"addEnhancedEcommerceProductContext",
					ci[rl].pid,ci[rl].pna,"","","","",
					ci[rl].ppr,ci[rl].pqt,"","",cur
				);
				window.zubitracker("trackEnhancedEcommerceAction","remove");
			});
		})(jQuery);');
	}
	function productPages() {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		// provide the opportunity to Ignore ebz
		if ( apply_filters( 'disable_ebz', false ) ) {
			return;
		}
		
		global $woocommerce; 
		global $product;
        $currency = get_woocommerce_currency();
		echo '<script type="text/javascript">window.zubitracker("addEnhancedEcommerceProductContext",
		"'.$product->get_id().'","'.$product->get_name().'","","","","","'.$product->get_price().'","","","","'.$currency.'");
		window.zubitracker("trackEnhancedEcommerceAction","view");</script>';
	}
	function customReadOrder($order_id) {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		// provide the opportunity to Ignore ebz
		if ( apply_filters( 'disable_ebz', false ) ) {
			return;
		}
		$sname = get_option( 'ebz_store_name' );
		if ( empty( $sname ) ) {
			$sname = 'default_store';
		}
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		//getting order object
		$order = wc_get_order($order_id);
		
		echo '<script type="text/javascript">zubitracker("addTrans","'.$order->get_id().'","'.$sname.'","'.$order->get_total().'","'.$order->get_total_tax().'","'.$order->get_shipping_total().'","'.$order->get_billing_city().'","'.$order->get_billing_state().'","'.$order->get_billing_country().'","'.$currency.'");</script>';
		
		$items = $order->get_items();
		foreach ($items as $item_id => $item_data) {
			// Get an instance of corresponding the WC_Product object
			$product = $item_data->get_product();
			$item_quantity = $item_data->get_quantity(); // Get the item quantity
			$unit_price = number_format(((float)$item_data->get_total()/(float)$item_quantity), 2, '.', '');
			
			echo '<script type="text/javascript">zubitracker("addItem","'.$order->id.'","'.$product->get_id().'","'.$product->get_name().'","","'.$unit_price.'","'.$item_quantity.'");</script>';
		}
		echo '<script type="text/javascript">zubitracker("trackTrans");</script>';
	}
	
	function allPages() {
		// Ignore admin, feed, robots or trackbacks
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}
		// provide the opportunity to Ignore ebz
		if ( apply_filters( 'disable_ebz', false ) ) {
			return;
		}
		// Get meta
		$ukey = get_option( 'ebz_user_key' );
		$sname = get_option( 'ebz_store_name' );
		if ( empty( $sname ) ) {
			$sname = 'default_store';
		}
		if ( empty( $ukey ) ) {
			return;
		}
		if ( trim( $ukey ) == '' ) {
			return;
		}
		
		$ukey = str_replace(array('\'', '"'), '', $ukey);
		$sname = str_replace(array('\'', '"'), '', $sname);
		$cd = str_replace('www','', parse_url(get_site_url(), PHP_URL_HOST));
		
		$meta = '<script type="text/javascript">
					;(function(p,l,o,w,i,n,g){if(!p[i]){p.GlobalSnowplowNamespace=p.GlobalSnowplowNamespace||[];
					p.GlobalSnowplowNamespace.push(i);p[i]=function(){(p[i].q=p[i].q||[]).push(arguments)};
					p[i].q=p[i].q||[];n=l.createElement(o);g=l.getElementsByTagName(o)[0];
					n.async=1;n.src=w;g.parentNode.insertBefore(n,g)}}
					(window,document,"script","//d1fc8wv8zag5ca.cloudfront.net/2.9.3/sp.js","zubitracker"));
					window.zubitracker("newTracker", "'.$ukey.'", "tracker.zubi.ai", {
						appId: "'.$sname.'",
						cookieDomain: "'.$cd.'",
						forceSecureTracker: true,
						cookieName: "zl",
						contexts: {webPage: true,gaCookies: true}
					});
					window.zubitracker("trackPageView");
					window.zubitracker("enableLinkClickTracking");
					zubitracker("enableFormTracking");
				</script>';
		// Output
		echo wp_unslash( $meta );
	}
	function trigger_for_ajax_add_to_cart() {
		global $woocommerce; 
        $currency = get_woocommerce_currency();
		?>
			<script type="text/javascript">				
				(function($){
					var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
					var cur = '<?php echo $currency; ?>';
					$('body').on( 'added_to_cart', function(event, fragments, cart_hash, $button){
						var pid = JSON.stringify($button.context.dataset.product_id);
						var qty = JSON.stringify($button.context.dataset.quantity);
						jQuery.get(
							ajaxurl, 
							{'action': 'zl_ajax_product', 'pid' : pid}, 
							function(data) {
								data = JSON.parse(data);
								window.zubitracker("addEnhancedEcommerceProductContext",String(data.id),data.name,"","","","",data.price,qty,"","",cur);
								window.zubitracker("trackEnhancedEcommerceAction","add");
								alert("added_to_cart")
							}
						);
					});
					$('body').on( 'removed_from_cart', function(event, fragments, cart_hash, $button ){
						var pid = JSON.stringify($button.context.dataset.product_id);
						var qty = JSON.stringify($button.context.dataset.quantity);
						jQuery.get(
							ajaxurl, 
							{'action': 'zl_ajax_product', 'pid' : pid}, 
							function(data) {
								data = JSON.parse(data);
								window.zubitracker("addEnhancedEcommerceProductContext",String(data.id),data.name,"","","","",data.price,qty,"","",cur);
								window.zubitracker("trackEnhancedEcommerceAction","remove");
								alert("removed_from_cart")
							}
						);
					});
				})(jQuery);
			</script>
		<?php
	}
}
$ebz = new eCommerceByZubi();
