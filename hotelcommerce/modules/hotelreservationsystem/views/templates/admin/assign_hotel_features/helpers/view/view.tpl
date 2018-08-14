{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($assignFeatures) && $assignFeatures}
	<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal {$name_controller|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
		{if isset($edit)}
			<input name="edit_hotel_id" type="hidden" value="{$hotel_id}">
		{/if}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-user"></i> {l s='Assign Features' mod='hotelreservationsystem'}
			</div>
			{if isset($hotels) && $hotels}
				<div class="form-wrapper">
					<div class="form-group">
						{if isset($edit)}
							<label class="control-label col-sm-5">
								<span>{l s='Hotel Name' mod='hotelreservationsystem'} : </span>
							</label>
							<select class="fixed-width-xl" name="id_hotel">
								{foreach $hotels as $hotel}
									{if $hotel_id == $hotel.id}
										<option readonly="true" selected="true" value="{$hotel.id|escape:'html':'UTF-8'}" >{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
									{/if}
								{/foreach}
							</select>
						{else}
							<label class="control-label col-sm-5">
								<span>{l s='Select Hotel' mod='hotelreservationsystem'} : </span>
							</label>
							<div class="col-sm-4">
								<select class="fixed-width-xl" name="id_hotel">
								<option value='0'>{l s='Select Hotel' mod='hotelreservationsystem'}</option>>
									{foreach $hotels as $hotel}
										<option value="{$hotel.id|escape:'html':'UTF-8'}" >{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
						{/if}
					</div>
				</div>
				{assign var=i value=1}
				{foreach from=$features_list item=value}
				<div class="accordion">
					<div class="accordion-section">
						<a class="accordion-section-title" href="#accordion{$i}"><span class="icon-plus"></span>&nbsp&nbsp{l s={$value.name} mod='hotelreservationsyatem'}</a>
						<div id="accordion{$i}" class="accordion-section-content">
							<table id="" class="table" style="max-width:100%">
								<tbody>
									{if isset($value.children) && $value.children}
										{foreach from=$value.children item=val}
											<tr>
												<td class="border_top border_bottom border_bold">
													<span class=""> {l s={$val.name} mod='hotelreservationsyatem'} </span>
												</td>
												<td style="">
													<input name="hotel_fac[]" type="checkbox" value="{$val.id}" class="form-control" {if isset($edit) && $val.selected}checked='true'{/if}>
												</td>
											</tr>
										{/foreach}
									{/if}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				{assign var=i value=$i+1}
				{foreachelse}
					<!-- code for foreachelse -->
				{/foreach}
				<div class="panel-footer">
					<a href="{$link->getAdminLink('AdminHotelFeatures')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='hotelreservationsystem'}</a>
					<button type="submit" name="submitAddhtl_features" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Assign' mod='hotelreservationsystem'}</button>
					<!-- <button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
						<i class="process-icon-save"></i> {l s='Assign and stay' mod='hotelreservationsystem'}
					</button> -->
				</div>
			{else}
				<div class="alert alert-warning">
					{l s='No hotel found to assign features.' mod='hotelreservationsystem'}
				</div>
			{/if}
		</div>
	</form>
{else}
	<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal {$name_controller|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
		<div class="panel" style="float:left">
			<div class="panel-heading">
				{l s='Hotel Features' mod='hotelreservationsystem'}
			</div>
			{foreach from=$features_list item=value}
				<div class="col-sm-12 feature_div" id="grand_feature_div_{$value.id}">
					<div class="row row-margin-bottom row-margin-top">
						<div class="col-sm-12">
							<div class="row feature-border-div">
								<div class="col-sm-12 feature-header-div">
									<span>{l s={$value.name} mod='hotelreservationsyatem'}</span>
									<a class="btn btn-primary pull-right edit_feature col-sm-1" href="{$link->getAdminLink('AdminHotelFeatures')}&amp;updatehtl_features&amp;id={$value.id}"><span><i class="icon-pencil"></i>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Edit' mod='hotelreservationsystem'}</span></a>
									<button class="btn btn-primary pull-right dlt-feature col-sm-1" data-feature-id="{$value.id}"><i class="icon-trash"></i>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Delete' mod='hotelreservationsystem'}</button>
								</div>
							</div>
						</div>
					</div>
					<div class="row child-features-container">
						<div class="col-sm-12">
							{if isset($value.children) && $value.children}
								{foreach from=$value.children item=val}
									<p>{l s={$val.name} mod='hotelreservationsyatem'}</p>
								{/foreach}
							{/if}
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</form>
{/if}

{strip}
	{addJsDef delete_url=$link->getAdminLink('AdminHotelFeatures') js=1 mod='hotelreservationsystem'}
	{addJsDefL name=success_delete_msg}{l s='Successfully Deleted.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=error_delete_msg}{l s='Some error occured while deleting feature.Please try again.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=confirm_delete_msg}{l s='Are you sure?' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}
