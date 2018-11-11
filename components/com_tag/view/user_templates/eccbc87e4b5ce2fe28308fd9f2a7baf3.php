{*
 * @version $Id: eccbc87e4b5ce2fe28308fd9f2a7baf3.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 This template is used when viewing a whole article
*}

{if $taglinelocation == "bottom"}
	{*
	 This line prints out the content item
	*}
	{$CONTENT_ITEM}
	
	<a name="tags"></a>
	
	<div class="headerTab">
	{if $SOCIALBOOKMARKS}
		<div style="float:right;margin:0;padding:0;">
		{*
		 This line prints out the links to several social bookmark sites
		*}
		{$SOCIALBOOKMARKS}
		</div>
	{/if}
	<div><h2><span>{bfText}Tags{/bfText}</span></h2></div>
	<!--<a class="bullet-cloud bfsmaller" href="{$TAGCLOUD_LINK}" title="{bfText}Guarda tutti i TAG{/bfText}">{bfText}Guarda tutti i TAG{/bfText}</a> -->
	{if $allowfrontendsubmission eq '1'}
		{if $frontendsubmissionaccesslevel eq '1'}
			<a href="javascript:void(0);" onclick="bf_tag.bf_showAddTagDiv('{$ID}');" class="add bfsmaller">{bfText}Add New Tag{/bfText}...</a>
		{/if}
	{/if}
	
	</div>
	
	{*
	 This line includes the form to add new tags,
	 Position it where you want the tags to appear
	*}
	{include file="e4da3b7fbbce2345d7772b0674a318d5.php"}
	
	{*
	 This line includes the tags,
	 Position it where you want the tags to appear
	*}
	{include file="a87ff679a2f3e71d9181a67b7542122c.php"}
	
	


	<br />
	<div class="clearer"> </div>
	

{/if}



{if $taglinelocation == "top"}

	<div class="headerTab">
	{if $SOCIALBOOKMARKS}
		<div style="float:right;margin:0;padding:0;">
		{*
		 This line prints out the links to several social bookmark sites
		*}
		{$SOCIALBOOKMARKS}
		</div>
	{/if}
	<div><h2><span>{bfText}Tags{/bfText}</span></h2></div>
	<!--<a class="bullet-cloud bfsmaller" href="{$TAGCLOUD_LINK}" title="{bfText}Guarda tutti i TAG{/bfText}">{bfText}Guarda tutti i TAG{/bfText}</a>-->
	{if $allowfrontendsubmission eq '1'}
		{if $frontendsubmissionaccesslevel eq '1'}
			<a href="javascript:void(0);" onclick="bf_tag.bf_showAddTagDiv('{$ID}');" class="add bfsmaller">{bfText}Add New Tag{/bfText}...</a>
		{/if}
	{/if}
	<br/>
	</div>
	
	{*
	 This line includes the form to add new tags,
	 Position it where you want the tags to appear
	*}
	{include file="e4da3b7fbbce2345d7772b0674a318d5.php"}
	
	{*
	 This line includes the tags,
	 Position it where you want the tags to appear
	*}
	{include file="a87ff679a2f3e71d9181a67b7542122c.php"}
	
	<div style="color:#000;background-color:#AEC4E2;height:1px;font-size:1px;margin-top: 10px;"></div>

	<br />
	<div class="clearer"></div>
	{*
	 This line prints out the content item
	*}
	{$CONTENT_ITEM}
	
	<br />


{/if}