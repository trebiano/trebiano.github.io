{*
 * @version $Id: e4da3b7fbbce2345d7772b0674a318d5.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 This template is included by other templates
 I display the textbox to add new tags
*}

{if $allowfrontendsubmission eq '1'}
	{if $frontendsubmissionaccesslevel eq '1'}
		<span class="clearer"></span>
		<div id="tagaddform{$ID}" class="add-tag-div">
		<small style="color: #808080;text-align:left;">{bfText}Please Enter New Tags Separated By Comma's{/bfText}</small>
		<input type="text" id="tagname{$ID}" name="tagname{$ID}" class="tagaddbox" />
		<br />
		<input type="hidden" id="content_id{$ID}" name="content_id{$ID}" class="inputbox bfinputbox" value="{$ID}" />
		<input type="hidden" id="scope{$ID}" name="scope{$ID}" class="inputbox bfinputbox" value="com_content" />
		<input type="button" class="button bfButton" title="{bfText}Add Tag{/bfText}" value="{bfText}Add Tags{/bfText}..." onclick="bf_tag.bf_addTag('{$ID}');" />&nbsp;&nbsp;<a href="javascript:void(0);" onclick="bf_tag.bf_hideAddTagDiv('{$ID}');">{bfText}or close{/bfText}</a>
		
		

		
		</div>
		
	{/if}
{/if}