<?php /* Smarty version 2.6.16, created on 2007-09-04 21:55:24
         compiled from 1679091c5a880faf6fb5e6087eb1b2dc.php */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 'bfText', '1679091c5a880faf6fb5e6087eb1b2dc.php', 15, false),array('block', 'bfAmpReplace', '1679091c5a880faf6fb5e6087eb1b2dc.php', 29, false),)), $this); ?>

<div class="headerTab blue">
	<div><h2><span>[ <?php echo $this->_tpl_vars['TAGNAME']; ?>
 ]</span></h2></div>
	 <br /><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Questa pagina contiene una lista degli <b>articoli di approfondimento su <?php echo $this->_tpl_vars['TAGNAME']; ?>
 e dintorni</b> che sono stati etichettati col TAG [ <b><?php echo $this->_tpl_vars['TAGNAME']; ?>
</b> ] su <a href="http://www.trebiano.it/blog/" title="Trebiano E-Business Blog">Trebiano E-Business Blog</a>, il blog curato dallo Studio Trebiano ed inerente principalmente i seguenti argomenti: <?php echo $this->_tpl_vars['TAGNAME']; ?>
, internet, marketing e comunicazione on-line. Attualmente ci sono (<b><?php echo $this->_tpl_vars['COUNT']; ?>
</b>) articoli aventi il TAG [ <?php echo $this->_tpl_vars['TAGNAME']; ?>
 ] associato:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?>  
	<!-- <a href="<?php echo $this->_tpl_vars['TAGCLOUD_LINK']; ?>
" class="cloud bullet-cloud" rel="index"><?php $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>Guarda tutti i Tag<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></a> -->
<br /><br />
</div>
<?php if ($this->_tpl_vars['TAGDESC']): ?>
<div id="bf_tagdesc">
<?php echo $this->_tpl_vars['TAGDESC']; ?>

</div>
<?php endif; ?>
	
<ul class="bfsubmenu" style="margin-left: 20px;">
<?php if ($this->_tpl_vars['content_items']): ?>
	<?php unset($this->_sections['i']);
$this->_sections['i']['name'] = 'i';
$this->_sections['i']['loop'] = is_array($_loop=$this->_tpl_vars['content_items']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
		<?php echo '<li class="bullet-article"><h5><a href="';  $this->_tag_stack[] = array('bfAmpReplace', array()); $_block_repeat=true;smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['content_items'][$this->_sections['i']['index']]['url'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfAmpReplace($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '" title="';  $this->_tag_stack[] = array('bfText', array()); $_block_repeat=true;smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start();  echo '';  echo $this->_tpl_vars['content_items'][$this->_sections['i']['index']]['title'];  echo '';  $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_bfText($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack);  echo '">';  echo $this->_tpl_vars['content_items'][$this->_sections['i']['index']]['title'];  echo '</a></h5></li>'; ?>

	<?php endfor; endif;  else: ?>
	<li />
<?php endif; ?>
</ul>


<div id="bfpagenav" style="text-align: center; align: center;">
##PAGENAV_LINKS##<br />
##PAGENAV_LIMITBOX##<br />
##PAGENAV_INFO##<br />
</div>

