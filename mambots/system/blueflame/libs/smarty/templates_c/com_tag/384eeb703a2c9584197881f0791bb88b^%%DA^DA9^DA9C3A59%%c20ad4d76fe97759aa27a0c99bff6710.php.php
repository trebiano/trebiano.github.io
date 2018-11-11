<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:15
         compiled from c20ad4d76fe97759aa27a0c99bff6710.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfIfAdminConsole', 'c20ad4d76fe97759aa27a0c99bff6710.php', 17, false),array('block', 'bfText', 'c20ad4d76fe97759aa27a0c99bff6710.php', 17, false),array('block', 'bfAmpReplace', 'c20ad4d76fe97759aa27a0c99bff6710.php', 17, false),)), $this); ?>

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

