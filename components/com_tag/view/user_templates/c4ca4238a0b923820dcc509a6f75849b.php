{*
 * @version $Id: c4ca4238a0b923820dcc509a6f75849b.php 1100 2007-07-13 16:10:30Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.

 This is the standard framework layout
*}

<h1 class="componentheading">{bfText}Items Tagged With{/bfText} {$TAGNAME}</h1>

{if $tagtext} 
 <p>{$tagtext}</p>
{/if}

{section name=i loop=$content_items}
    {strip}
    <div class="contentitem">
        <span class="contentheading clearer"><a href="{bfAmpReplace}{$content_items[i].url}{/bfAmpReplace}">{$content_items[i].title}</a></span>
        <br />
        <span class="small clearer">{bfText}Written By{/bfText}: {$content_items[i].author}</span>
        <br />
        <span class="small clearer">{$content_items[i].created}</span>
        <br />
        <span class="introtext clearer">
            {$content_items[i].text}
        </span>
    {if $content_items[i].fulltext}
        <span class="introtext clearer">
        <br />
            <a href="{bfAmpReplace}{$content_items[i].url}{/bfAmpReplace}">{bfText}Read More About {$content_items[i].title|capitalize}{/bfText}...</a>
        </span>
    {/if}
    
    <br />
    <br />
    </div>
    
    {/strip}
{/section}
<br /><br /><br />
<p>{bfText}There are{/bfText} {$COUNT} {bfText}items tagged with{/bfText} <b>{$TAGNAME}</b>. You can view all our tags in the <a href="{bfAmpReplace}{$TAGCLOUD_LINK}{/bfAmpReplace}" rel="index">Tag Cloud</a></p>

<div id="bfpagenav" style="text-align: center; align: center;">
##PAGENAV_LINKS##<br />
##PAGENAV_LIMITBOX##<br />
##PAGENAV_INFO##<br />
</div>