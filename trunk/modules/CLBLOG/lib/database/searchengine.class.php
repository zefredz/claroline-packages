<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Database
     */
    
    define( "SEARCH_MODE_CONTAINS"     , __LINE__ );
    define( "SEARCH_MODE_START_WITH"   , __LINE__ );
    define( "SEARCH_MODE_END_WITH"     , __LINE__ );
    define( "SEARCH_MODE_EXACT_MATCH"  , __LINE__ );
    define( "SEARCH_FOR_ANY"           , __LINE__ );
    define( "SEARCH_FOR_ALL"           , __LINE__ );
    define( "SEARCH_FOR_EXPRESSION"    , __LINE__ );
    
    class Database_Search_Engine
    {
        var $connection;
        var $searchFieldArray;
        
        function Database_Search_Engine( &$connection, $searchFieldArray, $e )
        {
            $this->connection =& $connection;
            $this->searchFieldArray = $searchFieldArray;
        }
        
        function search( $needle
            , $searchMode = SEARCH_MODE_CONTAINS
            , $keywordMode = SEARCH_FOR_ANY
            )
        {
            $smode = ( $keywordMode == SEARCH_FOR_ANY )
                ? ' OR '
                : ' AND '
                ;
                
            $keywords = ( $keywordMode == SEARCH_FOR_EXPRESSION )
                ? $needle
                : preg_split( '~(,| |;|+)~', $needle )
                ;
                
            $searchOperator = ' LIKE ';
                
            switch( $searchMode )
            {
                case SEARCH_MODE_START_WITH:
                {
                    $searchPattern = "'%E'";
                    break;
                }
                case SEARCH_MODE_END_WITH:
                {
                    $searchPattern = "'E%'";
                    break;
                }
                case SEARCH_MODE_EXACT_MATCH:
                {
                    $searchPattern = "'E'";
                    $searchOperator = ' = ';
                    break;
                }
                default:
                {
                    $searchPattern = "'%E%'";
                    break;
                }
            }
            
            // create SQL query;
            $tableList = array();
            
            foreach ( $this->searchFieldArray as $field )
            {
                $table = explode( '.', $field );
                $table = $table[0];
                
                $tableList[] = $table;
            }
            
            $sql = "SELECT " . implode( ',', $this->searchFieldArray ) . " "
                . "FROM " . implode( ',', $tableList ) . " "
                ;
                
            $where = "WHERE ";
            
            if ( $keywordMode == SEARCH_FOR_EXPRESSION )
            {
                $searchString = str_replace( 'E', $searchPattern, $needle );
                $searchString = $searchOperator . $searchString;
                $searchString  = implode( $searchString . ' OR ', $this->searchFieldArray )
                    . $searchString
                    ;
                    
                $where .= $searchString;
            }
            else
            {
                $searchArray = array();
                
                foreach ( $this->searchFieldArray as $field )
                {
                    $searchField = $field . $searchOperator;
                    $search = array();
                    
                    foreach ( $keywords as $keyword )
                    {
                        $search[] = $searchField 
                            . str_replace( 'E', $searchPattern, $needle )
                            ;
                    }
                    
                    $searchArray[] = '(' . implode( $searchOperator, $search ) . ')';
                }
                
                $where .= implode( ' OR ', $searchArray );
            }
            
            $sql .= $where;
            
            // return $this->connection->getAllRowsFromQuery( $sql );
            return $sql;
        }
    }
?>
