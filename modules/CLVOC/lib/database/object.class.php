<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
	
	/**
     * @author  Frederic Minne <zefredz@claroline.net>
     * @copyright Copyright &copy; 2006-2007, Frederic Minne
     * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version 1.0
     * @package Database
     */
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    require_once dirname(__FILE__) . '/../error/handling.class.php';
    
    class Database_Object extends Error_Handling
    {
        var $connection;
        var $tableList;
        
        function Database_Object( &$connection, $tableList )
        {
            $this->connection =& $connection;
            $this->tableList = $tableList;
        }
        
        function hasError()
        {
            return ( $this->objectError() || $this->connectionError() );
        }
        
        function connectionError()
        {
            return $this->connection->hasError();
        }
        
        function objectError()
        {
            return $this->_isError;
        }
    }
?>
