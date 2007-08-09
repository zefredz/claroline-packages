<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    // vim>600: set foldmethod=marker:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }

// {{{ SCRIPT INITIALISATION
{ 
    // local variable initialisation
    //$isAllowedToEdit = claro_is_allowed_to_edit();
    
    // set diplay mode
    $dispPrintText              = false;
    $dispPrintDict              = false;
    $dispExportText             = false;
    $dispExportDict             = false;
    
    // set service state
    //$loadDictionary         = true;
    
    // set error variables
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = true;  // display back button on error
    $err                    = '';    // error string
    
    // load modules and libraries
    require_once dirname(__FILE__) . '/../lib/glossary/dictionary.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/dictionarylist.class.php';
    //require_once dirname(__FILE__) . '/../lib/print/print.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/text.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/highlighter.class.php';
    require_once dirname(__FILE__) . '/../lib/html/sanitizer.class.php';
    require_once dirname(__FILE__) . '/../lib/yml/yaml.lib.php';
    
}
// }}}
    
// {{{ MODEL
{
    $connection = new Claroline_Database_Connection;
    $dictionary = new Glossary_Dictionary( $connection, $GLOBALS['glossaryTables'] );
    $dictionaryList = new Glossary_Dictionary_List( $connection, $GLOBALS['glossaryTables'] );
    $list = new Glossary_Dictionary_List( $connection, $GLOBALS['glossaryTables'] );
    $san = new HTML_Sanitizer;
}
// }}}

