<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:16
         compiled from c9f0f895fb98ab9159f51fd0297e236d.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfText', 'c9f0f895fb98ab9159f51fd0297e236d.php', 15, false),array('block', 'bfIfAdminConsole', 'c9f0f895fb98ab9159f51fd0297e236d.php', 21, false),array('block', 'bfAmpReplace', 'c9f0f895fb98ab9159f51fd0297e236d.php', 21, false),array('block', 'bfIfNotAdminConsole', 'c9f0f895fb98ab9159f51fd0297e236d.php', 59, false),)), $this); ?>

<div class="headerTab">
	<div><h2><span><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>&raquo TAG Cloud<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span></h2></div><br />
</div>

<p class="TagCloud">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['items']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
	<?php echo '<a ';  $this->_tag_stack[] = array('bfIfAdminConsole', array()); $_block_repeat=true;smarty_block_bfIfAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo ' target="_blank" ';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfIfAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo 'title="';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['qty'];  echo ' ';  $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo 'items tagged with';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo ' ';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['tagname'];  echo '" style="font-size: ';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['size'];  echo '%;" href="';  $this->_tag_stack[] = array('bfAmpReplace', array()); $_block_repeat=true;smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['link'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '">';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['tagname'];  echo '';  if ($this->_tpl_vars['showqty']):  echo ' (';  echo $this->_tpl_vars['items'][$this->_sections['i']['index']]['qty'];  echo ')';  endif;  echo '</a>'; ?>

<?php endfor; endif; ?>
</p>

<!-- <div style="color:#000;background-color:#AEC4E2;height:1px;font-size:1px;margin-top: 10px;"></div> -->

<div class="headerTab green">
	<div><h2><span><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>&raquo TAG pi� cliccati<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></span></h2></div><br />
</div>

<p class="TagCloud">
<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['itemsbyhits']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['i']['show'] = true;
$this->_sections['i']['max'] = $this->_sections['i']['loop'];
$this->_sections['i']['step'] = 1;
$this->_sections['i']['start'] = $this->_sections['i']['step'] > 0 ? 0 : $this->_sections['i']['loop']-1;
if ($this->_sections['i']['show']) {
    $this->_sections['i']['total'] = $this->_sections['i']['loop'];
    if ($this->_sections['i']['total'] == 0)
        $this->_sections['i']['show'] = false;
} else
    $this->_sections['i']['total'] = 0;
if ($this->_sections['i']['show']):

            for ($this->_sections['i']['index'] = $this->_sections['i']['start'], $this->_sections['i']['iteration'] = 1;
                 $this->_sections['i']['iteration'] <= $this->_sections['i']['total'];
                 $this->_sections['i']['index'] += $this->_sections['i']['step'], $this->_sections['i']['iteration']++):
$this->_sections['i']['rownum'] = $this->_sections['i']['iteration'];
$this->_sections['i']['index_prev'] = $this->_sections['i']['index'] - $this->_sections['i']['step'];
$this->_sections['i']['index_next'] = $this->_sections['i']['index'] + $this->_sections['i']['step'];
$this->_sections['i']['first']      = ($this->_sections['i']['iteration'] == 1);
$this->_sections['i']['last']       = ($this->_sections['i']['iteration'] == $this->_sections['i']['total']);
?>
	<?php echo '<a ';  $this->_tag_stack[] = array('bfIfAdminConsole', array()); $_block_repeat=true;smarty_block_bfIfAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo ' target="_blank" ';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfIfAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo 'title="';  $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo 'Tag visited';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo ' ';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['qty'];  echo ' ';  $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo 'times';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo ' ';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['tagname'];  echo '" style="font-size: ';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['size'];  echo '%;" href="';  $this->_tag_stack[] = array('bfAmpReplace', array()); $_block_repeat=true;smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['link'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '">';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['tagname'];  echo '';  if ($this->_tpl_vars['showqty']):  echo ' (';  echo $this->_tpl_vars['itemsbyhits'][$this->_sections['i']['index']]['qty'];  echo ')';  endif;  echo '</a>'; ?>

<?php endfor; endif; ?>
</p>

<!-- <div style="color:#000;background-color:#70B794;height:1px;font-size:1px;margin-top: 10px;"></div> -->


<div class="col-left">

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "8f14e45fceea167a5a36dedd4bea2543.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>

<div class="col-right">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "45c48cce2e2d7fbdea1afc51c7c6ad26.php", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div class="clearer">&nbsp;</div>

<?php $this->_tag_stack[] = array('bfIfNotAdminConsole', array()); $_block_repeat=true;smarty_block_bfIfNotAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>
	<!--<form method="get" action="index.php">
	<p class="jumpbox">
	<b>Cerca TAG :</b>
	<input type="text" class="inputbox bfinputbox" style="width: 200px;" name="tag"/>
	<input type="hidden" name="option" value="com_tag"/>
	<input class="button bfbutton" type="submit" value="GO"/>
	</p>
	</form>-->
<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfIfNotAdminConsole($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>

