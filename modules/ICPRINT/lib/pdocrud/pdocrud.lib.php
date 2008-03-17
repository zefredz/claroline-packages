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
    
    require_once dirname(__FILE__) . '/pdofactory.lib.php';
    require_once dirname(__FILE__) . '/pdomapperbuilder.lib.php';
    
    class PDOCrud
    {
        protected $database;
        protected $builder;
        protected $dsn;
        
        protected static $_instance = false;
        
        protected function __construct( $dsn )
        {
            $this->dsn = $dsn;
            $this->database = PDOFactory::getConnection( $dsn );
            $this->builder = new PDOMapperBuilder( $this->database );
        }
        
        protected function _getDatabase()
        {
            return $this->database;
        }
        
        protected function _getBuilder()
        {
            return $this->builder;
        }
        
        public static function initDatabase( $dsn )
        {
            if ( !self::$_instance )
            {
                self::initInstance();
            }
            
            self::$_instance->_setDatabase( PDOFactory::getConnection( $dsn ) );
        }
        
        public static function getDatabase()
        {
            if ( ! self::$_instance )
            {
                throw new Exception( 'PDOCrud not initialized' );
            }
            
            return self::$_instance->_getDatabase();
        }
        
        public static function getBuilder()
        {
            if ( ! self::$_instance )
            {
                throw new Exception( 'PDOCrud not initialized' );
            }
            
            return self::$_instance->_getBuilder();
        }
        
        public static function init( $dsn )
        {
            if ( ! self::$_instance )
            {
                self::$_instance = new PDOCrud( $dsn );
            }
            else
            {
                throw new Exception( 'PDOCrud allready inituialised' );
            }
        }
        
        public static function initialized()
        {
            return (self::$_instance != false);
        }
    }
?>