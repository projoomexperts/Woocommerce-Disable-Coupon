<?php
/**
 * Plugin Name: Woocommerce Disable Coupon
 * Plugin URI: https://raaz.me/
 * Description: Gives you more control over the coupon field. It will disable the coupon field for all not logged in user and then Enable / Disable Specific Users.
 * Version: 1.0
 * Author: Mashiur Rahman
 * Author URI: https://raaz.me/
 * Text Domain: woo-disable-coupon
 * WC requires at least: 3.0
 * WC tested up to: 5.0.0
 */


add_action( 'show_user_profile', 'wdc_enable_coupon_field' );
add_action( 'edit_user_profile', 'wdc_enable_coupon_field' );

function wdc_enable_coupon_field( $user ) { 
	$coupon_field = get_the_author_meta( 'coupon_field', $user->ID );
	if(!$coupon_field){
		$options = get_option( 'wdc_settings' );
		if($options){ 
			$coupon_field = $options['wdc_coupon_field'];
		} else {
			$coupon_field = 'disable';
		}
	}
	?>
	<h3>Woocommerce Disable Coupon</h3>
	<table class="form-table">
		<tr>
			<th><label for="coupon_field_enable">Coupon Field</label></th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>Enable / Disable WooCommerce Coupon Field</span></legend>
					<label for="coupon_field_enable">
						<input type="radio" name="coupon_field" id="coupon_field_enable" value="enable" <?php if($coupon_field == 'enable'){ echo 'checked'; } ?>  />
						Enable
					</label>
					<label for="coupon_field_disable">
						<input type="radio" name="coupon_field" id="coupon_field_disable" value="disable" <?php if($coupon_field == 'disable'){ echo 'checked'; } ?>   />
						Disable
					</label>
				</fieldset>
			</td>
		</tr>
	</table>
<?php }

add_action( 'personal_options_update', 'wdc_save_coupon_field' );
add_action( 'edit_user_profile_update', 'wdc_save_coupon_field' );

function wdc_save_coupon_field( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	update_usermeta( $user_id, 'coupon_field', $_POST['coupon_field'] );
}

add_action( 'admin_menu', 'wdc_add_admin_menu' );
add_action( 'admin_init', 'wdc_settings_init' );
function wdc_add_admin_menu(  ) { 
	add_submenu_page( 'woocommerce', 'Woo Disable Coupon', 'Woo Disable Coupon', 'manage_options', 'woocommerce_disable_coupon', 'wdc_options_page' , 50 );
}

function wdc_settings_init(  ) { 
	register_setting( 'pluginPage', 'wdc_settings' );
	add_settings_section(
		'wdc_pluginPage_section', 
		__( 'Enable / Disable Coupon Field in Shopping Cart Page', 'woo-disable-coupon' ), 
		'wdc_settings_section_callback', 
		'pluginPage'
	);
	add_settings_field( 
		'wdc_coupon_field', 
		__( 'Coupon Field', 'woo-disable-coupon' ), 
		'wdc_coupon_field_render', 
		'pluginPage', 
		'wdc_pluginPage_section' 
	);
}


function wdc_coupon_field_render(  ) { 
	$options = get_option( 'wdc_settings' );
	?>
	<fieldset>
		<label>
			<input type='radio' name='wdc_settings[wdc_coupon_field]' <?php checked( $options['wdc_coupon_field'], 'enable' ); ?> value='enable'>
			Enable
		</label>
		<label>
			<input type='radio' name='wdc_settings[wdc_coupon_field]' <?php checked( $options['wdc_coupon_field'], 'disable' ); ?> value='disable'>
			Disable
		</label>
	</fieldset>
	<?php
}


function wdc_settings_section_callback(  ) { 
	//
}


function wdc_options_page(  ) { 
	?>
	<form action='options.php' method='post'>
		<h2>Woocommerce Disable Coupon</h2>
		<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
		?>
	</form>
	<?php
}



function wdc_hide_coupon_field_on_woocommerce_cart( $enabled ) {
	$user_id = get_current_user_id();
	if( $user_id ){
		$coupon_field = get_the_author_meta( 'coupon_field', $user_id );
		if(!$coupon_field){
			$options = get_option( 'wdc_settings' );
			if($options){ 
				$coupon_field = $options['wdc_coupon_field'];
			} else {
				$coupon_field = 'disable';
			}
		}
		if( $coupon_field == 'enable'){
			$enabled = true;
		} else {
			$enabled = false;
		}
	} else {
		$enabled = false;
	}
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'wdc_hide_coupon_field_on_woocommerce_cart' );
