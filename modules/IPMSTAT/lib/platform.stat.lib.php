<?php // $Id$
/**
 * CLAROLINE
 *
 * @version 1.0 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSTAT
 *
 * @author Christophe Gesché <moosh@claroline.net>
 *
 */


function claro_get_course_quantity()
{
    $tbl = claro_sql_get_tbl2( array('cours'));

    $sql = "SELECT count(`cours_id`) crsqty FROM `" . $tbl ['cours'] . "` ";

    return claro_sql_query_get_single_value($sql);
}


function claro_get_course_not_scanned_count($passe)
{
    $tbl = claro_sql_get_tbl2(array('stat_courses','cours'));
    $sql = "
    SELECT count(c.`cours_id`) crsleft
           FROM `" . $tbl ['cours'] . "` c
           LEFT JOIN `" . $tbl ['stat_courses'] . "` sc
           ON c.code = sc.course_code
           WHERE sc.pass  != '" . $passe . "' or sc.pass IS NULL
           ";

    return claro_sql_query_get_single_value($sql);
}


function claro_get_course_not_scanned($passe)
{
    $tbl = claro_sql_get_tbl2(array('stat_courses','cours'));
    $sql = "
SELECT c.`cours_id`,
       c.`code` `code`,
       c.`languageCourse` ,
       c.visible ,
  c.`intitule` ,
  c.`faculte` ,
  c.`titulaires` ,
  c.`fake_code`,
  c.`creationDate` ,
  c.dbName,
  c.directory path,
  sc.id stat_id
       FROM `" . $tbl ['cours'] . "` c
       LEFT JOIN `" . $tbl ['stat_courses'] . "` sc
       ON c.code = sc.course_code
       WHERE sc.pass  != '" . $passe . "' or sc.pass IS NULL
       ORDER BY  code
       LIMIT ". (int) get_conf('courseByStep',20);

    return claro_sql_query_fetch_all($sql);
}




class toolStat
{
    function get_current_scan_id()
    {
        return date('mi');
    }

    function write_stat($code_cours, $resultArray, $passe)
    {
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));

        foreach ($resultArray as $resName => $resValue)
        {
            $sqlAddRec[] = " (
                          '" . $code_cours . "',
                          '" . addslashes($resName) . "',
                          '" . addslashes($resValue) . "',
                          '" . $passe . "')";

        }

        $sqlAdd = "INSERT INTO `" . $tbl ['stat_data_matrix'] . "`
                    (`course_code` , `valueName` , `content` , `pass`)
                   VALUES ". implode(',',$sqlAddRec);

        return $this->remove_stat_course($code_cours)
        &&     claro_sql_query($sqlAdd)
        &&     claro_sql_query("
INSERT INTO `" . $tbl ['stat_courses'] . "` SET
  `id` =  '" . $resultArray['stat_id'] . "',
  `course_code` = '" . $code_cours . "',
  `content` = '" . addslashes(var_export($resultArray,1)) . "',
  `pass` = '" . $passe . "'");
    }

    function remove_stat_course($code_cours, $passe=null)
    {
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));

        $sqlWashMatrix = "DELETE
                          FROM  `" . $tbl ['stat_data_matrix'] . "`
                          WHERE `course_code` = '" . addslashes($code_cours) . "'";
        $sqlWashScan = "DELETE
        FROM `" . $tbl ['stat_courses'] . "`
        WHERE `course_code` = '" . $code_cours . "'";

        if (!is_null($passe))
        {
            $sqlWashMatrix .="
                      AND `pass` = '" . addslashes($passe) . "'";
            $sqlWashScan .= "
                      AND `pass` = '" . addslashes($passe) . "'";
        }
        return  claro_sql_query($sqlWashMatrix)
        &&      claro_sql_query($sqlWashScan);
    }


    function remove_stat_scan_session($passe)
    {

        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));
        $sqlWashMatrix = "DELETE
                          FROM  `" . $tbl ['stat_data_matrix'] . "`
                          WHERE `pass` = '" . addslashes($passe) . "'";
        $sqlWashScan = "DELETE
                        FROM `" . $tbl ['stat_courses'] . "`
                        WHERE `pass` = '" . addslashes($passe) . "'";

        return  claro_sql_query($sqlWashMatrix)
        &&      claro_sql_query($sqlWashScan);
    }

    function read_stat_column_names()
    {
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));

        $sql = "SELECT DISTINCT valueName  FROM  `" . $tbl ['stat_data_matrix'] . "`";

        $colList = claro_sql_query_fetch_all($sql);
        foreach($colList as $thisCol => $col)
        {
            $colList[$thisCol] = $col['valueName'];
        }
        sort($colList);
        return $colList;
    }

    function read_stat_row($course_id=null)
    {
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));
        $sql = "SELECT course_code, valueName, content
                FROM  `" . $tbl ['stat_data_matrix'] . "` "
        .      (is_null($course_id) ? " ORDER BY course_code, valueName"
        : " WHERE course_code LIKE '". addslashes($course_id) . "'"
        . " ORDER BY valueName"
        );

        $cellMatrix = claro_sql_query_fetch_all($sql);
        $matrix=array();
        foreach ($cellMatrix as $cell)
        $matrix[$cell['course_code']][$cell['valueName']] = $cell['content'];
        //    return $matrix;

        $colList = toolStat::read_stat_column_names();
        foreach ($matrix as $course => $stats)
        foreach ($colList as $col)
        if (array_key_exists($col,$matrix[$course]))
        $matrix2[$course][$col] = $matrix[$course][$col];
        else
        $matrix2[$course][$col] = '';
        return $matrix2;


    }

    function read_stat_digest()
    {
        $tbl = claro_sql_get_tbl2( array('stat_courses','stat_data_matrix'));
        $sql = "SELECT valueName, content, count(course_code) qty
                FROM  `" . $tbl ['stat_data_matrix'] . "` "
        . "where valueName like '%QTY' "
        . " GROUP BY valueName, content"
        ;

        $cellMatrix = claro_sql_query_fetch_all($sql);
        $matrix=array();
        foreach ($cellMatrix as $cell)
        $matrix[$cell['content']][$cell['valueName']] = $cell['qty'];
        //  return $matrix;

        $colList = toolStat::read_stat_column_names();
        foreach ($matrix as $content => $stats)
        {
            $matrix2[$content]['content'] = $content;

            foreach ($colList as $col)
            {
                if (array_key_exists($col,$matrix[$content]))
                {
                    $matrix2[$content][$col] = $matrix[$content][$col];
                }
                else
                {
                    $matrix2[$content][$col] = '';
                }
            }
        }
        return $matrix2;


    }

}


