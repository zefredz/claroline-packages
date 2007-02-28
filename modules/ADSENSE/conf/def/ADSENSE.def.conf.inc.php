<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * This file describe the parameter for Google AdSense module
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package ADSENSE
 *
 * TODO : add support of multiple selected colors ["0000FF","0000FF","0000CC","0000CC"]
 */
$conf_def['config_code'] = 'ADSENSE';
$conf_def['config_file'] = 'ADSENSE.conf.php';
$conf_def['config_name'] = 'Google AdSense';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['description']= 'See <a href="http://www.google.com/support/adsense">Google AdSense help center</a> for help about the required values.';
$conf_def['section']['main']['properties'] =
array ( 'google_ad_client'
      , 'google_ad_format'
      , 'google_ad_width'      
      , 'google_ad_height'
      , 'google_ad_type'
      , 'google_ad_channel' 
      );

$conf_def['section']['colors']['label']      = 'Colors';
$conf_def['section']['colors']['description']= 'Set the color scheme of you Google AdSense.  Set color using hexadecimal color code without "#".';
$conf_def['section']['colors']['properties'] =
array ( 'google_ad_borderColor'
      , 'google_ad_bgColor'
      , 'google_ad_linkColor'
      , 'google_ad_urlColor'
      , 'google_ad_textColor' 
      );

// PROPERTIES

$conf_def_property_list['google_ad_client'] =
array ( 'label'       => 'Google AdSense client id'
      , 'description' => 'ex : pub-0123456789012345'
      , 'default'     => ''
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['google_ad_format'] =
array ( 'label'       => 'Format'
      , 'description' => 'google_ad_format value'
      , 'default'     => '728x90_as'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
      
$conf_def_property_list['google_ad_width'] =
array ('label'     => 'Width'
        ,'description' => 'google_ad_width value, should match the first number of \'Format\''
        ,'default'   => '728'
        ,'unit'     => 'pixels'
        ,'type'      => 'integer'
        ,'display'       => TRUE
        ,'readonly'      => FALSE
        ,'acceptedValue' => array ( 'min' => '0' )
        );
              
$conf_def_property_list['google_ad_height'] =
array ('label'     => 'Height'
        ,'description' => 'google_ad_height value, should match the second number of \'Format\''
        ,'default'   => '90'
        ,'unit'     => 'pixels'
        ,'type'      => 'integer'
        ,'display'       => TRUE
        ,'readonly'      => FALSE
        ,'acceptedValue' => array ( 'min' => '0' )
        );                    
     
$conf_def_property_list['google_ad_type'] =
array ( 'label'       => 'Type'
      , 'description' => 'google_ad_type value'
      , 'default'     => 'text_image'
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
      
$conf_def_property_list['google_ad_channel'] =
array ( 'label'       => 'Channel'
      , 'description' => 'google_ad_channel value'
      , 'default'     => ''
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

/* Colors scheme : use a regexp to check that string is an hexadecimal value */     
 
$conf_def_property_list['google_ad_borderColor'] =
array ( 'label'       => 'Border color'
      , 'description' => 'ex : BCBCBC'
      , 'default'     => 'BCBCBC'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'         
      );
      
$conf_def_property_list['google_ad_bgColor'] =
array ( 'label'       => 'Background color'
      , 'description' => 'ex : FFFFFF'
      , 'default'     => 'FFFFFF'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'       
      );      

$conf_def_property_list['google_ad_linkColor'] =
array ( 'label'       => 'Links color:'
      , 'description' => 'ex : 336699'
      , 'default'     => '336699'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'        
      );  
      
$conf_def_property_list['google_ad_urlColor'] =
array ( 'label'       => 'URL color'
      , 'description' => 'ex : 800000'
      , 'default'     => '800000'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'     
      );  

$conf_def_property_list['google_ad_textColor'] =
array ( 'label'       => 'Text color'
      , 'description' => 'ex : 6F6F6F'
      , 'default'     => '6F6F6F'
      , 'type'        => 'regexp'
      , 'display'     => TRUE
      , 'readonly'    => FALSE 
      , 'acceptedValue' => '^[0-9A-Fa-f]{6,6}$'        
      );                   
?>
