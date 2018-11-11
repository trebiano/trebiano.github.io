      <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2"><span class="indent bullet-category">{php} echo bfHTML::mooToolTip('Layout Information...','Basic information about the layout');{/php}</span></th>
          </tr>
        </thead>

        <tbody>
          <tr class="row0">
            <td><b>{php} echo bfHTML::mooToolTip('Layout Name','Sets the Layout name');{/php}:</b></td>

            <td><input name="title" type="text" class="flatinputbox" size="73" value="{$TITLE}" /></td>
          </tr>
           <tr class="row0">
            <td valign="top"><b>{php} echo bfHTML::mooToolTip('Layout Description','A friendly description of this template');{/php}:</b></td>

            <td><textarea class="flatinputbox" style="width: 100%; height: 50px;" name="desc" id="desc">{$DESC}</textarea></td>
          </tr>
 		  <tr class="row0">
            <td><b>{php} echo bfHTML::mooToolTip('Layout Scope','The type of object this layout pertains to, i.e., A listing or a category');{/php}:</b></td>

            <td>{$APPLIESTO}</td>
          </tr>
           <tr class="row1">
            <td><b>{php} echo bfHTML::mooToolTip('Template Filename','The full path to the template file');{/php}:</b></td>

            <td>{$FILENAME}</td>
          </tr>
        <thead>
          <tr>
            <th colspan="2"><span class="indent bullet-category"><b>{php} echo bfHTML::mooToolTip('HTML','A detailed HTML for the Layout');{/php}:</b></span></th>
          </tr>
        </thead>

        <tr class="row0">
          <td colspan="2">

          <span class="bullet-info indent" style="min-height: 20px; height: 20px;line-height: 22px;">
          <a href="javascript:void(0);" onclick="jQuery('#tip').show('slow');">
          {php} echo bfHTML::mooToolTip('View Template Creation Notes...','Click here for information on creating your own layout templates with the smarty engine');{/php}</a>
          </span>

          <div id="tip" class="tipdiv" style="display: none; border: 1px solid #ccc; background-color: #fff; padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">

          <h2><img src="{$LIVE_SITE}/mambots/system/blueflame/view/images/info.png" alt="Tip" align="absmiddle" /> {bfText}Template Structure{/bfText}</h2>
          <p>{bfText}With the exception of framework templates, all custom user templates are smarty templates, you can find the{/bfText} <a href="http://smarty.php.net/docs.php" target="_blank">{bfText}full documentation{/bfText}</a> {bfText}for smarty on their website.{/bfText}</p>
          <p>{bfText}The place holders can be any of the fields for the item (E.g. Category){/bfText}</p>

          </div>

			<textarea class="flatinputbox" cols="50" style="width: 100%; height: 600px;" name="html" id="html">{$HTML}</textarea></td>
        </tr>
      </table>
      <input type="hidden" name="id" value="{$ID}" />
