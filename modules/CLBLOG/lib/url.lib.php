<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * URL handling function helpers to
     * - add a request variable to URL
     * - replace a request variable value in URL 
     * - remove a request variable from URL
     * - add a variable list to URL
     * 
     * @uses pcre regexp library
     * @author      Frederic Minne <zefredz@claroline.net>
     * @copyright   Copyright &copy; 2006, Frederic Minne
     * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @version     1.0
     * @package     Utils
     */

     /**
      * add a GET request variable to the given URL
      * 
      * @param string url url
      * @param string name name of the variable
      * @param string value value of the variable
      * @return string url
      */
    function add_request_variable_to_url( &$url, $name, $value )
    {
        if ( !preg_match( '~%\d\d~', $value ) )
        {
            $value = rawurlencode( $value );
        }
        
        if ( strstr( $url, "?" ) === false )
        {
            $url .= "?$name=$value";   
        }
        else
        {
            $url .= "&amp;$name=$value";
        }
        
        return $url;
    }
    
    /**
     * replace a GET request variable value in the given URL, add it to
     * the url if not found
     * 
     * @uses add_request_variable_to_url to add a non existing variable 
     *     to the url
     * @param string url url
     * @param string name name of the variable
     * @param string value value of the variable
     * @return string url
     */
    function replace_request_variable_in_url( &$url, $name, $value )
    {
        if ( preg_match( "~(&amp;|&)($name=)~", $url ) )
        {
            if ( !preg_match( '~%\d\d~', $value ) )
            {
                $value = rawurlencode( $value );
            }
            
            $url = preg_replace( "~(&amp;|&|\?)($name=)[^&]~", "$1$2$value", $url );
        }
        else
        {
            add_request_variable_to_url( $url, $name, $value );
        }
        
        return $url;
    }
    
    /**
     * remove a GET request variable from the given URL
     * 
     * @param string url url
     * @param string name name of the variable
     * @return string url
     */
    function remove_request_variable_from_url( &$url, $name )
    {
        $url = preg_replace( "~(&amp;|&)$name=[^&]*~", "", $url );
        $url = preg_replace( "~\?$name=[^&]*~", "?", $url );
        
        return $url;
    }
    
    /**
     * add a GET request variable list to the given URL
     * @param string url url
     * @param array variableList list of the request variables to add
     * @return string url
     */
    function add_request_variable_list_to_url( &$url, $variableList )
    {
        foreach ( $variableList as $name => $value )
        {
            $url = add_request_variable_to_url( $url, $name, $value );
        }
        
        return $url;
    }
    
    /**
     * Query String manipulation class
     */
    class QueryString
    {
        function add( &$url, $name, $value )
        {
            if ( !preg_match( '~%\d\d~', $value ) )
            {
                $value = rawurlencode( $value );
            }
            
            if ( strstr( $url, "?" ) === false )
            {
                $url .= "?$name=$value";   
            }
            else
            {
                $url .= "&amp;$name=$value";
            }
            
            return $url;
        }
        
        function replace( &$url, $name, $value )
        {   
            if ( !preg_match( '~%\d\d~', $value ) )
            {
                $value = rawurlencode( $value );
            }
            
            $url = preg_replace( "~(&amp;|&|\?)($name=)[^&]~", "$1$2$value", $url );

            return $url;
        }
        
        function addOrReplace( &$url, $name, $value )
        {
            if ( preg_match( "~(&amp;|&)($name=)~", $url ) )
            {
                QueryString::replace( $url, $name, $value );
            }
            else
            {
                QueryString::add( $url, $name, $value );
            }
            
            return $url;
        }
        
        function addOrReplaceList( &$url, $variableList )
        {
            foreach ( $variableList as $name => $value )
            {
                $url = QueryString::addOrReplace( $url, $name, $value );
            }
            
            return $url;
        }
        
        function delete( &$url, $name )
        {
            $url = preg_replace( "~(&amp;|&)$name=[^&]*~", "", $url );
            $url = preg_replace( "~\?$name=[^&]*~", "?", $url );
            
            return $url;
        }
        
        function formatForHtml( &$url )
        {
            $url = preg_replace( '/(&amp;|&)/', '&amp;', $url );
            
            return $url;
        }
        
        function formatForHttp( &$url )
        {
            $url = str_replace( '&amp;', '&', $url );
            
            return $url;
        }
    }
?>