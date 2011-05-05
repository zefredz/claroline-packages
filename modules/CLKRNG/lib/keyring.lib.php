<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Keyring lib
 *
 * @version     1.9 $Revision$
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     CLKRNG
 */

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
    
    public static function checkKeyForHost ( $serviceName, $serviceHost, $serviceKey )
    {
        $mngr = self::getInstance();
        
        $serviceHostList = array( $serviceHost );
    
        $serviceHostList[] = @gethostbyaddr( $serviceHost );
        $serviceHostList[] = @gethostbyname( $serviceHost );

        if ( false !== ( $hostNameList = @gethostbynamel( $serviceHost ) ) ) 
        {   
            foreach ( $hostNameList as $hostName )
            {   
                $serviceHostList[] = $hostName;
            }   
        }   

        foreach ( $serviceHostList as $serviceHost )
        {   
            if ( $mngr->check ( $serviceName, $serviceHost, $serviceKey ) )
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * This is a helper funcion to allow easy integration of CLKRNG
     * This is a bit ugly since this function echoes some data and stops the 
     * execution of the script
     * @param string $serviceName
     */
    public static function checkForService( $serviceName )
    {
        $userInput = Claro_userInput::getInstance();
        
        try
        {
            $serviceUser = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : null;
            $serviceKey = $userInput->getMandatory('serviceKey');

            $checked = false;

            if ( !empty( $serviceUser ) )
            {
                $checked = self::checkKeyForHost( $serviceName, $serviceUser, $serviceKey );
            }

            if ( ! $checked )
            {
                header( 'Forbidden', true, 403 );

                echo '<h1>Forbidden !</h1>';
                echo '<p>Wrong service key or host</p>';

                if ( claro_debug_mode() )
                {
                    var_dump( $serviceUser.'::'.$serviceKey );
                }

                exit();
            }
        }
        catch ( Exception $e )
        {
            header( 'Forbidden', true, 403 );

            echo '<h1>Forbidden !</h1>';

            if ( claro_debug_mode() )
            {
                echo '<pre>'.$e->__toString().'</pre>';
            }
            else
            {
                echo '<p>An exception occurs : '.$e->getMessage().'</p>';
            }        

            exit();
        }
    }


    protected $serviceKeyring;
    
    protected function __construct ()
    {
        $this->keyring = array();
        $this->path = get_path('rootSys'). 'platform/keyring.lst';
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
                throw new Exception ("Invalid key ring file {$this->path}");
            }
            
            $tmp = explode(':', $line);
            
            if ( count ( $tmp ) != 3 )
            {
                throw new Exception ("Invalid key ring file {$this->path}");
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
        foreach ( $this->keyring as $idx => $value )
        {
            if ( $value['serviceName'] == $serviceName
                && $value['serviceHost'] == $serviceHost )
            {
                $this->keyring[$idx] = array( 'serviceName' => $serviceName, 'serviceHost' => $serviceHost, 'serviceKey' => $serviceKey );
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
       foreach ( $this->keyring as $idx => $value )
        {
            if ( $value['serviceName'] == $serviceName
                && $value['serviceHost'] == $serviceHost )
            {
                unset ( $this->keyring[$idx] );
                $this->save();
                return;
            }
        }
    
        throw new Exception ("no key for {$serviceName}:{$serviceHost}");
    }
    
    public function get ( $serviceName, $serviceHost )
    {
       foreach ( $this->keyring as $idx => $value )
        {
            if ( $value['serviceName'] == $serviceName
                && $value['serviceHost'] == $serviceHost )
            {
                return $this->keyring[$idx];
            }
        }
    
        throw new Exception ("no key for {$serviceName}:{$serviceHost}");
    }
    
    public function check ( $serviceName, $serviceHost, $serviceKeyToCheck )
    {
       foreach ( $this->keyring as $idx => $value )
        {
            if ( $value['serviceName'] == $serviceName
                && $value['serviceHost'] == $serviceHost
                && $value['serviceKey'] == $serviceKeyToCheck )
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