// {{{ CONTROLLER
{    
    $allowedActions = array( 
        'exportText',
        'exportDict'
    );
    
    $allowedFormat = array( 
        'text'
        ,'csv'
        ,'yml'
    );
    
    // get request variables
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : NULL
        ;
        
    $format = ( isset( $_REQUEST['format'] ) 
            && in_array( $_REQUEST['format'], $allowedFormat ) )
        ? $_REQUEST['format']
        : NULL
        ;

    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : null
        ;
    
    $rootId =  ( is_null( $dictionaryId ) )
        ? 0
        : $dictionaryId
        ;
        
    $textId = isset( $_REQUEST['textId'] )
        ? (int) $_REQUEST['textId']
        : null
        ;

    if ( !is_null( $dictionaryId ) )
    {
        $dictionaryExists = $dictionaryList->dictionaryExists( $dictionaryId );
        
        if ( $dictionaryExists )
        {
            $dictionary->setId( $dictionaryId );
            $dictionaryInfo = $dictionaryList->getDictionaryInfo( $dictionaryId );
        }
        elseif ( $dictionaryId === 0 )
        {
            $dictionary->setId( $dictionaryId );
            $dictionaryInfo = null;
        }
        else
        {
            // Make sure we cannot do anything else
            $dictionaryInfo = null;
            $dictionaryId = null;
            $action = 'noAction';
            
            $err = 'Cannot find dictionary : %s'; 
            $reason = 'invalid id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            
            // $this->setOutput( MessageBox::FatalError( $errorMsg ) );
            
            $dispError = true;
            $fatalError = true;
            $dispErrorBoxBackButton = false;
        }
    }

    // exportText
    if ( 'exportText' == $action )
    {
        if ( !is_null( $textId ) )
        {
            $dispExportText = true;
            
            $glossaryText = new Glossary_Text( $connection, $GLOBALS['glossaryTables'] );
            $glossaryText->setId($textId);
            $glossaryText->load();
            
            $textTitle = $glossaryText->getTitle();
            
            $fileName = preg_replace('/\s+/', '_', $textTitle);

            $content = $glossaryText->getContent();        
            $content = $san->sanitize( $content );
            $glossaryWord = $glossaryText->getGlossary();
        }
        else
        {
            $dispError = true;
            $err = 'Cannot load text : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
        }
    }
        
    // exportDict
    if ( 'exportDict' == $action )
    {
        if ( !is_null( $dictionaryId ) )
        {
            
            $list->setRootId( $rootId );
            $dictionaryList = $list->getDictionaryList();
            $dictionary->setId( $dictionaryId );
            $dictionaryInfo = $list->getDictionaryInfo( $dictionaryId );
            $dict = $dictionary->getDictionary();
            
            if ( $connection->hasError() )
            {
                $dispError = true;
                $fatalError = true;
                
                $err = 'Dictionary cannot be loaded : %s'; 
                $reason = $connection->getError();
                
                $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
            }
            else
            {
                $dispExportDict = true;
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot load dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
        }
        
        if ( is_null( $dictionaryId ) || ! isset( $dictionaryInfo) )
        {
            $fileName = sprintf(get_lang('Dictionary_%s')
                , get_lang('Default') );
        }
        else
        {
            $fileName = sprintf(get_lang('Dictionary_%s')
                , htmlspecialchars($dictionaryInfo['name']) );
        }
    }
    
    if ( NULL == $action )
    {
        $err = 'Cannot find action : %s'; 
        $reason = 'invalid action';

        $errorMsg .= sprintf( $err, $reason ) . "\n";
        
        // $this->setOutput( MessageBox::FatalError( $errorMsg ) );
        
        $dispError = true;
        $fatalError = true;
        $dispErrorBoxBackButton = false;
    }
    
}
// }}}

// {{{ VIEW
{
    $output = '';

    if ( true == $dispError )
    {
        // display error
        $errorMessage =  '<h2>'
            . ( ( true == $fatalError ) 
                ? get_lang( 'Error (Fatal)' ) 
                : get_lang( 'Error' ) )
            . '</h2>'
            . "\n"
            ;
        
        $errorMessage .= '<p>'
            . htmlspecialchars($errorMsg) . '</p>' 
            . "\n"
            ;
        // display back link    
        // but back to where ???? (in case of fatal error)
        if ( true == $dispErrorBoxBackButton )
        {
            $errorMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict'
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                .'">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        if ( true == $fatalError )
        {
            $output .= MessageBox::FatalError( $errorMessage );
        }
        else
        {
            $output .= MessageBox::Error( $errorMessage );
        }
    }
    
    // no fatal error
    if ( true != $fatalError )
    {
        // Export text
        if ( true == $dispExportText )
        {
            // format text
            if ( 'text' == $format )
            {
                //Declaration du Header
                header("Content-type: application/force-download; charset=ISO-8859-1");
                header("Content-disposition: attachment; filename=".date('Ymd')."_".$fileName.".txt");
                
                $output .= $textTitle . "\n\n";
                            
                $output .= $content . "\n\n";

                $output .= get_lang( 'List vocabularies' ) . "\n\n";
                            
                $lastWord = '';
                $i = 1;
                
                foreach ( $glossaryWord as $word )
                {
                    
                    if( empty( $lastWord ) || $lastWord != $word['name'] )
                    {
                        $i = 1;
                        $lastWord = $word['name'];
                        $output .= '[ ' . $word['name'] . ' ]' ."\n";
                    }
                    
                        $output .= "\t" . $i . ') ' . $word['definition'] . "\n";
                        $i++;
                }        
                
                echo( $output );
                exit;
            }  
            
            // Format csv
            if ( 'csv' == $format )
            {
            }
            
            // Format yml
            if ( 'yml' == $format )
            {
            }
        }

        // Export dict
        if ( true == $dispExportDict )
        {
            // Format text
            if ( 'text' == $format )
            {
                //Declaration du Header
                header("Content-type: application/force-download; charset=ISO-8859-1");
                header("Content-disposition: attachment; filename=".date('Ymd')."_".$fileName.".txt");

                $output .= '[ ' . get_lang('Dictionaries') . ' ]' . "\n\n";
                
                $output .= '| ' . get_lang( 'Title' ) . ' |' . "\t" . '| ' . get_lang( 'Definition' ) . ' |' . "\n\n";
                            
                if( $dictionaryList ) 
                {
                    foreach ( $dictionaryList as $key )
                    {
                        $output .= $key['name'] . "\t" .' - '. "\t" . $key['description'] . "\n";
                    }        
                }
                else
                {
                    $output .= get_lang( 'Empty' ) . "\t" .' - '. "\t" . get_lang( 'Empty' ) . "\n";
                }
                
                $output .= "\n\n";
                
                $output .= '[ ' . sprintf( get_lang('Entries in dictionary %s'), htmlspecialchars($dictionaryInfo['name']) ) . ' ]' . "\n\n";
                
                $output .= '| ' . get_lang( 'Word' ) . ' |' . "\t" . '| ' . get_lang( 'Definition' ) . ' |' . "\n\n";
                
                if( $dict ) 
                {
                    foreach ( $dict as $key )
                    {
                        $output .= $key['name'] . "\t" .' - '. "\t" . $key['definition'] . "\n";
                    }
                }
                else
                {
                    $output .= get_lang( 'Empty' ) . "\t" .' - '. "\t" . get_lang( 'Empty' ) . "\n";
                }
                
                echo( $output );
                exit;
            }

            // Format csv
            if ( 'csv' == $format )
            {
            }
            
            // Format yml
            if ( 'yml' == $format )
            {
                $array = array();
                $array['Dictionary']['Name'] = 'mydict'; 
                $array['Dictionary']['Description'] = 'un joli dico'; 
                $array['Dictionary']['Tags'][0] =  array( 'Tag' => 'test','Description' => 'un essai');
                $array['Dictionary']['Tags'][1] =  array( 'Tag' => 'test2','Description' => 'un autre essai');
                $array['Dictionary']['Content'][0]['Word'] = 'lapin'; 
                $array['Dictionary']['Content'][0]['Definitions'][0]['Definition'] = 'animal avec des longues oreilles';
                $array['Dictionary']['Content'][0]['Definitions'][0]['Tags'] = 'test,test2'; 
                $array['Dictionary']['Content'][0]['Definitions'][1]['Definition'] = 'mange des carottes';  
                $array['Dictionary']['Content'][1]['Word'] = 'cigogne'; 
                $array['Dictionary']['Content'][1]['Definitions'][0]['Definition'] = 'apporte les bébés';
                
                $generator = new YAML_Generator;
                $yaml = $generator->generate( $array );
                
                header("Content-type: application/force-download; charset=ISO-8859-1");
                header("Content-disposition: attachment; filename=".date('Ymd')."_".$fileName.".yml");
                
                echo( $yaml );
                exit;
            }
        }    
    // fatal error
    }
    else
    {
        // fatal error nothing else to do...
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php'
        , 'name' => get_lang("Glossary"));
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=list'
        , 'name' => get_lang("Dictionary List"));
    
    if ( !is_null( $dictionaryId ) )
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=dict&amp;dictionaryId='.(int)$dictionaryId
            , 'name' => $dictionaryInfo['name']);
    }
    else
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=dict'
            , 'name' => get_lang("Dictionary"));
    }
    
    // send output to dispatcher
    $this->setOutput($output);
}
// }}}
?>