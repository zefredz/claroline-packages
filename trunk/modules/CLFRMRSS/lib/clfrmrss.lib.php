<?php

/**
* Forum RSS Generator function : generate forum rss from user course
*
* @version     1.9 $Revision$
* @copyright   2001-2009 Universite catholique de Louvain (UCL)
* @author      Dimitri Rambout <dimitri.rambout@uclouvain.be>
* @license     http://www.gnu.org/copyleft/gpl.html
*              GNU GENERAL PUBLIC LICENSE
* @package     CLFRMRSS
*/

if (count(get_included_files() ) == 1 )
{
    die('The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

function generate_forum_rss( $courseId )
{
    include_once get_path('incRepositorySys') . '/lib/forum.lib.php';
    require_once dirname(__FILE__) . '/rss.lib.php';
    
    $userId = claro_get_current_user_id();
    
    $tblCourse = claro_sql_get_course_tbl();
    
    $forum_id = isset($_REQUEST['forumId']) ? (int) $_REQUEST['forumId'] : null;
    $topic_id = isset($_REQUEST['topicId']) ? (int) $_REQUEST['topicId'] : null;
    
    
    $sql = "SELECT p.`post_id`, p.`post_time`, t.`topic_title`, pt.`post_text`, f.`forum_name`, p.`nom`, p.`prenom`
            FROM `" . $tblCourse['bb_posts'] . "` p
            JOIN `" . $tblCourse['bb_topics'] . "` t ON p.`topic_id` = t.`topic_id`
            JOIN `" . $tblCourse['bb_posts_text'] . "` pt ON p.`post_id` = pt.`post_id`
            JOIN `" . $tblCourse['bb_forums'] . "` f ON p.`forum_id` = f.`forum_id` ";
    $sql_cond = "";
    if( $forum_id )
    {
        if( $sql_cond )
        {
            $sql_cond .= " AND ";
        }
        $sql_cond .= " f.`forum_id` = '" . (int) $forum_id . "' ";
    }
    
    if( $topic_id )
    {
        if( $sql_cond )
        {
            $sql_cond .= " AND ";
        }
        $sql_cond .= " t.`topic_id` = '" . $topic_id . "' ";
    }
    
    if( $sql_cond )
    {
        $sql .= " WHERE " . $sql_cond;
    }
    
    $sql .= " ORDER BY p.`post_time` DESC
            LIMIT " . get_conf('clfrmrss_max_items', 10);
            
    $data = claro_sql_query_fetch_all_rows( $sql );
    
        
    $rss = new CLFRMRss;
    
    $xmlAnswer = $rss->generate( $data );
    
    return $xmlAnswer;
        
    
    
}

?>