<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * Enter description here...
 *
 * @param string $completePath
 * @param string $baseFile
 * @return string : path fragement
 *
 */
function get_slashed_argument($completePath, $baseFile)
{

    $pahtElementList = explode($baseFile, $completePath);

    if ( count($pahtElementList) > 1)
    {
        $argument = array_pop($pahtElementList);

        $questionMarkPos = strpos($argument, '?');

        if (is_int($questionMarkPos)) return substr($argument, 0, $questionMarkPos);
        else                          return $argument;

    }
    else
    {
        return '';
    }
}

/**
 * Returns the name of the current script, WITH the querystring portion.
 * this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
 * return different things depending on a lot of things like your OS, Web
 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)
 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
 *
 * @since 1.8
 * @return string
 */
function get_request_uri()
{
    if (!empty($_SERVER['REQUEST_URI']))
    {
        return $_SERVER['REQUEST_URI'];
    }
    else if (!empty($_SERVER['PHP_SELF']))
    {
        if (!empty($_SERVER['QUERY_STRING']))
        {
            return $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['PHP_SELF'];

    }
    elseif (!empty($_SERVER['SCRIPT_NAME']))
    {
        if (!empty($_SERVER['QUERY_STRING']))
        {
            return $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['SCRIPT_NAME'];

    }
    elseif (!empty($_SERVER['URL']))
    {     // May help IIS (not well tested)
        if (!empty($_SERVER['QUERY_STRING']))
        {
            return $_SERVER['URL'] .'?'. $_SERVER['QUERY_STRING'];
        }
        return $_SERVER['URL'];

    }
    else
    {
        pushClaroMessage('Warning: Could not find any of these web server variables: $REQUEST_URI, $PHP_SELF, $SCRIPT_NAME or $URL');
        return false;
    }
}

function get_path_info()
{
    if ( isset( $_SERVER['PATH_INFO'] ) && !empty($_SERVER['PATH_INFO']) )
    {
        return $_SERVER['PATH_INFO'];
    }
    else
    {
        return urldecode( get_slashed_argument( get_request_uri(), 
            'document/goto/index.php' ) );
    }
}

?>