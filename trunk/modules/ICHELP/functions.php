<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.5 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if( $_SERVER['PHP_SELF'] != get_module_url( 'ICHELP' ) . '/controller.php' )
{
    unset( $_SESSION[ 'ICHELP_data' ] );
}