<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * OPML generator : create OPML file for a user containing all RSS from
     *  his course. User is identified by user id, username or official code
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLOPML
     */

    require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

    require_once dirname(__FILE__) . '/lib/clfrmrss.lib.php';
    
    // need to be in a course
    if( ! claro_is_in_a_course() )
    {
        echo '<form >cidReq = <input name="cidReq" type="text" /><input type="submit" /></form>';
        exit;
    }
    else
    {
        if ( $_course['visibility'] && !claro_is_course_allowed() )
        {
            if (!isset($_SERVER['PHP_AUTH_USER']))
            {
                header('WWW-Authenticate: Basic realm="'. get_lang('Rss feed for %course', array('%course' => $_course['name']) ) . '"');
                header('HTTP/1.0 401 Unauthorized');
                echo '<h2>' . get_lang('You need to be authenticated with your %sitename account', array('%sitename'=>$siteName) ) . '</h2>'
                .    '<a href="index.php?cidReq=' . claro_get_current_course_id() . '">' . get_lang('Retry') . '</a>'
                ;
                exit;
            }
            else
            {
                if ( get_magic_quotes_gpc() ) // claro_unquote_gpc don't wash
                {
                    $_REQUEST['login']    = stripslashes($_SERVER['PHP_AUTH_USER']);
                    $_REQUEST['password'] = stripslashes($_SERVER['PHP_AUTH_PW']);
                }
                else
                {
                    $_REQUEST['login']    = $_SERVER['PHP_AUTH_USER'];
                    $_REQUEST['password'] = $_SERVER['PHP_AUTH_PW'] ;
                }
                require get_path('incRepositorySys') . '/claro_init_local.inc.php';
                if ($_course['visibility'] && !claro_is_course_allowed())
                {
                    header('WWW-Authenticate: Basic realm="'. get_lang('Rss feed for %course', array('%course' => $_course['name']) ) .'"');
                    header('HTTP/1.0 401 Unauthorized');
                    echo '<h2>' . get_lang('You need to be authenticated with your %sitename account', array('%sitename'=>$siteName) ) . '</h2>'
                    .    '<a href="index.php?cidReq=' . claro_get_current_course_id() . '">' . get_lang('Retry') . '</a>'
                    ;
                    exit;
                }
            }
        }
    }
    
    if ( ( claro_is_platform_admin() || claro_is_allowed_to_edit() ) && claro_is_in_a_course() )
    {
        $rss = generate_forum_rss( claro_get_current_course_id() );
        
        if( $rss )
        {
            header("Content-Type: application/rss+xml");
            echo $rss;
        }
        else
        {
            claro_die( get_lang('No RSS available'));
        }
    }
    else
    {
        claro_die( get_lang('Not allowed'));
    }
    
?>