{*
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
*}

<div class="panel">
	<div class="row qvapay-header">
		<img src="{$module_dir|escape:'html':'UTF-8'}views/img/qvapay.png" class="col-xs-6 col-md-4 text-center" id="payment-logo" style="width: auto;" />
		<div class="col-xs-6 col-md-4 text-center">
			<h4>{l s='Procesador de Pagos Online' mod='qvapay'}</h4>
			<h4>{l s='Rapido - Seguro - Confiable' mod='qvapay'}</h4>
		</div>
		<div class="col-xs-12 col-md-4 text-center">
			<a href="https://qvapay.com/register" target="_blank" class="btn btn-primary" id="create-account-btn">{l s='Crea una cuenta ahora!' mod='qvapay'}</a><br />
			{l s='Ya tienes una cuenta?' mod='qvapay'}<a href="https://qvapay.com/login" target="_blank"> {l s='Log in' mod='qvapay'}</a>
		</div>
	</div>

	<hr />

	<div class="qvapay-content">
		<div class="row">
			<div class="col-md-6">
				<h5>{l s='Utiliza esta url como callback en tu aplicación de QvaPay' mod='qvapay'}</h5><br/>
				<code>{$callback}</code>
			</div>
		</div>
	</div>
</div>
