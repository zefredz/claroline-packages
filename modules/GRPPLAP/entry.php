<?php // $Id: 
/**
 *
 * @version 0.1 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @author Claroline team <info@claroline.net>
 * @author Dimitri Rambout <dim@clarolinet.net>
 *
 * @package GRPPLAP
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );

require_once( get_module_path('GRPPLAP' ) . '/functions.php' );
// TODO : on install, check if GRAPPLE module exist.
/*require_once( get_module_path( 'GRAPPLE' ) . '/lib/grapple.class.php' );
require_once( get_module_path( 'GRPPLAP' ) . '/lib/grapple.listener.class.php' );*/



//Login
// Check if user is authenticated to send data
//$grappleListener->userLogin();

//Student enrollment
//$grappleListener->studentEnrollment();

?>
