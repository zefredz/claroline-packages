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
     
    define ( 'FORTUNE_DIRECTORY', dirname(__FILE__) . '/fortune-files' );
    
    define ( 'FORTUNE_FILE_LIST', FORTUNE_DIRECTORY . '/index.dat' );
    
    function listFortuneFiles()
    {
        $handle = opendir( FORTUNE_DIRECTORY );
        $fileList = array();
        $ignore = array( '.', '..', 'index.dat' );
        while ( false !== ( $file = readdir( $handle ) ) )
        {
            if ( in_array( $file, $ignore ) )
            {
                continue;
            }
            else
            {
                $fileList[] = $file;
            }
        }
        
        return $fileList;
    }
    
    function currentFileList()
    {
        if ( file_exists( FORTUNE_FILE_LIST ) )
        {
            $fileList = array();
            
            $contents = file( FORTUNE_FILE_LIST );
            
            foreach ( $contents as $file )
            {
                $file = trim( $file );
                
                if ( file_exists( FORTUNE_DIRECTORY . '/' . $file ) )
                {
                    $fileList[] = $file;
                }
            }
            
            return $fileList;
        }
        else
        {
            return false;
        }
    }
    
    function checkFileList( $newList, $fortuneFileList )
    {
        $ret = array();
        
        foreach ( $newList as $file )
        {
            if ( in_array( $file, $fortuneFileList ) )
            {
                $ret[] = $file;
            }
        }
        
        return $ret;
    }
    
    function writeFileList( $arr )
    {
        $contents = implode( "\n", $arr );
        
        $fd = fopen( FORTUNE_FILE_LIST );
        fwrite( $fd, $contents );
        fclose( $fd );
    }
    
    function displayFileChooser( $fileList, $currentList )
    {
        $ret = '<form name="fortuneFiles" action="'
            .$_SERVER['PHP_SELF'].'?cmd=saveList" method="get">'
            ;
            
        $idx = 0;
        
        foreach ( $fileList as $file )
        {
            if ( false === $currentList || in_array( $file, $currentList ) )
            {
                $checked = ' checked="checked"';
            }
            else
            {
                $checked = '';
            }
            
            $ret .= '<input type="checkbox" name="fortune['.$idx.']"'
                . ' value="' . $file . '"'
                . $checked.' />'
                . htmlspecialchars( $file ) . '<br />' . "\n"
                ;
                
            $idx++;
        }
        
        $ret .= '<input type="submit" name="submit" value="'.get_lang('Ok').'" />';
        
        $ret .= '</form>';
        
        return $ret;
    }
    
    require_once '../../claroline/inc/claro_init_global.inc.php';
    
    $messageList = array();
    $messageList['error'] = array();
    $messageList['info'] = array();
    
    $cmd = isset( $_REQUEST['cmd'] )
        ? $_REQUEST['cmd']
        : ''
        ;
        
    if ( !empty( $cmd ) )
    {
        switch ( $cmd )
        {
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
    
    echo displayFileChooser( $fileList, $currentList );
    
    require_once get_path('includePath') . '/claro_init_footer.inc.php';
?>
