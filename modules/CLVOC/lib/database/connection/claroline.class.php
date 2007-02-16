<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4: 
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Database
     */
     
    require_once dirname(__FILE__) . '/../connection.class.php';

    class Claroline_Database_Connection extends Database_Connection
    {
        function Claroline_Database_Connection()
        {
            // use only in claroline tools
            
            // always connected whithin Claroline
            $this->connected = true;
        }
        
        /**
         * @see DatabaseConnection::setError
         */
        function setError( $errmsg = '', $errno = 0 )
        {
            
            if ( $errmsg != '' || $errno != 0 )
            {
                $this->errmsg = $errmsg;
                $this->errno = $errno;
            }
            else
            {
                $this->errmsg = @claro_sql_error() != '' ? @claro_sql_error() : 'Unknown Error';
                $this->errno = @claro_sql_errno() != 0 ? @claro_sql_errno() : 0;
            }
        }
        
        /**
         * @see DatabaseConnection::connect
         */
        function connect( $forceReconnect = false )
        {
            if ( $forceReconnect )
            {
                // forceReconnect does not work for Claroline
                return false;
            }
            else
            {
                return true;
            }
        }
        
        /**
         * @see DatabaseConnection::close
         */
        function close()
        {

        }
        
        /**
         * @see DatabaseConnection::executeQuery
         */
        function executeQuery( $sql )
        {
            $res = claro_sql_query( $sql );
            
            if( @claro_sql_errno() != 0 )
            {
                $this->setError();

                return 0;
            }

            return @mysql_affected_rows( );
        }
        
        /**
         * @see DatabaseConnection::getAllObjectsFromQuery
         */
        function getAllObjectsFromQuery( $sql )
        {
            $result = claro_sql_query( $sql );

            if ( @mysql_num_rows( $result ) > 0 )
            {
                $ret= array();

                while( ( $item = @mysql_fetch_object( $result ) ) != false )
                {
                    $ret[] = $item;
                }
            }
            elseif  ( ! $result )
            {
                $this->setError();
                
                @mysql_free_result( $result );

                return null;
            }
            else
            {
                $ret = array();
            }

            @mysql_free_result( $result );

            return $ret;
        }
        
        /**
         * @see DatabaseConnection::getObjectFromQuery
         */
        function getObjectFromQuery( $sql )
        {
            $result = claro_sql_query( $sql );

            if ( ( $item = @mysql_fetch_object( $result ) ) != false )
            {
                @mysql_free_result( $result );

                return $item;
            }
            elseif  ( ! $result )
            {
                $this->setError();

                @mysql_free_result( $result );
                return null;
            }
            else
            {
                return null;
            }
        }
        
        /**
         * @see DatabaseConnection::getAllRowsFromQuery
         */
        function getAllRowsFromQuery( $sql )
        {
            $result = claro_sql_query( $sql );

            if ( @mysql_num_rows( $result ) > 0 )
            {
                $ret= array();

                while ( ( $item = @mysql_fetch_array( $result ) ) != false )
                {
                    $ret[] = $item;
                }
            }
            elseif  ( ! $result )
            {
                $this->setError();

                @mysql_free_result( $result );

                return null;
            }
            else
            {
                $ret = array();
            }

            @mysql_free_result( $result );

            return $ret;
        }
        
        /**
         * @see DatabaseConnection::getRowFromQuery
         */
        function getRowFromQuery( $sql )
        {
            $result = claro_sql_query( $sql );

            if ( ( $item = @mysql_fetch_array( $result ) ) != false )
            {
                @mysql_free_result( $result );

                return $item;
            }
            elseif ( !$result )
            {
                $this->setError();

                @mysql_free_result( $result );
                return null;
            }
            else
            {
                return null;
            }
        }

        /**
         * @see DatabaseConnection::queryReturnsResult
         */
        function queryReturnsResult( $sql )
        {
            $result = claro_sql_query( $sql );
            
            if ( @mysql_errno() == 0 )
            {

                if ( @mysql_num_rows( $result ) > 0 )
                {
                    @mysql_free_result( $result );

                    return true;
                }
                else
                {
                    @mysql_free_result( $result );

                    return false;
                }
            }
            else
            {
                $this->setError();

                return false;
            }
        }
        
        /**
         * @see DatabaseConnection::getLastInsertId
         */
        function getLastInsertId()
        {
            if ( $this->hasError() )
            {
                return 0;
            }
            else
            {
                return mysql_insert_id();
            }
        }
    }
?>
