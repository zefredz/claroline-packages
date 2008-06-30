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

require_once dirname( __FILE__ ) . '/../../../claroline/inc/claro_init_global.inc.php';

if ( !claro_is_tool_allowed() )
{
	if ( claro_is_in_a_course() )
	{
		claro_die( get_lang( "Not allowed" ) );
	}
    else
	{
		claro_redirect("index.php");
	}
}

/*
 * init request vars
 */
$acceptedCmdList = array('doCommit', 'rqRefresh', 'rqContentUrl', 'rqToc');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) $cmd = $_REQUEST['cmd'];
else                                                                         $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;

/*
 * Tool libraries
 */
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/item.class.php';
require_once dirname( __FILE__ ) . '/../lib/attempt.class.php';

require_once dirname( __FILE__ ) . '/../lib/scormInterface.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm12.lib.php';
require_once dirname( __FILE__ ) . '/../lib/scorm13.lib.php';

/*
 * Shared libraries
 */
require_once get_path('clarolineRepositorySys') . '/linker/resolver.lib.php';

// end script here if no cmd is set
if( is_null($cmd) ) echo 'Error : no command.';


// force headers
header('Content-Type: text/html; charset=UTF-8'); // Charset
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if( $cmd == 'doCommit' )
{
    lpDebug('doCommit');

    $decodedScormData = json_decode($_REQUEST['scormdata']);

	// get serialized attempt
	$thisAttempt = unserialize($_SESSION['thisAttempt']);

	// create new attempt for this item
	$itemAttempt = new itemAttempt();
	$itemAttempt->setAttemptId($thisAttempt->getId());
	$itemAttempt->setItemId($itemId);

	// try to load itemAttempt
	$itemAttempt->load($thisAttempt->getId(), $itemId);
	
	// load path
    if( is_null($pathId) || !$path->load($pathId) )
    {
        // cannot find path ... 
        return false;
    }
    
    if( $path->getVersion() == 'scorm12' )
    {
        $scormAPI = new Scorm12();
    }
    else
    {
    
        $scormAPI = new Scorm13();
    }
lpDebug($dataModelValues);
	$scormAPI->api2ItemAttempt($dataModelValues, $itemAttempt);
	if( $itemAttempt->validate() )
    {
        if( $itemAttempt->save() )
        {
        	lpDebug('item attempt saved');
        	// get new item attempt list
        	// compute new values of attempt
        	// save attempt

        	// what does a save attempt do here ?
        	$thisAttempt->save();
			return true;
        }
        else
        {
        	return false;
        }
    }

    return false;
}


/**
 * Get the url of one single item by id
 */
if( $cmd == 'rqContentUrl' )
{
    $item = new item();
    $itemUrl = '';

    if( $item->load($itemId) )
    {
        if( $item->getType() == 'MODULE' )
        {
        	$resolver = new Resolver(get_path('rootWeb'));

        	$itemUrl = $resolver->resolve($item->getSysPath());
        }
        elseif( $item->getType() == 'SCORM' )
        {
        	$scormBaseUrl = get_path('coursesRepositoryWeb') . claro_get_course_path() . '/scormPackages/path_' . $pathId . '/';

            $itemUrl = $scormBaseUrl . $item->getSysPath();
        }
        else
        {
            return false;
        }

	    echo $itemUrl;
	    return true;
    }
    else
    {
    	return false;
    }
}

if( $cmd == 'rqToc' )
{
    // check that we have all parameters required
    if( is_null($pathId) )
    {
        return get_lang("Error : missing parameters");
    }
    // prepare list to display
    $itemList = new itemList();

    $itemListArray = $itemList->getFlatList($pathId);


    // init what will be required to resolve urls
    //  urls of claroline tools
    $resolver = new Resolver(get_path('rootWeb'));
    //  urls of scorm packages
    $scormBaseUrl = get_path('coursesRepositoryWeb') . claro_get_course_path() . '/scormPackages/path_' . $pathId . '/';

    $html = "\n";

    $html .= '<table style="font-size: small;" width="100%" border="0" cellspacing="2" id="toc" >' . "\n";

	// TODO path title
	$html .= '<tr>' . "\n"
	.	 '<th>Path title here</th>' . "\n"
	.	 '</tr>' . "\n";

    foreach( $itemListArray as $anItem )
    {
        $html .= '<tr id="item_'.$anItem['id'].'">' . "\n";

        // title
        $html .= '<td align="left" style="padding-left:'.($anItem['deepness']*10).'px;">';

        if( $anItem['type'] == 'MODULE' || $anItem['type'] == 'SCORM' )
        {
            $html .= '<img src="'.get_module_url('CLLP').'/img/item.png" alt="" />'
			.	 '&nbsp;<a href="#" onClick="lpHandler.setContent(\''.$anItem['id'].'\');return false;">' . $anItem['title'] . '</a>';
        }
        else
        {
            $html .= '<img src="'.get_module_url('CLLP').'/img/chapter.png" alt="" />'
			.	 '&nbsp;' . $anItem['title'];
        }

        $html .= '</td>' . "\n";

        $html .= '</tr>' . "\n";
    }

    $html .= '</table>';

    echo $html;
}

function lpDebug($var)
{
    if( claro_debug_mode() )
    {
        $debugFile = get_path('rootSys') . 'tmp/debug.txt';
        
        $msg = '['.date('d-M-Y H:i:s'). '] ' . $var . "\n";     
    
    	$fp = file_put_contents($debugFile,$msg, FILE_APPEND);
    } 
}
// ajax output
?>