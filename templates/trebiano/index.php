<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
// needed to seperate the ISO number from the language file constant _ISO
$iso = explode( '=', _ISO );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php mosShowHead(); ?>
<?php
if ( $my->id ) {
	initEditor();
}
?>
<meta http-equiv="content-language" content="it" />
<link href="<?php echo $mosConfig_live_site;?>/templates/trebiano/css/template_css.css" rel="stylesheet" type="text/css"/>
</head>


<?php 
// *** Tracking FEEDBURNER per il Blog ***
if ($Itemid==9){echo '
<script src="http://feeds.feedburner.com/~s/trebiano?i='. $mosConfig_live_site . $_GET['url_attuale'] . '" type="text/javascript" charset="utf-8"></script>';} ?>


<body class="waterbody" <?php if ($option=='com_contact'){echo 'onload="showMap();" onunload="GUnload()"';} ?>>
<!-- barra cookies -->
<script src="/templates/trebiano/js/cookiechoices.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function(event) {
    cookieChoices.showCookieConsentBar('Questo sito usa i cookie per migliorare l\'esperienza di navigazione dell\'utente.',
      'OK', 'Ulteriori informazioni', 'http://www.trebiano.it/informativa-cookie.pdf');
  }); 
</script>
<!-- END barra cookies -->


<h1><?php echo $mainframe->getPageTitle(); ?></h1>
<h2><?php echo $mosConfig_MetaDesc; ?></h2>


<div align="center">
<div id="container">
	<div id="containerbg">
		<div id="outerleft">
			<!-- start logo -->
			<div id="logo">
			<a href="<?php echo $mosConfig_live_site;?>"><div id="logo_link" title="Web Agency a La Spezia Trebiano E-Business Partner">

			</div></a></div>
			<!-- end logo -->
			<!-- start top menu -->
			<div id="topmenu">
			<?php mosLoadModules('top',-1); ?>
			</div>
			<!-- end top menu.  -->
			<!-- start image header -->
			<div id="imgheader">
			<?php mosLoadModules('user1'); ?>
			</div>
			<!-- end image header -->
			
<!-- Contenuti in area centrale -->			
			
			<div id="servizi_internet_la_spezia">
			
              <div id="leftcol">
			   <?php mosLoadModules('left'); ?>
               <?php if ($option!='com_frontpage'){mosPathWay();} ?>
			   <?php mosMainBody(); ?>
			  </div>
			  
              <div id="rightcol">
			   <?php mosLoadModules('right'); ?>
              </div>
			  
			</div>
		</div>
	
		<div class="clear">
		</div>
		<?php
		if (mosCountModules('bottom') >= 1) {
		?>
			<!-- Riquadro footer  -->
			<div id="bottom">
			<?php mosLoadModules('bottom'); ?>
			</div>
			<!-- end Riquadro footer -->
		<?php
		}
		?>


	</div>
	<!-- copyright notice -->
	<div id="copyright">
	<?php include_once( $GLOBALS['mosConfig_absolute_path'] . '/includes/footer.php' ); ?>
	</div> 

</div>
</div>

<?php mosLoadModules('debug', -1);?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{
var pageTracker = _gat._getTracker("UA-2560711-1");
_gat._anonymizeIp();
pageTracker._trackPageview();
} catch(err) {}
</script>
</body>
</html>
