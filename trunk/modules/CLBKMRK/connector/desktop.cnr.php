<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
* CLAROLINE
*
* User desktop : Bookmarks
* FIXME : move to annoucements module
*
* @version      1.9 $Revision$
* @copyright    (c) 2001-2008 Universite catholique de Louvain (UCL)
* @license      http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
* @package      CLBKMRK
* @author       Claroline team <info@claroline.net>
*
*/

// require_once dirname(__FILE__) . '/lib/bookmarks.lib.php';

From::module('CLBKMRK')->uses('bookmarks.lib');

JavascriptLoader::getInstance()->load('jquery');
JavascriptLoader::getInstance()->load('jquery.form');
// JavascriptLoader::getInstance()->load('jquery.livequery');

class CLBKMRK_Portlet extends UserDesktopPortlet
{
    public function __construct()
    {
        if (file_exists(claro_get_conf_repository() . 'CLBKMRK.conf.php'))
        {
            include claro_get_conf_repository() . 'CLBKMRK.conf.php';
        }
    }

    public function renderContent()
    {
        $output = '<div id="bookmarkList"><img src="'.get_icon_url('loading','CLBKMRK').'" alt="" /></div>';

        $output .= "<script type=\"text/javascript\">
$(document).ready( function(){
    $('#bookmarkList').load('"
        .get_module_entry_url('CLBKMRK')."?cmd=list&amp;userId="
        .(int)claro_get_current_user_id()."');
});
</script>";

        return $output;
    }

    public function renderTitle()
    {
        return get_lang('My bookmarks');
    }
}
