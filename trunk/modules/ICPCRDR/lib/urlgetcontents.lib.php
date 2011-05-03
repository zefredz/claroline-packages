<?php // vim: expandtab sw=4 ts=4 sts=4:

/**
 * Get contents from a given URL using various PHP libs
 *
 * @version     1.0
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @author      Frederic Minne <frederic.minne@uclouvain.be>
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 *              GNU AFFERO GENERAL PUBLIC LICENSE version 3
 */

/**
 * Get a file from a remote url using several remote file access mechanism
 * (file_get_contents, curl, or fsockopen)
 * @param string $url
 * @return string file contents 
 * @throws Exception if no remote file access mechanism available
 */
function url_get_contents( $url )
{
    if ( ini_get( 'allow_url_fopen' ) )
    {
        return @file_get_contents( $url );
    }
    elseif ( function_exists('curl_init') )
    {
        $ch = curl_init();
        
        // set the url to fetch
        curl_setopt( $ch, CURLOPT_URL, $url );
        // don't give me the headers just the content
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        // return the value instead of printing the response to browser
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        
        // get content
        $content = @curl_exec( $ch );
        
        if ( false !== $content )
        {
            curl_close( $ch );
            return $content;
        }
        else
        {
            return false;
        }
    }
    elseif ( function_exists( 'fsockopen' ) )
    {
        return fsockopen_get_contents( $url );
    }
    else
    {
        throw new Exception("Your PHP install does not support url access.");
    }
}

/**
 * Get remote file contents using fsockopen
 * @param string $url
 * @return string file contents
 */
function fsockopen_get_contents( $url )
{
    // get the host name and url path
    $parsedUrl = parse_url( $url );
    $host = $parsedUrl['host'];
    
    if ( isset( $parsedUrl['path'] ) )
    {
        $path = $parsedUrl['path'];
    }
    else
    {
        // the url is pointing to the host like http://www.mysite.com
        $path = '/';
    }

    if ( isset( $parsedUrl['query'] ) )
    {
        $path .= '?' . $parsedUrl['query'];
    }

    if ( isset( $parsedUrl['port'] ) )
    {
        $port = $parsedUrl['port'];
    }
    else 
    {
        // most sites use port 80
        $port = '80';
    }

    $timeout = 10;
    $response = '';

    // connect to the remote server
    $fp = @fsockopen( $host, $port, $errno, $errstr, $timeout );

    if ( !$fp )
    {
        return false;
    }
    else
    {
        // send the necessary headers to get the file
        fputs( $fp, "GET {$path} HTTP/1.0\r\n" .
                 "Host: {$host}\r\n" .
                 "Accept: */*\r\n" .
                 "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n" .
                 "Keep-Alive: 300\r\n" .
                 "Connection: keep-alive\r\n" );

        // retrieve the response from the remote server
        while ( $line = fread( $fp, 4096 ) )
        {
            $response .= $line;
        }

        fclose( $fp );

        // strip the headers
        $pos      = strpos( $response, "\r\n\r\n" );
        $response = substr( $response, $pos + 4 );
    }

    // return the file content
    return $response;
}
