<?php /*
  Plugin Name: Robokassa Payment Gateway
  Plugin URI: 
  Description: Allows you to use Robokassa payment gateway with the WooCommerce plugin.
  Version: 0.7
  Author: Alexander Kurganov
  Author URI: http://polzo.ru
 */


/*

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */
 
 /**
 * Add roubles in carrencies
 * 
 * @since 0.3
 */
function robokassa_rur_currency_symbol( $currency_symbol, $currency ) {
    if($currency == "RUR") {
        $currency_symbol = 'р.';
    }
    return $currency_symbol;
}

add_filter( 'woocommerce_currency_symbol', 'robokassa_rur_currency_symbol', 10, 2 );

function robokassa_rur_currency( $currencies ) {
    $currencies["RUR"] = 'Russian Roubles (р.)';
    return $currencies;
}
add_filter( 'woocommerce_currencies', 'robokassa_rur_currency', 10, 1 );


/* Add a custom payment class to WC
  ------------------------------------------------------------ */
add_action('plugins_loaded', 'woocommerce_robokassa', 0);
function woocommerce_robokassa()
{
	if (!class_exists('WC_Payment_Gateway'))
		return; // if the WC payment gateway class is not available, do nothing
	if(class_exists('WC_ROBOKASSA'))
		return;
class WC_ROBOKASSA extends WC_Payment_Gateway
{
	public function __construct()
	{
		$plugin_dir = plugin_dir_url(__FILE__);
		
		global $woocommerce;

		$this->id = 'robokassa';
		$this->icon = apply_filters('woocommerce_robokassa_icon', ''.$plugin_dir.'robokassa.png');
		$this->has_fields = false;
        $this->liveurl = 'https://merchant.roboxchange.com/Index.aspx';
		$this->testurl = 'http://test.robokassa.ru/Index.aspx';

		
		
		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables
		$this->title = $this->settings['title'];
		$this->robokassa_merchant = $this->settings['robokassa_merchant'];
		$this->robokassa_key1 = $this->settings['robokassa_key1'];
		$this->robokassa_key2 = $this->settings['robokassa_key2'];
		$this->testmode = $this->settings['testmode'];
		$this->debug = $this->settings['debug'];
		$this->description = $this->settings['description'];
		$this->instructions = $this->settings['instructions'];

		// Logs
		if ($this->debug == 'yes')
		{
			$this->log = $woocommerce->logger();
		}

		// Actions
		add_action('init', array(&$this, 'check_ipn_response'));
		add_action('valid-robokassa-standard-ipn-reques', array(&$this, 'successful_request') );
		add_action('woocommerce_receipt_robokassa', array(&$this, 'receipt_page'));
		add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));

		if (!$this->is_valid_for_use())
		{
			$this->enabled = false;
		}
	}
	
	/**
	 * Check if this gateway is enabled and available in the user's country
	 */
	function is_valid_for_use()
	{
		if (!in_array(get_option('woocommerce_currency'), array('RUR')))
		{
			return false;
		}
		return true;
	}
	
	/**
	* Admin Panel Options 
	* - Options for bits like 'title' and availability on a country-by-country basis
	**/
	public function admin_options() {
		?>
		<h3><?php _e('ROBOKASSA', 'woocommerce'); ?></h3>
		<p><?php _e('Настройка приема электронных платежей через Merchant ROBOKASSA.', 'woocommerce'); ?></p>
		<table class="form-table">
		<?php
			if ( $this->is_valid_for_use() ) :
    	
    			// Generate the HTML For the settings form.
    			$this->generate_settings_html();
    		
    		else :
		?>
		<div class="inline error"><p><strong><?php _e('Шлюз отключен', 'woocommerce'); ?></strong>: <?php _e('ROBOKASSA не поддерживает валюты Вашего магазина.', 'woocommerce' ); ?></p></div>
		<?php
			endif;
		?>
		</table><!--/.form-table-->
		<?php
    } // End admin_options()

	function init_form_fields()
	{
		$this->form_fields = array
			(
				'enabled' => array
				(
					'title' => __('Включить/Выключить', 'woocommerce'),
					'type' => 'checkbox',
					'label' => __('Включен', 'woocommerce'),
					'default' => 'yes'
				),
				'title' => array
				(
					'title' => __('Название', 'woocommerce'),
					'type' => 'text', 
					'description' => __( 'Это название, которое пользователь видит во время проверки.', 'woocommerce' ), 
					'default' => __('ROBOKASSA', 'woocommerce')
				),
				'robokassa_merchant' => array
				(
					'title' => __('Логин', 'woocommerce'),
					'type' => 'text',
					'description' => __('Пожалуйста введите Логин', 'woocommerce'),
					'default' => 'demo'
				),
				'robokassa_key1' => array
				(
					'title' => __('Пароль #1', 'woocommerce'),
					'type' => 'password',
					'description' => __('Пожалуйста введите паоль №1.', 'woocommerce'),
					'default' => ''
				),
				'robokassa_key2' => array
				(
					'title' => __('Пароль #2', 'woocommerce'),
					'type' => 'password',
					'description' => __('Пожалуйста введите пароль №2.', 'woocommerce'),
					'default' => ''
				),
				'testmode' => array(
					'title' => __('Тест режим', 'woocommerce'),
					'type' => 'checkbox', 
					'label' => __('Включен', 'woocommerce'),
					'description' => __('В этом режиме плата за товар не снимается.', 'woocommerce'),
					'default' => 'no'
				),
				'debug' => array(
					'title' => __('Debug', 'woocommerce'),
					'type' => 'checkbox',
					'label' => __('Включить логирование (<code>woocommerce/logs/paypal.txt</code>)', 'woocommerce'),
					'default' => 'no'
				),
				'description' => array(
					'title' => __( 'Description', 'woocommerce' ),
					'type' => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your website.', 'woocommerce' ),
					'default' => 'Pay with cash upon delivery.'
				),
				'instructions' => array(
					'title' => __( 'Instructions', 'woocommerce' ),
					'type' => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page.', 'woocommerce' ),
					'default' => 'Pay with cash upon delivery.'
				)
			);
	}

	/**
	* There are no payment fields for sprypay, but we want to show the description if set.
	**/
	function payment_fields()
	{
		if ($this->description)
		{
			echo wpautop(wptexturize($this->description));
		}
	}
	/**
	* Generate the dibs button link
	**/
	public function generate_form($order_id)
	{
		global $woocommerce;

		$order = new WC_Order( $order_id );

		if ($this->testmode == 'yes')
		{
			$action_adr = $this->testurl;
		}
		else
		{
			$action_adr = $this->liveurl;
		}

		$out_summ = number_format($order->order_total, 2, '.', '');

		$crc = $this->robokassa_merchant.':'.$out_summ.':'.$order_id.':'.$this->robokassa_key1;

		$args = array
			(
				// Merchant
				'MrchLogin' => $this->robokassa_merchant,
				'OutSum' => $out_summ,
				'InvId' => $order_id,
				//'Desc' => ,
				'SignatureValue' => md5($crc),
				//'Shp_item' => 2,
				//'IncCurrLabel' => 'PCR',
				'Culture' => 'ru',
			);

		$paypal_args = apply_filters('woocommerce_robokassa_args', $args);

		$args_array = array();

		foreach ($args as $key => $value)
		{
			$args_array[] = '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
		}
/*
		$woocommerce->add_inline_js('
			jQuery("body").block({ 
					message: "<img src=\"'.esc_url( $woocommerce->plugin_url() ).'/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to PayPal to make payment.', 'woocommerce').'", 
					overlayCSS: 
					{ 
						background: "#fff", 
						opacity: 0.6 
					},
					css: { 
				        padding:        20, 
				        textAlign:      "center", 
				        color:          "#555", 
				        border:         "3px solid #aaa", 
				        backgroundColor:"#fff", 
				        cursor:         "wait",
				        lineHeight:		"32px"
				    } 
				});
			jQuery("#submit_robokassa_payment_form").click();
		');
*/
		return
			'<form action="'.esc_url($action_adr).'" method="POST" id="robokassa_payment_form">'."\n".
			implode("\n", $args_array).
			'<input type="submit" class="button alt" id="submit_robokassa_payment_form" value="'.__('Оплатить', 'woocommerce').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Отказаться от оплаты & вернуться в корзину', 'woocommerce').'</a>'."\n".
			'</form>';
	}
	
	/**
	 * Process the payment and return the result
	 **/
	function process_payment($order_id)
	{
		$order = new WC_Order($order_id);

		return array
		(
			'result' => 'success',
			'redirect'	=> add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
		);
	}
	
	/**
	* receipt_page
	**/
	function receipt_page($order)
	{
		echo '<p>'.__('Спасибо за Ваш заказ, пожалуйста, нажмите кнопку ниже, чтобы заплатить.', 'woocommerce').'</p>';
		echo $this->generate_form($order);
	}
	
	/**
	 * Check RoboKassa IPN validity
	 **/
	function check_ipn_request_is_valid($posted)
	{
		$out_summ = $posted['OutSum'];
		$inv_id = $posted['InvId'];
		$shp_item = $posted['Shp_item'];
		if ($posted['SignatureValue'] == strtoupper(md5($out_summ.':'.$inv_id.':'.$this->robokassa_key2)))
		{
			echo 'OK'.$inv_id;
			return true;
		}

		return false;
	}
	
	/**
	* Check Response
	**/
	function check_ipn_response()
	{
		global $woocommerce;

		if (isset($_GET['robokassa']) AND $_GET['robokassa'] == 'result')
		{
			@ob_clean();

			$_POST = stripslashes_deep($_POST);

			if ($this->check_ipn_request_is_valid($_POST))
			{
            	do_action('valid-robokassa-standard-ipn-reques', $_POST);
			}
			else
			{
				wp_die('IPN Request Failure');
			}
		}
		else if (isset($_GET['robokassa']) AND $_GET['robokassa'] == 'success')
		{
			$inv_id = $_POST['InvId'];
			$order = new WC_Order($inv_id);
			$order->update_status('on-hold', __('Платеж успешно оплачен', 'woocommerce'));
			// Reduce stock levels
			$order->reduce_order_stock();
			$woocommerce->cart->empty_cart();
			wp_redirect(add_query_arg('key', $order->order_key, add_query_arg('order', $inv_id, get_permalink(get_option('woocommerce_thanks_page_id')))));
			exit;
		}
		else if (isset($_GET['robokassa']) AND $_GET['robokassa'] == 'fail')
		{
			$inv_id = $_POST['InvId'];
			$order = new WC_Order($inv_id);
			$order->update_status('failed', __('Платеж не оплачен', 'woocommerce'));
			//$woocommerce->cart->empty_cart();
			wp_redirect($order->get_cancel_order_url());
			exit;
		}

		//echo add_query_arg('key', $order->order_key, add_query_arg('order', $inv_id, get_permalink(get_option('jigoshop_thanks_page_id'))));
	}

	/**
	* Successful Payment!
	**/
	function successful_request($posted)
	{
		global $woocommerce;

		$out_summ = $posted['OutSum'];
		$inv_id = $posted['InvId'];
		$shp_item = $posted['Shp_item'];

		$order = new WC_Order($inv_id);

		// Check order not already completed
		if ($order->status == 'completed')
		{
			exit;
		}

		// Payment completed
		//$order->add_order_note(__('Платеж успешно завершен.', 'woocommerce'));
		$order->payment_complete();
		exit;
	}
}

/**
 * Add the gateway to WooCommerce
 **/
function add_robokassa_gateway($methods)
{
	$methods[] = 'WC_ROBOKASSA';
	return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_robokassa_gateway');
}

 ?>