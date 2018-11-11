<?php /* Smarty version 2.6.16, created on 2007-06-18 14:29:09
         compiled from edit_soapmap.php */ ?>
    <div class="col60">
      <table class="bfadminlist">
        <thead>
          <tr>
            <th colspan="2" class="headerrow"><span class="indent bullet-article"><?php  echo bfHTML::mooToolTip('Map SOAP Fields...','Map the fields for streetmap upload'); ?> </span></th>
          </tr>
        </thead>

        <tr class="row0">
          <td width="150"><b><?php  echo bfHTML::mooToolTip('Streetmap Field','Streetmap XML '); ?> :</b></td>

          <td><?php echo $this->_tpl_vars['SM_ID']; ?>
</td>
        </tr> 
           <tr class="row0">
          <td width="150"><b><?php  echo bfHTML::mooToolTip('mosDirectory Field','The note\'s title'); ?> :</b></td>

          <td><?php echo $this->_tpl_vars['DIRECTORY']; ?>
</td>
        </tr> 
           <tr class="row0">
          <td width="150"><b><?php  echo bfHTML::mooToolTip('TOTW SOAP Field','The note\'s title'); ?> :</b></td>

          <td><?php echo $this->_tpl_vars['SUGAR']; ?>
</td>
        </tr> 
     </div>
    <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ID']; ?>
" />