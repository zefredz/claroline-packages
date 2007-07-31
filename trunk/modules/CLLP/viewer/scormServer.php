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

/*
 * Shared libraries
 */
include_once get_path('incRepositorySys') . '/lib/embed.lib.php';
require_once get_path('clarolineRepositorySys') . '/linker/resolver.lib.php';

// end script here if no cmd is set
if( is_null($cmd) ) echo 'Error : no command.';


if( $cmd == 'doCommit' )
{
	require_once dirname( __FILE__ ) . '/../lib/JSON.php';

	$json = new Services_JSON();
	// it returns an object so we have to cast it to an array to be able to use it correctly
	// do not forget to also cast sub-arrays like cmi.comments_from_learner
	$decodedScormData = (array) $json->decode($_REQUEST['scormdata']);

	$thisAttempt = unserialize($_SESSION['thisAttempt']);

	$itemAttempt = new itemAttempt();
	$itemAttempt->load($thisAttempt['id'], $itemId); // TODO do that properly ^^

	if( isset($thisAttempt['itemAttemptList'][$itemId]) )
	{

	}
	else
	{

	}
}

if( $cmd == 'rqRefresh' )
{

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
dump($itemUrl);
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

    $html = '';

    $html .= '<table style="font-size: small;" width="100%" border="0" cellspacing="2">' . "\n";

    foreach( $itemListArray as $anItem )
    {
        $html .= '<tr>' . "\n";

        // result
    	$html .= '<td>' . "\n"
    	.    '<img src="' . get_path('imgRepositoryWeb') . 'checkbox_on.gif" border="0" alt="' . get_lang('Checked') . '" />' . "\n"
    	.    '</td>' . "\n";

        // title
        $html .= '<td align="left" style="padding-left:'.($anItem['deepness']*10).'px;">'
        .    '<img src="'.get_module_url('CLLP').'/img/'.(($anItem['type'] == 'CONTAINER')? 'chapter.png': 'item.png').'" alt="" />';

        if( $anItem['type'] == 'MODULE' )
        {
            $itemUrl = $resolver->resolve($anItem['sys_path']);
            $html .= '&nbsp;<a href="'.$itemUrl.'" target="lp_content" onClick="openItem(\''.$anItem['id'].'\');return false;">' . $anItem['title'] . '</a>';
        }
        elseif( $anItem['type'] == 'SCORM' )
        {
            $itemUrl = $scormBaseUrl . $anItem['sys_path'];
            $html .= '&nbsp;<a href="'.$itemUrl.'" target="lp_content" onClick="openItem(\''.$anItem['id'].'\');return false;">' . $anItem['title'] . '</a>';
        }
        else
        {
            $html .= '&nbsp;' . $anItem['title'];
        }

        $html .= '</td>' . "\n";

        $html .= '</tr>' . "\n";
    }

    $html .= '</table>';

    echo $html;
}

function dump($var)
{
	$fp = fopen('debug.txt','a');
	fwrite($fp, "\n" . '------------------------------' . "\n" . print_r($var,true));
	fclose($fp);
}
// ajax output
?>