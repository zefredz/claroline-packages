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

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
 * Forum RSS Generator Class
 */
class CLFRMRss
{
    public function generate( $data )
    {
        
        $_course = claro_get_course_data();
        $rssTitle = $_course['name'] . ' (' . $_course['officialCode'] . ')';
        $rssEmail = $_course['email'] == '' ? get_conf('administrator_email') : $_course['email'];
           
        $rss =  '<?xml version="1.0" encoding="utf-8" standalone="yes" ?'.'>'."\n"
        .       '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n"
        .       '<channel>' . "\n"
        .       '<title>' . trim(strip_tags($rssTitle)) . '</title>' . "\n"
        .       '<description>' . get_lang('List of RSS from all my forums') . '</description>' . "\n"
        .       '<link></link>' . "\n"
        .       '<generator>Claroline-PEARSerializer</generator>' . "\n"
        .       '<webmaster>' . get_conf('administrator_email') . '</webmaster>' . "\n"
        .       '<managingEditor>' . $rssEmail . '</managingEditor>' . "\n"
        .       '<language>' . get_locale('iso639_1_code') . '</language>' . "\n"
        .       '<docs>' . 'http://blogs.law.harvard.edu/tech/rss' . '</docs>' . "\n"
        .       '<pubDate>' . date("r", time()) . '</pubDate>' . "\n";
        
        foreach( $data as $item )
        {
            $post_time = datetime_to_timestamp($item['post_time']);
            
            $rss .= '<item>' . "\n"
            .       '<title>' . trim(strip_tags($item['forum_name'])) . ' - ' . trim(strip_tags($item['topic_title'])) . ' - ' . htmlspecialchars( get_lang('%firstname %lastname', array('%firstname' => $item['prenom'], '%lastname' => $item['nom']) ) ) . '</title>' . "\n"
            .       '<category>' . trim(strip_tags($item['forum_name'])) . '</category>' . "\n"
            .       '<guid>' . get_conf('rootWeb') . 'claroline/phpbb/viewtopic.php?topic=' . $item['post_id'] .'&amp;cidReset=true&amp;cidReq=' . claro_get_current_course_id() . '</guid>' . "\n"
            .       '<link>' . get_conf('rootWeb') . 'claroline/phpbb/viewtopic.php?topic=' . $item['post_id'] .'&amp;cidReset=true&amp;cidReq=' . claro_get_current_course_id() . '</link>' . "\n"
            .       '<author>' . htmlspecialchars( get_lang('%firstname %lastname', array('%firstname' => $item['prenom'], '%lastname' => $item['nom']) ) ) .'</author>'
            .       '<description>' . trim(strip_tags( $item['post_text'] )) . '</description>' . "\n"
            .       '<pubDate>' . date("r", $post_time) . '</pubDate>' . "\n"
            .       '</item>' . "\n"
            ;
        }
        
        $rss .= '</channel>' . "\n"
        .       '</rss>' . "\n";   
        
        
        return $rss;
    }
}

?>