<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );

    require_once dirname(__FILE__) . '/../error/handling.class.php';
    require_once dirname(__FILE__) . '/service.class.php';
    
    // define default service identifier
    !defined('DISPATCHER_DEFAULT_SERVICE') 
        && define('DISPATCHER_DEFAULT_SERVICE', 'DISPATCHER_DEFAULT_SERVICE');
    
    /**
     * Service dispatcher
     * Receive a requested service identifier and executes the corresponding 
     * service. Dispatcher is like a routing table.
     * 
     * @author      Frederic Minne <zefredz@claroline.net>
     * @copyright   Copyright &copy; 2006-2007, Frederic Minne
     * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version     1.0
     * @package     Service
     */
    class Dispatcher extends Error_Handling
    {
        /**
         * Bind table
         * @access  private
         */
        var $registry;
        
        /**
         * Constructor
         * @param   initConfig array initial services array
         */
        function Dispatcher( $initConfig = null )
        {
            if ( !empty( $initConfig ) && is_array( $initConfig ) )
            {
                $this->registry = $initConfig;
            }
            else
            {
                $this->registry = array();
            }
        }
        
        /**
         * Bind a service to a service identifier
         * @param   request string service identifier
         * @param   service Service service object
         * @param   overwrite boolean overwrites an existing entry 
         *  with the same identifier
         * @return  boolean true if binding succeeds, else returns false
         */ 
        function bind( $request, $service, $overwrite = false )
        {
            if ( $overwrite || ! array_key_exists( $request, $this->registry ) )
            {
                $this->registry[$request] = $service;
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Bind the default service
         * @param   service Service service object
         * @return  boolean true if binding succeeds, else returns false
         */
        function setDefault( $service )
        {
            return $this->bind( DISPATCHER_DEFAULT_SERVICE, $service, true );
        }
        
        /**
         * Unbind the service corresponding to the given service identifier
         * @param   request string service identifier
         * @return  mixed Service if unbinding succeeds, else returns false
         */
        function unbind( $request )
        {
            if ( array_key_exists( $request, $this->registry ) )
            {
                $tmp = $this->registry[$request];
                unset( $this->registry[$request] );
                return $tmp;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * Rebind a service to a service identifier, same as Dispatcher::bind()
         * with $overwrite set to true
         * @param   request string service identifier
         * @param   service Service service object
         * @return  boolean true if binding succeeds, else returns false
         */ 
        function rebind( $request, $service )
        {
            return $this->bind( $request, $service, true );
        }
        
        /**
         * Run the service corresponding to the given identifier
         * @param   request string service identifier
         * @return  mixed Service object if succeeds, false else
         */
        function serve( $request )
        {
            if ( array_key_exists( $request, $this->registry )  )
            {
                $svc = $this->registry[$request];
                $svc->run();
                
                return $svc;
            }
            else
            {
                $this->setError( "Unknown page $request", 404 );
                
                return false;
            }
        }
        
        /**
         * Run the default service
         * @return  mixed Service object if succeeds, false else
         */
        function serveDefault()
        {
            return $this->serve( DISPATCHER_DEFAULT_SERVICE );
        }
        
        // SERIALIZATION
        
        function serialize( $regPath = 'dispatcher_registry.cfg' )
        {
            Registry::serialize( $this->registry, $regPath );
        }
        
        function unserialize( $regPath = 'dispatcher_registry.cfg' )
        {
            $reg = Registry::unserialize( $regPath );
            $this->registry = array_merge( $this->registry, $reg );
        }
    }
?>
