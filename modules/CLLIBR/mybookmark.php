<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib'
                , 'utils/validator.lib'
                , 'display/layout.lib' );

From::Module( 'CLLIBR' )->uses( 'resource.lib'
                              , 'collection.lib'
                              , 'storedresource.lib'
                              , 'librarylist.lib'
                              , 'library.lib'
                              , 'metadata.lib'
                              , 'pluginloader.lib' );

load_module_language( 'CLLIBR' );

$userId = claro_get_current_user_id();

$myBookmark = new Collection( Claroline::getDatabase() , 'bookmark' , $userId );

$portlet = new ModuleTemplate( 'CLLIBR' , 'bookmark.tpl.php' );
$portlet->assign( 'in_portlet' , true );
$portlet->assign( 'resourceList' , $myBookmark->getResourceList() );
$portlet->assign( 'userId' , $userId );
$portlet->assign( 'icon' , get_module_icon_url( 'CLLIBR' ) );

echo claro_utf8_encode( $portlet->render() );