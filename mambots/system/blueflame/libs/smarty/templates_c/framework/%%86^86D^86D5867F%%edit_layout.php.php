<?php /* Smarty version 2.6.16, created on 2007-07-09 12:43:23
         compiled from edit_layout.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfText', 'edit_layout.php', 45, false),)), $this); ?>
      <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2"><span class="indent bullet-category"><?php  echo bfHTML::mooToolTip('Layout Information...','Basic information about the layout'); ?></span></th>
          </tr>
        </thead>

        <tbody>
          <tr class="row0">
            <td><b><?php  echo bfHTML::mooToolTip('Layout Name','Sets the Layout name'); ?>:</b></td>

            <td><input name="title" type="text" class="flatinputbox" size="73" value="<?php echo $this->_tpl_vars['TITLE']; ?>
" /></td>
          </tr>
           <tr class="row0">
            <td valign="top"><b><?php  echo bfHTML::mooToolTip('Layout Description','A friendly description of this template'); ?>:</b></td>

            <td><textarea class="flatinputbox" style="width: 100%; height: 50px;" name="desc" id="desc"><?php echo $this->_tpl_vars['DESC']; ?>
</textarea></td>
          </tr>
 		  <tr class="row0">
            <td><b><?php  echo bfHTML::mooToolTip('Layout Scope','The type of object this layout pertains to, i.e., A listing or a category'); ?>:</b></td>

            <td><?php echo $this->_tpl_vars['APPLIESTO']; ?>
</td>
          </tr>
           <tr class="row1">
            <td><b><?php  echo bfHTML::mooToolTip('Template Filename','The full path to the template file'); ?>:</b></td>

            <td><?php echo $this->_tpl_vars['FILENAME']; ?>
</td>
          </tr>
        <thead>
          <tr>
            <th colspan="2"><span class="indent bullet-category"><b><?php  echo bfHTML::mooToolTip('HTML','A detailed HTML for the Layout'); ?>:</b></span></th>
          </tr>
        </thead>

        <tr class="row0">
          <td colspan="2">

          <span class="bullet-info indent" style="min-height: 20px; height: 20px;line-height: 22px;">
          <a href="javascript:void(0);" onclick="jQuery('#tip').show('slow');">
          <?php  echo bfHTML::mooToolTip('View Template Creation Notes...','Click here for information on creating your own layout templates with the smarty engine'); ?></a>
          </span>

          <div id="tip" class="tipdiv" style="display: none; border: 1px solid #ccc; background-color: #fff; padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">

          <h2><img src="<?php echo $this->_tpl_vars['LIVE_SITE']; ?>
/mambots/system/blueflame/view/images/info.png" alt="Tip" align="absmiddle" /> <?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Template Structure<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></h2>
          <p><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>With the exception of framework templates, all custom user templates are smarty templates, you can find the<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <a href="http://smarty.php.net/docs.php" target="_blank"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>full documentation<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a> <?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>for smarty on their website.<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></p>
          <p><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>The place holders can be any of the fields for the item (E.g. Category)<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></p>

          </div>

			<textarea class="flatinputbox" cols="50" style="width: 100%; height: 600px;" name="html" id="html"><?php echo $this->_tpl_vars['HTML']; ?>
</textarea></td>
        </tr>
      </table>
      <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ID']; ?>
" />