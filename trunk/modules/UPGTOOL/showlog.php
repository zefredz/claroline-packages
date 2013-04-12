<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
* Upgrade to 1.9 stable
*
* @version     1.8-backport $Revision$
* @copyright   2001-2007 Universite catholique de Louvain (UCL)
* @author      Frederic Minne <zefredz@claroline.net>
* @license     http://www.gnu.org/copyleft/gpl.html
*              GNU GENERAL PUBLIC LICENSE version 2 or later
* @package     icprint
*/

//Tool label
$tlabelReq = 'UPGTO19';

//Load claroline kernel
require_once dirname( __FILE__ ) . '/../../claroline/inc/claro_init_global.inc.php';

if ( ! claro_is_platform_admin() )
{
    claro_die( get_lang('Not allowed !') );
}

if ( isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'showCourseUpgradeLog'  )
{
    echo '<h1>Course upgrade log</h1>';
    
    if ( file_exists(get_path('rootSys') . 'platform/upgto19.course.log') )
    {
        echo '<pre>'.file_get_contents(get_path('rootSys') . 'platform/upgto19.course.log').'</pre>';
    }
    else
    {
        echo "Nothing to display !";
    }
}
else
{
    echo '<h1>Main upgrade log</h1>';
    
    if ( file_exists(get_path('rootSys') . 'platform/upgto19.main.log') )
    {
        echo '<pre>'.file_get_contents(get_path('rootSys') . 'platform/upgto19.main.log').'</pre>';
    }
    else
    {
        echo "Nothing to display !";
    }
}