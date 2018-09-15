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
				{if $PS_CATALOG_MODE}
					{capture name=path}{l s='Your shopping cart'}{/capture}
					<h2 id="cart_title">{l s='Your shopping cart'}</h2>
					<p class="alert alert-warning">{l s='Your new order was not accepted.'}</p>
				{else}
					{if $productNumber && isset($cart_htl_data)}
						<div class="col-md-8">
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
							{* <div class="card col-sm-12">
								<!-- Carrier -->
								{include file="$tpl_dir./order-carrier.tpl"}
								<!-- END Carrier -->
							</div> *}
							<div class="card col-sm-12">
								<!-- Payment -->
								{include file="$tpl_dir./order-payment.tpl"}
								<!-- END Payment -->
							</div>
						</div>
						<div class="col-md-4">
							{* Total cart details, tax details, advance payment details and voucher details *}
							<div class="col-sm-12 card cart_total_detail_block">
								<p>
									<span>{l s='Total Rooms Cost'}</span> -
									<span class="cart_total_values">{displayPrice price=$total_products}</span>
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
								<p {if $total_wrapping == 0} class="unvisible"{/if}>
									<span>
										{if $use_taxes}
											{if $display_tax_label}{l s='Total gift wrapping (tax incl.)'}{else}{l s='Total gift-wrapping cost'}{/if}
										{else}
											{l s='Total gift-wrapping cost'}
										{/if}
									</span> -
									<span class="cart_total_values">
										{if $use_taxes}
											{if $priceDisplay}
												{displayPrice price=$total_wrapping_tax_exc}
											{else}
												{displayPrice price=$total_wrapping}
											{/if}
										{else}
											{displayPrice price=$total_wrapping_tax_exc}
										{/if}
									</span>
								</p>
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
								<p><span>{l s='Have promocode ?'}</span></p>
								<div class="row margin-btm-10">
									<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
										<div class="col-xs-8">
											<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
											<input type="hidden" name="submitDiscount" />
										</div>
										<div class="col-xs-4">
											<button type="submit" name="submitAddDiscount" class="btn opc-button-medium">
												<span>{l s='Apply'}</span>
											</button>
										</div>
									</form>
								</div>


								{* {if $use_taxes}
									{if $priceDisplay}
										<tr class="cart_total_price table_tfoot">
											<td rowspan="6" colspan="3" id="cart_voucher" class="cart_voucher">
												{if $voucherAllowed}
													{if isset($errors_discount) && $errors_discount}
														<ul class="alert alert-danger">
															{foreach $errors_discount as $k=>$error}
																<li>{$error|escape:'html':'UTF-8'}</li>
															{/foreach}
														</ul>
													{/if}
													<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
														<fieldset>
															<h4>{l s='Vouchers'}</h4>
															<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
															<input type="hidden" name="submitDiscount" />
															<button type="submit" name="submitAddDiscount" class="btn btn-default"><span>{l s='OK'}</span></button>
														</fieldset>
													</form>
													{if $displayVouchers}
														<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
														<div id="display_cart_vouchers">
															{foreach $displayVouchers as $voucher}
																{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
															{/foreach}
														</div>
													{/if}
												{/if}
											</td>
										</tr>
									{else}
										<tr class="cart_total_price table_tfoot">
											<td rowspan="6" colspan="3" id="cart_voucher" class="cart_voucher">
												{if $voucherAllowed}
													{if isset($errors_discount) && $errors_discount}
														<ul class="alert alert-danger">
															{foreach $errors_discount as $k=>$error}
																<li>{$error|escape:'html':'UTF-8'}</li>
															{/foreach}
														</ul>
													{/if}
													<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
														<fieldset>
															<h4>{l s='Vouchers'}</h4>
															<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
															<input type="hidden" name="submitDiscount" />
															<button type="submit" name="submitAddDiscount" class="btn btn-default"><span>{l s='OK'}</span></button>
														</fieldset>
													</form>
													{if $displayVouchers}
														<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
														<div id="display_cart_vouchers">
															{foreach $displayVouchers as $voucher}
																{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
															{/foreach}
														</div>
													{/if}
												{/if}
											</td>
										</tr>
									{/if}
								{else}
									<tr class="cart_total_price table_tfoot">
										<td rowspan="{$rowspan_total}" colspan="3" id="cart_voucher" class="cart_voucher">
											{if $voucherAllowed}
												{if isset($errors_discount) && $errors_discount}
													<ul class="alert alert-danger">
														{foreach $errors_discount as $k=>$error}
															<li>{$error|escape:'html':'UTF-8'}</li>
														{/foreach}
													</ul>
												{/if}
												<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
													<fieldset>
														<h4>{l s='Vouchers'}</h4>
														<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
														<input type="hidden" name="submitDiscount" />
														<button type="submit" name="submitAddDiscount" class="btn btn-default">
															<span>{l s='OK'}</span>
														</button>
													</fieldset>
												</form>
												{if $displayVouchers}
													<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
													<div id="display_cart_vouchers">
														{foreach $displayVouchers as $voucher}
															{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
														{/foreach}
													</div>
												{/if}
											{/if}
										</td>
									</tr>
								{/if} *}

								{* The available highlighted vouchers for the customer*}
								{* {if $displayVouchers}
									<div class="row">
										{foreach $displayVouchers as $voucher}
											<div class="col-sm-12 cart_applied_voucher">
												<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">
													{$voucher.code|escape:'html':'UTF-8'} <i class="icon-times pull-right"></i>
												</span>
												<span class="voucher_apply_state pull-right">
													<img src="./themes/hotel-reservation-theme/img/icon/form-ok-circle.svg" /> {l s='Applied'}
												</span>
											</div>
										{/foreach}
									</div>
								{/if} *}


								{if sizeof($discounts)}
									<hr class="seperator">
									<div class="row">
										{foreach $discounts as $discount}
											{if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
												{continue}
											{/if}

											<div class="col-sm-12 margin-btm-10 cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
												<span class="cart_discount_name">
													{$discount.name|escape:'html':'UTF-8'}
													{if strlen($discount.code)}
														<a
															href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}"
															class="price_discount_delete pull-right"
															title="{l s='Delete'}">
															<i class="icon-times"></i>
														</a>
													{/if}
												</span>
												<span class="voucher_apply_state pull-right">
													<img src="./themes/hotel-reservation-theme/img/icon/form-ok-circle.svg" /> {l s='Applied'}
												</span>
											</div>
										{/foreach}
									</div>
								{/if}
							</div>
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
		</section>
	</div>
</section>