<?php
    // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:

    if (count(get_included_files() ) == 1 )
    {
        die('The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    require_once dirname(__FILE__) . '/opml.lib.php';
    
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

        if (is_array($courseSysCodeList) )
        {
            $opmlData = array();
            
            $opmlData['outlines'] = array();

            foreach($courseSysCodeList as $thisCourseSys)
            {
                $opmlData['outlines'][] = array(
                    'xmlUrl' => get_path('rootWeb') . 'claroline/rss/?cidReq=' . $thisCourseSys
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