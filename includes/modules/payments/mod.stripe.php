<?php
/*
=====================================================

// Stripe Checkout Plugin for vldPersonals
// by Peter Laws (peter.laws@laws-hosting.co.uk)
// of Laws Hosting ( https://www.laws-hosting.co.uk )
// 17th July 2016

=====================================================
*/

require_once('stripe/init.php');

$data = array(
	'name' => 'Stripe Checkout',
	'label' => 'stripe',
	'settings' => array(
  		array(
			'name' => 'Description',
			'label' => 'stripe_description',
			'type' => 'text',
			'items' => array(),
			'value' => 'You will need to renew manually just before your subscription runs out.',
			'help' => 'This is the description to describe to your users',
		),
		array(
			'name' => 'Stripe Checkout Secret Key',
			'label' => 'secret_key',
			'type' => 'text',
			'items' => array(),
			'value' => '',
			'help' => 'Please enter your Stripe Checkout Gateway Secret Key here. Note: Do not give this out publically!',
		),
		array(
			'name' => 'Stripe Checkout Publishable Key',
			'label' => 'publishable_key',
			'type' => 'text',
			'items' => array(),
			'value' => '',
			'help' => 'Please enter your Stripe Checkout Gateway Publishable Key here.',
		),
		array(
			'name' => 'Stripe Checkout Test Secret Key',
			'label' => 'test_secret_key',
			'type' => 'text',
			'items' => array(),
			'value' => '',
			'help' => 'Please enter your Stripe Checkout Test Secret Key here. Note: Do not give this out publically!',
		),
		array(
			'name' => 'Stripe Checkout Test Publishable Key',
			'label' => 'test_publishable_key',
			'type' => 'text',
			'items' => array(),
			'value' => '',
			'help' => 'Please enter your Stripe Checkout Test Publishable Key here.',
		),
 		array(
			'name' => 'Currency',
			'label' => 'stripe_currency',
			'type' => 'select',
			'items' => array(
				'GBP' => '(GBP) Pound Sterling',
				'AUD' => '(AUD) Australian Dollar',
				'BRL' => '(BRL) Brazilian Real',
				'CAD' => '(CAD) Canadian Dollar',
				'CZK' => '(CZK) Czech Koruna',
				'DKK' => '(DKK) Danish Krone',
				'EUR' => '(EUR) Euro',
				'HKD' => '(HKD) Hong Kong Dollar',
				'HUF' => '(HUF) Hungarian Forint',
				'ILS' => '(ILS) Israeli New Sheqel',
				'JPY' => '(JPY) Japanese Yen',
				'MYR' => '(MYR) Malaysian Ringgit',
				'MXN' => '(MXN) Mexican Peso',
				'NOK' => '(NOK) Norwegian Krone',
				'NZD' => '(NZD) New Zealand Dollar',
				'PHP' => '(PHP) Philippine Peso',
				'PLN' => '(PLN) Polish Zloty',
				'SGD' => '(SGD) Singapore Dollar',
				'SEK' => '(SEK) Swedish Krona',
				'CHF' => '(CHF) Swiss Franc',
				'TWD' => '(TWD) Taiwan New Dollar',
				'THB' => '(THB) Thai Baht',
				'TRY' => '(TRY) Turkish Lira',
				'USD' => '(USD) U.S. Dollar',
			),
			'value' => '',
			'help' => 'Select preferred currency.',
		),
		array(
			'name' => 'Test mode',
			'label' => 'stripe_test',
			'type' => 'boolean',
			'items' => array(),
			'value' => '0',
			'help' => 'Enable if you want to run test transactions.',
		),
	),
);

