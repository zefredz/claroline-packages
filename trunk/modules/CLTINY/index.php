<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

$tlabelReq = 'CLTINY';

require_once dirname(__FILE__) . '/../../claroline/inc/claro_init_global.inc.php';

require_once dirname(__FILE__) . '/lib/tiny.lib.php';
require_once dirname(__FILE__) . '/lib/datagrid.lib.php';

$tiny = new TinyUrl;
$dispAddForm = false;
$dispDeleteForm = false;
$message = '';

$allowedActionList = array( 'get' );

if ( claro_is_platform_admin() )
{
    $allowedActionList = array(
        'get', 'list', 'rqDelete', 'exDelete', 'rqAdd', 'exAdd'
    );
}

if ( array_key_exists( 'action', $_REQUEST ) )
{
    $action = in_array ( $_REQUEST['action'], $allowedActionList )
        ? $_REQUEST['action']
        : 'get'
        ;
}
else
{
    $action = 'get';
}

$tinyId = array_key_exists( 'tinyId', $_REQUEST )
    ? $_REQUEST['tinyId']
    : ''
    ;

$url = array_key_exists( 'url', $_REQUEST )
    ? $_REQUEST['url']
    : ''
    ;

if ( 'get' === $action )
{
    if ( !empty( $tinyId ) )
    {
        if ( false !== ( $url = $tiny->getUrl ( $tinyId ) ) )
        {
            claro_redirect( $url );
            exit();
        }
        else
        {
            header('HTTP/1.1 404 Not Found');
            claro_die( 'No url found for given id !' );
            exit();
        }
    }
    else
    {
        claro_die ( 'Missing tinyId !' );
        exit();
    }
}
else
{
    if ( 'rqAdd' === $action )
    {
        $dispAddForm = true;
    }
    elseif ( 'rqDelete' === $action )
    {
        $dispDeleteForm = true;
    }
    elseif ( 'exAdd' === $action )
    {
        if ( empty ( $url ) )
        {
            $message = 'Missing url !';
        }
        else
        {
            if ( false !== ( $tinyId = $tiny->create( $url ) ) )
            {
                $pathInfo = parse_url( get_path('rootWeb') );

                $tinyUrl = $pathInfo['scheme'] . '://' . $pathInfo['host']
                    . $_SERVER['PHP_SELF']
                    . '?tinyId='.rawurlencode($tinyId)
                    ;

                $message = 'Url added ! The tiny url for the document is : '
                    . '<a href="'.htmlspecialchars($tinyUrl).'">'
                    .htmlspecialchars($tinyUrl).'</a>'
                    ;

            }
            else
            {
                $message = 'Cannot add url !';
            }
        }
    }
    elseif ( 'exDelete' === $action )
    {
        if ( empty ( $tinyId ) )
        {
            $message = 'Missing id !';
        }
        else
        {
            if ( false !== ( $tiny->remove( $tinyId ) ) )
            {
                $message = 'Url removed !';

            }
            else
            {
                $message = 'Cannot remove url !';
            }
        }
    }
    elseif ( 'list' === $action )
    {
    }
    else
    {
        // impossible to go here
        trigger_error( 'Fatal Error invalid action', E_USER_ERROR );
    }

    $list = $tiny->listAll();
}

if ( $dispAddForm )
{
    $message .= '<h1>Add an Url</h1>'."\n"
        . '<form method="post" action="'
        . $_SERVER['PHP_SELF'] . '">'."\n"
        . '<input type="hidden" name="action" value="exAdd" />' . "\n"
        . '<label for="name">Url :</label><br />' . "\n"
        . '<input type="text" name="url" value="" />' . "\n"
        . '<input type="submit" name="submit" value="Submit" />'
        . '</form>' . "\n"
        ;
}
elseif ( $dispDeleteForm )
{
    if ( !empty( $tinyId )
        &&  ( false !== ( $url = $tiny->getUrl ( $tinyId ) ) ) )
    {
        $message .= '<form method="post" action="'
            . $_SERVER['PHP_SELF'] . '">'."\n"
            . '<input type="hidden" name="action" value="exDelete" />' . "\n"
            . '<input type="hidden" name="tinyId" value="'.$tinyId.'" />' . "\n"
            . 'Are your sure to delete the url ?' . '<br />'
            . '<input type="submit" name="submit" value="Yes" />'
            . '<a href="'.$_SERVER['PHP_SELF'].'?action=list"><input type="button" name="cancel" value="No" /></a>'
            . '</form>'
            ;
    }
    else
    {
        $message .= 'Missing or invalid id';
    }
}

if ( !empty( $message ) )
{
    $claroline->display->body->appendContent(claro_html_message_box( $message ));
}

$table = new HTML_Datagrid_Table;

$table->setTitle( get_lang('Tiny Urls') );

$dataFields = array(
    'tinyId' => 'TinyId',
    'url' => 'Original Url'
);

$table->setDataFields( $dataFields );

$pathInfo = parse_url( get_path('rootWeb') );

$dataUrls = array(
    'tinyId' => '<a href="'
        . $pathInfo['scheme'] . '://' . $pathInfo['host']
        . $_SERVER['PHP_SELF']
        . '?tinyId=%uu(tinyId)%'
        . '">%tinyId%'
        . '</a>'

);

$table->setDataUrls( $dataUrls );

$actionFields = array(
    'tinyUrl' => 'Tiny Url',
    'delete' => get_lang( 'Delete' )
);

$table->setActionFields( $actionFields );

$actionUrls = array(
    'tinyUrl' => '<a href="'
        . $pathInfo['scheme'] . '://' . $pathInfo['host']
        . $_SERVER['PHP_SELF']
        . '?tinyId=%uu(tinyId)%'
        . '">'
        . $pathInfo['scheme'] . '://' . $pathInfo['host']
        . $_SERVER['PHP_SELF']
        . '?tinyId=%uu(tinyId)%'
        . '</a>',
    'delete' => '<a href="'
        . $_SERVER['PHP_SELF']
        . '?action=rqDelete&amp;tinyId='
        . '%uu(tinyId)%">'
        . '<img src="'.get_icon('delete.gif').'" alt="['
        . get_lang( 'Delete' ) . ']"/></a>'
);

$table->setActionUrls( $actionUrls );

$footer = '<a class="claroCmd" href="'
    . $_SERVER['PHP_SELF']
    . '?action=rqAdd" " title="'.get_lang('Click here to add a new Url').'">'
    . '<img src="'.get_icon('new.gif').'" alt="'
    . get_lang('New url').'" />'
    . '&nbsp;'.get_lang('Add a new Url').'</a>'
    ;

$table->setFooter( $footer );

$table->setData( $list );

$claroline->display->body->appendContent($table->render());

echo $claroline->display->render();
