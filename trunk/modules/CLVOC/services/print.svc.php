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
    $dispPrint              = false;
    
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
    require_once dirname(__FILE__) . '/../lib/print/print.class.php';
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
    $san = new HTML_Sanitizer;
}
// }}}

// {{{ CONTROLLER
{    
    $allowedActions = array( 
        'print'           // print
    );
    
    // get request variables
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'showDict'
        ;

    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : null
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
    
    if ( 'print' == $action )
    {
        //$dispTitleDictionary = false;
        //$dispDictionary = false;
        $dispPrint = true;
        
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

    // display print
        if ( true == $dispPrint )
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