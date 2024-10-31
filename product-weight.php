<?php
/**
 * Plugin Name: Product Weight - Price Per Weight
 * Plugin URI: https://wordpress.org/plugins/product-weight
 * Description: Show Product Weight and Product Price Per weight on WooCommerce single category and product page. Show Price per 100g and 1kg. 100% FREE PLUGIN - compatible with WooCommerce products, show support, rate 5 stars and share :).
 * Version: 1.0
 * Author: Omar Dieh
 * Author URI: https://omardieh.com
 **/

//------------------------------------------------------
// add database table
function create_db_table() {      
	global $wpdb;
	$test_db_version = '1.0.0';
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$charset_collate = $wpdb->get_charset_collate();
	if ($wpdb->get_var( "show tables like '$db_table_name'" ) != $db_table_name ) {
		 $sql = "CREATE TABLE $db_table_name (
				  id int NOT NULL auto_increment,
				  show_weight int NOT NULL,
				  show_price int NOT NULL,
				  UNIQUE KEY id (id)
		  ) $charset_collate;";
	 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	 	dbDelta( $sql );
	 	add_option( 'test_db_version', $test_db_version );
   	};
  };
register_activation_hook( __FILE__, 'create_db_table' );

//------------------------------------------------------
// # insert row into table
function insert_db_row() {	
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$db_row = $wpdb->get_var("SELECT ID FROM $db_table_name WHERE id = 1");
	if ($db_row == null) {
		 $row_data = array(
			 'id' => 1,
			 'show_weight' => 1,
			 'show_price' => 1
		 );
		 $sql = $wpdb->insert(
			 $db_table_name, $row_data
		 );
	 	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	 	dbDelta( $sql );
   	};
  };
register_activation_hook( __FILE__, 'insert_db_row' );

//------------------------------------------------------
// # handle price single product page
add_action ('woocommerce_single_product_summary', 'handle_show_price_product', 20);
function handle_show_price_product() {
	global $wpdb, $table_prefix;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$show_price = $wpdb->get_var("SELECT show_price FROM $db_table_name WHERE id = 1");
	global $product;
	$weight = $product->get_weight();
	global  $woocommerce;
	$currency = get_woocommerce_currency_symbol();
	$weight_unit = get_option('woocommerce_weight_unit');
	$gram_text = ' / 100'.$weight_unit.'';
	$unit_price = 0;
	if($show_price) {
		if( $product->is_on_sale() ) {
		$unit_price = $product->get_sale_price();
		}
		else {
			$unit_price = $product->get_regular_price();
		}	
		if ( $product->has_weight() && $product->get_weight() != "-" && $product->get_weight() <= 250 ) {
		echo '<p class="ppw_price_product"> '.esc_attr(round($unit_price / $weight *100, 2) .$currency .$gram_text).'</p>'.PHP_EOL;
		};
		if ( $product->has_weight() && $product->get_weight() != "-" && $product->get_weight() > 250 ) {
		echo '<p class="ppw_price_product"> '.esc_attr(round($unit_price / $weight *1000, 2) .$currency .' / 1kg').'</p>'.PHP_EOL;
		};
	};
};

//------------------------------------------------------
// # handle price single category page
add_action( 'woocommerce_after_shop_loop_item', 'handle_show_price_category', 10 );
function handle_show_price_category() {
	global $wpdb, $table_prefix;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$show_price = $wpdb->get_var("SELECT show_price FROM $db_table_name WHERE id = 1");
    global $product;
	$weight = $product->get_weight();
	global  $woocommerce;
	$currency = get_woocommerce_currency_symbol();
	$weight_unit = get_option('woocommerce_weight_unit');
	$gram_text = ' / 100'.$weight_unit.'';
	$unit_price = 0;
	if($show_price) {
		if( $product->is_on_sale() ) {
		$unit_price = $product->get_sale_price();
		}
		else {
			$unit_price = $product->get_regular_price();
		}
		
		if ( $product->has_weight() && $product->get_weight() != "-" && $product->get_weight() <= 250 ) {
		echo '<p class="ppw_price_category"> '.esc_attr(round($unit_price / $weight *100, 2) .$currency .$gram_text).'</p>'.PHP_EOL;
		};
		if ( $product->has_weight() && $product->get_weight() != "-" && $product->get_weight() > 250 ) {
		echo '<p class="ppw_price_category"> '.esc_attr(round($unit_price / $weight *1000, 2) .$currency .' / 1kg').'</p>'.PHP_EOL;
		};
	};
};

//------------------------------------------------------
// # handle weight single product page
add_action( 'woocommerce_single_product_summary', 'handle_show_weight_product', 20 );
function handle_show_weight_product() {
	global $wpdb, $table_prefix;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$show_weight = $wpdb->get_var("SELECT show_weight FROM $db_table_name WHERE id = 1");
    global $product;
	$weight = $product->get_weight();
	$weight_unit = get_option('woocommerce_weight_unit');
	if($show_weight) {
		if ( $product->has_weight() && $product->get_weight() != "-") {
		echo '<p class="ppw_weight_product">'.esc_attr($weight .$weight_unit).'</p>'.PHP_EOL;
		};
	};
};

