<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:12
         compiled from e4da3b7fbbce2345d7772b0674a318d5.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfText', 'e4da3b7fbbce2345d7772b0674a318d5.php', 18, false),)), $this); ?>

<?php if ($this->_tpl_vars['allowfrontendsubmission'] == '1'): ?>
	<?php if ($this->_tpl_vars['frontendsubmissionaccesslevel'] == '1'): ?>
		<span class="clearer"></span>
		<div id="tagaddform<?php echo $this->_tpl_vars['ID']; ?>
" class="add-tag-div">
		<small style="color: #808080;text-align:left;"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Please Enter New Tags Separated By Comma's<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></small>
		<input type="text" id="tagname<?php echo $this->_tpl_vars['ID']; ?>
" name="tagname<?php echo $this->_tpl_vars['ID']; ?>
" class="tagaddbox" />
		<br />
		<input type="hidden" id="content_id<?php echo $this->_tpl_vars['ID']; ?>
" name="content_id<?php echo $this->_tpl_vars['ID']; ?>
" class="inputbox bfinputbox" value="<?php echo $this->_tpl_vars['ID']; ?>
" />
		<input type="hidden" id="scope<?php echo $this->_tpl_vars['ID']; ?>
" name="scope<?php echo $this->_tpl_vars['ID']; ?>
" class="inputbox bfinputbox" value="com_content" />
		<input type="button" class="button bfButton" title="<?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Add Tag<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>" value="<?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Add Tags<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>..." onclick="bf_tag.bf_addTag('<?php echo $this->_tpl_vars['ID']; ?>
');" />&nbsp;&nbsp;<a href="javascript:void(0);" onclick="bf_tag.bf_hideAddTagDiv('<?php echo $this->_tpl_vars['ID']; ?>
');"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>or close<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>
		
		

		
		</div>
		
	<?php endif;  endif; ?>