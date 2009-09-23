<?php
class DUPUtils
{
/**
     * Intelligently join different path.
     * The end result will always be a path with no slashes at the beginning or end and no double slashes within.
     * 
     * You can pass as many path snippets as you want, either as array or separate arguments:
     *  joinPaths(array('my/path', 'is', '/an/array'));
     *  joinPaths('my/paths/', '/are/', 'a/r/g/u/m/e/n/t/s/');
     */
    
    public static function joinPaths()
    {
        $args = func_get_args();
        $paths = array();
        foreach ( $args as $arg )
        {
            $paths = array_merge( $paths, (array) $arg );
        }
        foreach ($paths as &$path)
        {
            $path = trim( $path, '/');
        }
        return join( '/', $paths );
    }
    
    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/repos/v/function.copyr.php
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    public static function copyr( $source, $dest )
    {
        // Simple copy for a file
        if (is_file($source))
        {
            return copy( $source, $dest );
        }
    
        // Make destination directory
        if ( ! is_dir( $dest ) )
        {
            mkdir($dest);
        }
    
        // If the source is a symlink
        if ( is_link( $source ) )
        {
            $link_dest = readlink( $source );
            return symlink( $link_dest, $dest );
        }
    
        // Loop through the folder
        $dir = dir( $source );
        while (false !== $entry = $dir->read() )
        {
            // Skip pointers
            if ( $entry == '.' || $entry == '..' )
            {
                continue;
            }
    
            // Deep copy directories
            if ( $dest !== $source . '/' . $entry ) {
                DUPUtils::copyr( $source . '/' . $entry, $dest . '/' . $entry );
            }
        }
    
        // Clean up
        $dir->close();
        return true;
    }
}

?>