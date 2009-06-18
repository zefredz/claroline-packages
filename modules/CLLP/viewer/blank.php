<?php

$tlabelReq = 'CLLP';

require_once dirname(__FILE__) . '/../../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_tool_allowed() )
{
    if ( claro_is_in_a_course() )
    {
        claro_die( get_lang( "Not allowed" ) );
    }
    else
    {
        claro_disp_auth_form( true );
    }
}

require_once dirname( __FILE__ ) . '/../lib/path.class.php';

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


/*
 * init other vars
 */

// admin only page and path is required as we edit a path ...
if( is_null($pathId) )
{
	claro_die(get_lang("Not allowed"));
}

$path = new path();

if( ! $path->load( $pathId ) )
{
  claro_die(get_lang("Not allowed"));
}

$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '
. '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n"
. '<html>' . "\n"
. '<head>' . "\n"
. '<title></title>' . "\n"
. '<meta http-equiv="Content-Script-Type" content="text/javascript" />' . "\n"
. '<meta http-equiv="Content-Style-Type" content="text/css" />' . "\n"
. '<meta http-equiv="Content-Type" content="text/HTML; charset=' . get_locale('charset') . '"  />' . "\n"
;
$out .= link_to_css( get_conf('claro_stylesheet') . '/main.css', 'screen, projection, tv' );

if ( get_locale('text_dir') == 'rtl' ): 
    $out .= link_to_css( get_conf('claro_stylesheet') . '/rtl.css', 'screen, projection, tv' );
endif; 

$out .= link_to_css( 'print.css', 'print' )
. '<link rel="top" href="' . get_path('url') . '/index.php" title="" />' . "\n"
. '<link href="http://www.claroline.net/documentation.htm" rel="Help" />' . "\n"
. '<link href="http://www.claroline.net/credits.htm" rel="Author" />' . "\n"
. '<link href="http://www.claroline.net" rel="Copyright" />' . "\n"
;
if (file_exists(get_path('rootSys').'favicon.ico')): 
$out .= '<link href="' . rtrim( get_path('clarolineRepositoryWeb'), '/' ).'/../favicon.ico' .'" rel="shortcut icon" />';
endif;

$out .= '<script type="text/javascript" src="'. get_path( 'rootWeb' ) .'web/js/jquery.js"></script>' . "\n"
. '<script type="text/javascript" src="' . get_path( 'rootWeb' ) .'web/js/claroline.js"></script>' . "\n"
. '<script type="text/javascript" src="' . get_path( 'rootWeb' ) .'web/js/claroline.ui.js"></script>' . "\n"
. '</head>' . "\n"
. '<body>' . "\n"
. '<div id="description">'
. '<span style="font-weight: bold;">'.$path->getTitle().'</span>' . "\n"
. ( $path->getDescription()  ? '<p>' . $path->getDescription() . '</p>' . "\n" : '') 
. '<p>' . get_lang('Click on the left to start a module.') .'</p>' . "\n"
. '</div>' . "\n"
. '<iframe style="width: 100%; height: 100%; border: 0;" frameborder="0" id="content" />' . "\n"
. '</body>'
. '</html>'
;

echo $out;