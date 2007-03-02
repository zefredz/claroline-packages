<?php // $Id$
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
 * CLAROLINE
 *
 * @copyright 2001-2006 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @see http://www.claroline.net/wiki/index.php/Config
 *
 * @author Claro Team <cvs@claroline.net>
 *
 * @package HTMLJS
 */
$conf_def['config_code'] = 'HTMLJS';
$conf_def['config_file'] = 'HTMLJS.conf.php';
$conf_def['config_name'] = 'HTML/JS';

$conf_def['section']['main']['label']      = 'Main';
$conf_def['section']['main']['properties'] =
array ( 'htmljs_text'
      );

// PROPERTIES

$conf_def_property_list['htmljs_text'] =
array ( 'label'       => 'HTML / JS '
      , 'description' => 'Can contain html and/or javascript'
      , 'default'     => ''
      , 'type'        => 'text'
      , 'display'     => TRUE
      , 'readonly'    => FALSE
      );

?>
