<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:12
         compiled from eccbc87e4b5ce2fe28308fd9f2a7baf3.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfText', 'eccbc87e4b5ce2fe28308fd9f2a7baf3.php', 30, false),)), $this); ?>

<?php if ($this->_tpl_vars['taglinelocation'] == 'bottom'): ?>
		<?php echo $this->_tpl_vars['CONTENT_ITEM']; ?>

	
	<a name="tags"></a>
	
	<div class="headerTab">
	<?php if ($this->_tpl_vars['SOCIALBOOKMARKS']): ?>
		<div style="float:right;margin:0;padding:0;">
				<?php echo $this->_tpl_vars['SOCIALBOOKMARKS']; ?>

		</div>
	<?php endif; ?>
	<div><h2><span><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Tags<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span></h2></div>
	<!--<a class="bullet-cloud bfsmaller" href="<?php echo $this->_tpl_vars['TAGCLOUD_LINK']; ?>
" title="<?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Guarda tutti i TAG<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Guarda tutti i TAG<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a> -->
	<?php if ($this->_tpl_vars['allowfrontendsubmission'] == '1'): ?>
		<?php if ($this->_tpl_vars['frontendsubmissionaccesslevel'] == '1'): ?>
			<a href="javascript:void(0);" onclick="bf_tag.bf_showAddTagDiv('<?php echo $this->_tpl_vars['ID']; ?>
');" class="add bfsmaller"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Add New Tag<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>...</a>
		<?php endif; ?>
	<?php endif; ?>
	
	</div>
	
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "e4da3b7fbbce2345d7772b0674a318d5.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "a87ff679a2f3e71d9181a67b7542122c.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	


	<br />
	<div class="clearer"> </div>
	

<?php endif; ?>



<?php if ($this->_tpl_vars['taglinelocation'] == 'top'): ?>

	<div class="headerTab">
	<?php if ($this->_tpl_vars['SOCIALBOOKMARKS']): ?>
		<div style="float:right;margin:0;padding:0;">
				<?php echo $this->_tpl_vars['SOCIALBOOKMARKS']; ?>

		</div>
	<?php endif; ?>
	<div><h2><span><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Tags<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span></h2></div>
	<!--<a class="bullet-cloud bfsmaller" href="<?php echo $this->_tpl_vars['TAGCLOUD_LINK']; ?>
" title="<?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Guarda tutti i TAG<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Guarda tutti i TAG<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a>-->
	<?php if ($this->_tpl_vars['allowfrontendsubmission'] == '1'): ?>
		<?php if ($this->_tpl_vars['frontendsubmissionaccesslevel'] == '1'): ?>
			<a href="javascript:void(0);" onclick="bf_tag.bf_showAddTagDiv('<?php echo $this->_tpl_vars['ID']; ?>
');" class="add bfsmaller"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Add New Tag<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>...</a>
		<?php endif; ?>
	<?php endif; ?>
	<br/>
	</div>
	
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "e4da3b7fbbce2345d7772b0674a318d5.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "a87ff679a2f3e71d9181a67b7542122c.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<div style="color:#000;background-color:#AEC4E2;height:1px;font-size:1px;margin-top: 10px;"></div>

	<br />
	<div class="clearer"></div>
		<?php echo $this->_tpl_vars['CONTENT_ITEM']; ?>

	
	<br />


<?php endif; ?>