function dircount($dirToScan)
{
    $count =0;
    static $countprotect=0;
    $countprotect++;
    if ($countprotect>300) die('boucle infinie ?');
    if (is_dir($dirToScan))
    {
        if (false!== ($dh = opendir($dirToScan)))
        {
            while (($entry = readdir($dh)) !== false)
            {
                if($entry =='.' or $entry =='..') continue;
                if (is_dir($entry))
                {
                    $count += dircount(realpath($dirToScan . "/" . $entry));
                }
                else
                {
                    $count++;
                }
            }
            closedir($dh);
        }
    }
    else
    {
        return 1;
    }
    return $count;
}


function claro_sql_get_tbl2( $tableList='%TBLPRIM', $contextData=null)
{
    /**
     * If it's in a course, $courseId is set or $courseId is null but not claro_get_current_course_id()
     * if both are null, it's a main table
     *
     * when
     */

    if( ! is_array($tableList))
    {
        $tableListArr[] = $tableList;
        $tableList = $tableListArr;
    }
    else $tableList = $tableList;

    /**
     * Tool Context capatibility
     *
     * There is many context in claroline,
     * a new tool can don't provide initially
     * all field to discrim each context in fields.
     * When a tool can't discrim a context,
     * the table would be duplicated for each instance
     * and the name of table (or db) contain the discriminator
     *
     * This extreme modularity provide an easy growing
     * and integration but
     * easy
     *
     * Easy can't mean slowly.
     * If  I prupose a blog tool wich can't discrim user
     * I need to duplicate all blog table (in same or separate db).
     */

    if (!is_array($contextData)) $contextData = array();

    if ( array_key_exists(CLARO_CONTEXT_TOOLLABEL,$contextData) )
    {
        $toolId = $contextData[CLARO_CONTEXT_TOOLLABEL];
    }
    elseif ( claro_is_in_a_tool() )
    {
        $toolId = rtrim(claro_get_current_course_tool_data('label'),'_');
    }
    else
    {
        $toolId = null;
    }
    $contextDependance = get_context_db_discriminator($toolId);
    // Now place discriminator in db & table name.
    // if a context is needed ($contextData) and $contextDependance is found,
    // add the discriminator in schema name or table prefix

    $schemaPrefix = array();

    if (is_array($contextDependance) )
    {

        if (array_key_exists('schema',$contextDependance))
        {
            if (array_key_exists(CLARO_CONTEXT_COURSE,$contextData)
            && !is_null($contextData[CLARO_CONTEXT_COURSE])
            && in_array(CLARO_CONTEXT_COURSE, $contextDependance['schema']))
            {
                $schemaPrefix[] = get_conf('courseTablePrefix') . claro_get_course_db_name($contextData[CLARO_CONTEXT_COURSE]);
            }
        }

        $tablePrefix = '';

        if (array_key_exists('table',$contextDependance))
        {
            if (array_key_exists(CLARO_CONTEXT_COURSE,$contextData)
            && !is_null($contextData[CLARO_CONTEXT_COURSE])
            && in_array(CLARO_CONTEXT_COURSE, $contextDependance['table']))
            {
                $tablePrefix .= 'C_' . $contextData[CLARO_CONTEXT_COURSE] . '_';
            }
        }
    }

    //$schemaPrefix = (0==count($schemaPrefix) ? get_conf('mainDbName') : implode(get_conf('dbGlu'),$schemaPrefix)); // ne pas utiliser dbGlu tant qu'il peut valoir .
    $schemaPrefix = (0 == count($schemaPrefix) ? get_conf('mainDbName') : implode('_',$schemaPrefix));

    if(!array_key_exists(CLARO_CONTEXT_COURSE,$contextData))
    $tablePrefix  = ('' == $tablePrefix) ? get_conf('mainTblPrefix') : $tablePrefix;

    foreach ($tableList as $tableId)
    {
        /**
         *  Read this  to understand chanche  since  previous version thant 1.8
         *
         * Until 1.8  there was 2 functions
         *
         * function claro_sql_get_main_tbl()
         * function claro_sql_get_course_tbl($dbNameGlued = null)
         *
         * both was using  conf values
         * claro_sql_get_main_tbl was using  conf values
         * * get_conf('mainDbName')
         * * get_conf('mainTblPrefix')
         *
         */
        $tableNameList[$tableId] = $schemaPrefix . '`.`' . $tablePrefix . $tableId;
    }

    return $tableNameList;
}


?>