{*
 * @version $Id: a87ff679a2f3e71d9181a67b7542122c.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 This template is included by other templates
 I display the list of tags already on a content item
*}
<br />
<a name="tags"></a>
<span id="tagsfor{$ID}" class="fs16">
	{section name=anything loop=$mmm}
	{strip}
	<a class="tagslink" href="{bfAmpReplace}{$mmm[anything].url}{/bfAmpReplace}" rel="tag category" title="{$mmm[anything].tagname}">
		{bfText}{$mmm[anything].tagname}{/bfText}
	</a>
	&nbsp;
	{if $technorati}
	<a href="http://technorati.com/tag/{$mmm[anything].tagname}" rel="tag">
		<img style="border:0;vertical-align:middle;margin-left:.1em" src="http://static.technorati.com/static/img/pub/icon-utag-16x13.png?tag={$mmm[anything].tagname}" alt="{$mmm[anything].tagname}" />
	</a>
	{/if}
	{/strip}
	{/section}

</span>
<span class="clearer"></span>