function stripe_payment_form($package_id, $package_name, $package_amount, $package_group_id, $package_credits, $package_term_length, $package_term_type, $package_recurring, $member_gift_id = 0)
{
	global $DB, $PREFS, $SESSION, $LANG, $TEMPLATE, $file;

	stripe_fetch_settings();

	$notify_url = ($PREFS->conf['fancy_urls'] ? 'account/upgrade/ipn/stripe/' : 'index.php?m=account_upgrade&p=ipn&id=stripe');
	$redirect_url = ($PREFS->conf['fancy_urls'] ? 'account/settings' : 'index.php?m=account_settings');

  $html = '<h4>Choose Manually payment by Credit or Debit Card</h4>
          <p>'.$PREFS->conf['gateways']['stripe']['stripe_description'].'</p>
          <form name="StripeForm" id="StripeForm" method="post" action="">
          <input type="hidden" id="stripeToken" name="stripeToken" value="0">
          <input type="hidden" id="package_id" name="package_id" value="0">
          <input type="hidden" id="package_name" name="package_name" value="0">
          <input type="hidden" id="package_amount" name="package_amount" value="0">
          <input type="hidden" id="package_group_id" name="package_group_id" value="0">
          <input type="hidden" id="package_credits" name="package_credits" value="0">
          <input type="hidden" id="package_term_length" name="package_term_length" value="0">
          <input type="hidden" id="package_term_type" name="package_term_type" value="0">
          <input type="hidden" id="package_recurring" name="package_recurring" value="0">
          <input type="hidden" id="member_gift_id" name="member_gift_id" value="0">
          <button class="customButton" id="customButton"></button>
          </form>
          <h5>Note: We do not store your card details on our system.</h5>
          <script data-cfasync="false" src="https://checkout.stripe.com/checkout.js"></script>
          <script data-cfasync="false" type="text/javascript">
        var handler = StripeCheckout.configure({
        key: \'' . ($PREFS->conf['gateways']['stripe']['stripe_test'] == '1' ? $PREFS->conf['gateways']['stripe']['test_publishable_key'] : $PREFS->conf['gateways']['stripe']['publishable_key']) . '\',
        image: \'\',
        locale: \'auto\',
        token: function(token) {
            document.getElementById("stripeToken").value = token.id;
            document.getElementById("package_id").value = '.$package_id.';
            document.getElementById("package_name").value = "'.$package_name.'";
            document.getElementById("package_amount").value = '.$package_amount.';
            document.getElementById("package_group_id").value = '.$package_group_id.';
            document.getElementById("package_term_length").value = '.$package_term_length.';
            document.getElementById("package_term_type").value = '.$package_term_type.';
            document.getElementById("package_credits").value = '.$package_credits.';
            document.getElementById("package_recurring").value = '.$package_recurring.';
            document.getElementById("member_gift_id").value = '.$member_gift_id.';

            var formData = $(\'#StripeForm\').serialize();

            $.ajax({
                url : "'.VIR_PATH.$notify_url.'",
                type: \'POST\',
                data : formData,
                success: function(result, textStatus, jqXHR)
                  {
                   var dataRet = JSON.parse(result);

                   if ( dataRet.error == \'success\') {
                     BootstrapDialog.show({
                         title: \'Card Payment\',
                         message: \'Success: \' + dataRet.message,
                         type: \'TYPE_DANGER\',
                         buttons: [{
                            id: \'btn-ok\',
                            icon: \'glyphicon glyphicon-check\',
                            label: \'Continue\',
                            cssClass: \'btn-primary\',
                            autospin: false,
                            action: function(dialogRef){
                                  dialogRef.close();
                                  window.location.replace(\''.VIR_PATH.$redirect_url.'\');
                                 }
                         }]
                     });
                   }

                   if ( dataRet.error == \'error\') {
                     BootstrapDialog.show({
                         title: \'Card Payment\',
                         message: \'Error: \' + dataRet.message + \'<br>Please try again, or contact us.\',
                         type: \'TYPE_DANGER\',
                         buttons: [{
                            id: \'btn-ok\',
                            icon: \'glyphicon glyphicon-check\',
                            label: \'Close\',
                            cssClass: \'btn-primary\',
                            autospin: false,
                            action: function(dialogRef){
                                  dialogRef.close();
                                 }
                         }]
                     });
                   }

                   //if

                  },
                error: function (jqXHR, textStatus, errorThrown)
                  {
                     BootstrapDialog.show({
                         title: \'Card Payment\',
                         message: \'Error: \' + jqXHR.responseText,
                         type: \'TYPE_DANGER\',
                         buttons: [{
                            id: \'btn-ok\',
                            icon: \'glyphicon glyphicon-check\',
                            label: \'Close\',
                            cssClass: \'btn-primary\',
                            autospin: false,
                            action: function(dialogRef){
                                  dialogRef.close();
                                 }
                         }]
                     });

                  }
            });

        }
    });

    $(\'#customButton\').on(\'click\', function(e) {

        openHandler();

        e.preventDefault();
    });

    function openHandler() {
        handler.open({
            name: \'' . $PREFS->conf["app_title"] . '\',
            email: \'' . $SESSION->conf["member_email"] . '\',
            allowrememberme: false,
            description: \'' . htmlentities2utf8($package_name) . ' to '. $SESSION->conf["member_name"].'\',
            amount: ' . $package_amount*100 . ',
            currency: \'' . $PREFS->conf["gateways"]["stripe"]["stripe_currency"] . '\'
        });
    }
    $(window).on(\'popstate\', function() {
        handler.close();
    });
</script>';

	return $html;
}

