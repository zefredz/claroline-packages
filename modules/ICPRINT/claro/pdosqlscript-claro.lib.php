<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Description
     *
     * @version     1.9 $Revision$
     * @copyright   2001-2007 Universite catholique de Louvain (UCL)
     * @author      Claroline Team <info@claroline.net>
     * @author      Frederic Minne <zefredz@claroline.net>
     * @license     http://www.gnu.org/copyleft/gpl.html
     *              GNU GENERAL PUBLIC LICENSE version 2 or later
     * @package     PACKAGE_NAME
     */

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    abstract class SQLScript
    {
        abstract public function execute( $script );
        
        public function parseMultipleQuery( $sql )
        {
            // make something usable...
            
            $ret = $this->pmaParse( $sql );
            
            $queries = array();
        
            foreach ( $ret as $item )
            {
                if ( ! $item['empty'] )
                {
                    $queries[] = $item['query'];
                }
            }
            
            return $queries;
        }
        
        protected function pmaParse( $sql )
        {
            $ret = array();
            
            $sql          = rtrim($sql, "\n\r");
            $sql_len      = strlen($sql);
            $char         = '';
            $string_start = '';
            $in_string    = false;
            $nothing      = true;
            
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
                            $in_string         = false;
                            break;
                        }
                        // one or more Backslashes before the presumed end of string...
                        else
                        {
                            // ... first checks for escaped backslashes
                            $j                     = 2;
                            $escaped_backslash     = false;
                            while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                                $escaped_backslash = !$escaped_backslash;
                                $j++;
                            }
                            // ... if escaped backslashes: it's really the end of the
                            // string -> exit the loop
                            if ($escaped_backslash)
                            {
                                $string_start  = '';
                                $in_string     = false;
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
                    if ($i === false)
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
                    $nothing    = true;
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
                    $in_string    = true;
                    $nothing      = false;
                    $string_start = $char;
                } // end else if (is start of string)
                elseif ($nothing)
                {
                    $nothing = false;
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
    
    class PDOSQLScript extends SQLScript
    {
        protected $_pdo;
        protected static $queryCounter = 1;
        
        public function __construct( $pdo )
        {
            $this->_pdo = $pdo;
        }
        
        public function execute( $sqlScript )
        {
            $queries = $this->parseMultipleQuery( $sqlScript );
            
            if ( !is_array( $queries ) || empty( $queries ) )
            {
                throw new Exception( 'Invalid SQL script' );
            }
            
            if ( $this->_pdo->getAttribute(PDO::ATTR_ERRMODE) != PDO::ERRMODE_EXCEPTION )
            {
                $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            foreach ( $queries as $query )
            {
                $query = toClaroQuery( $query );
                
                if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
                {
                    $start = microtime();
                }
                
                $this->_pdo->query( $query );
                
                if ( get_conf('CLARO_DEBUG_MODE',false) && get_conf('CLARO_PROFILE_SQL',false) )
                {
                    $duration = microtime()-$start;
                    $info = 'execution time : ' . ($duration > 0.001 ? '<b>' . round($duration,4) . '</b>':'&lt;0.001')  . '&#181;s'  ;
                    $info .= ': affected rows :' . claro_sql_affected_rows();
                    pushClaroMessage( '<br>Query counter : <b>pdo_' . self::$queryCounter++ . '</b> : ' . $info . '<br />'
                        . '<code><span class="sqlcode">' . nl2br($query) . '</span></code>'
                        , 'pdo');
                }
            }
        }
    }    
?>