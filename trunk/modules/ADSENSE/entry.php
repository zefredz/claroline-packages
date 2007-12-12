<?php // $Id$
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 *
 * @package ADSENSE
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

require_once dirname(__FILE__) . '/adsense.class.php';

//$tlabelReq = 'ADSENSE';

include_once claro_get_conf_repository().'ADSENSE.conf.php'; 


$adSense = new adsense();

$claro_buffer->append($adSense->display());

?>