//------------------------------------------------
// Process payment
//------------------------------------------------
function stripe_ipn()
{
 	global $DB, $PREFS, $SESSION, $LANG, $TEMPLATE, $file;

	if ( !count($_POST) ) return;
  stripe_fetch_settings();

  if ($PREFS->conf['gateways']['stripe']['stripe_test'] == '1') {
     \Stripe\Stripe::setApiKey($PREFS->conf['gateways']['stripe']['test_secret_key']);
  } else {
     \Stripe\Stripe::setApiKey($PREFS->conf['gateways']['stripe']['secret_key']);
  }

        $token = $_POST['stripeToken'];
        $totalAmount = sprintf("%01.2f", round($_POST['package_amount'], 2)) * 100;

        try {
          $charge = \Stripe\Charge::create( array (
           'source'      => $token,
           'amount'      => $totalAmount,
           'currency'    => $PREFS->conf['gateways']['stripe']['stripe_currency'],
           'description' => $PREFS->conf["app_title"] . ' Premium Membership - ' . htmlentities2utf8($_POST['package_name']) . ' to '. $SESSION->conf['member_name'],
           'metadata'    => array( 'order_id' => $SESSION->conf['member_id'] )
             ) );

            $charge = $charge->__toArray(TRUE);

            if($charge['failure_message'] == ''){

                if($charge['object'] == 'charge'){

                        if ( $charge['paid'] == true && in_array($charge['status'], array('succeeded', 'paid')) ) {
                            $chargeAmount = sprintf("%01.2f", round(($charge['amount'] / 100), 2));

                            $result = $DB->query("SELECT * FROM " . DB_PREFIX . "packages WHERE package_id=".$_POST['package_id']." LIMIT 1");
                            if ( !$DB->num_rows($result) ) {
                              echo json_encode( array(  'error'  => 'error', 'message' => 'No rows match' ) );
                              exit;
                            }
                              $obj = $DB->fetch_object($result);
                             	$package_price = $obj->price;
                             	$package_term = $obj->term;
                             	$package_type = $obj->termtype;
                             	$package_credits = $obj->credits;
                             	$new_group_id = $obj->group_id;

                          	if ( $chargeAmount != $package_price ) {
                              echo json_encode( array( 'error'  => 'error', 'message' => 'Price Mismatch ' . $chargeAmount . ' != ' . $package_price ) );
                              exit;
                            }
                             	$DB->query("INSERT INTO " . DB_PREFIX . "orders (member_id, member_gift_id, package_id, txn_id, paymentdate, amount, credits, term, termtype)
                               VALUES('" . $SESSION->conf['member_id'] . "','" . $_POST['$member_gift_id'] . "','" . $_POST['package_id'] .
                               "','" . $charge['id'] . "','" . $charge['created'] . "','" . $package_price . "','" . $package_credits .
                               "','" . $package_term . "','" . $package_type . "')");
                             	set_membership_term($SESSION->conf['member_id'], $_POST['member_gift_id'], $new_group_id, $package_type, $package_term, $package_credits);
                              $chargeAmount = $chargeAmount . ' ' . $PREFS->conf['gateways']['stripe']['stripe_currency'];
                              echo json_encode( array( 'error'  => 'success', 'message' => 'Your card was charged ' . $chargeAmount . '. Thank you.' ) );
                              exit;
                        }else{
                              echo json_encode( array( 'error'  => 'error', 'message' => 'There was an error performing this operation, please contact us' ) );
                              exit;
                        }
                }else{
                   echo json_encode( array( 'error'  => 'error', 'message' => 'There was an error performing this operation, please contact us' ) );
                   exit;
                }
            }else{
                echo json_encode( array( 'error'  => 'error', 'message' => 'There was an error performing this operation, please contact us' ) );
                exit;
            }
        } catch(\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (\Stripe\Error\RateLimit $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (\Stripe\Error\InvalidRequest $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (\Stripe\Error\Authentication $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (\Stripe\Error\ApiConnection $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (\Stripe\Error\Base $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        } catch (Exception $e) {
            echo json_encode( array(  'error'  => 'error', 'message' => $err['message'] ) );
            exit;
        }

}


//-----------------------------------------
// Create customer Stripe Checkout profile
//-----------------------------------------
function createCustomerProfile($params)
{
   global $DB, $PREFS, $SESSION, $LANG, $TEMPLATE, $file;

        try {
            if ($PREFS->conf['gateways']['stripe']['stripe_test'] == '1') {
                 \Stripe\Stripe::setApiKey($PREFS->conf['gateways']['stripe']['test_secret_key']);
            } else {
                 \Stripe\Stripe::setApiKey($PREFS->conf['gateways']['stripe']['secret_key']);
            }

            if(isset($params['stripeToken'])){
                $customer = \Stripe\Customer::create(array(
                    'email' => $SESSION->conf['member_email'],
                    'source'  => $params['stripeToken']
                ));
                fwrite( $file, 'customer create: ' . print_r ( $customer, TRUE ) . PHP_EOL );
            } else {
                echo FALSE;
            }
            $profile_id = $customer->id;

            // save stripe id
            $result = $DB->query("UPDATE " . DB_PREFIX . "members_conf SET stripecheckout = '".$profile_id."' WHERE conf_id = ".$SESSION->conf['member_id']."");

            echo array(
                'error'      => FALSE,
                'profile_id' => $profile_id
            );
        } catch(\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation: '.$err['message']
            );
        } catch (\Stripe\Error\RateLimit $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. Too many requests made to the API too quickly. '.$err['message']
            );
        } catch (\Stripe\Error\InvalidRequest $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. Invalid parameters were supplied to Stripe\'s API : ' .$err['message']
            );
        } catch (\Stripe\Error\Authentication $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. Authentication with Stripe\'s API failed. Maybe you changed API keys recently : '.$err['message']
            );
        } catch (\Stripe\Error\ApiConnection $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. Network communication with Stripe failed: '.$err['message']
            );
        } catch (\Stripe\Error\Base $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. '.$err['message']
            );
        } catch (Exception $e) {
            echo array(
                'error'  => TRUE,
                'message' => 'There was an error performing this operation. '.$e->getMessage()
            );
        }
    }

