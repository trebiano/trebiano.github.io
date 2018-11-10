<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
// needed to seperate the ISO number from the language file constant _ISO
$iso = split( '=', _ISO );
// xml prolog
echo '<?xml version="1.0" encoding="'. $iso[1] .'"?' .'>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php mosShowHead(); ?>
<?php
if ( $my->id ) {
	initEditor();
}

if (mosCountModules('user1') + mosCountModules('user2') < 2) {
  $greybox = 'large';
} else {
  $greybox = 'small';
}
?>
<meta http-equiv="Content-Type" content="text/html; <?php echo _ISO; ?>" />
<link href="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/css/template_css.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo $mosConfig_live_site;?>/images/favicon.ico" />
</head>
<body id="page_bg">
<a name="up" id="up"></a>

<div class="center" align="center">
  <table class="minimal" width="810" id="main">
    <tr>
      <td class="left_shadow"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="1" width="17" /><br /></td>
      <td class="wrapper">
    		<div id="header">
					<div id="logo"></div>
					</div>
    		<div id="tabbar">
					<?php mosLoadModules('user3', -1); ?>
				</div>
				<div id="colorbar"></div>
				<div id="contentarea">
					<table cellpadding="0" cellspacing="0" border="0" width="760">
						<tr valign="top">
							<td class="sidenav">
							  <div class="forcewidth">
								<?php if (mosCountModules('top') > 0) { ?>
									<div class="box_t"></div>
									<div class="box_m">
										<?php mosLoadModules('top', -1); ?>
									</div>
									<div class="box_b"></div>
								<?php } ?>
								<?php if (mosCountModules('left') > 0) { ?>
									<div id="left">
										<?php mosLoadModules('left', -2); ?>
									</div>
								<?php } ?>
								<br />
								<img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="1" width="194" /><br />
							  </div>
              </td>
              <td class="seperator"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="1" width="16" /></td>
							<td class="middle">
							<div class="banner"><?php mosLoadModules('banner', -1); ?></div>
							  <?php if (mosCountModules('user1') || mosCountModules('user2')) { ?>
								<table class="minimal" width="550" id="greybox">
									<tr valign="top">
									  <?php if (mosCountModules('user1') > 0) { ?>
										<td class="<?php echo $greybox; ?>box">
											<?php mosLoadModules('user1', -2); ?>
										</td>
										<?php } ?>
										<?php if (mosCountModules('user1') && mosCountModules('user2')) { ?>
										<td class="spacer"></td>
										<?php } ?>
										<?php if (mosCountModules('user2') > 0) { ?>
										<td class="<?php echo $greybox; ?>box">
											<?php mosLoadModules('user2', -2); ?>
										</td>
										<?php } ?>
									</tr>
								</table>
								<?php } ?>
								<div id="pathway"><?php mosPathWay(); ?></div>
								<table width="100%">
									<tr valign="top">
										<td>	<?php mosMainBody(); ?>
												<div class="copyright">
													<?php mosLoadModules('footer'); ?><?php include_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/footer.php' ); ?>
												</div>
										</td>
										<?php if (mosCountModules('right') > 0) { ?>

										<td class="seperator"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="1" width="16" /></td>
										<td>
										<div >
											<?php mosLoadModules('right', -2); ?>
										</div>

										</td>
										<?php } ?>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
      </td>
      <td class="right_shadow"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="1" width="17" /><br /></td>
    </tr>
    <tr>
      <td class="left_bot_shadow"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="41" width="17" /><br /></td>
      <td class="bottom">
        <div id="footer">
        		design by <a href="http://www.rockettheme.com">rockettheme.com</a> | edited by <a href="http://www.joomlaitalia.com">joomlaitalia.com</a>
				</div>
      </td>
      <td class="right_bot_shadow"><img src="<?php echo $mosConfig_live_site;?>/templates/<?php echo $mainframe->getTemplate(); ?>/images/spacer.png" alt="spacer.png, 0 kB" title="spacer" class="" height="41" width="17" /><br /></td>

    </tr>
  </table>
</div>


<?php mosLoadModules( 'debug', -1 );?>
</body>
</html>
