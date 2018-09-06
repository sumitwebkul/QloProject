{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
	{else}
	{assign var="back_order_page" value="order.php"}
{/if}

<section id="wrapper">
	<div class="container">
		<section id="content">
			<div class="row">
				<div class="col-md-8">
					{if $PS_CATALOG_MODE}
						{capture name=path}{l s='Your shopping cart'}{/capture}
						<h2 id="cart_title">{l s='Your shopping cart'}</h2>
						<p class="alert alert-warning">{l s='Your new order was not accepted.'}</p>
					{else}
						{if $productNumber && isset($cart_htl_data)}
							 {* Shopping Cart  *}
							{if isset($cartChanged) && $cartChanged}
								<p class="alert alert-danger">{l s='Your Booking Cart has been changed automatically as some rooms in your booking cart has been booked by another customer.'}</p>
							{/if}
							<div class="card col-sm-12">
								{* This tpl includes room type lists in the orders *}
								{include file="$tpl_dir./shopping-cart.tpl"}
							</div>
							{* End Shopping Cart *}
							{if !$is_logged}
								<div class="card col-sm-12">
									<!-- Create account / Guest account / Login block -->
									{include file="$tpl_dir./order-opc-new-account.tpl"}
									<!-- END Create account / Guest account / Login block -->
								</div>
							{/if}
							<div class="card col-sm-12">
								{* Advanced Payment Options *}
								{include file="$tpl_dir./order-opc-advanced-payment-option.tpl"}
							</div>

							<div class="card col-sm-12">
								<!-- Carrier -->
								{include file="$tpl_dir./order-carrier.tpl"}
								<!-- END Carrier -->
							</div>
							<div class="card col-sm-12">
								<!-- Payment -->
								{include file="$tpl_dir./order-payment.tpl"}
								<!-- END Payment -->
							</div>
						{else}
							{capture name=path}{l s='Your shopping cart'}{/capture}
							<h2 class="page-heading">{l s='Your shopping cart'}</h2>
							{include file="$tpl_dir./errors.tpl"}

							{if isset($cartChanged) && $cartChanged}
								<p class="alert alert-danger">{l s='Your booking cart has been changed automatically as some rooms in your booking cart has been booked by another customer.'}</p>
							{/if}

							<p class="alert alert-warning">{l s='Till now you did not enter any room in your cart.'}</p>
						{/if}
						{strip}
							{addJsDef imgDir=$img_dir}
							{addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
							{addJsDef orderOpcUrl=$link->getPageLink("order-opc", true)|escape:'quotes':'UTF-8'}
							{addJsDef historyUrl=$link->getPageLink("history", true)|escape:'quotes':'UTF-8'}
							{addJsDef guestTrackingUrl=$link->getPageLink("guest-tracking", true)|escape:'quotes':'UTF-8'}
							{addJsDef addressUrl=$link->getPageLink("address", true, NULL, "back={$back_order_page}")|escape:'quotes':'UTF-8'}
							{addJsDef orderProcess='order-opc'}
							{addJsDef guestCheckoutEnabled=$PS_GUEST_CHECKOUT_ENABLED|intval}
							{addJsDef displayPrice=$priceDisplay}
							{addJsDef taxEnabled=$use_taxes}
							{addJsDef conditionEnabled=$conditions|intval}
							{addJsDef vat_management=$vat_management|intval}
							{addJsDef errorCarrier=$errorCarrier|@addcslashes:'\''}
							{addJsDef errorTOS=$errorTOS|@addcslashes:'\''}
							{addJsDef checkedCarrier=$checked|intval}
							{addJsDef addresses=array()}
							{addJsDef isVirtualCart=$isVirtualCart|intval}
							{addJsDef isPaymentStep=$isPaymentStep|intval}
							{addJsDefL name=txtWithTax}{l s='(tax incl.)' js=1}{/addJsDefL}
							{addJsDefL name=txtWithoutTax}{l s='(tax excl.)' js=1}{/addJsDefL}
							{addJsDefL name=txtHasBeenSelected}{l s='has been selected' js=1}{/addJsDefL}
							{addJsDefL name=txtNoCarrierIsSelected}{l s='No carrier has been selected' js=1}{/addJsDefL}
							{addJsDefL name=txtNoCarrierIsNeeded}{l s='No carrier is needed for this order' js=1}{/addJsDefL}
							{addJsDefL name=txtConditionsIsNotNeeded}{l s='You do not need to accept the Terms of Service for this order.' js=1}{/addJsDefL}
							{addJsDefL name=txtTOSIsAccepted}{l s='The service terms have been accepted' js=1}{/addJsDefL}
							{addJsDefL name=txtTOSIsNotAccepted}{l s='The service terms have not been accepted' js=1}{/addJsDefL}
							{addJsDefL name=txtThereis}{l s='There is' js=1}{/addJsDefL}
							{addJsDefL name=txtErrors}{l s='Error(s)' js=1}{/addJsDefL}
							{addJsDefL name=txtDeliveryAddress}{l s='Delivery address' js=1}{/addJsDefL}
							{addJsDefL name=txtInvoiceAddress}{l s='Invoice address' js=1}{/addJsDefL}
							{addJsDefL name=txtModifyMyAddress}{l s='Modify my address' js=1}{/addJsDefL}
							{addJsDefL name=txtInstantCheckout}{l s='Instant checkout' js=1}{/addJsDefL}
							{addJsDefL name=txtSelectAnAddressFirst}{l s='Please start by selecting an address.' js=1}{/addJsDefL}
							{addJsDefL name=txtFree}{l s='Free' js=1}{/addJsDefL}

							{capture}{if $back}&mod={$back|urlencode}{/if}{/capture}
							{capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
							{addJsDef addressUrl=$smarty.capture.addressUrl}
							{capture}{'&multi-shipping=1'|urlencode}{/capture}
							{addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
							{capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='}{/capture}
							{addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
							{addJsDef opc=$opc|boolval}
							{capture}<h3 class="page-subheading">{l s='Your billing address' js=1}</h3>{/capture}
							{addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
							{capture}<h3 class="page-subheading">{l s='Your delivery address' js=1}</h3>{/capture}
							{addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
							{capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd}" title="{l s='Update' js=1}"><span>{l s='Update' js=1}<i class="icon-chevron-right right"></i></span></a>{/capture}
							{addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
						{/strip}
					{/if}
				</div>
				<div class="col-md-4">
					{* Total cart details, tax details, advance payment details and voucher details *}
					<div class="col-sm-12 card cart_total_detail_block">
						<p>
							<span>Total Quantity Of Rooms</span> -
							<span class="cart_total_values">$ 1000.00</span>
						</p>
						<p>
							<span>Hotel GST</span> -
							<span class="cart_total_values">$ 1000.00</span>
						</p>
						<p>
							<span>Hotel Charges</span> -
							<span class="cart_total_values">$ 1000.00</span>
						</p>
						<p>
							<span>Total Quantity Of Rooms</span> -
							<span class="cart_total_values">$ 1000.00</span>
						</p>
						<p>
							<span>Total Quantity Of Rooms</span> -
							<span class="cart_total_values">$ 1000.00</span>
						</p>
						{if $use_taxes && $show_taxes && $total_tax != 0 }
							{if $priceDisplay != 0}
								<p class="cart_total_price">
									<span>{if $display_tax_label}{l s='Total (tax excl.)'}{else}{l s='Total'}{/if}</span> -
									<span class="cart_total_values">{displayPrice price=$total_price_without_tax}</span>
								</p>
							{/if}
							<p class="cart_total_tax">
								<span>{l s='Tax'}</span> -
								<span class="cart_total_values">{displayPrice price=$total_tax}</span>
							</p>
						{/if}
						{if $use_taxes && $show_taxes && $total_tax != 0 }
							<p class="cart_total_price">
								<span>{if $display_tax_label}{l s='Total (tax excl.)'}{else}{l s='Total'}{/if}</span> -
								<span class="cart_total_values">{displayPrice price=$total_price_without_tax}</span>
							</p>
							{if $priceDisplay != 0}
							<tr class="table_tfoot table_total_tr cart_total_price">
								<td colspan="4" class="text-right"></td>
								<td colspan="3" class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
							</tr>
							{/if}
							<tr class="table_tfoot cart_total_tax">
								<td colspan="4" class="text-right">{l s='Tax'}</td>
								<td colspan="3" class="price" id="total_tax">{displayPrice price=$total_tax}</td>
							</tr>
						{/if}
						<p class="total_discount_block {if $total_discounts == 0} unvisible{/if}">
							<span>
								{if $display_tax_label}
									{if $use_taxes && $priceDisplay == 0}
										{l s='Total vouchers (tax incl.)'}
									{else}
										{l s='Total vouchers (tax excl.)'}
									{/if}
								{else}
									{l s='Total vouchers'}
								{/if}
							</span> -
							<span class="cart_total_values">
								{if $use_taxes && $priceDisplay == 0}
									{assign var='total_discounts_negative' value=$total_discounts * -1}
								{else}
									{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
								{/if}
								{displayPrice price=$total_discounts_negative}
							</span>
						</p>
						{if isset($customer_adv_dtl)}
							<p>
								<span>{l s='Advance Payment Amount'}</span> -
								<span class="cart_total_values">{displayPrice price=$adv_amount}</span>
							</p>
							<p>
								<span>{l s='Due Amount'}</span> -
								<span class="cart_total_values">{displayPrice price=$customer_adv_dtl['due_amount']}</span>
							</p>
						{/if}
						<p class="cart_final_total_block">
							{if isset($customer_adv_dtl)}
								<span>Total Quantity Of Rooms</span> -
								<span class="cart_total_values">{displayPrice price=$customer_adv_dtl['total_to_be_paid']}</span>
							{else}
								<span>{l s='Total'}</span> -
								<span class="cart_total_values">
									{if $use_taxes}
										{displayPrice price=$total_price}
									{else}
										{displayPrice price=$total_price_without_tax}
									{/if}
								</span>
								<div class="hookDisplayProductPriceBlock-price">
									{hook h="displayCartTotalPriceLabel"}
								</div>
							{/if}
						</p>
					</div>
					<div class="col-sm-12 card cart_voucher_detail_block">
						<p class="cart_voucher_head">{l s='Apply Coupon'}</p>
						{if $displayVouchers}
							<p><span>{l s='Have promocode ?'}</span></p>
							<div id="display_cart_vouchers">
								{foreach $displayVouchers as $voucher}
									<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">
										{$voucher.code|escape:'html':'UTF-8'} <i class="icon-times"></i>
									</span>
									<span class="voucher_apply_state">
										<img src="./themes/hotel-reservation-theme/img/icon/form-ok-circle.svg" /> {l s='Applied'}
									</span>
								{/foreach}
							</div>
						{/if}
					</div>
				</div>
			</div>
		</section>
	</div>
</section>