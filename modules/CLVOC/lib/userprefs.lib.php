<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
	
	define ('USER_PREFS_GET_VALUE',     'USER_PREFS_GET_VALUE' );
    define ('USER_PREFS_GET_ALL',       'USER_PREFS_GET_ALL' );
    define ('USER_PREFS_KEY_NOT_FOUND', 'USER_PREFS_KEY_NOT_FOUND' );

    /**
     * User preference lib
     * @author  Frederic Minne <zefredz@claroline.net>
     * @version 1.0
     * @copyright   Copyright &copy; 2006, Frederic Minne
     * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
     * @package userprefs
     */
     
    /*
     
    Sample pref file :
    
        &lt;?php
        user_prefs( 'test', true );
        user_prefs( 'plop', 'gnome' );
        user_prefs( 'db_driver', 'mysql' );
        user_prefs( 'db_connect', 'mysql_pconnect' );
        ?&gt;
    
    Sample usage :
    
        user_prefs_load_file( 'user.prefs.php' );
        
        var_dump( user_prefs( 'plop' ) );
        var_dump( user_prefs( 'gnome' ) );
        var_dump( user_prefs() );
    */
    
    /**
     * Set or get user preference
     * 
     * Usage :
     * - user_prefs( 'key', value ) : set pref key-value pair
     * - user_prefs( 'key' ) : get value for the given key
     *      , returns USER_PREFS_KEY_NOT_FOUND if not found
     * - user_prefs() : get all prefs in an array
     * 
     * @param   string key pref key or USER_PREFS_GET_ALL (default)
     * @param   string value pref value or USER_PREFS_GET_VALUE (default)
     * @return  mixed value, USER_PREFS_KEY_NOT_FOUND or array of all prefs
     */ 
    function user_prefs( $key = USER_PREFS_GET_ALL, $value = USER_PREFS_GET_VALUE )
    {
        static $prefs = array();
        
        if ( USER_PREFS_GET_ALL === $key )
        {
            return $prefs;
        }
        elseif ( USER_PREFS_GET_VALUE === $value )
        {
            if ( array_key_exists( $key, $prefs ) )
            {
                return $prefs[$key];
            }
            else
            {
                return USER_PREFS_KEY_NOT_FOUND;
            }
        }
        else
        {
            $prefs[$key] = $value;
            return $key;
        }
    }
    
    /**
     * Load a preference file
     * @param   string filePath
     * @return  true if file loaded, false if file not found
     */
    function user_prefs_load_file( $filePath )
    {
        if ( file_exists( $filePath ) )
        {
            require $filePath;
            
            return true;
        }
        else
        {
            return false;
        }
    }
?>