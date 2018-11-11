<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:12
         compiled from a87ff679a2f3e71d9181a67b7542122c.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfAmpReplace', 'a87ff679a2f3e71d9181a67b7542122c.php', 18, false),array('block', 'bfText', 'a87ff679a2f3e71d9181a67b7542122c.php', 19, false),)), $this); ?>
<br />
<a name="tags"></a>
<span id="tagsfor<?php echo $this->_tpl_vars['ID']; ?>
" class="fs16">
	<?php unset($this->_sections['anything']);
$this->_sections['anything']['name'] = 'anything';
$this->_sections['anything']['loop'] = is_array($_loop=$this->_tpl_vars['mmm']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['anything']['show'] = true;
$this->_sections['anything']['max'] = $this->_sections['anything']['loop'];
$this->_sections['anything']['step'] = 1;
$this->_sections['anything']['start'] = $this->_sections['anything']['step'] > 0 ? 0 : $this->_sections['anything']['loop']-1;
if ($this->_sections['anything']['show']) {
    $this->_sections['anything']['total'] = $this->_sections['anything']['loop'];
    if ($this->_sections['anything']['total'] == 0)
        $this->_sections['anything']['show'] = false;
} else
    $this->_sections['anything']['total'] = 0;
if ($this->_sections['anything']['show']):

            for ($this->_sections['anything']['index'] = $this->_sections['anything']['start'], $this->_sections['anything']['iteration'] = 1;
                 $this->_sections['anything']['iteration'] <= $this->_sections['anything']['total'];
                 $this->_sections['anything']['index'] += $this->_sections['anything']['step'], $this->_sections['anything']['iteration']++):
$this->_sections['anything']['rownum'] = $this->_sections['anything']['iteration'];
$this->_sections['anything']['index_prev'] = $this->_sections['anything']['index'] - $this->_sections['anything']['step'];
$this->_sections['anything']['index_next'] = $this->_sections['anything']['index'] + $this->_sections['anything']['step'];
$this->_sections['anything']['first']      = ($this->_sections['anything']['iteration'] == 1);
$this->_sections['anything']['last']       = ($this->_sections['anything']['iteration'] == $this->_sections['anything']['total']);
?>
	<?php echo '<a class="tagslink" href="';  $this->_tag_stack[] = array('bfAmpReplace', array()); $_block_repeat=true;smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['url'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '" rel="tag category" title="';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['tagname'];  echo '">';  $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['tagname'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '</a>&nbsp;';  if ($this->_tpl_vars['technorati']):  echo '<a href="http://technorati.com/tag/';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['tagname'];  echo '" rel="tag"><img style="border:0;vertical-align:middle;margin-left:.1em" src="http://static.technorati.com/static/img/pub/icon-utag-16x13.png?tag=';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['tagname'];  echo '" alt="';  echo $this->_tpl_vars['mmm'][$this->_sections['anything']['index']]['tagname'];  echo '" /></a>';  endif;  echo ''; ?>

	<?php endfor; endif; ?>

</span>
<span class="clearer"></span>