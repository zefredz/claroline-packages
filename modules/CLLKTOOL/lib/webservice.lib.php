<?php

require_once dirname(__FILE__) . '/webserviceauth.lib.php';

class WebService_Utils
{
    public static function getRawPostData()
    {
        return file_get_contents("php://input");
    }
    
    public static function getAllRequestHeaders()
    {
        if ( function_exists('apache_request_headers') )
        {
            return apache_request_headers();
        }
        else
        {
            throw new Exception('Apache_Request_Headers() function not supported');
        }
    }
}

class WebService_Client
{
    private $secret;
    private $remoteUrl;
    
    public function __construct( $remoteUrl, $secret )
    {
        $this->secret = $secret;
        $this->remoteUrl = $remoteUrl;
    }
    
    public function send( $params, $callback )
    {
        list( $nonce, $timestamp, $hash ) = WebServiceAuth_Credential::generate( $this->secret );
        
        $params['sec_nonce'] = $nonce;
        $params['sec_timestamp'] = $timestamp;
        $params['sec_hash'] = $hash;
        
        $query = http_build_query($params);
        
        $post = curl_init($this->remoteUrl);
        curl_setopt($post, CURLOPT_HEADER, 1);
        curl_setopt($post, CURLOPT_POST, 1);
        curl_setopt($post, CURLOPT_POSTFIELDS, $query);
        if ( curl_setopt($post, CURLOPT_RETURNTRANSFER, 1) )
        {
            $response = curl_exec( $post );
            
            $info = curl_getinfo( $post );
            
            curl_close( $post );
            
            $this->handleResponseCode( $info['http_code'] );
            
            return call_user_func( $callback, $response, $info );
        }
        else
        {
            curl_close( $post );
            throw new Exception( "Service request send on {$this->remoteUrl} with parameters {$query}" );
        }
    }
}

class WebService_Server
{
    protected $registry = array();
    
    public function registerService( $action, WebService_Service $service )
    {
        $this->registry[$action] = $service;
    }
    
    public function serve( $action, $request )
    {
        if ( $this->auth( $request ) )
        {
            
        }
        else
        {
            
        }
    }
}

interface WebService_Service
{
    public function run( $request );
}
