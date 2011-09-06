<?php

/**
	* WP eCommerce Test Merchant Gateway
	* This is the file for the test merchant gateway
	*
	* @package wp-e-comemrce
	* @since 3.7.6
	* @subpackage wpsc-merchants
*/

$nzshpcrt_gateways[$num] = array(
	'name' => 'TPV SERMEPA',
	'internalname' => 'wpsc_sermepa_tpv',
	'form' => 'form_sermepa_tpv',
	'function' => 'gateway_sermepa_tpv',
	'submit_function' => 'submit_sermepa_tpv',
	'payment_type' => "credit_card",
	'display_name' => 'Tarjeta de credito',
	'requirements' => array(
		'php_version' => 5.0,
	)
);


function gateway_sermepa_tpv($seperator, $sessionid){
	
	//$wpdb is the database handle,
	global $wpdb, $wpsc_cart;
	
	//This grabs the purchase log id from the database that refers to the $sessionid
	$purchase_log_sql = "SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `sessionid`= ".$sessionid." LIMIT 1";
	$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;

	$cart_sql = "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
	$cart = $wpdb->get_results($cart_sql,ARRAY_A) ;
	
	
	// Ds_Merchant_MerchantCode = 'qwertyasdf0123456789';
	// Ds_Merchant_Terminal='4'; //número de terminal
	// Ds_Merchant_TransactionType = '0';
	// Ds_Merchant_Currency='978'; 978 EUR, 
	// Ds_Merchant_MerchantURL='http://www.sermepa.es';
	// Ds_Merchant_MerchantCode='999008881'; //código de comercio proporcionado por caixa
	// Ds_Merchant_Order=date('ymdHis');
	// $amount= number_format($wpsc_cart->total_price,2);
	// $signature = strtoupper(sha1($message));
	
	//variables obligatorias
	$data['Ds_Merchant_Terminal'] = get_option('merchant_terminal');
	$data['Ds_Merchant_TransactionType'] = get_option('transaction_type');
	$data['Ds_Merchant_MerchantURL'] = get_option('siteurl');
	$data['Ds_Merchant_Currency'] = get_option('merchant_currency');
	
	// variables opcionales
	$data['Ds_Merchant_MerchantName'] = get_option('merchant_name');
	$data['Ds_Merchant_ProductDescription'] = get_option('merchant_description');
	$data['Ds_Merchant_Titular'] = get_option('merchant_titular');
	$data['Ds_Merchant_MerchantCode'] = get_option('merchant_code');
	$data['Ds_Merchant_UrlOK'] = get_option('merchant_url_ok')."/?sermepa_tpv_callback=ok";
	$data['Ds_Merchant_UrlKO'] = get_option('merchant_url_ko')."/?sermepa_tpv_callback=ko";
	

	$data['Ds_Merchant_Order'] = str_pad((int) $purchase_log[0]['id'],12,"0",STR_PAD_LEFT);;
	$data['Ds_Merchant_Amount'] = number_format(sprintf("%01.2f", $wpsc_cart->total_price),2,'.','') * 100;
	$data['Ds_Merchant_MerchantData'] = $sessionid;
	
	$key = trim(get_option('merchant_key'));
	$message =  $data['Ds_Merchant_Amount'].
				$data['Ds_Merchant_Order'].
				$data['Ds_Merchant_MerchantCode'].
				$data['Ds_Merchant_Currency'].
				$data['Ds_Merchant_TransactionType'].
				$data['Ds_Merchant_MerchantURL'].
				$key;
				
	$data['Ds_Merchant_MerchantSignature'] = strtoupper(sha1($message));
	
	// Create form to redirect to payment gateway
	$output = "<p>Redireccionando automaticamente a la pasarela de pago.</p>";
	$output = "<form id=\"tpv_form\" name=\"tpv_form\" method=\"post\" action=\"".get_option('merchant_url')."\">\n";
	
	foreach($data as $n=>$v) {
			$output .= "			<input type=\"hidden\" name=\"$n\" value=\"$v\" />\n";
	}
	
	$output .= "			<input type=\"submit\" value=\"Continuar\" />
		</form>
	";		
	
	// echo form.. 
	if( get_option('merchant_debug') == 1)
	{
		echo ("DEBUG MODE ON!!<br/>");
		echo("The following form is created and would be posted to Sermespa for processing.  Press submit to continue:<br/>");
		echo("<pre>".htmlspecialchars($output)."</pre>");
	}
	
	echo($output);
	
	if(get_option('merchant_debug') == 0)
	{
		echo "<script language=\"javascript\" type=\"text/javascript\">document.getElementById('tpv_form').submit();</script>";
	}

  	exit();	
}
function submit_sermepa_tpv() {
	
	if(isset($_POST['merchant_terminal']))
    {
    	update_option('merchant_terminal', $_POST['merchant_terminal']);
    }
    
  	if(isset($_POST['transaction_type']))
    {
    	update_option('transaction_type', $_POST['transaction_type']);
    }
    
  	if(isset($_POST['merchant_url']))
    {
    	update_option('merchant_url', $_POST['merchant_url']);
    }
    
	if(isset($_POST['merchant_url_ok']))
    {
    	update_option('merchant_url_ok', $_POST['merchant_url_ok']);
    }
	if(isset($_POST['merchant_url_ko']))
    {
    	update_option('merchant_url_ko', $_POST['merchant_url_ko']);
    }

  	if(isset($_POST['merchant_currency']))
    {
    	update_option('merchant_currency', $_POST['merchant_currency']);
    }
    
  	if(isset($_POST['merchant_name']))
    {
    	update_option('merchant_name', $_POST['merchant_name']);
    }

 	if(isset($_POST['merchant_description']))
    {
    	update_option('merchant_description', $_POST['merchant_description']);
    }

 	if(isset($_POST['merchant_titular']))
    {
    	update_option('merchant_titular', $_POST['merchant_titular']);
    }

 	if(isset($_POST['merchant_code']))
    {
    	update_option('merchant_code', $_POST['merchant_code']);
    }
 	if(isset($_POST['merchant_key']))
    {
    	update_option('merchant_key', $_POST['merchant_key']);
    }

  	if(isset($_POST['merchant_debug']))
    {
    	update_option('merchant_debug', $_POST['merchant_debug']);
    }

  	if(isset($_POST['merchant_debug']))
    {
    	update_option('merchant_debug', $_POST['merchant_debug']);
    }

  	if(isset($_POST['merchant_notification']))
    {
    	update_option('merchant_notification', $_POST['merchant_notification']);
    }
    
	return true;
}
function form_sermepa_tpv()
{	
	$select_currency[get_option('merchant_currency')] = "selected='selected'";
	$merchant_debug = get_option('merchant_debug');
	$merchant_url = (  get_option('merchant_url') == ''  ? 'https://sis-t.sermepa.es:25443/sis/realizarPago' : get_option('merchant_url') );
	$merchant_url_ok = (  get_option('merchant_url_ok') == ''  ? get_option('siteurl') : get_option('merchant_url_ok') );
	$merchant_url_ko = (  get_option('merchant_url_ko') == ''  ? get_option('siteurl') : get_option('merchant_url_ko') );
		
	$merchant_debug1 = "";
	$merchant_debug2 = "";

	switch($merchant_debug)
	{
		case 0:
			$merchant_debug2 = "checked ='checked'";
			break;
		case 1:
			$merchant_debug1 = "checked ='checked'";
			break;
	}
	
	if (!isset($select_currency['978'])) $select_currency['978'] = ''; 
	if (!isset($select_currency['USD'])) $select_currency['USD'] = ''; 
	if (!isset($select_currency['GBP'])) $select_currency['GBP'] = ''; 

	
	$output = "
		<tr>
			<td>Merchant Name</td>
			<td><input type='text' size='40' value='".get_option('merchant_name')."' name='merchant_name' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This will be displayed as your shop name.</small></td>
		</tr>
		<tr>
			<td>Merchant Description</td>
			<td><input type='text' size='40' value='".get_option('merchant_description')."' name='merchant_description' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Short description of your shop.</small></td>
		</tr>
		<tr>
			<td>Merchant Code</td>
			<td><input type='text' size='40' value='".get_option('merchant_code')."' name='merchant_code' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Code</small></td>
		</tr>
		<tr>
			<td>Merchant Key</td>
			<td><input type='text' size='40' value='".get_option('merchant_key')."' name='merchant_key' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Code</small></td>
		</tr>
		<tr>
			<td>Titular</td>
			<td><input type='text' size='40' value='".get_option('merchant_titular')."' name='merchant_titular' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Titular</small></td>
		</tr>
		<tr>
			<td>Accepted Currency (USD, EUR)</td>
			<td><select name='merchant_currency'>
					<option ".$select_currency['978']." value='978'>EUR - Euros</option>
					<option ".$select_currency['USD']." value='USD'>USD - U.S. Dollar</option>
					<option ".$select_currency['GBP']." value='GBP'>GBP - Great Britain Pounds</option>
				</select> 
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>The currency code that Sermespa will process the payment in. All products must be set up in this currency.</small></td>
		</tr>
		<tr>
			<td>Transaction OK Url</td>
			<td><input type='text' size='40' value='".$merchant_url_ok."' name='merchant_url_ok' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This page will be shown when transation is ok.</small></td>
		</tr>
		<tr>
			<td>Transaction KO Url</td>
			<td><input type='text' size='40' value='".$merchant_url_ko."' name='merchant_url_ko' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This page will be shown when transation is failed.</small></td>
		</tr>
		<tr>
			<td>Payment Url</td>
			<td><input type='text' size='40' value='".$merchant_url."' name='merchant_url' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This is the url for connecting provided by Sermepa TPV (debug or production one)</small></td>
		</tr>
		<tr>
			<td>N. Terminal</td>
			<td><input type='text' size='40' value='".get_option('merchant_terminal')."' name='merchant_terminal' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Number of terminal provided by payment gateway</small></td>
		</tr>
		<tr>
			<td>Transaction Type</td>
			<td><input type='text' size='40' value='".get_option('transaction_type')."' name='transaction_type' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Transaction Type</small></td>
		</tr>
		<tr>
			<td>Debug Mode</td>
			<td>
				<input type='radio' value='1' name='merchant_debug' id='merchant_debug1' ".$merchant_debug1." /> <label for='merchant_debug1'>".__('Yes', 'wpsc')."</label> &nbsp;
				<input type='radio' value='0' name='merchant_debug' id='merchant_debug2' ".$merchant_debug2." /> <label for='merchant_debug2'>".__('No', 'wpsc')."</label>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Debug mode is used to write HTTP communications between the Sermespa server and your host to a log file.  This should only be activated for testing!</small></td>
		</tr>
		<tr>
			<td>Email Notification</td>
			<td><input type='text' size='40' value='".get_option('merchant_notification')."' name='merchant_notification' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Email used to send notifications for debug.</small></td>
		</tr>
	";
	return $output;
}