//------------------------------------------------------
// # handle weight single category page
add_action( 'woocommerce_after_shop_loop_item', 'handle_show_weight_category', 10 );
function handle_show_weight_category() {
	global $wpdb, $table_prefix;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$show_weight = $wpdb->get_var("SELECT show_weight FROM $db_table_name WHERE id = 1");
    global $product;
	$weight = $product->get_weight();
	$weight_unit = get_option('woocommerce_weight_unit');
	if($show_weight) {
		if ( $product->has_weight() && $product->get_weight() != "-") {
		echo '<p class="ppw_weight_category">'.esc_attr($weight .$weight_unit).'</p>'.PHP_EOL;
		};
	};
};

//------------------------------------------------------
// # create admin menu in WooCommerce 
add_action('admin_menu', 'add_admin_menu');
function add_admin_menu() {
    add_submenu_page( 'woocommerce', 'Price Per Weight', 'Price Per Weight', 'manage_options', 'price-per-weight', 'admin_component'); 
};
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'apd_settings_link' );
function apd_settings_link( array $links ) {
    $url = get_admin_url() . "admin.php?page=product-weight";
    $settings_link = '<a href="' . $url . '">' . __('Settings', 'textdomain') . '</a>';
      $links[] = $settings_link;
    return $links;
};

function admin_component() {
	global $wpdb, $table_prefix;
	$db_table_name = $wpdb->prefix . 'price_per_weight';
	$show_weight = $wpdb->get_var("SELECT show_weight FROM $db_table_name WHERE id = 1");
	$show_price = $wpdb->get_var("SELECT show_price FROM $db_table_name WHERE id = 1");	
	$plugin_name = "Product Weight - Price Per Weight";
	$input_price_title = $show_price == 1 ? "off" : "on";
	$input_weight_title = $show_weight == 1 ? "off" : "on";
	$input_price_status = $show_price == 1 ? "enabled" : "disabled";
	$input_weight_status = $show_weight == 1 ? "enabled" : "disabled";
	$plugin_desc = "
1. free features compatible with WooCommerce products
2. show Product Weight and Product Price per 100G and 1KG.
3. if the weight of the product is equal or less than 250 grams then price will be shown per 100g.
4. if the weight of the product is more than 250 grams then price will be shown per 1kg.
5. works too for on sale products and will change the price dynamically when sale date ends. 
6. elements classes for styling are provided too.
7. FREE PLUGIN - show support rate 5 stars and share :)";
	if (isset($_POST['submit_price'])) {
		$toggler = $show_price == 1 ? 0 : 1;
		$sql = "UPDATE $db_table_name SET show_price = $toggler WHERE id = 1";;
		dbDelta( $sql );
		header("Refresh:0");		
	};
	
	if (isset($_POST['submit_weight'])) {
		$toggler = $show_weight == 1 ? 0 : 1;
		$sql = "UPDATE $db_table_name SET show_weight = $toggler WHERE id = 1";;
		dbDelta( $sql );
		header("Refresh:0");				
	};

	echo "
	<body>
		<pre>".esc_attr(" ")."</pre>
		<h1>".esc_attr($plugin_name)."</h1>
		<pre>".esc_attr($plugin_desc)."</pre>
		<h3>".esc_attr("configurations")."</h3>
		<form method='post' action=''>
			<strong>".esc_attr('Show Price Per Weight')."</strong> ".esc_attr('current status is')." <strong>".esc_attr($input_price_status)."</strong>, ".esc_attr('click to switch')." <input type='submit' name='submit_price' value=".esc_attr($input_price_title)."><br/><br/>
			<strong>".esc_attr('Show Product Weight')."</strong> ".esc_attr('current status is')." <strong>".esc_attr($input_weight_status)."</strong>, ".esc_attr('click to switch')." <input type='submit' name='submit_weight' value=".esc_attr($input_weight_title).">
		</form>
		<pre>".esc_attr(" ")."</pre>
		<h3>".esc_attr("elements classes for styling")."</h3>
		<h4>".esc_attr("on WooCommerce single product page :")."</h4>
		<p>".esc_attr("weight element class : 'ppw_weight_product' ")."</p>
		<p>".esc_attr("price element class : 'ppw_price_product' ")."</p>
		<h4>".esc_attr("on WooCommerce single category page :")."</h4>
		<p>".esc_attr("weight element class : 'ppw_weight_category' ")."</p>
		<p>".esc_attr("price element class : 'ppw_price_category' ")."</p>
		<pre>".esc_attr(" ")."</pre>
		<pre>".esc_attr("Developed By : Omar Dieh")."</pre>
	</body>";
};
?>