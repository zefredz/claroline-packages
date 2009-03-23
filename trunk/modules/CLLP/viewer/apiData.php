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
require_once dirname( __FILE__ ) . '/../lib/path.class.php';
require_once dirname( __FILE__ ) . '/../lib/attempt.class.php';

/*
 * init request vars
 */
if( isset($_REQUEST['pathId']) && is_numeric($_REQUEST['pathId']) )   $pathId = (int) $_REQUEST['pathId'];
else                                                                  $pathId = null;

if( isset($_REQUEST['itemId']) && is_numeric($_REQUEST['itemId']) )   $itemId = (int) $_REQUEST['itemId'];
else                                                                  $itemId = null;


header( 'Content-Type: text/javascript' );

?>
if( ! $.browser.msie && top.window.console && top.window.console.log )
{
	var info = "API data refresh request for item #<?php echo $itemId; ?> in path #<?php echo $pathId; ?>";
        console.info(info);
}

<?php

if( is_null( $pathId ) )
{
    //header("Location: ../index.php");
    exit();
}
else
{
    $path = new path();
    if( !$path->load( $pathId ) )
    {
        //header("Location: ../index.php");
    	exit();
    }
    else
    {
        if( $path->getVersion() == 'scorm12' )
        {
            ?>
            var _api = API;
            <?php
        }
        else
        {
            ?>
            var _api = API_1484_11;
            <?php
        }
    }
}

?>
_api.init();

<?php

if( isset( $_SESSION['thisAttempt'] ) )
{
    $thisAttempt = unserialize( $_SESSION[ 'thisAttempt' ] );
    $itemAttempt = new itemAttempt();
    if( $itemAttempt->load($thisAttempt->getId(), $itemId) )
    {
        ?>
        _api._datamodel['cmi.score.raw']['value'] = <?php echo $itemAttempt->getScoreRaw(); ?>        
        <?php
        
    }
}


?>


<?php
/*
//


// ====================================================
// CMI Elements and Values
//
/*
	// entry handling
    if (isset($userdata->status)) {
        //if ($userdata->status == ''&& (!(($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))&& !($userdata->{'adl.nav.request'} == 'suspendAll'))||($userdata->{'cmi.exit'} == 'normal')) {      //antes solo llegaba esta l�nea hasta el &&
        if (!isset($userdata->{'cmi.exit'}) || (($userdata->{'cmi.exit'} == 'time-out') || ($userdata->{'cmi.exit'} == 'normal'))) {
                $userdata->entry = 'ab-initio';
        } else {
            //if ((isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout')))||(($userdata->{'adl.nav.request'} == 'suspendAll')&& isset($userdata->{'adl.nav.request'}) )) {
            if (isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))) {
                $userdata->entry = 'resume';
            } else {
                $userdata->entry = '';
            }
        }
    }

*/
exit;
?>