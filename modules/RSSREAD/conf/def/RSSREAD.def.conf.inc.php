<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package RSSREAD
 */
$conf_def['config_code'] = 'RSSREAD';
$conf_def['config_file'] = 'RSSREAD.conf.php';
$conf_def['config_name'] = 'Display titles of a RSS feed';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'feedUrl'
      , 'feedTitle'
      , 'feedCharset'
      , 'itemsToShow'
      , 'cacheTime'
      );

// PROPERTIES

$conf_def_property_list['feedUrl'] =
array ( 'label'       => 'Feed URL'
      , 'description' => ''
      , 'default'     => 'http://www.claroline.net/rss.php'
      , 'type'        => 'url'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
      
$conf_def_property_list['feedTitle'] =
array ( 'label'       => 'Feed title'
      , 'description' => 'Leave empty to use the title found in RSS feed.'
      , 'default'     => ''
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

$conf_def_property_list['feedCharset'] =
array ( 'label'       => 'Feed charset'
      , 'description' => 'iso-8859-1, UTF-8,... usefull only if you have problems with the feed.'
      , 'default'     => ''
      , 'type'        => 'string'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );
            
$conf_def_property_list['itemsToShow'] =
array ( 'label'       => 'No. of items to show'
      , 'description' => ''
      , 'default'     => '5'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE     
      , 'acceptedValue' => array ( 'min'=> '0' )       
      );    

$conf_def_property_list['cacheTime'] =
array ( 'label'       => 'Cache time'
      , 'description' => ''
      , 'default'     => '30'
      , 'unit'        => 'minutes'
      , 'type'        => 'integer'
      , 'display'     => TRUE
      , 'readonly'    => FALSE   
      , 'acceptedValue' => array ( 'min'=> '0' )         
      );   

?>
