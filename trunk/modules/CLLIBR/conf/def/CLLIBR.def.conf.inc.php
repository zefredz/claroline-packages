<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLLIBR';
$conf_def['config_file'] = 'CLLIBR.conf.php';
$conf_def['config_name'] = 'Online Library for Claroline';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= '';
$conf_def['section']['main']['properties'] = array ( 'CLLIBR_storage_directory' );


$conf_def_property_list[ 'CLLIBR_storage_directory' ] = array (
         'label'       => 'Repository path'
       , 'description' => ''
       , 'type'        => 'string'
       , 'default'     => get_path( 'rootSys' ) . 'cllibrary/'
       );