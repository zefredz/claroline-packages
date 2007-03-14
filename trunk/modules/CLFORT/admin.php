<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

    /**
     *
     * @version 0.1 $Revision$
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     *
     * @author Frederic Minne <zefredz@claroline.net>
     *
     * @package clfort
     *
     */

    require_once '../../claroline/inc/claro_init_global.inc.php';
    
    require_once dirname(__FILE__) . '/include/lib.fortune.php';
    
    if ( ! claro_is_platform_admin() )
    {
        claro_die( 'Not allowed' );
    }
    
    $messageList = array();
    $messageList['error'] = array();
    $messageList['info'] = array();
    $dispAddFileForm = false;
    
    $allowedCommandList = array(
        'saveList',
        'exAddFile',
        'rqAddFile'
    );
    
    $cmd = isset( $_REQUEST['cmd'] ) && in_array( $_REQUEST['cmd'], $allowedCommandList )
        ? $_REQUEST['cmd']
        : ''
        ;
        
    if ( !empty( $cmd ) )
    {
        switch ( $cmd )
        {
            case 'rqAddFile':
            {
                $dispAddFileForm = true;
            } break;
            case 'exAddFile':
            {
                require_once get_path('includePath') . '/lib/fileUpload.lib.php';
                require_once get_path('includePath') . '/lib/file.lib.php';
                
                if ( ! treat_uploaded_file( $_FILES['fortuneFile']
                    , FORTUNE_DIRECTORY
                    , ''
                    , 10000000 ) )
                {
                    $messageList['error'][] = claro_failure::get_last_failure();
                }
                
                chdir( dirname(__FILE__) );
                
            } break;
            case 'saveList':
            {
                $newFileList = isset ( $_REQUEST['fortune'] ) && is_array( $_REQUEST['fortune'] )
                    ? $_REQUEST['fortune']
                    : null
                    ;
                if ( ! is_null ( $newFileList ) )
                {
                    $fileList = listFortuneFiles();
                    $newList = checkFileList( $newFileList, $fileList );
                    
                    writeFileList( $newList );
                    
                    $messageList['info'][] = get_lang( 'List updated' );
                }
                
            } break;
            default:
            {
                $messageList['error'][] = get_lang( 'Unknown command' );
            }
        }
    }
    
    $fileList = listFortuneFiles();
    $currentList = currentFileList();
    
    require_once get_path('includePath') . '/claro_init_header.inc.php';
    
    echo claro_html_msg_list( $messageList );
    
    if ( $dispAddFileForm )
    {
        echo displayFileAdder();
    }
    else
    {
        echo '<p><a class="claroCmd" href="'
            . $_SERVER['PHP_SELF'].'?cmd=rqAddFile">'
            . '<img src="'.get_icon('new').'" alt="new" />'
            . get_lang('Add file')
            . '</a></p>'
            ;
    }
    
    echo '<br />';
    
    echo displayFileChooser( $fileList, $currentList );
    
    require_once get_path('includePath') . '/claro_init_footer.inc.php';
?>