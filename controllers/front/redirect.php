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

class QvapayRedirectModuleFrontController extends ModuleFrontController
{
    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function postProcess()
    {
        /*
         * Oops, an error occured.
         */
        if (Tools::getValue('action') == 'error') {
            return $this->displayError('An error occurred while trying to redirect the customer');
        } else {

            /**
			* set de params and redirect to QvaPay process payment page
			* app_id: ID de la aplicación
			* app_secret: Clave secreta de la aplicación
			* amount: Cantidad de dinero a recibir (en dólares y con 2 decimales)
			* description: Descripción de la factura a generar, útil para brindar información al pagador. (No más de 300 caracteres)
			* remote_id: ID de factura en el sistema remoto (no requerido)
			* signed: Generación de una URL firmada o no (URL firmadas vencen a los 30 minutos, aportando más seguridad o caducidad)
             */
            $endpoint = 'create_invoice';
            $remote_id = $this->context->cart->id.'_'.$this->context->customer->id;
            $amount = $this->context->cart->getOrderTotal();
            $data = array(
            	'app_id' => Configuration::get('QVAPAY_ACCOUNT_ID'),
            	'app_secret' => Configuration::get('QVAPAY_ACCOUNT_SECRET'),
            	'amount' => $amount,
            	'description' => $this->trans('Payment from Services on ',[],$this->module->name).$this->context->shop->name,
            	'remote_id' => $remote_id,
            	'signed' => true,
            );
	        require_once $this->module->getLocalPath().'classes/Api.php';
            $api = new QvaPay\Api();
            $api->get($endpoint, $data);
            if ($api->error()){
            	return $this->displayError($api->errorMessage());
            }else{
	            $response = $api->response();
	            if (!empty($response->signedUrl)){
            		Tools::redirect($response->signedUrl);
	            }
            }

        }
    }

    protected function displayError($message, $description = false)
    {
        /*
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);
	    $this->context->smarty->assign('errors',$this->errors);
        return $this->setTemplate('module:qvapay/views/templates/front/error.tpl');
    }
}
