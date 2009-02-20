<?php

class WebServiceAuth_Utils
{
    protected static function alreadyUsed( $nonce )
    {
        $tbl = get_module_main_tbl(array('nonce'));
        
        $res = Claroline::getDatabase()->query( "SELECT count(*) FROM {$tbl['nonce']} WHERE nonce = " . Claroline::getDatabase()->quote( $nonce ) );
        
        
        if ( count( $res ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    protected static function format( $base )
    {
        $nonce = "{$base}";
        
        return  substr($nonce, 0,8) . '-' . substr($nonce, 8,4) . '-' . substr($nonce, 12, 4) . '-' . substr($nonce, 16,4) . '-' . substr($nonce, 20);
    }
    
    public static function generateNonce()
    {
        /*do
        {
            $nonce = self::format( uniqid() );
        } while( self::alreadyUsed( $nonce ) );*/
        
        return self::format( uniqid() );;
    }
    
    public static function generateHash( $nonce, $timestamp, $secret )
    {
        return base64_encode( sha1( $nonce.$timestamp.$secret ) );
    }
}

class WebServiceAuth_Credential
{
    public static function generate( $secret )
    {
        $nonce      = WebServiceAuth_Utils::generateNonce();
        $hash       = WebServiceAuth_Utils::generateHash( $nonce, $timestamp, $secret );
        $timestamp  = gmdate('c');
        
        return array( $nonce, $timestamp, $hash );
    }

    public static function check( $hash, $nonce, $timestamp, $secret)
    {
        return $hash == WebServiceAuth_Utils::generateHash( $nonce, $timestamp, $secret );
    }
}
