<?php // $Id$
/**
 * Online Meetings for Claroline
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class CLMEETNG_OpenMeetingsClient
{
    static public $serviceList = array( 'calendar',
                                        'file',
                                        'jabber',
                                        'room',
                                        'user' );
    
    static public $typeList = array(
        '+error+',
        'Conference',
        'Audience',
        'Restricted',
        'Interview',
        'Custom' );
    
    static public $langList = array(
        '+error+',
        'en',
        'de',
        'de',
        'fr',
        'it',
        'pt',
        'pt-br',
        'es',
        'ru',
        'sv',
        'zh-CN',
        'zh-TW',
        'ko',
        'ar',
        'ja',
        'id',
        'hu',
        'tr',
        'uk',
        'th',
        'fa',
        'cs',
        'gl',
        'fi',
        'pl',
        'el',
        'nl',
        'he',
        'ca',
        'bg',
        'da',
        'sk' );

    
    public $serverUrl;
    public $serverPort = 5080;
    public $url;
    
    public $lang;
    public $type;
    
    protected $sessionId;
    
    /**
     * Constructor
     * @param string $serverUrl : the server's base url
     * @param string $serverPort : the service's port
     * @param string $serviceName : the service's name
     * @param string $sessionId : the OpenMeetings session id
     * @param int $type : the meeting's type
     * @param int $lang : the meeting's lang id
     */
    public function __construct( $serverUrl , $serverPort , $serviceName , $sessionId = null , $type = 1 , $lang = 1 )
    {
        $this->serverUrl = $serverUrl;
        $this->serverPort = $serverPort;
        $this->url = $serverUrl . ':' . $serverPort . '/' . $serviceName . '/';
        $this->sessionId = $sessionId;
        $this->type = $type;
        $this->lang = $lang;
    }
    
    /**
     * Gets the session id
     * or creates one if not exists
     * @return string $SID
     */
    public function getSessionId()
    {
        if( $this->sessionId
            || ( $this->serviceAvailable()
                 && $this->_generateSessionId() ) )
        {
            return $this->sessionId;
        }
    }
    
    /**
     * verifies if the service is available
     * @return boolean true if available, false if not
     */
    public function serviceAvailable()
    {
        return @file( $this->url );
    }
    
    /**
     * Logs the user in
     * @param string $username : username
     * @param string $password : password
     * @return boolean true on success
     */
    public function logIn( $username , $password )
    {
        return $this->_callService( 'user' ,
                                    'loginUser' ,
                                    array( 'username' => $userName ,
                                           'userpass' => $password ) );
    }
    
    /**
     * Generates SID (private method)
     */
    private function _generateSessionId()
    {
        return $this->sessionId = $this->_callService( 'user' , 'getSession' )->session_id;
    }
    
    /**
     * Calls services (private method)
     * @param string $service : the service identifier
     * @param string $command : the command to use
     * @param array $arg : the command's arguments ( ['argument'] => #value# )
     */
    private function _callService( $service , $command , $args = null )
    {
        if( $this->serviceAvailable()
            && in_array( $service , self::$serviceList ) )
        {
            $url = $this->url . 'services/' . ucwords( $service ) . 'Service?wsdl';
            
            if( is_array( $args )
                && ! array_key_exists( 'SID' , $args) )
            {
                $args[ 'SID' ] = $this->getSessionId();
            }
            
            $soap = new soapClient( $url );
            
            return $soap->{$command}( $args )->return;
        }
    }
    
    /**
     * Gets language id (static method)
     * @param string $lang : the Claroline language identifier
     * @return int $langId : OpenMeetings' lang id
     */
    static public function getLangId( $lang )
    {
        return array_search( $lang , self::$langList );
    }
    
    /**
     * Gets type id (static method)
     * @param string $type : the type's name
     * @return int $typeId : OpenMeetings's type id
     */
    static public function getTypeId( $type )
    {
        return array_search( ucwords( $type ) , self::$typeList );
    }
    
    /**
     * Gets language from id (static method)
     * @param int $langId : OpenMeetings' lang id
     * @return string $lang : Claroline's language identifier
     */
    static public function getLang( $langId )
    {
        if( $langId && array_key_exists( $langId , self::$langList ) )
        {
            return self::$langList[ $langId ];
        }
    }
}