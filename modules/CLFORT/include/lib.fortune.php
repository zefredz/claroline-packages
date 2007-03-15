<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
    
    require_once dirname(__FILE__) . '/class.fortune.php';

    define ( 'FORTUNE_DIRECTORY', dirname(__FILE__) . '/../fortune-files' );

    define ( 'FORTUNE_FILE_LIST', FORTUNE_DIRECTORY . '/index.dat' );
    
    function listFortuneFiles()
    {
        $handle = opendir( FORTUNE_DIRECTORY );
        $fileList = array();
        $ignore = array( '.', '..', 'index.dat', 'CVS' );
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

        $fd = fopen( FORTUNE_FILE_LIST, 'w' );
        fwrite( $fd, $contents );
        fclose( $fd );
    }

    function displayFileChooser( $fileList, $currentList )
    {
        $ret = '<h4>'.get_lang( 'Manage Fortune Files' ).'</h4>' . "\n";
        $ret .= '<form name="fortuneFiles" action="'
            .$_SERVER['PHP_SELF'].'" method="post">'
            . '<input type="hidden" name="cmd" value="saveList" />'
            ;

        $idx = 0;
        
        $ret .= '<table class="claroTable">';

        foreach ( $fileList as $file )
        {
            $ret .= '<tr><td>';
            
            
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
                . '</td><td>'
                . htmlspecialchars( $file ) . '</td>'
                ;
                
            $ret .= '<td>'
                . '<a class="claroCmd" href="'
                . $_SERVER['PHP_SELF'] .'?cmd=rqDeleteFile&amp;fileName='
                . rawurlencode($file).'">'
                . '<img src="'.get_icon('delete').'" alt="'.get_lang('Delete').'" />'
                . '</a>'
                . '</td>'
                ;
                
            $ret .= '</tr>' . "\n";

            $idx++;
        }
        
        $ret .= '</table>';

        $ret .= '<input type="submit" name="submit" value="'.get_lang('Ok').'" />';

        $ret .= '</form>';

        return $ret;
    }

    function displayFileAdder()
    {
        $ret = '<h4>'.get_lang( 'Add Fortune Files' ).'</h4>' . "\n";
        $ret .= '<form name="fortuneFiles" action="'
            .$_SERVER['PHP_SELF'].'" method="post"  enctype="multipart/form-data">'
            . '<input type="hidden" name="cmd" value="exAddFile" />'
            ;

        $ret .= '<label for="fortuneFile">Choose File:</label><br />'
            . '<input type="file" name="fortuneFile" id="fortuneFile" /><br />'
            ;

        $ret .= '<input type="submit" name="submit" value="'.get_lang('Ok').'" />';
        
        $ret .= '<a href="'.$_SERVER['PHP_SELF'].'">'
            . '<input type="button" name="cancel" value="'
            . get_lang('Cancel').'" onclick="window.location=\'\''
            . $_SERVER['PHP_SELF'].'" /></a>'
            ;

        $ret .= '</form>';

        return $ret;
    }
    
    function deleteFortuneFile( $file )
    {
        require_once get_path('includePath') . '/lib/fileManage.lib.php';
        
        if ( file_exists( FORTUNE_DIRECTORY . '/' . $file ) )
        {
            return claro_delete_file( FORTUNE_DIRECTORY . '/' . $file );
        }
        else
        {
            return claro_failure::set_failure('FILE_NOT_FOUND');
        }
    }
?>