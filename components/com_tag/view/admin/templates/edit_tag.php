<div class="col60">
      <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article">{php} echo bfHTML::mooToolTip('About the tag...','The main details about the tag');{/php} </span></th>
          </tr>
        </thead>
		<tbody>
        <tr class="row0">
          <td width="150" valign="top"><b>{php} echo bfHTML::mooToolTip('Tag name','The tag\'s main title');{/php} :</b><br />
          <span class="indent exclamation"></span><small><a href="javascript:void(0);" onclick="jQuery('.help').toggle('slow');">{bfText}Important Notes{/bfText}</a></small><br />
</td>

          <td><input type="text" name="tagname" value="{$TAGNAME}" class="flatinputbox" maxlength="255" />
          <span class="help" style="display: none;"><br />{bfText}Tags cannot contain special chars that are reserved for use in URL's, because if they do then nice SEF urls will not work.  Also certain chars are not allowed to be in a valid url. Do not use the following in tags:{/bfText}
          <br /><ul>
          			<li>backslash \</li>
          			<li>forwardslash /</li>
          			<li>underscore _</li>
          			<li>asterisk *</li>
          			<li>brackets ( )</li>
          		</ul>
          		</span></td>
        </tr>
       </tbody>
      </table>

      <table cellspacing="0" cellpadding="10" border="0" class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article">{php} echo bfHTML::mooToolTip('Description Text...','The main body of the tag');{/php}</span></th>
          </tr>
        </thead>
<tbody>
        <tr class="row0">
          <td colspan="2">
           <textarea mce_editable="true" class="flatinputbox" cols="50" style="width: 100%; height: 200px;" name="desc" id="desc">{$DESC}</textarea></td>
        </tr>
        </tbody>
      </table>
      
       <table cellspacing="0" cellpadding="10" border="0" class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article">{php} echo bfHTML::mooToolTip('Tagged Items','Content Items Tagged with this Tag');{/php}</span></th>
          </tr>
        </thead>

        <tr class="row0">
          <td colspan="2">
          {bfText}Click an item to remove the tag from that item.{/bfText}
          <ul class="noindent bfsubmenu">
			{section name=i loop=$content_items}
				{strip}
				<li><a href="javascript:void(0);" onclick="{$content_items[i].onclick}">{$content_items[i].title}</a></li>
				{/strip}
			{/section}
			</ul>
          </td>
        </tr>


      </table>
    </div>

    <div class="col1">
      &nbsp;
    </div>

    <div class="col35" id="outer">
     <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article">{php} echo bfHTML::mooToolTip('Display Options...','Configure the display options for this tag');{/php}</span></th>
          </tr>
        </thead>
	<tbody>
        <tr class="row0">
          <td width="150" colspan="2"><b>{php} echo bfHTML::mooToolTip('Layout Template','This is the template that is used a single tag is clicked on');{/php} :</b></td>
        </tr>
		<tr class="row1">
          <td colspan="2">{$TEMPLATES}</td>
        </tr>
        
         <tr class="row0">
          <td width="150" colspan="2"><b>{php} echo bfHTML::mooToolTip('Layout Order By','When this tag is clicked what order should we show the content items by');{/php} :</b></td>
        </tr>
		<tr class="row1">
          <td colspan="2">{$LAYOUT_ORDERBY}</td>
        </tr>
        
        <tr class="row0">
          <td width="150" colspan="2"><b>{php} echo bfHTML::mooToolTip('Layout Order Direction','When this tag is clicked what order direction should we show the content items by');{/php} :</b></td>
        </tr>
		<tr class="row1">
          <td colspan="2">{$LAYOUT_DIR}</td>
        </tr>
</tbody>
      </table>
     <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article">{php} echo bfHTML::mooToolTip('Metadata...','Metadata about the tag that is used for the search facility');{/php}</span></th>
          </tr>
        </thead>
<tbody>
        <tr class="row0">
          <td width="150"><b>{php} echo bfHTML::mooToolTip('Meta Title','The tag\'s meta title');{/php} :</b></td>

          <td><input type="text" name="meta_title" value="{$META_TITLE}" class="flatinputbox" maxlength="255" /></td>
        </tr>

        <tr class="row0">
          <td><b>{php} echo bfHTML::mooToolTip('Meta Description','The tag\'s meta description');{/php} :</b></td>

          <td><input type="text" name="meta_desc" value="{$META_DESC}" class="flatinputbox" /></td>
        </tr>

        <tr class="row0">
          <td><b>{php} echo bfHTML::mooToolTip('Meta Keywords','Keywords that describe the tag');{/php} :</b></td>

          <td><input type="text" name="meta_keywords" value="{$META_KEYWORDS}" class="flatinputbox" /></td>
        </tr>
        </tbody>
      </table>

      <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-info">{php} echo bfHTML::mooToolTip('Publishing Information...','Detailed information about the tag');{/php}</span></th>
          </tr>
        </thead>
<tbody>
        <tr class="row0">
          <td><b>{php} echo bfHTML::mooToolTip('Access','The level of access required to view the tag');{/php}:</b></td>

          <td><span id="xaccess{$ID}">{$ACCESS}</span></td>
        </tr>

        <tr class="row1">
          <td width="80"><b>{php} echo bfHTML::mooToolTip('Published','Shows if the tag is published');{/php}:</b></td>

          <td align="left">
            <div align="left">
              <span id="pub{$ID}">{$PUBLISHED}</span>
            </div>
          </td>
        </tr>

        <tr class="row0">
          <td><b>{php} echo bfHTML::mooToolTip('Hits','The amount of times that the tag has been read');{/php}:</b></td>

          <td align="center">
            <div align="left">
              {$HITSANDRESET}
            </div>
          </td>
        </tr>
        </tbody>
        </table>
    </div>
    <input type="hidden" name="id" value="{$ID}" />
