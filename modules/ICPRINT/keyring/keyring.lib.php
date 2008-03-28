<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    interface Keyring
    {
        public static function getInstance();
        
        public static function checkKey ( $serviceName, $serviceHost, $serviceKey );
        
        public function add ( $serviceName, $serviceHost, $serviceKey );
        
        public function update ( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey );
        
        public function delete ( $serviceName, $serviceHost );
        
        public function get ( $serviceName, $serviceHost );
        
        public function check ( $serviceName, $serviceHost, $serviceKeyToCheck );
        
        public function getServiceList();
    }
    
    class KeyringFactory
    {
        public static function getInstance( $driver )
        {
            require_once dirname( __FILE__ ) . "/keyring-{$driver}.lib.php";
            $class = "Keyring".ucfirst($driver);
            $instance = new $class();
            
            return $instance;
        }
    }
?>