//------------------------------------------------
// Remove Customer
//------------------------------------------------
function CustomerRemove($params){

   global $DB, $PREFS, $SESSION, $LANG, $TEMPLATE, $file;

        try {
            // Use Stripe's bindings...
            \Stripe\Stripe::setApiKey($this->settings->get('plugin_stripecheckout_Stripe Checkout Gateway Secret Key'));


            $profile_id = '';
            $Billing_Profile_ID = '';
            $profile_id_array = array();
            $user = new User($params['User ID']);
            if($user->getCustomFieldsValue('Billing-Profile-ID', $Billing_Profile_ID) && $Billing_Profile_ID != ''){
                $profile_id_array = unserialize($Billing_Profile_ID);
                if(is_array($profile_id_array) && isset($profile_id_array['stripecheckout'])){
                    $profile_id = $profile_id_array['stripecheckout'];
                }
            }

            if($profile_id != ''){
                $customer = \Stripe\Customer::retrieve($profile_id);
                $customer = $customer->delete();

                if($customer->id == $profile_id && $customer->deleted == true){

                    if(is_array($profile_id_array)){
                        unset($profile_id_array['stripecheckout']);
                    }else{
                        $profile_id_array = array();
                    }

                    $user->updateCustomTag('Billing-Profile-ID', serialize($profile_id_array));
                    $user->save();

                    echo array(
                        'error'      => FALSE,
                        'profile_id' => $profile_id
                    );
                }else{
                    echo array(
                        'error'  => TRUE,
                        'message' => $this->user->lang("There was an error performing this operation.")
                    );
                }
            }else{
                echo array(
                    'error'  => TRUE,
                    'message' => $this->user->lang("There was an error performing this operation.").' '.$this->user->lang("The customer hasn't stored their credit card.")
                );
            }

        } catch(\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$err['message']
            );
        } catch (\Stripe\Error\RateLimit $e) {
            // Too many requests made to the API too quickly
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$this->user->lang("Too many requests made to the API too quickly.")." ".$err['message']
            );
        } catch (\Stripe\Error\InvalidRequest $e) {
            // Invalid parameters were supplied to Stripe's API.
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$this->user->lang("Invalid parameters were supplied to Stripe's API.")." ".$err['message']
            );
        } catch (\Stripe\Error\Authentication $e) {
            // Authentication with Stripe's API failed. Maybe you changed API keys recently.
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$this->user->lang("Authentication with Stripe's API failed. Maybe you changed API keys recently.")." ".$err['message']
            );
        } catch (\Stripe\Error\ApiConnection $e) {
            // Network communication with Stripe failed.
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$this->user->lang("Network communication with Stripe failed")." ".$err['message']
            );
        } catch (\Stripe\Error\Base $e) {
            // Display a very generic error to the user, and maybe send yourself an email.
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //A human-readable message giving more details about the error.
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$err['message']
            );
        } catch (Exception $e) {
            // Something else happened, completely unrelated to Stripe
            echo array(
                'error'  => TRUE,
                'message' => $this->user->lang("There was an error performing this operation.")." ".$e->getMessage()
            );
        }
}


//------------------------------------------------
// Fetch stripe id
//------------------------------------------------
function get_id()
{
	global $DB, $PREFS, $SESSION, $file;

	$result = $DB->query("SELECT stripecheckout FROM " . DB_PREFIX . "members_conf WHERE conf_id=".$SESSION->conf['member_id']." AND stripecheckout IS NOT NULL LIMIT 1");

	if ( $DB->num_rows($result) )
  {
     $obj = $DB->fetch_object($result);
     return $obj->stripecheckout;
  }
  return FALSE;
}

//------------------------------------------------
// Fetch settings
//------------------------------------------------
function stripe_fetch_settings()
{
  global $DB, $PREFS, $file;

	$result = $DB->query("SELECT * FROM " . DB_PREFIX . "payment_gateways WHERE label='stripe' LIMIT 1");

	if ($DB->num_rows($result))
	{
		$obj = $DB->fetch_object($result);
		$PREFS->conf['gateways']['stripe'] = @unserialize($obj->settings);
	}
}
