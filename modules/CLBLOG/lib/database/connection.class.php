<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Database
     */

    require_once dirname(__FILE__) . '/../error/handling.class.php';
    
    /**
     * Abstract database connection class
     */
    class Database_Connection extends Error_Handling
    {
        var $connected = false;

        /**
         * open a connection to the database
         * @param   boolean forceReconnect
         * @return  boolean
         */
        function connect( $forceReconnect = false )
        {
            return false;
        }
        
        /**
         * reconnect a connection to the database
         * @return  boolean
         */
        function reconnect()
        {
            return $this->connect( true );
        }

        /**
         * check if the connection is active
         * @return boolean true if the conection is active, false either
         */
        function isConnected()
        {
            return $this->connected;
        }

        /**
         * close the connection
         */
        function close()
        {
        }

        /**
         * execute a query to the database and returns the number of
         * rows affected
         * @param string query database query
         * @return int number of affected rows
         */
        function executeQuery( $query )
        {
            return 0;
        }

        /**
         * execute the given query and returns all results as objects in an array
         * @param string query
         * @return array of objects
         */
        function getAllObjectsFromQuery( $query )
        {
            return array();
        }

        /**
         * execute the given query and returns one result as an object
         * @param string query
         * @return object
         */
        function getObjectFromQuery( $query )
        {
            return null;
        }

        /**
         * execute the given query and returns all returned rows in an array
         * @param string query
         * @return array of rows
         */
        function getAllRowsFromQuery( $query )
        {
            return array();
        }

        /**
         * execute the given query and returns one row in an array
         * @param string query
         * @return array row
         */
        function getRowFromQuery( $query )
        {
            return array();
        }
        
        /**
         * execute a database query and return true if the query returns a
         * result
         * @param string query
         * @return boolean true if the query returns a result, false either
         */
        function queryReturnsResult( $query )
        {
            return false;
        }

        /**
         * get the ID of the last inserted row in the database
         * return int id of the last inserted row
         */
        function getLastInsertId()
        {
            return 0;
        }
        
        // ----- extended connection -----
        
        function getColumnFromQuery( $sql )
        {
            $res = $this->getAllRowsFromQuery( $sql );
            
            if ( $this->hasError() )
            {
                return false;
            }
            else
            {
                $tmp = array();
                foreach ( $res as $row )
                {
                    $tmp[] = $row[0];
                }
                return $tmp;
            }
        }
        
        function getSingleValueFromQuery( $sql )
        {
            $row = $this->getRowFromQuery( $sql );
            
            if ( $this->hasError() )
            {
                return false;
            }
            else
            {
                if ( is_array( $row ) && !empty( $row ) )
                {
                    return $row[0];
                }
                else
                {
                    return false;
                }
            }
        }
        
        // Prepared query
        
        function prepareQuery( $query, $array )
        {
            if ( is_array( $array ) && count( $array ) > 0 )
            {   
                foreach ( $array as $key => $value )
                {
                    $query = str_replace ( ':' . $key, $value, $query );
                }
                
                return $query;
            }
            return false;
        }
        
        function executePreparedQuery( $query, $array )
        {
            if ( false !== 
                ( $sql = $this->prepareQuery( $query, $array ) ) )
            {
                    return $this->executeQuery( $sql );
            }
            else
            {
                return 0;
            }
        }
        
        function getAllRowsFromPreparedQuery( $query, $array )
        {
            $ret = array();
            
            if ( false !== 
                ( $sql = $this->prepareQuery( $query, $array ) ) )
            {
                $ret[] = $this->getAllRowsFromQueryQuery( $sql );
            }
            
            return $ret;
        }
        
        function getRowFromPreparedQuery( $query, $array )
        {
            $ret = array();
            
            if ( false !== 
                ( $sql = $this->prepareQuery( $query, $array ) ) )
            {
                    $ret[] = $this->getRowFromQueryQuery( $sql );
            }
            
            return $ret;
        }
        
        function getAllObjectsFromPreparedQuery( $query, $array )
        {
            $ret = array();
            
            if ( false !== 
                ( $sql = $this->prepareQuery( $query, $array ) ) )
            {
                $ret[] = $this->getAllObjectsFromQueryQuery( $sql );
            }
            
            return $ret;
        }
        
        function getObjectFromPreparedQuery( $query, $array )
        {
            $ret = array();
            
            if ( false !== 
                ( $sql = $this->prepareQuery( $query, $array ) ) )
            {
                $ret[] = $this->getObjectFromQueryQuery( $sql );
            }
            
            return $ret;
        }
    }
?>
