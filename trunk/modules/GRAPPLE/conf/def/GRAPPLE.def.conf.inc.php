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
 * @package GRAPPLE
 */

$conf_def['config_file'] = 'GRAPPLE.conf.php';
$conf_def['config_code'] = 'GRAPPLE';
$conf_def['config_name'] = 'Learning path (Grapple)';
$conf_def['config_class'] = 'tool';


$conf_def['section']['display']['label']='Display Settings';
$conf_def['section']['display']['properties'] =
array ( 'scorm_api_debug' );

$conf_def_property_list['scorm_api_debug'] =
array ( 'label'       => 'SCORM API debug mode'
      , 'description' => 'If setted debug messages from the SCORM API will be shown to course administrator in the learning path viewer'
      , 'default'       => 0
      , 'type'          => 'enum'
      , 'display'       => TRUE
      , 'readonly'      => FALSE
      , 'acceptedValue' => array ( 0 => 'No debug messages'
                               , 1 => 'Messages send by content'
                               , 2 => 'All messages'
                               )
      );

$conf_def['section']['webservices']['label'] = 'Web Services';
$conf_def['section']['webservices']['properties'] =
array ( 'geb_wsdl', 'courses_wsdl' );

$conf_def_property_list['geb_wsdl'] =
array (  'label'        => 'GEB Webservice'
       , 'description'  => 'URL of the GEB Webservice'
       , 'default'      => ''
       ,  'type'        => 'string'
       ,  'display'     => true
       ,  'readonly'    => false
       );

$conf_def_property_list['courses_wsdl'] =
array (  'label'        => 'GEB Webservice (courses)'
       , 'description'  => 'URL of the GEB Webservice (courses)'
       , 'default'      => ''
       ,  'type'        => 'string'
       ,  'display'     => true
       ,  'readonly'    => false
       );


$conf_def['section']['privacy']['label'] = 'Quiz privacy';
$conf_def['section']['privacy']['description'] = 'Specify which event you want to send on the Grapple Event Bus';
$conf_def['section']['privacy']['properties'] =
array( 'grapple_privacy_quiz_starttime'
      , 'grapple_privacy_quiz_stoptime'
      , 'grapple_privacy_quiz_attemptnb'
      , 'grapple_privacy_quiz_score_total'
      , 'grapple_privacy_quiz_score_min'
      , 'grapple_privacy_quiz_score_max'
      , 'grapple_privacy_quiz_treshold'
      , 'grapple_privacy_quiz_title'
      , 'grapple_privacy_quiz_description');

$conf_def_property_list['grapple_privacy_quiz_starttime'] =
array ( 'description' => 'Enable to send the start time of a Quiz'
      , 'label'       => 'Quiz start time'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_stoptime'] =
array ( 'description' => 'Enable to send the stop time of a Quiz'
      , 'label'       => 'Quiz stop time'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_attemptnb'] =
array ( 'description' => 'Enable to send the number of attempts of a Quiz'
      , 'label'       => 'Number of Quiz attempts'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_score_total'] =
array ( 'description' => 'Enable to send the result of a Quiz'
      , 'label'       => 'Result of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_score_min'] =
array ( 'description' => 'Enable to send the minimum score of a Quiz'
      , 'label'       => 'Minimum score of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_score_max'] =
array ( 'description' => 'Enable to send the maximum score of a Quiz'
      , 'label'       => 'Maximum score of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_treshold'] =
array ( 'description' => 'Enable to send completion treshold of a Quiz'
      , 'label'       => 'Completion threshold of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_title'] =
array ( 'description' => 'Enable to send title of a Quiz'
      , 'label'       => 'Title of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

$conf_def_property_list['grapple_privacy_quiz_description'] =
array ( 'description' => 'Enable to send description of a Quiz'
      , 'label'       => 'Description of the Quiz'
      , 'default'     => true
      , 'type'        => 'boolean'
      , 'acceptedValue' => array ('TRUE'=>'Yes'
                               ,'FALSE'=>'No'
                               )
      , 'display'     => true
      , 'readonly'    => false
      );

?>
