<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    class Keyring
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
            $this->path = dirname (__FILE__) . '/../keyring.db';
            
            if ( ! file_exists( $this->path ) )
            {
                $this->sqlite = sqlite_factory( $this->path );
                
                if ( ! $this->sqlite )
                {
                    throw new Exception('Cannot create keyring database');
                }
                
                $sql = <<<__CREATE_TABLE__
CREATE TABLE services (
    serviceName CHAR(255),
    serviceHost CHAR(255),
    serviceKey  CHAR(255)
)
__CREATE_TABLE__;

                if ( ! $this->sqlite->queryExec ( $sql ) )
                {
                    throw new Exception( 'Cannot create services table : ' 
                        . sqlite_error_string( sqlite_last_error() ) );
                }
            }
            else
            {
                $this->sqlite = sqlite_factory( $this->path );
            }            
            
            if ( ! $this->sqlite )
            {
                throw new Exception('Cannot open keyring');
            }
        }
        
        public function add ( $serviceName, $serviceHost, $serviceKey )
        {
            $sql = "INSERT INTO services(serviceName,serviceHost,serviceKey)\n"
                . "VALUES('"
                    . sqlite_escape_string($serviceName)."','"
                    . sqlite_escape_string($serviceHost)."','"
                    . sqlite_escape_string($serviceKey)
                ."')"
                ;
                
            if ( ! $this->sqlite->queryExec ( $sql ) )
            {
                throw new Exception('Cannot add service in keyring : ' 
                    . sqlite_error_string( sqlite_last_error() ));
            }
        }
        
        public function update ( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey )
        {
            $sql = "UPDATE services\n"
                . "SET "
                    . "serviceName = '" . sqlite_escape_string($serviceName)."',"
                    . "serviceHost = '" . sqlite_escape_string($serviceHost)."',"
                    . "serviceKey = '" . sqlite_escape_string($serviceKey)."' "
                ."WHERE "
                    . "serviceName = '" . sqlite_escape_string($oldServiceName)."'"
                    . " AND "
                    . "serviceHost = '" . sqlite_escape_string($oldServiceHost)."'"
                ;
                
            if ( ! $this->sqlite->queryExec ( $sql ) )
            {
                throw new Exception('Cannot update service in keyring : ' 
                    . sqlite_error_string( sqlite_last_error() ));
            }
        }
        
        public function delete ( $serviceName, $serviceHost )
        {
            $sql = "DELTE FROM services\n"
                ."WHERE "
                    . "serviceName = '" . sqlite_escape_string($serviceName)."'"
                    . " AND "
                    . "serviceHost = '" . sqlite_escape_string($serviceHost)."'"
                ;
                
            if ( ! $this->sqlite->queryExec ( $sql ) )
            {
                throw new Exception('Cannot update service in keyring : ' 
                    . sqlite_error_string( sqlite_last_error() ));
            }
        }
        
        public function get ( $serviceName, $serviceHost )
        {
            $sql = "SELECT serviceName,serviceHost,serviceKey FROM services\n"
                ."WHERE "
                    . "serviceName = '" . sqlite_escape_string($serviceName)."'"
                    . " AND "
                    . "serviceHost = '" . sqlite_escape_string($serviceHost)."'"
                ;
                
            $result = $this->sqlite->query( $sql );
                
            if ( ! $result )
            {
                throw new Exception ("No key for {$serviceName}:{$serviceHost}");
            }
            elseif ( ! $result->numRows() )
            {
                throw new Exception('Service not found in keyring' 
                    . sqlite_error_string( sqlite_last_error() ));
            }
            else
            {
                return $result->fetchArray( SQLITE_ASSOC );
            }
        }
        
        public function check ( $serviceName, $serviceHost, $serviceKeyToCheck )
        {
            $serviceName = $this->get( $serviceName, $serviceHost );
            
            return $serviceName['serviceKey'] == $serviceKeyToCheck;
        }
        
        public function getServiceList()
        {
            $sql = "SELECT serviceName,serviceHost,serviceKey FROM services";
            
            $result = $this->sqlite->query( $sql );
                
            if ( ! $result )
            {
                throw new Exception ("Keyring error !");
            }
            else
            {
                return $result->fetchAll( SQLITE_ASSOC );
            }
        }
    }
?>