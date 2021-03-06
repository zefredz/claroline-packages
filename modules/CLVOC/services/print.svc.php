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
        'printText',
        'printDict'
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
    
    // printText
    if ( 'printText' == $action )
    {
        if ( !is_null( $textId ) )
        {
            $dispPrintText = true;
            
            $glossaryText = new Glossary_Text( $connection, $GLOBALS['glossaryTables'] );
            $glossaryText->setId($textId);
            $glossaryText->load();
            
            $textTitle = $glossaryText->getTitle();
            $content = $glossaryText->getContent();
            $content = $san->sanitize( $content );
            
            $wordList = $glossaryText->getWordList();
            $glossaryWord = $glossaryText->getGlossary();
            
            if (! empty( $wordList ) )
            {
                $callback = $_SERVER['PHP_SELF'] 
                    . '?page=dict'
                    . '&amp;action=showDefs'
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.$dictionaryId:'')
                    ;
                    
                $highlighter = new Glossary_Print_Highlighter;
                $content = $highlighter->highlightList( $content
                    , $wordList
                    , $callback );
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot load text : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
        }
    }
    
    // printDict
    if ( 'printDict' == $action )
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
                $dispPrintDict = true;
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot load dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
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
        //impression du text
        if ( true == $dispPrintText )
        {
            $output .= '<p class="linkPrintWindow"><a href="javascript:window.print()">' . get_lang( 'Print this page' ) . '</a></p>';
            
            $output .= '<h1>' . $textTitle . '</h1>';
                        
            $output .= '<p class="glossaryText">'
                . nl2br( $content )
                . '</p>'
                . "\n"
                ;

            $output .= '<h1>' . get_lang( 'List vocabularies' ) . '</h1>';
                        
            $lastWord = '';
            $i = 1;
            
            $output .= '<dl class="glossaryWord">';
            foreach ( $glossaryWord as $word )
            {
                
                if( empty( $lastWord ) || $lastWord != $word['name'] )
                {
                    $i = 1;
                    $lastWord = $word['name'];
                    $output .= '<dt>' . $word['name'] . '</dt>';
                }
                
                    $output .= '<dd>' . $i . ')&nbsp;' . $word['definition'] . '</dd>';
                    $i++;
            }        
            $output .= '</dl>';
                
            $output .= '<p class="linkPrintWindow"><a href="javascript:window.print()">' . get_lang( 'Print this page' ) . '</a></p>';

        }
                
        //impression du dictionnaire
        if ( true == $dispPrintDict )
        {
            $output .= '<p class="linkPrintWindow"><a href="javascript:window.print()">' . get_lang( 'Print this page' ) . '</a></p>';
            
            if ( is_null( $dictionaryId ) || ! isset( $dictionaryInfo) )
            {
                $output .= '<h1>'.sprintf(get_lang('Dictionary : %s')
                    , get_lang('Default') ).'</h1>' . "\n";
            }
            else
            {
                $output .= '<h1>'.sprintf(get_lang('Dictionary : %s')
                    , htmlspecialchars($dictionaryInfo['name'])).'</h1>' . "\n";
            }
            
            $table = new HTML_Datagrid_Table;
            $table->setTitle( get_lang('Dictionaries') );
            $dataFields = array(
                'name' => get_lang( 'Title' ),
                'description' => get_lang( 'Description' )
            );
            $table->setDataFields( $dataFields );
            $table->setData( $dictionaryList );
            
            $output .= $table->render();
             
            $table = new HTML_Datagrid_Table;
            $table->setTitle( sprintf( get_lang('Entries in dictionary %s'), htmlspecialchars($dictionaryInfo['name'] ) )  );
            $dataFields = array(
                'name' => get_lang( 'Word' ),
                'definition' => get_lang( 'Definition' )
            );
            $table->setDataFields( $dataFields );
            $table->setData( $dict );
            
            $output .= $table->render();
            
            $output .= '<p class="linkPrintWindow"><a href="javascript:window.print()">' . get_lang( 'Print this page' ) . '</a></p>';
        }
    }
    else
    {
        // fatal error
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