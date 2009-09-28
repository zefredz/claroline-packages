<?php
class DUPUtils
{
/**
     * Intelligently join different path.
     * The end result will always be a path with no slashes at end and no double slashes within.
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
        $prefix = ('/' == $paths[0][0] ? '/' : '');
        foreach ($paths as &$path)
        {
            $path = trim( $path, '/');
        }
        return  $prefix .  join( '/', $paths );
    }
    
    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/repos/v/function.copyr.php
     * @param       string   		$source    Source path
     * @param       string   		$dest      Destination path
     * @param(opt)  string/octal   	$mode      Mode of the created dirs/files (can be octal like 0755 or a string like "g+r"
     * @return      bool     		Returns TRUE on success, FALSE on failure
     */
    public static function copyr( $source, $dest, $mode = 0755 )
    {
        if( ! file_exists($source))
        {
        	throw new Exception($source . " cannot be copied because it doesn't exists");
        }
    	
		// Simple copy for a file
		if (is_file($source)) 
		{
			$c = copy($source, $dest);
			chmod($dest, $mode);
			return $c;
		}
		// Make destination directory
		if (!is_dir($dest)) 
		{
			$oldumask = umask();
			mkdir($dest, $mode,true);
			umask($oldumask);
		}
		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) 
		{
			// Skip pointers
			if ($entry == "." || $entry == "..") 
			{
				continue;
			}
			// Deep copy directories
			if ( $dest !== $source . '/' . $entry ) 
			{
				DUPUtils::copyr($source . '/' . $entry, $dest . '/' . $entry , $mode);
			}
		}
		// Clean up
		$dir->close();
		return true;
        
    }     
}

?>