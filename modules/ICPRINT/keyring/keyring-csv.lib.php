<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    class Keyring // Csv implements Keyring
    {
        protected static $instance = false;
        
        public static function getInstance()
        {
            if ( ! self::$instance )
            {
                self::$instance = new Keyring;
            }
            
            return self::$instance;
        }
        
        public static function checkKey ( $serviceName, $serviceHost, $serviceKey )
        {
            $mngr = self::getInstance();
            
            return $mngr->check ( $serviceName, $serviceHost, $serviceKey );
        }
        
        protected $serviceKeyring;
        
        protected function __construct ()
        {
            $this->keyring = array();
            $this->path = dirname (__FILE__) . '/../keyring.lst';
            $this->load();
            
        }
        
        protected function load ()
        {   
            if ( file_exists( $this->path ) )
            {
                $serviceKeyring = file( $this->path );
                
                foreach ( $serviceKeyring as $line )
                {
                    $line = trim( $line );
                    
                    if ( empty ( $line ) 
                        || preg_match( '/^\s*\#/', $line ) )
                    {
                        continue;
                    }
                    
                    if ( ! strpos( $line, ':' ) )
                    {
                        throw new Exception ("Invalid key ring file {$path}");
                    }
                    
                    $tmp = explode(':', $line);
                    
                    if ( count ( $tmp ) != 3 )
                    {
                        throw new Exception ("Invalid key ring file {$path}");
                    }
                    
                    $this->keyring[]= array( 'serviceName' => $tmp[0], 'serviceHost' => $tmp[1], 'serviceKey' => $tmp[2] );
                }
            }
        }
        
        protected function save()
        {
            $content = '';
            
            foreach ( $this->keyring as $serviceName )
            {
                $content .= "{$serviceName['serviceName']}:{$serviceName['serviceHost']}:{$serviceName['serviceKey']}\n";
            } 
            
            file_put_contents( $this->path, $content );
        }
        
        public function add ( $serviceName, $serviceHost, $serviceKey )
        {
            for ( $i = 0; $i < count( $this->keyring ); $i++ )
            {
                if ( $this->keyring[$i]['serviceName'] == $serviceName
                    && $this->keyring[$i]['serviceHost'] == $serviceHost )
                {
                    $this->keyring[$i] = array( 'serviceName' => $serviceName, 'serviceHost' => $serviceHost, 'serviceKey' => $serviceKey );
                    return;
                }
            }
            
            $this->keyring[] = array( 'serviceName' => $serviceName, 'serviceHost' => $serviceHost, 'serviceKey' =>$serviceKey );
            
            $this->save();
        }
        
        public function update ( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey )
        {
            $this->delete ( $oldServiceName, $oldServiceHost );
            $this->add ( $serviceName, $serviceHost, $serviceKey );
        }
        
        public function delete ( $serviceName, $serviceHost )
        {
            for ( $i = 0; $i < count( $this->keyring ); $i++ )
            {
                if ( $this->keyring[$i]['serviceName'] == $serviceName
                    && $this->keyring[$i]['serviceHost'] == $serviceHost )
                {
                    unset ( $this->keyring[$i] );
                    $this->save();
                    return;
                }
            }
            
            throw new Exception ("no key for {$serviceName}:{$serviceHost}");
        }
        
        public function get ( $serviceName, $serviceHost )
        {
            for ( $i = 0; $i < count( $this->keyring ); $i++ )
            {
                if ( $this->keyring[$i]['serviceName'] == $serviceName
                    && $this->keyring[$i]['serviceHost'] == $serviceHost )
                {
                    return $this->keyring[$i];
                }
            }
            
            throw new Exception ("no key for {$serviceName}:{$serviceHost}");
        }
        
        public function check ( $serviceName, $serviceHost, $serviceKeyToCheck )
        {
            for ( $i = 0; $i < count( $this->keyring ); $i++ )
            {
                if ( $this->keyring[$i]['serviceName'] == $serviceName
                    && $this->keyring[$i]['serviceHost'] == $serviceHost
                    && $this->keyring[$i]['serviceKey'] == $serviceKeyToCheck )
                {
                    return true;
                }
            }
            
            return false;
        }
        
        public function getServiceList()
        {
            return $this->keyring;
        }
    }
?>