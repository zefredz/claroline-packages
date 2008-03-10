<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * Replacement for $_REQUEST superglobal using only $_GET and $_POST
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
    
    class DataInputException extends Exception{};
    
    interface DataInput
    {
        public function get( $name, $default = null );
        public function getMandatory( $name );
    }
    
    class ArrayDataInput implements DataInput
    {
        protected $input;
        
        public function __construct( $input )
        {
            $this->input = $input;
        }
        
        public function get( $name, $default = null )
        {
            if ( array_key_exists( $name, $this->input ) )
            {
                return $this->input[$name];
            }
            else
            {
                return $default;
            }
        }
        
        public function getMandatory( $name )
        {
            $ret = $this->get( $name );
            
            if ( is_null( $ret ) )
            {
                throw new DataInputException( "{$name} not found in ".get_class($this)." !" );
            }
            else
            {
                return $ret;
            }
        }
    }
    
    class FilteredDataInput implements DataInput
    {
        protected $filters;
        protected $input;
        
        public function __construct( DataInput $input )
        {
            $this->filters = array();
            $this->input = $input;
        }
        
        public function setFilter( $name, $filtercallback )
        {
            $this->filters[$name] = $filtercallback;
        }
        
        public function get( $name, $default = null )
        {
            $tainted = $this->input->get( $name, $default );
            
            return $this->filter( $name, $tainted );
        }
        
        public function getMandatory( $name )
        {
            $tainted = $this->input->getMandatory( $name );
            
            return $this->filter( $name, $tainted );
        }
        
        public function filter( $name, $tainted )
        {
            if ( array_key_exists( $name, $this->filters ) )
            {
                if ( ! call_user_func( $this->filters[$name], $tainted ) )
                {
                    throw new DataInputException( "{$name} does not pass the filter !" );
                }
            }
            
            return $tainted;
        }
    }
    
    class UserInput
    {        
        protected static $instance = false;
        
        public static function getInstance()
        {
            if ( ! self::$instance )
            {
                self::$instance = new ArrayDataInput( array_merge( $_GET, $_POST ) );
            }
            
            return self::$instance;
        }
    }
    
    class FilteredUserInput
    {        
        protected static $instance = false;
        
        public static function getInstance()
        {
            if ( ! self::$instance )
            {
                self::$instance = new FilteredDataInput( UserInput::getInstance() );
            }
            
            return self::$instance;
        }
    }
?>