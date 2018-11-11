{*
 * @version $Id: c20ad4d76fe97759aa27a0c99bff6710.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 Tag Cloud Page
 Used by Admin and frontend - with slight differences
*}

<p class="TagCloud">
{section name=i loop=$items}
	{strip}
	<a {bfIfAdminConsole} target="_blank" {/bfIfAdminConsole}title="{$items[i].qty} {bfText}items tagged with{/bfText} {$items[i].tagname}" style="font-size: {$items[i].size}%;" href="{bfAmpReplace}{$items[i].link}{/bfAmpReplace}">{$items[i].tagname}{if $showqty} ({$items[i].qty}){/if}</a>
	{/strip}
{/section}
</p>


