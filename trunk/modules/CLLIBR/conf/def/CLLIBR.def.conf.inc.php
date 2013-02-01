<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 1.1.5
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 */

if ( count( get_included_files() ) == 1 ) die( '---' );

$conf_def['config_code'] = 'CLLIBR';
$conf_def['config_file'] = 'CLLIBR.conf.php';
$conf_def['config_name'] = 'Online Library for Claroline';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description'] = '';
$conf_def['section']['main']['properties'] = array ( 'CLLIBR_encryption_key' , 'CLLIBR_restricted_deletion' );

$conf_def_property_list[ 'CLLIBR_encryption_key' ] = array (
          'label'       => get_lang( 'Encryption key' )
        , 'description' => get_lang( 'This keyword is used to encrypt the name of the files stored in repository' )
        , 'type'        => 'string'
        , 'default'     => ''
       );

$conf_def_property_list[ 'CLLIBR_restricted_deletion' ] = array (
          'label'       => get_lang( 'Restricted rights for resource deletion' )
        , 'description' => get_lang( 'Users can only delete resources they submitted themselves' )
        , 'type'        => 'boolean'
        , 'default'     => FALSE
        , 'acceptedValue' => array('TRUE' => 'Yes', 'FALSE' => 'No')
       );