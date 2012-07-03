<?php // $Id$
/**
 * Online Meetings for Claroline
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLMEETNG';
$conf_def['config_file'] = 'CLMEETNG.conf.php';
$conf_def['config_name'] = 'Online Meetings for Claroline';

$conf_def['section']['main']['label'] = 'Main';
$conf_def['section']['main']['description' ]= '';
$conf_def['section']['main']['properties'] = array (  'CLMEETNG_server_url'
                                                    , 'CLMEETNG_server_port'
                                                    , 'CLMEETNG_service_name'
                                                    , 'CLMEETNG_admin_username'
                                                    , 'CLMEETNG_admin_password' );

$conf_def_property_list[ 'CLMEETNG_server_url' ] = array (
         'label'       => get_lang( 'Server URL' )
       , 'description' => ''
       , 'type'        => 'string'
       , 'default'     => ''
       );

$conf_def_property_list[ 'CLMEETNG_server_port' ] = array (
         'label'       => get_lang( 'Server port' )
       , 'description' => ''
       , 'type'        => 'int'
       , 'default'     => '5080'
       );

$conf_def_property_list[ 'CLMEETNG_service_name' ] = array (
         'label'       => get_lang( 'Service name' )
       , 'description' => ''
       , 'type'        => 'string'
       , 'default'     => 'openmeetings'
       );

$conf_def_property_list[ 'CLMEETNG_admin_username' ] = array (
         'label'       => get_lang( 'Openmeetings Server Admin\'s identifiant' )
       , 'description' => ''
       , 'type'        => 'string'
       , 'default'     => ''
       );

$conf_def_property_list[ 'CLMEETNG_admin_password' ] = array (
         'label'       => get_lang( 'Openmeetings Server Admin\'s password' )
       , 'description' => ''
       , 'type'        => 'string'
       , 'default'     => ''
       );