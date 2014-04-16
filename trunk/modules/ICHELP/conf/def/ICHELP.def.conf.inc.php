<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.9 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'ICHELP';
$conf_def['config_file'] = 'ICHELP.conf.php';
$conf_def['config_name'] = 'Online Help Form';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'ICHELP_mail_main' , 'ICHELP_mail_alt' );

$conf_def_property_list[ 'ICHELP_mail_main' ] = 
 array ( 'label'       => 'Mail'
       , 'description' => 'Main mail adress which requests are sent to'
       , 'type'        => 'string'
       , 'default'     => 'icampus@uclouvain.be'
    );

$conf_def_property_list[ 'ICHELP_mail_alt' ] = 
 array ( 'label'       => 'Altenative mail'
       , 'description' => 'Mail adress of the external helpdesk'
       , 'type'        => 'string'
       , 'default'     => 'icampus-8282@uclouvain.be'
    );