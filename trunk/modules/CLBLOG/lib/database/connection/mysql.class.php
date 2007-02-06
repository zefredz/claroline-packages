<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Database
     */
    
    require_once dirname(__FILE__) . "/../connection.class.php";

    /**
     * MySQL database connection class
     */
    class MySQL_Database_Connection extends Database_Connection
    {
        var $db_link;
        var $host;
        var $username;
        var $passwd;
        var $dbname;
    
        /**
         * Constructor
         * @param   string host
         * @param   string username
         * @param   string passwd
         * @param   string dbname
         */
        function MySQL_Database_Connection( $host, $username, $passwd, $dbname )
        {
            $this->db_link = null;
            $this->host = $host;
            $this->username = $username;
            $this->passwd = $passwd;
            $this->dbname = $dbname;
        }

        /**
         * Redefine ErrorHandling::setError();
         * @see ErrorHandling::setError()
         */
        function setError( $errmsg = '', $errno = 0 )
        {
            if ( $errmsg != '' )
            {
                $this->errmsg = $errmsg;
                $this->errno = $errno;
            }
            else
            {
                $this->errmsg = ( @mysql_error() !== false ) ? @mysql_error() : 'Unknown error';
                $this->errno = ( @mysql_errno() !== false ) ? @mysql_errno() : 0;
            }

            $this->connected = false;
        }

        function connect( $forceReconnect = false )
        {
            if ( $this->isConnected() && ! $forceReconnect )
            {
                return true;
            }
            
            $this->db_link = @mysql_connect( $this->host, $this->username, $this->passwd );

            if( ! $this->db_link )
            {
                $this->setError();

                return false;
            }

            if( @mysql_select_db( $this->dbname, $this->db_link ) )
            {
                $this->connected = true;
                return true;
            }
            else
            {
                $this->setError();

                return false;
            }
        }

        function close()
        {
            if( $this->db_link != false )
            {
                @mysql_close( $this->db_link );
            }
            else
            {
                $this->setError( "No connection found" );
            }
            $this->connected = false;
        }

        function executeQuery( $query )
        {
            mysql_query( $query, $this->db_link );

            if( @mysql_errno( $this->db_link ) != 0 )
            {
                $this->setError();

                return 0;
            }

            return @mysql_affected_rows( $this->db_link );
        }

        function getAllObjectsFromQuery( $query )
        {
            $result = mysql_query( $query, $this->db_link );

            $ret= array();

            if ( @mysql_num_rows( $result ) > 0 )
            {
                while( ( $item = @mysql_fetch_object( $result ) ) != false )
                {
                    $ret[] = $item;
                }
            }
            else
            {
                $this->setError();
            }

            @mysql_free_result( $result );

            return $ret;
        }

        function getObjectFromQuery( $query )
        {
            $result = mysql_query( $query, $this->db_link );

            if ( false != ( $item = @mysql_fetch_object( $result ) ) )
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

        function getAllRowsFromQuery( $query )
        {
            $result = mysql_query( $query, $this->db_link );

            $ret= array();

            if ( @mysql_num_rows( $result ) > 0 )
            {
                while ( ( $item = @mysql_fetch_array( $result ) ) != false )
                {
                    $ret[] = $item;
                }
            }
            else
            {
                $this->setError();
            }

            @mysql_free_result( $result );

            return $ret;
        }

        function getRowFromQuery( $query )
        {
            $result = mysql_query( $query, $this->db_link );

            if ( false != ( $item = @mysql_fetch_array( $result ) ) )
            {
                @mysql_free_result( $result );

                return $item;
            }
            elseif ( ! $result )
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

        function queryReturnsResult( $query )
        {
            $result = mysql_query( $query, $this->db_link );

            if ( 0 == @mysql_errno( $this->db_link ) )
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
            elseif ( ! $result )
            {
                $this->setError();

                return false;
            }
            else
            {
                return false;
            }
        }

        function getLastInsertId()
        {
            if ( $this->hasError() )
            {
                return 0;
            }
            else
            {
                return mysql_insert_id( $this->db_link );
            }
        }
    }
?>
