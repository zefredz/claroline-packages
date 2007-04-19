<?php // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package PlugIt
     */
     
    // used by claro_embed and ClarolineScriptEmbed class
    require_once dirname(__FILE__) . "/html/popup_helper.class.php";
    
    /**
     * Embed script output into Claroline layout
     * @param   string  $output output to embed
     * @param   bool    $hide_banner hide Claroline banner (opt)
     * @param   bool    $hide_footer hide Claroline banner (opt)
     * @param   bool    $hide_body hide Claroline banner (opt)
     * @return  void
     */
    function claro_embed( $output
        , $inPopup = false
        , $hide_banner = false
        , $hide_footer = false
        , $hide_body = false )
    {
        // global variables needed by header and footer...
        // FIXME make global objects with all these craps !!!
        global $includePath, $clarolineRepositoryWeb, $claro_stylesheet, $urlAppend ,
               $siteName, $text_dir, $_uid, $_cid, $administrator_name, $administrator_email;
        global $is_platformAdmin, $_course, $_user, $_courseToolList, $coursesRepositoryWeb,
               $is_courseAllowed, $imgRepositoryWeb, $_tid, $is_courseMember, $_gid;
        global $claroBodyOnload, $httpHeadXtra, $htmlHeadXtra, $charset, $interbredcrump,
               $noPHP_SELF, $noQUERY_STRING;
        global $institution_name, $institution_url;
               
        if ( true == $inPopup )
        {
            $output = HTML_Popup_Helper::popupEmbed( $output );
            $hide_banner = true;
            $hide_footer = true;
        }
        
        // embed script output here
        require $includePath . '/claro_init_header.inc.php';
        echo $output;
        require $includePath . '/claro_init_footer.inc.php' ;
    }
    
    /**
     * Claroline script embed class
     */
    class ClarolineScriptEmbed
    {
        var $inPopup = false;
        var $inFrame = true;
        var $hide_footer = false;
        var $hide_banner = false;
        var $hide_body = false;
        var $content = '';
        
        // claroline diplay options
        
        function hideBanner()
        {
            $this->hide_banner = true;
        }
        function hideFooter()
        {
            $this->hide_footer = true;
        }
        function hideBody()
        {
            $this->hide_body = true;
        }
        
        // display mode
        
        function popupMode()
        { 
            $this->hideBanner();
            $this->hideFooter();
            $this->inPopup = true;
        }
        function frameMode()
        {
            $this->hideBanner();
            $this->hideFooter();
            $this->inFrame = true;
        }
        function embedInPage()
        {
            $this->hideBanner();
            $this->hideFooter();
            $this->hideBody();
        }
        
        function setContent( $content )
        {
            $this->content = $content;
        }
        
        // claroline header methods
        
        function addHtmlHeader( $header )
        {
            $GLOBALS['htmlHeadXtra'][] = $header;
        }
        function addHttpHeader( $header )
        {
            $GLOBALS['httpHeadXtra'][] = $header;
        }
        function addBodyOnloadFunction( $function )
        {
            $GLOBALS['claroBodyOnload'][] = $function;
        }
        
        // output methods
        
        function output()
        {
            if ( $this->inPopup )
            {
                $this->content = HTML_Popup_Helper::popupEmbed( $this->content );
            }
            
            $this->embed( $this->content
                , $this->hide_banner
                , $this->hide_footer
                , $this->hide_body );
        }
        
        function embed( $output
            , $hide_banner = false
            , $hide_footer = false
            , $hide_body = false )
        {
            // global variables needed by header and footer...
            // FIXME make global objects with all these craps !!!
            global $includePath, $clarolineRepositoryWeb, $claro_stylesheet, $urlAppend ,
               $siteName, $text_dir, $_uid, $_cid, $administrator_name, $administrator_email;
            global $is_platformAdmin, $_course, $_user, $_courseToolList, $coursesRepositoryWeb,
                   $is_courseAllowed, $imgRepositoryWeb, $_tid, $is_courseMember, $_gid;
            global $claroBodyOnload, $httpHeadXtra, $htmlHeadXtra, $charset, $interbredcrump,
                   $noPHP_SELF, $noQUERY_STRING;
            global $institution_name, $institution_url;
            
            // embed script output here
            require $includePath . '/claro_init_header.inc.php';
            echo $this->content;
            require $includePath . '/claro_init_footer.inc.php' ;
        }
    }
    
    /**
     * Get list of table names 'localized' for the given course
     * @param array $arrTblName tableId => tableName
     * @param string $courseCode course code
     * @return arra $tblId => $dbNameGlue . $tblName
     */
    function claro_get_tbl_name_list_for_course( $arrTblName, $courseCode )
    {
        $currentCourseDbNameGlu = claro_get_course_db_name_glued( $courseCode );
        
        foreach ( $arrTblName as $key => $name )
        {
            $arrTblName[$key] = $currentCourseDbNameGlu . $name;
        }
        
        return $arrTblName;
    }
    
    /**
     * Get list of table names 'localized' for the main db
     * @param array $arrTblName tableId => tableName
     * @return arra $tblId => mainTblPrefix . $tblName
     */
    function claro_get_tbl_name_list_in_main_db( $arrTblName )
    {
        $mainDbNameGlu = get_conf('mainTblPrefix');
        
        foreach ( $arrTblName as $key => $name )
        {
            $arrTblName[$key] = $mainDbNameGlu . $name;
        }
        
        return $arrTblName;
    }
    
    /**
     * Claroline Installer
     *
     * Installer for various Claroline scripts/plugins/modules/whatever
     * Refactored version of the crap lying in sqlxtra.lib.php
     */
    class ClarolineInstaller
    {
        var $baseDir;
        
        function ClarolineInstaller( $baseDir )
        {
            $this->baseDir = $baseDir;
        }
        
        /**
         * Alias of parseAndExecuteSqlFile
         * @see parseAndExecuteSqlFile
         */
        function installDatabase( $courseCode = null )
        {
            if ( empty( $courseCode ) )
            {
                $file = $this->baseDir . '/setup/install.sql';
            }
            else
            {
                $file = $this->baseDir .'/setup/course_install.sql';
            }
            
            if ( file_exists( $file ) )
            {
                return ClarolineInstaller::parseAndExecuteSqlFile( $file, $courseCode );
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Upgrade database
         * @todo    TODO use module version
         */
        function updateDatabase( $toVersion = '', $courseCode = null )
        {
            $toVersion = empty( $toVersion ) ? '' : '_'.$toVersion;
            
            if ( empty( $courseCode ) )
            {
                $file = $this->baseDir . '/setup/update'.$toVersion.'.sql';
            }
            else
            {
                $file = $this->baseDir .'/setup/course'.$toVersion.'update.sql';
            }
            
            if ( file_exists( $file ) )
            {
                return ClarolineInstaller::parseAndExecuteSqlFile( $file, $courseCode );
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Alias of parseAndExecuteSqlFile
         * @see parseAndExecuteSqlFile
         */
        function uninstallDatabase( $courseCode = null )
        {
            if ( empty( $courseCode ) )
            {
                $file = $this->baseDir . '/setup/uninstall.sql';
            }
            else
            {
                $file = $this->baseDir .'/setup/course_uninstall.sql';
            }
            
            return ClarolineInstaller::parseAndExecuteSqlFile( $file, $courseCode );
        }
        
        /**
         * Parse and execute the given sql file
         * @param string $file path to the sql file
         * @param string $courseCode code of the course, null if not in course (default)
         * @return boolean succeeded
         */
        function parseAndExecuteSqlFile( $file, $courseCode = null )
        {
            if ( !file_exists( $file ) )
            {
                return claro_failure::set_failure('SQL_FILE_NOT_FOUND');
            }
            else
            {
                $sql = file_get_contents( $file );
                
                if ( !empty( $courseCode ) )
                {
                    $currentCourseDbNameGlu = claro_get_course_db_name_glued( $courseCode );
                    $sql = str_replace('__CL_COURSE__', $currentCourseDbNameGlu, $sql );
                }
                
                $sql = str_replace ('__CL_MAIN__',get_conf('mainTblPrefix'), $sql);
                
                return ClarolineInstaller::_multipleQuery( $sql );
            }
        }
        
        // ----------------------- private methods ---------------------------
        
        function _multipleQuery( $sqlStr )
        {
            $queryArray = ClarolineInstaller::_parseQuery( $sqlStr );
            if ( !empty( $queryArray) )
            {
                return ClarolineInstaller::_executeMultipleQuery( $queryArray );
            }
            else
            {
                return false;
            }
        }
        
        function _parseQuery( $sqlStr )
        {
            $queryArray = ClarolineInstaller::_pmaSplitSql( $sqlStr );
            return $queryArray;
        }
        
        function _executeMultipleQuery( $queryArray )
        {
            foreach ($queryArray as $theQuery)
            {
                if (!$theQuery['empty'])
                {
                    if ( false == claro_sql_query($theQuery['query']))
                    {
                        return false;
                    }
                }
            }
            
            return true;
        }
        
        // --------------------- Vendors Code --------------------------------
        
        /**
         * FUNCTION TAKEN FROM PHPMYADMIN TO ALLOW MULTIPLE SQL QUERIES AT ONCE
         * Removes comment lines and splits up large sql files into individual queries
         *
         * Last revision: September 23, 2001 - gandon
         *
         * @param   string  the sql commands
         *
         * @return  array   the splitted queries
         *
         * @access  public
         */
        function _pmaSplitSql( $sql )
        {
            $ret = array();
            // do not trim, see bug #1030644
            //$sql          = trim($sql);
            $sql          = rtrim($sql, "\n\r");
            $sql_len      = strlen($sql);
            $char         = '';
            $string_start = '';
            $in_string    = FALSE;
            $nothing      = TRUE;
            for ($i = 0; $i < $sql_len; ++$i)
            {
                $char = $sql[$i];
                // We are in a string, check for not escaped end of strings except for
                // backquotes that can't be escaped
                if ($in_string)
                {
                    for (;;)
                    {
                        $i         = strpos($sql, $string_start, $i);
                        // No end of string found -> add the current substring to the
                        // returned array
                        if (!$i)
                        {
                            $ret[] = array('query' => $sql, 'empty' => $nothing);
                            return $ret;
                        }
                        // Backquotes or no backslashes before quotes: it's indeed the
                        // end of the string -> exit the loop
                        else if ($string_start == '`' || $sql[$i-1] != '\\')
                        {
                            $string_start      = '';
                            $in_string         = FALSE;
                            break;
                        }
                        // one or more Backslashes before the presumed end of string...
                        else
                        {
                            // ... first checks for escaped backslashes
                            $j                     = 2;
                            $escaped_backslash     = FALSE;
                            while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                                $escaped_backslash = !$escaped_backslash;
                                $j++;
                            }
                            // ... if escaped backslashes: it's really the end of the
                            // string -> exit the loop
                            if ($escaped_backslash)
                            {
                                $string_start  = '';
                                $in_string     = FALSE;
                                break;
                            }
                            // ... else loop
                            else
                            {
                                $i++;
                            }
                        } // end if...elseif...else
                    } // end for
                } // end if (in string)
                
                // lets skip comments (/*, -- and #)
                else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') 
                    || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*'))
                {
                    $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
                    // didn't we hit end of string?
                    if ($i === FALSE)
                    {
                        break;
                    }
                    if ($char == '/') $i++;
                }
                
                // We are not in a string, first check for delimiter...
                else if ($char == ';')
                {
                    // if delimiter found, add the parsed part to the returned array
                    $ret[]      = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
                    $nothing    = TRUE;
                    $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
                    $sql_len    = strlen($sql);
                    if ($sql_len)
                    {
                        $i      = -1;
                    }
                    else
                    {
                        // The submited statement(s) end(s) here
                        return $ret;
                    }
                } // end else if (is delimiter)
        
                // ... then check for start of a string,...
                else if (($char == '"') || ($char == '\'') || ($char == '`'))
                {
                    $in_string    = TRUE;
                    $nothing      = FALSE;
                    $string_start = $char;
                } // end else if (is start of string)
                elseif ($nothing)
                {
                    $nothing = FALSE;
                }
            } // end for
        
            // add any rest to the returned array
            if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql))
            {
                $ret[] = array('query' => $sql, 'empty' => $nothing);
            }
        
            return $ret;
        }
    }
?>
