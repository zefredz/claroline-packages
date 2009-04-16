<?php

if ( !isset($_SERVER['HTTP_REFERER']) )
{
    header("HTTP/1.0 403 Forbidden");
    die();
}
else
{
    $tmp = parse_url($_SERVER['HTTP_REFERER']);
    
    if ( dirname( $tmp['path'] ) !== dirname( $_SERVER['PHP_SELF'] ) ) {
        header("HTTP/1.0 403 Forbidden");
        var_dump($tmp);
        die();
    }
}

require_once dirname(__FILE__) . '/lib/urlgetcontents.lib.php';

header("Content-Type: text/xml");
echo url_get_contents($_REQUEST['url']);
