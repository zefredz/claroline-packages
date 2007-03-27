<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for CLJCHAT config file
 *
 * @version 1.8 $Revision$
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 * @see http://www.claroline.net/wiki/index.php/CLCHT
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package CLJCHAT
 */

$conf_def['config_file']='CLJCHAT.conf.php';
$conf_def['config_code']='CLJCHAT';
$conf_def['config_name']='Chat';
$conf_def['config_class']='tool';


$conf_def['section']['display']['label']='Display Settings';
$conf_def['section']['display']['properties'] =
array ( 'refresh_display_rate' , 
        'max_nick_length'
      );

$conf_def_property_list['refresh_display_rate'] =
array ( 'label'       => 'Refresh time'
      , 'description' => 'Time to automatically refresh the user screen. Each refresh is a request to your server.'."\n"
                       . 'Too low value can be hard for your server. Too high value can be hard for user.'."\n"
      , 'default'     => '10'
      , 'unit'        => 'seconds'
      , 'acceptedValue' => array( 'min' => 4, 'max' => 90)
      , 'type'        => 'integer'
      );


$conf_def_property_list['max_nick_length'] =
array ( 'label'       => 'Maximum lengh for a nick'
      , 'description' => 'If the name and the firstname are longer than this value, the script reduce it.'."\n"
                       . 'For revelance, it\'s interesting to not work with to little value'
      , 'default'     => '20'
      , 'unit'        => 'characters'
      , 'acceptedValue' => array( 'min' => 5, 'max' => 60)
      , 'type'        => 'integer'
      );

?>
