<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.4 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

FromKernel::uses( 'utils/input.lib' , 'utils/validator.lib' , 'display/layout.lib' );
From::Module( 'CLLIBR' )->uses( 'resource.lib' , 'resourceset.lib', 'storedresource.lib' , 'bookmark.lib'
                              , 'librarylist.lib', 'library.lib', 'pluginloader.lib', 'tools.lib' );

load_module_language( 'CLLIBR' );

$userId = claro_get_current_user_id();

$myBookmark = new Bookmark( $userId );

$portlet = new PhpTemplate( dirname( __FILE__ ) . '/templates/bookmark.tpl.php' );
$portlet->assign( 'resourceList' , $myBookmark->getResourceList() );
$portlet->assign( 'userId' , $userId );
$portlet->assign( 'icon' , get_path( 'rootWeb' ) . 'module/CLLIBR/icon.png' );

echo $portlet->render();