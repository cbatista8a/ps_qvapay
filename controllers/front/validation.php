<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
class QvapayValidationModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        /*
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false || !Tools::isSubmit('remote_id') || !Tools::isSubmit('id')) {
            die;
        }

        if ((bool)Configuration::get('QVAPAY_LIVE_MODE') == false){
        	die(json_encode(['response' => 'test mode active','id' => Tools::getValue('id','test'),'remote_id' => Tools::getValue('remote_id','test')]));
        }


        /**
         * get the send values
         */
        $remote_id = Tools::getValue('remote_id');
        list($cart_id,$customer_id) = explode('_',$remote_id);
		$cart = new Cart((int)$cart_id);
	    if (!$cart->id || $cart->id_customer != $customer_id){
		    die(json_encode(['response' => 'error','message' => 'Invalid remote_id']));
	    }
	    $amount = $cart->getOrderTotal();
        /*
         * Restore the context from the $cart_id & the $customer_id to process the validation properly.
         */
        Context::getContext()->cart = $cart;
        Context::getContext()->customer = new Customer((int) $customer_id);
        Context::getContext()->currency = new Currency((int) Context::getContext()->cart->id_currency);
        Context::getContext()->language = new Language((int) Context::getContext()->customer->id_lang);

        $secure_key = Context::getContext()->customer->secure_key;

        if ($this->isValidOrder($amount, $remote_id) === true) {
            $payment_status = Configuration::get('PS_OS_PAYMENT');
            $message = null;
        } else {
            $payment_status = Configuration::get('PS_OS_ERROR');

            /**
             * Add a message to explain why the order has not been validated
             */
            $message = $this->module->l('An error occurred while processing payment');
        }

        $module_name = $this->module->displayName;
        $currency_id = (int) Context::getContext()->currency->id;

        return $this->module->validateOrder($cart_id, $payment_status, $amount, $module_name, $message, array(), $currency_id, false, $secure_key);
    }

	/**
	 * @param int    $amount
	 * @param string $remote_id
	 *
	 * @return bool
	 */
	protected function isValidOrder($amount = 0, $remote_id = '')
    {
	    $endpoint = 'transactions';
	    $data = array(
		    'app_id' => Configuration::get('QVAPAY_ACCOUNT_ID'),
		    'app_secret' => Configuration::get('QVAPAY_ACCOUNT_SECRET'),
	    );
	    require_once $this->module->getLocalPath().'classes/Api.php';
	    $api = new QvaPay\Api();
	    $api->get($endpoint, $data);
	    if ($api->error()){
		    $this->errors[] = $api->errorMessage();
		    return false;
	    }else{
		    $response = $api->response();
		    $transactions = (array)$response->data;
		    return $this->transactionIsset($amount, $remote_id, $transactions);
	    }
    }

	/**
	 * @param $amount
	 * @param $remote_id
	 * @param $transactions
	 *
	 * @return bool
	 */
	public function transactionIsset($amount,$remote_id, $transactions){
	    foreach ($transactions as $transaction) {
	    	if ($transaction['amount'] == $amount && $transaction['remote_id'] == $remote_id){
	    		return true;
		    }
    	}
	    return false;
    }
}
