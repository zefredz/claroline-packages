<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Keyring lib
 *
 * @version     1.12 $Revision$
 * @copyright   2001-2014 Universite catholique de Louvain (UCL)
 * @author      Claroline Team <info@claroline.net>
 * @author      Frederic Minne <zefredz@claroline.net>
 * @license     http://www.gnu.org/copyleft/gpl.html
 *              GNU GENERAL PUBLIC LICENSE version 2 or later
 * @package     CLKRNG
 */

class Keyring
{
    protected static $instance = false;
    protected static $options = array();
    
    public static function getInstance()
    {
        if ( ! self::$instance )
        {
            self::$instance = new Keyring;
        }
        
        return self::$instance;
    }
    
    public static function setOption($option, $value)
    {
        self::$options[$option] = $value;
    }
    
    public static function getOption($option)
    {
        return isset( self::$options[$option]) ? self::$options[$option] : null;
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
                if ( self::getOption ( 'errorMode' ) == 'exception' )
                {
                    throw new Exception(get_lang('Wrong service key or host'));
                }
                else
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
        }
        catch ( Exception $e )
        {
            if ( self::getOption ( 'errorMode' ) == 'exception' )
            {
                throw $e;
            }
            else
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
    }
    
    public function __construct ()
    {
        $this->database = Claroline::getDatabase();
        $this->table = get_module_main_tbl('clkrng_keyring');
    }
    
    public function add ( $serviceName, $serviceHost, $serviceKey )
    {
        if ( ! $this->database->exec("
            INSERT 
            INTO
                `{$this->table['clkrng_keyring']}`
            SET
                `service` = ".$this->database->quote($serviceName).",
                `host` = ".$this->database->quote($serviceHost).",
                `key` = ".$this->database->quote($serviceKey) ."
        ") )
        {
             throw new Exception(get_lang("Cannot insert key {$serviceKey} for service {$serviceName} and host {$serviceHost}"));
        }
                
        return $this;
    }
    
    public function update ( $oldServiceName, $oldServiceHost, $serviceName, $serviceHost, $serviceKey )
    {
        if ( ! $this->database->exec("
            UPDATE 
            TABLE
                `{$this->table['clkrng_keyring']}`
            SET
                `service` = ".$this->database->quote($serviceName).",
                `host` = ".$this->database->quote($serviceHost).",
                `key` = ".$this->database->quote($serviceKey)."
            WHERE
                `service` = ".$this->database->quote($oldServiceName)."
            AND
                `host` = ".$this->database->quote($oldServiceHost)."
        ") )
        {
             throw new Exception(get_lang("Cannot update key for service {$oldServiceName} and host {$oldServiceHost}"));
        }
                
        return $this;
    }
    
    public function delete ( $serviceName, $serviceHost )
    {
        if ( ! $this->database->exec("
            DELETE 
            FROM
                `{$this->table['clkrng_keyring']}`
            WHERE
                `service` = ".$this->database->quote($serviceName)."
            AND
                `host` = ".$this->database->quote($serviceHost)."
        ") )
        {
            throw new Exception ("no key for {$serviceName}:{$serviceHost}");
        }
        
        return $this;
    }
    
    public function get ( $serviceName, $serviceHost )
    {
        $result = $this->database->query("
            SELECT 
                `service`,
                `host`,
                `key`
            FROM
                `{$this->table['clkrng_keyring']}`
            WHERE
                `service` = ".$this->database->quote($serviceName)."
            AND
                `host` = ".$this->database->quote($serviceHost)."
        ")->fetch();
                
        if ( $result )
        {
            return $result;
        }
        else
        {
            throw new Exception ("no key for {$serviceName}:{$serviceHost}");
        }
    }
    
    public function check ( $serviceName, $serviceHost, $serviceKeyToCheck )
    {
        return $this->database->query("
            SELECT 
                `service`,
                `host`,
                `key`
            FROM
                `{$this->table['clkrng_keyring']}`
            WHERE
                `service` = ".$this->database->quote($serviceName)."
            AND
                `host` = ".$this->database->quote($serviceHost)."
            AND
                `key` = ".$this->database->quote($serviceKeyToCheck)."
        ")->numRows() > 0;
    }
    
    public function getServiceList()
    {
        $toRet = array();
        
        $resultSet = $this->database->query("
            SELECT 
                `service` AS `serviceName`,
                `host` AS `serviceHost`,
                `key` AS `serviceKey`
            FROM
                `{$this->table['clkrng_keyring']}`
            WHERE
                1 = 1
        ");
                
        foreach ( $resultSet as $row )
        {
            $toRet[] = $row;
        }
        
        return $toRet;
    }
}
