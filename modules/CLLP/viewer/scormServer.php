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
$acceptedCmdList = array('doCommit', 'rqRefresh', 'rqContentUrl', 'rqToc', 'getPrevious', 'getNext');

if( isset($_REQUEST['cmd']) && in_array($_REQUEST['cmd'],$acceptedCmdList) ) $cmd = $_REQUEST['cmd'];
else                                                                         $cmd = null;

if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;


$userId = claro_get_current_user_id();
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
FromKernel::uses('core/linker.lib');

// end script here if no cmd is set
if( is_null($cmd) ) echo 'Error : no command.';


// force headers
header('Content-Type: text/html; charset=UTF-8'); // Charset
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if( $cmd == 'doCommit' )
{
    lpDebug('doCommit');
    if( empty($_REQUEST['scormdata']) )
    {
        lpDebug("No data received.");
        return false;
    }
    
    try
    {
        $dataModelValues = json_decode($_REQUEST['scormdata']);
    }
    catch ( Exception $e )
    {
        lpDebug('doCommit failed : cannot decode json in '.__FILE__.' at line ' . __LINE__ . ' with message : ' . $e->getMessage());
        return false;
    }

    // get serialized attempt
    $thisAttempt = unserialize($_SESSION['thisAttempt']);

    // create new attempt for this item
    $itemAttempt = new itemAttempt();
    $itemAttempt->setAttemptId($thisAttempt->getId());
    $itemAttempt->setItemId($itemId);

    // try to load itemAttempt
    $itemAttempt->load($thisAttempt->getId(), $itemId);
    lpDebug($this->Attempt->getId());
    // load path
    $path = new Path();
    if( is_null($pathId) || !$path->load($pathId) )
    {
        // cannot find path ... 
        lpDebug('cannot find path #' . $pathId);
        return false;
    }
    
    if( $path->getVersion() == 'scorm12' )
    {
        $scormAPI = new Scorm12();
        lpDebug('Choose api 1.2');
    }
    else
    {
    
        $scormAPI = new Scorm13();
        lpDebug('Choose api 1.3');
    }

    $scormAPI->api2ItemAttempt($dataModelValues, $itemAttempt);
    
    if( $itemAttempt->validate() )
    {
        if( $itemAttempt->save() )
        {
            lpDebug('Item attempt saved');
            // get new item attempt list
            // compute new values of attempt (progress,...)
            $itemList = new PathUserItemList($pathId, $userId, $thisAttempt->getId());
            $itemListArray = $itemList->getFlatList();

            if( !empty($itemListArray) )
            {
                $progressTotal = 0;
                foreach ( $itemListArray as $anItem)
                {
                    if( $anItem['score_max'] > 0 )
                    {
                        // fixme : take into account the fact that min may be more than 0
                        $progressTotal += $anItem['score_raw'] / $anItem['score_max'] * 100; 
                    }
                }

                $thisAttempt->setProgress($progressTotal / count($itemListArray));
            }
            else
            {
                $thisAttempt->setProgress(0);
            }
            // save attempt        
            if( $thisAttempt->save() )
            {
                lpDebug('Attempt saved');
                $_SESSION['thisAttempt'] = serialize($thisAttempt);
            }
            return true;
        }
        else
        {
            lpDebug('Cannot save itemAttempt');
            return false;
        }
    }
    else
    {
        lpDebug('Cannot validate itemAttempt');
        return false;
    }

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
            $resolver = new ResourceLinkerResolver();
            $itemUrl = $resolver->resolve(ClarolineResourceLocator::parse($item->getSysPath()));
            
            // fix ? or &amp; depending if there is already a ? in url
            $itemUrl .= ( false === strpos($itemUrl, '?') )? '?':'&';
            
            $itemUrl .= 'calledFrom=CLLP&embedded=true'; 
            
            // temporary fix for document tool (FIXME when linker will be updated)
            // we have to open a frame that will discuss with API and open the document instead 
            // of directly opening it
            lpDebug($itemUrl);
            $itemUrl = str_replace('backends/download.php','document/connector/cllp.frames.cnr.php',$itemUrl);
            lpDebug($itemUrl);
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
    // load path
    $path = new Path();
    if( is_null($pathId) || !$path->load($pathId) )
    {
        // cannot find path ... 
        lpDebug('cannot find path #' . $pathId);
        return false;
    }
    
    // prepare list to display
    if( $userId )
    {
        // get serialized attempt
        $thisAttempt = unserialize($_SESSION['thisAttempt']);
        
        $itemList = new PathUserItemList($pathId, $userId, $thisAttempt->getId());
    }
    else
    {
        $itemList = new PathItemList($pathId);
    }

    $itemListArray = $itemList->getFlatList();

    // Output
    $html = "\n"
    .    '<div id="progress">' . claro_html_progress_bar($thisAttempt->getProgress(), 1) . '&nbsp;'.$thisAttempt->getProgress().'%</div>' . "\n"
    .    '<div id="table_of_content_inner" >' . "\n";

    $html .= '<h3>'.htmlspecialchars($path->getTitle()).'</h3>' . "\n";

    foreach( $itemListArray as $anItem )
    {
        $completionIcon = (strtolower($anItem['completion_status']) == 'completed')? 'completed':'incomplete';
        $html .= '<a id="item_'.$anItem['id'].'_anchor">' . "\n";

        // title
        $html .= '<div style="padding-left:'.($anItem['deepness']*10).'px;" class="item" id="item_'.$anItem['id'].'">';

        if( $anItem['type'] == 'MODULE' || $anItem['type'] == 'SCORM' )
        {
            $html .= '<img src="'.get_module_url('CLLP').'/img/item.png" alt="" />'
            .     '&nbsp;<a href="#" onClick="lpHandler.setContent(\''.$anItem['id'].'\');return false;">' . $anItem['title'] . '</a>'
            .    '<img id="item_'.$anItem['id'].'_status" src="'.get_icon_url($completionIcon).'" />';
        }
        else
        {
            $html .= '<img src="'.get_module_url('CLLP').'/img/chapter.png" alt="" />'
            .     '&nbsp;' . $anItem['title'];
        }

        $html .= '</div>' . "\n";
    }

    $html .= '</div>';

    echo claro_utf8_encode($html);
}

if( $cmd == 'getPrevious' )
{
    if( $userId )
    {
        // get serialized attempt
        $thisAttempt = unserialize($_SESSION['thisAttempt']);
        
        $itemList = new PathUserItemList($pathId, $userId, $thisAttempt->getId());
    }
    else
    {
        $itemList = new PathItemList($pathId);
    }
    
}

if( $cmd == 'getNext' )
{
    if( $userId )
    {
        // get serialized attempt
        $thisAttempt = unserialize($_SESSION['thisAttempt']);
        
        $itemList = new PathUserItemList($pathId, $userId, $thisAttempt->getId());
    }
    else
    {
        $itemList = new PathItemList($pathId);
    }
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