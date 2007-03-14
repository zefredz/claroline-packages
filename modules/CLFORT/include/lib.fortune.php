<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:
    
    require_once dirname(__FILE__) . '/class.fortune.php';

    define ( 'FORTUNE_DIRECTORY', dirname(__FILE__) . '/../fortune-files' );

    define ( 'FORTUNE_FILE_LIST', FORTUNE_DIRECTORY . '/index.dat' );

    function listFortuneFiles()
    {
        $handle = opendir( FORTUNE_DIRECTORY );
        $fileList = array();
        $ignore = array( '.', '..', 'index.dat' );
        while ( false !== ( $file = readdir( $handle ) ) )
        {
            if ( in_array( $file, $ignore )
                || is_dir( FORTUNE_DIRECTORY . '/' . $file ) )
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
        $ret = '<form name="fortuneFiles" action="'
            .$_SERVER['PHP_SELF'].'?cmd=saveList" method="post">'
            // . '<input type="hidden" name="cmd" value="saveList" />'
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
?>