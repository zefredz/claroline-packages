<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLLP
 *
 * @author Dimitri Rambout
 *
 */

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


if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/item.class.php';
require_once dirname( __FILE__ ) . '/../lib/attempt.class.php';
require_once dirname( __FILE__ ) . '/../lib/blockingcondition.class.php';

require_once dirname( __FILE__ ) . '/../lib/scormInterface.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm12.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm13.lib.php';

// force headers
header('Content-Type: text/html; charset=UTF-8'); // Charset
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$itemList = new PathItemList($pathId);
$itemListArray = $itemList->getFlatList();

$item = new item();

if( !$item->load($itemId) )
{
  $out = get_lang('Unable to load item');
}
else
{
  $blockcond = new blockingcondition( $itemId );
        
  $htmlPrereqContainer = '';
  
  // load blocking conditions dependencies
  if ( $item->getParentId() > 0 )
  {
    $blockcondsDependencies = array_reverse( $blockcond->loadRecursive( $item->getParentId(), true ) );
    if( count($blockcondsDependencies) )
    {            
      $htmlPrereqContainer .= '<div>' . "\n"
      .   '<strong>'. get_lang('Blocking conditions dependencies') .'</strong> <br />' . "\n";
      foreach( $blockcondsDependencies  as $dependency)
      {
        if( isset( $dependency['data'] ) )
        {
          $blockconds = $dependency['data'];
          $htmlPrereqContainer .= '<div>' . "\n"
          .    '<strong>'. htmlspecialchars($dependency['title']) .'</strong>';
          foreach( $blockconds['item'] as $key => $value)
          {
            $htmlPrereqContainer .= '<div>' . "\n";
            if( $key > 0 )
            {
                $htmlPrereqContainer .= '<select name="_condition[]" disabled="disabled">' . "\n"
                .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
                .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
                .   '</select>'
                .   '<br />' . "\n";
            }
            
            $htmlPrereqContainer .= '<select name="_item[]" disabled="disabled">' . "\n";
            foreach( $itemListArray as $anItem )
            {
                $htmlPrereqContainer .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
            }
            $htmlPrereqContainer .= '</select>'
            .   '<select name="_operator[]" disabled="disabled">' . "\n"
            .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
            .   '</select>'
            .   '<select name="_status[]" disabled="disabled">' . "\n"
            .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
            .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
            //.   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
            .   '<input type="'.($blockconds['status'][$key] == 'COMPLETED' ? 'text' : 'hidden').'" name="raw_to_pass[]" disabled="disabled" value="'.(int) $blockconds['raw_to_pass'][$key].'" style="width: 50px; text-align: right;" />%' . "\n"
            .   '</select>'
            .   '</div>' . "\n";
          }
          $htmlPrereqContainer .= '</div>' . "\n";
        }           
      }
      $htmlPrereqContainer .=   '</div> <br />' . "\n";
    }    
  }
  
  if( $blockcond->load() )
  {
    $blockconds = $blockcond->getBlockConds();
    
    // show prerequisites form
    $htmlPrereqContainer .= '<strong>'.get_lang('Blocking conditions').'</strong>' . "\n"
    ;
    
    foreach( $blockconds['item'] as $key => $value )
    {
      $htmlPrereqContainer .= '<div>' . "\n";
      if( $key > 0 )
      {
          $htmlPrereqContainer .= '<select name="condition[]" disabled="disabled">' . "\n"
          .   '<option value="AND" '.($blockconds['condition'][$key-1] == 'AND' ? 'selected="selected"' : '').'>'.get_lang('AND').'</option>' . "\n"
          .   '<option value="OR" '.($blockconds['condition'][$key-1] == 'OR' ? 'selected="selected"' : '').'>'.get_lang('OR').'</option>' . "\n"
          .   '</select>'
          .   '<br />' . "\n";
      }
      
      $htmlPrereqContainer .= '<select name="item[]" disabled="disabled">' . "\n";
      foreach( $itemListArray as $anItem )
      {
          $htmlPrereqContainer .= '<option value="'. $anItem['id'] .'" style="padding-left:'.(5 + $anItem['deepness']*10).'px;" '.($value == $anItem['id'] && $anItem['type'] != 'CONTAINER' ? 'selected="selected"' : '').' '.($anItem['type'] == 'CONTAINER' ? 'disabled="disabled"' : '').'>'.$anItem['title'].'</option>' . "\n";
      }
      $htmlPrereqContainer .= '</select>'
      .   '<select name="operator[]" disabled="disabled">' . "\n"
      .   '<option value="=" '.( $blockconds['operator'][$key] == '=' ? 'selected="selected"' : '').'>=</option>' . "\n"
      .   '</select>'
      .   '<select name="status[]" disabled="disabled">' . "\n"
      .   '<option value="COMPLETED" '.( $blockconds['status'][$key] == 'COMPLETED' ? 'selected="selected"' : '' ).'>'.get_lang('completed').'</option>' . "\n"
      .   '<option value="INCOMPLETE" '.( $blockconds['status'][$key] == 'INCOMPLETE' ? 'selected="selected"' : '' ).'>'.get_lang('incomplete').'</option>' . "\n"
      //.   '<option value="PASSED" '.( $blockconds['status'][$key] == 'PASSED' ? 'selected="selected"' : '' ).'>'.get_lang('passed').'</option>' . "\n"
      .   '<input type="'.($blockconds['status'][$key] == 'COMPLETED' ? 'text' : 'hidden').'" name="raw_to_pass[]" disabled="disabled" value="'.(int) $blockconds['raw_to_pass'][$key].'" style="width: 50px; text-align: right;" />%' . "\n"
      .   '</select>'
      .   '</div>' . "\n";
    }
  }
  $out = get_lang("This module is not avaiable because you don't fit with the prerequisite.")
  . '<br />' . "\n"
  . get_lang('Check bellow the conditions you need to have to take this module.')
  . '<br /><br />' . "\n\n"
  . $htmlPrereqContainer
  ;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title><?php echo get_lang('Blocking conditions'); ?></title>
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
  <?php echo claro_utf8_encode( $out, get_conf('charset') ); ?>
</body>
</html>