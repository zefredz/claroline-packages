<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * OPML Generator module
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @author      Dimitri Rambout <dimitri.rambout@uclouvain.be>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE
 * @package     CLFRMRSS
 */

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

// add a link to current user opml file in Claroline html header
if ( claro_is_user_authenticated() && claro_is_allowed_to_edit() )
{
    $GLOBALS['htmlHeadXtra'][] = '<link rel="alternate"'
        . ' type="application/rss+xml"'
        . ' title="'.get_lang('Last messages in the forum of this course').'"'
        . ' href="'.get_module_url('CLFRMRSS')
        . '/index.php?cidReq='. claro_get_current_course_id() . '&cidReset=true" />'
        ;
    
    $forum_id = isset($_REQUEST['forum']) ? (int) $_REQUEST['forum'] : null;
    $topic_id = isset($_REQUEST['topic']) ? (int) $_REQUEST['topic'] : null;
    
    if( !is_null($topic_id) )
    {
        FromKernel::uses('forum.lib');
        
        $topic_settings = get_topic_settings( $topic_id );
        if( $topic_settings )
        {
            $forum_id = $topic_settings['forum_id'];
        }
    }
    
    if( $GLOBALS['tlabelReq'] == 'CLFRM' && !is_null($forum_id) )
    {
        $GLOBALS['htmlHeadXtra'][] = '<link rel="alternate"'
            . ' type="application/rss+xml"'
            . ' title="'.get_lang('Last messages in this forum').'"'
            . ' href="'. htmlspecialchars( URL::Contextualize( get_module_url('CLFRMRSS')
            . '/index.php?cidReq='. claro_get_current_course_id() . '&cidReset=true&forumId=' . (int) $forum_id ) ) .'" />'
            ;
    }
    
    if( $GLOBALS['tlabelReq'] == 'CLFRM' && !is_null($topic_id) )
    {
        $GLOBALS['htmlHeadXtra'][] = '<link rel="alternate"'
            . ' type="application/rss+xml"'
            . ' title="'.get_lang('Last messages in this topic').'"'
            . ' href="'. htmlspecialchars( URL::Contextualize( get_module_url('CLFRMRSS')
            . '/index.php?cidReq='. claro_get_current_course_id() . '&cidReset=true&forumId=' . (int) $forum_id .'&topicId=' . (int) $topic_id ) ) . '" />'
            ;
    }
}
