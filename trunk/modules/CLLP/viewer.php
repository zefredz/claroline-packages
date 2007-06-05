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
 * @author Sebastien Piraux
 *
 */
 
$tlabelReq = 'CLLP';

require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

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

require_once dirname( __FILE__ ) . '/lib/path.class.php';

/*
 * init request vars
 */
$acceptedCmdList = array(  );

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'], $acceptedCmdList) )   $cmd = $_REQUEST['cmd'];
else                                                                            $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;


/*
 * init other vars
 */

// admin only page and path is required as we edit a path ...
if( is_null($pathId) )
{
	header("Location: ../index.php");
	exit();
}
else
{
    $path = new path();
    
    if( !$path->load($pathId) )
    {
        // path is required
        header("Location: ../index.php");
    	exit();
    }
}


claro_set_display_mode_available(false);

$user_id = claro_get_current_user_id();

$fullScreen = $path->isFullScreen();



/*
 * Output
 */
 
echo '<frameset rows="130,*" border="no">' . "\n"
//- header
.    '<frame id="header" src="">' . "\n"
//-- main content
.    ' <frameset cols="190,*">' . "\n"
//-- right column
.    '  <frameset rows="*,80,80">' . "\n"
//--- left menu
.    '   <frame src="">' . "\n"
.    '   <frame src="">' . "\n"
.    '   <frame src="">' . "\n"
.    '  </frameset>' . "\n"
//-- content
.    '  <frame src="">' . "\n"
.    ' </frameset>' . "\n"
//- no frame
.    ' <noframe>' . "\n"
.    get_lang('Your browser cannot see frames.')
.    ' </noframe>' . "\n"
.    '</frameset>' . "\n";

?>
