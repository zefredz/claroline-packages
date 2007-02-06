<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Error handling Class
     * 
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Error
     */
     
    class Error_Handling
    {
        var $errmsg = null;
        var $errno = null;
        var $_isError = false;

        /**
         * set error number and error message
         * @param string errmsg error message
         * @param int errno error number
         */
        function setError( $errmsg = '', $errno = 0 )
        {
            $this->errmsg = ( !empty( $errmsg ) ) ? $errmsg : 'Unknown error';
            $this->errno = $errno;
            $this->_isError = true;
            
            return false;
        }

        /**
         * get the last error and discard last error
         * @return string
         */
        function getError()
        {
            if ( $this->hasError()  )
            {
                $errno = is_null( $this->errno ) ? 'UNKNOWN' : $this->errno;
                $errmsg = is_null( $this->errmsg ) ? 'ERROR' : $this->errmsg;
                $this->_isError = false;
                return $errno.' - '.$errmsg;
            }
            else
            {
                return false;
            }
        }

        /**
         * check if an error occurs
         * @return boolean
         */
        function hasError()
        {
            return ( $this->_isError );
        }
        
        function getErrorMessage()
        {
            return $this->errmsg;
        }
        
        function getErrorNumber()
        {
            return $this->errno;
        }
    }
?>
