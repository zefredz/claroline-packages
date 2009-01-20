<?php
    // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * OPML Generator function : generate OPML from user course RSS
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Christophe Gesché <moosh@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE
     * @package     CLOPML
     */

    if (count(get_included_files() ) == 1 )
    {
        die('The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    require_once dirname(__FILE__) . '/opml.lib.php';
    
    /**
     * Generate opml file for a user given its user id
     * @param   int userId id of the user in the database
     * @return  string opml file contents
     *          boolean false if no opml generated
     */
    function generate_opml( $userId )
    {
        $tblList = claro_sql_get_main_tbl();
        
        $sql = "SELECT code_cours AS code
            FROM   `".$tblList['rel_course_user']."`
            WHERE  user_id = " . (int) $userId;

        $courseSysCodeList = claro_sql_query_fetch_all_cols($sql);
        
        if ( false === $courseSysCodeList
            || ! array_key_exists( 'code', $courseSysCodeList ) )
        {
            return false;
        }
        
        $courseSysCodeList = $courseSysCodeList['code'];

        // generate opml file content
        if (is_array($courseSysCodeList) )
        {
            $opmlData = array();
            
            $opmlData['outlines'] = array();

            // generate one entry for each course the user is enrolled in
            foreach($courseSysCodeList as $thisCourseSys)
            {
                $opmlData['outlines'][] = array(
                    'xmlUrl' => get_path('rootWeb') . 'claroline/backends/rss.php?cidReq=' . $thisCourseSys
                );
            }
            
            $opml = new Opml;

            $xmlAnswer = $opml->generate( $opmlData );

            return $xmlAnswer;
        }
        else
        {
            return false;
        }
    }
?>