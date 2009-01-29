<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * @version 1.0.0
 *
 * @version 1.8 $Revision$
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLOOVOO
 *
 * @author Wanjee <wanjee.be@gmail.com>
 *
 */

$conf_def['config_file'] = 'CLOOVOO.conf.php';
$conf_def['config_code'] = 'CLOOVOO';
$conf_def['config_name'] = 'ooVoo status notifier';
$conf_def['config_class'] = 'tool';


$conf_def['section']['display']['label']='Display Settings';
$conf_def['section']['display']['properties'] =
array ( 'oovoo_template', 'oovoo_theme' );

$conf_def_property_list['oovoo_template'] =
array ( 'label'       => 'Template'
      , 'description' => 'Choose one template from the selection available on "ooVoo link" page on http://www.oovoo.com/'
      , 'default'       => 0
      , 'type'          => 'enum'
      , 'display'       => TRUE
      , 'readonly'      => TRUE
      , 'acceptedValue' => array ( 0 => 'Claroline'
                               , 1 => 'ooVoo Template 1'
                               , 2 => 'ooVoo Template 2'
                               , 3 => 'ooVoo Template 3'
                               , 4 => 'ooVoo Template 4'
                               )
      );


$conf_def_property_list['oovoo_theme'] =
array ( 'label'       => 'Theme'
      , 'description' => 'Choose one theme from the selection available on "ooVoo link" page on http://www.oovoo.com/ (pick a number from 1 to 12).  Not required if Claroline template is choosed.'
      , 'default'       => '1'
      , 'type'          => 'int'
      , 'display'       => TRUE
      , 'readonly'      => TRUE
      ,'acceptedValue' => array ( 'min' => '0', 'max' => '12' )
      );

?>