function nzshpcrt_sermepa_tpv_callback()
{
	global $wpdb, $wpsc_cart;
	// needs to execute on page start
	// look at page 36
	if( isset($_GET['sermepa_tpv_callback']) )
	{
		$transactionok = $_GET['sermepa_tpv_callback'] == 'ok' ? true : false;
		$sessionid = $_GET['Ds_MerchantData'];
		// If in debug, email details
		if(get_option('merchant_debug') == 1)
		{
			$message = "This is a debugging message sent because it appears that you are in debug mode.\n\rEnsure debug is turned off once you are happy with the function.\n\r\n\r";
			$message .= "OUR_POST:\n\r".print_r($header . $req,true)."\n\r\n\r";
			$message .= "THEIR_POST:\n\r".print_r($_POST,true)."\n\r\n\r";
			$message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
			$message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
			$message .= "session:\n\r".$sessionid."\n\r\n\r";
			mail(get_option('merchant_notification'), "Sermepa Debug message", $message);
		}
		
		// Only transactions under 100 are considered accepted by sermepa. Any other transaction is dismissed
		if( $transactionok && isset($_GET['Ds_TransactionType']) && ($_GET['Ds_TransactionType'] < 100) )
		{
			$wpdb->query("UPDATE `".WPSC_TABLE_PURCHASE_LOGS."` SET 
											`processed` = '2'
										WHERE `sessionid` = ".$sessionid." LIMIT 1");
			// Clears the cart?
//			transaction_results( $sessionid, false );
			$wpsc_cart->empty_cart();
		} 
		else
		{
			$log_id = $wpdb->get_var("SELECT `id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `sessionid`='$sessionid' LIMIT 1");
			$delete_log_form_sql = "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='$log_id'";
			$cart_content = $wpdb->get_results($delete_log_form_sql,ARRAY_A);
			foreach((array)$cart_content as $cart_item)
			{
				$cart_item_variations = $wpdb->query("DELETE FROM `".WPSC_TABLE_CART_ITEM_VARIATIONS."` WHERE `cart_id` = '".$cart_item['id']."'", ARRAY_A);
			}
			$wpdb->query("DELETE FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='$log_id'");
			$wpdb->query("DELETE FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` IN ('$log_id')");
			$wpdb->query("DELETE FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `id`='$log_id' LIMIT 1");
		}
	}
}

add_action('init', 'nzshpcrt_sermepa_tpv_callback');

?>