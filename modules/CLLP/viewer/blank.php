<?php
require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Type" content="text/HTML; charset=<?php echo get_locale('charset');?>"  />
<?php echo link_to_css( get_conf('claro_stylesheet') . '/main.css', 'screen, projection, tv' );?> 
<?php 
if ( get_locale('text_dir') == 'rtl' ): 
    echo link_to_css( get_conf('claro_stylesheet') . '/rtl.css', 'screen, projection, tv' );
endif; 
?> 
<?php echo link_to_css( 'print.css', 'print' );?> 
<link rel="top" href="<?php get_path('url'); ?>/index.php" title="" />
<link href="http://www.claroline.net/documentation.htm" rel="Help" />
<link href="http://www.claroline.net/credits.htm" rel="Author" />
<link href="http://www.claroline.net" rel="Copyright" />
<?php if (file_exists(get_path('rootSys').'favicon.ico')): ?>
<link href="<?php echo rtrim( get_path('clarolineRepositoryWeb'), '/' ).'/../favicon.ico'; ?>" rel="shortcut icon" />
<?php endif; ?>
<script type="text/javascript" src="<?php echo get_path( 'rootWeb' ); ?>web/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo get_path( 'rootWeb' ); ?>web/js/claroline.js"></script>
<script type="text/javascript" src="<?php echo get_path( 'rootWeb' ); ?>web/js/claroline.ui.js"></script>
</head>
<body>
  <div id="description"><span><?php echo get_lang('Click on the left to start a module.'); ?></span></div>
  <iframe style="width: 100%; height: 100%; border: 0;" frameborder="0" id="content" />
</body>
</html>