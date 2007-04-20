<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
    // vim>600: set foldmethod=marker:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
    if ( count( get_included_files() ) == 1 ) die( '---' );

// {{{ SCRIPT INITIALISATION
{ 
    // local variable initialisation
    $isAllowedToEdit = claro_is_allowed_to_edit();
    
    // set diplay mode
    $dispToolTitle = true; 
    $dispTextList = false;
    $dispAddTextForm = false;
    $dispEditTextForm = false;
    $dispConfirmDelText = false;
    $dispText = false;
    $dispCheckWord = false;
    $dispWordAdded = false;
    $dispConfirmDelWord = false;
    $dispWordDeleted = false;
    $dispDefinition = false;
    
    // set error variables
    $dispError = false;             // display error box
    $fatalError = false;            // if set to true, the script ends after 
                                    // displaying the error
    $errorMsg = '';                 // error message to display
    $dispErrorBoxBackButton = true; // display back button on error
    $err = '';                      // error string
    
    // load modules and libraries
    require_once dirname(__FILE__) . '/../lib/glossary/text.class.php';   
    require_once dirname(__FILE__) . '/../lib/glossary/dictionary.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/dictionarylist.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/highlighter.class.php';
    require_once dirname(__FILE__) . '/../lib/html/sanitizer.class.php';
}
// }}}

// {{{ MODEL
{
    $connection = new Claroline_Database_Connection;
    $text = new Glossary_Text( $connection, $GLOBALS['glossaryTables'] );
    $list = new Glossary_Dictionary_List( $connection, $GLOBALS['glossaryTables'] );
    $san = new HTML_Sanitizer;
}
// }}}

// {{{ CONTROLLER
{    
    // set access right
    if ( $isAllowedToEdit == true )
    {
        $allowedActions = array(
            // all users
             'listText'              // list texts
            ,'showText'             // show text
            // manager only
            , 'rqAddText'           // display form to add a word
            , 'exAddText'           // add word to dictionary
            , 'rqEditText'          // display form to add a word
            , 'exEditText'          // add word to dictionary
            , 'rqDelText'           // display add definition form
            , 'exDelText'           // add definition to dictionary
            , 'rqAddWord'           // check word already in wordlist/dictionary
            , 'exAddWord'           // add word to wordlist/dictionary
            , 'rqDelWord'
            , 'exDelWord'
            // , 'showDefs'
        );
    }
    else
    {
        $allowedActions = array( 
            // all users
              'listText'             // list texts
            , 'showText'             // show text
            // , 'showDefs'
        );
    }
    
    // get request variables
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'listText'
        ;
        
    $title = isset( $_REQUEST['title'] )
        ? trim( $_REQUEST['title'] )
        : get_lang( 'Untitled' )
        ;
        
    $title = empty( $title )
        ? get_lang( 'Untitled' )
        : $title
        ;
            
    $content = isset( $_REQUEST['content'] )
        ? trim( $_REQUEST['content'] )
        : get_lang( 'Empty' )
        ;
        
    $content = empty( $content )
        ? get_lang( 'Empty' )
        : $content
        ;
        
    $textId = isset( $_REQUEST['textId'] )
        ? (int) $_REQUEST['textId']
        : NULL
        ;
        
    $word = isset( $_REQUEST['word'] )
        ? trim($_REQUEST['word'])
        : NULL
        ;
    
    $definition = isset( $_REQUEST['definition'] )
        ? trim($_REQUEST['definition'])
        : NULL
        ;
        
    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : NULL
        ;
    // var_dump( $dictionaryId );
    
    /*if ( 'showText' == $action && is_null( $textId ) )
    {
        $action = 'listText';
    }*/
    
    if ( ! is_null( $textId ) )
    {
        $text->setId( $textId );
        // $text->load();
    }
    
    if ( 'showText' == $action )
    {
        if ( ! is_null( $textId ) )
        {
            $textFound = $text->load();
            
            $err = get_lang( 'Cannot load text : %s' );
            
            if ( false == $textFound )
            {
                $dispError = true;
                $err = get_lang( 'Cannot load text : not found' );
                $errorMsg .= sprintf( $err, $textId ) . "<br />\n";
                $dispTextList = true;
            }
            else
            {
                $dispText = true;
                
                $title = $text->getTitle();
                $content = $text->getContent();
                $dictionaryId = $text->getDictionaryId();
                pushClaroMessage( "dictId:$dictionaryId" );
            }
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot load Text : missing text id' ) . "<br />\n";
            $dispTextList = true;
        }
    }
    
    if ( 'listText' == $action )
    {
        $dispTextList = true;
    }
    
    if ( 'rqAddText' == $action )
    {
        $dispAddTextForm = true;
        $dictionaryList = $list->getAllDictionaries();
        $defaultDictionary = 0;
    }
    
    if ( 'rqAddWord' == $action )
    {
        if ( empty( $word ) )
        {
            $dispError = true;
            $errorMsg = get_lang( 'Word is empty' );
            $dispToolTitle = false;
        }
        else
        {
            $dispCheckWord = true;
            
            $dict = new Glossary_Dictionary( $connection, $GLOBALS['glossaryTables'] );
            
            $dict->setId( $dictionaryId );
            
            $wordId = $dict->getWordId( $word );
            
            if ( !is_null( $wordId ) )
            {
                $definitionList = $dict->getDefinitionList( $wordId );
                
                if ( ! empty( $definitionList ) )
                {
                    $wordInDict = true;
                }
                else
                {
                    $wordInDict = false;
                }
            }
            else
            {
                $wordInDict = false;
                $definitionList = array();
            }
        }
    }
    
    if ( 'exAddWord' === $action )
    {
        if ( empty( $word ) )
        {
            $dispError = true;
            $errorMsg = get_lang( 'Word is empty' );
            $dispToolTitle = false;
        }
        else
        {
            $textFound = $text->load();
            
            if ( false == $textFound )
            {
                $dispError = true;
                $err = get_lang( 'Cannot load text : not found' );
                $errorMsg .= sprintf( $err, $textId ) . "<br />\n";
                $dispTextList = true;
            }
            else
            {
                // 1. if word not in dict, add it
                $dict = new Glossary_Dictionary( $connection, $GLOBALS['glossaryTables'] );
                
                pushClaroMessage( "dictId:$dictionaryId" );
                $dict->setId( $dictionaryId );
                
                // $wordId = $dict->getWordId( $word );
                
                $successfulyAdded = false;
                
                $dict->addWord( $word, $definition );
                
                //if ( is_null( $wordId ) )
                {
                    if ( !empty( $definition )  )
                    {
                        if ( false !== $dict->addWord( $word, $definition ) )
                        {
                            $successfulyAdded = true;
                            $dispText = true;
                        }
                        else
                        {
                            $dispError = true;
                            $errorMsg .= get_lang( 'Cannot add "%word"'
                                , array( '%word' => htmlspecialchars($word) ) ) . "<br />\n";
                            $dispToolTitle = false;
                        }
                    }
                    else
                    {
                        $dispError = true;
                        $errorMsg = get_lang( 'Definition is empty' );
                        $dispToolTitle = false;
                    }
                }
                // 2. add word to word list
                
                if ( $successfulyAdded )
                {
                    $text->addWord( $word );
                    $text->save();
                    
                    $dispWordAdded = true;
                    $dispText = true;
                }
                else
                {
                    $dispError = true;
                    $errorMsg .= get_lang( 'Cannot add "%word"'
                        , array( '%word' => htmlspecialchars($word) ) ) . "<br />\n";
                    $dispToolTitle = false;
                }
            }
            
        }
    }
    
    if ( 'rqDelWord' == $action )
    {
        if ( empty( $word ) )
        {
            $dispError = true;
            $errorMsg = get_lang( 'Word is empty' );
            $dispToolTitle = false;
        }
        else
        {
            $dispConfirmDelWord = true;
        }
    }
    
    if ( 'exDelWord' == $action )
    {
        if ( empty( $word ) )
        {
            $dispError = true;
            $errorMsg = get_lang( 'Word is empty' );
            $dispToolTitle = false;
        }
        else
        {
            $textFound = $text->load();
            
            if ( false == $textFound )
            {
                $dispError = true;
                $err = get_lang( 'Cannot load text : not found' );
                $errorMsg .= sprintf( $err, $textId ) . "<br />\n";
                $dispTextList = true;
            }
            else
            {
                $text->deleteWord( $word );
                $text->save();
                $dispWordDeleted = true;
                $dispText = true;
            }
        }
    }
    
    if ( 'exAddText' == $action )
    {
        $dispTextList = true;
        $text->setTitle( $title );
        $text->setContent( $content );
        $text->setDictionaryId( $dictionaryId );
        $text->save();
        
        $err = get_lang( 'Cannot add text : %s' );
    }
    
    if ( 'rqEditText' == $action )
    {
        if ( ! is_null( $textId ) )
        {
            $textFound = $text->load( $textId );
            
            $err = get_lang( 'Cannot load text : %s' );
            
            if ( false == $textFound )
            {
                $dispError = true;
                $err = get_lang( 'Cannot load text : not found' );
                $errorMsg .= sprintf( $err, $textId ) . "<br />\n";
                $dispTextList = true;
            }
            else
            {
                $dispEditTextForm = true;
                $title = $text->getTitle();
                $content = $text->getContent();
                $dictionaryId = $text->getDictionaryId();
                $dictionaryList = $list->getAllDictionaries();
                
                $defaultDictionary = is_null($dictionaryId) ? 0 : $dictionaryId;
            }
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot load Text : missing text id' ) . "<br />\n";
            $dispTextList = true;
        }
    }
    
    if ( 'exEditText' == $action )
    {
        if ( ! is_null( $textId ) )
        {
            $dispText = true;
            $text->load();
            $text->setDictionaryId( $dictionaryId );
            $text->setTitle( $title );
            $text->setContent( $content );
            
            $text->save();
        
            $err = get_lang( 'Cannot modify text : %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot modify Text : missing text id' ) . "<br />\n";
            $dispTextList = true;
        }
    }
    
    if ( 'rqDelText' == $action )
    {
        if ( ! is_null( $textId ) )
        {
            $dispTextList = false;
            
            $textFound = $text->load();
            
            $err = get_lang( 'Cannot load text : %s' );
            
            if ( false == $textFound )
            {
                $dispError = true;
                $err = get_lang( 'Cannot load text : text with id %d not found' );
                $errorMsg .= sprintf( $err, $textId ) . "<br />\n";
                $dispTextList = true;
            }
            else
            {
                $dispConfirmDelText = true;
                $title = $text->getTitle();
                // $content = $text->getContent();
            }
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete Text : missing text id' ) . "<br />\n";
            $dispTextList = true;
        }
    }
    
    if ( 'exDelText' == $action )
    {
        if ( ! is_null( $textId ) )
        {
            $dispTextList = true;
            $text->delete();
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete Text : missing text id' ) . "<br />\n";
            $dispTextList = true;
        }
    }
    
    // generate connection error
    if ( $connection->hasError() )
    {
        $dispError = true;
        $reason = $connection->getError();
        $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
    }
    
    // get dictionary    
    if ( true != $fatalError )
    {
        $textList = $text->getList();
        
        if ( $connection->hasError() )
        {
            $dispError = true;
            $fatalError = true;
            
            $err = 'Text list cannot be loaded : %s'; 
            $reason = $connection->getError();
            
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
        }
    }
}
// }}}

// {{{ VIEW
{
    $output = '';
    
    if ( is_null( $textId ) )
    {    
        $output .= '<h1>'.get_lang('Text List').'</h1>' . "\n";
    }
    else
    {
        $output .= '<h1>'.sprintf(get_lang('Text : %s')
            , htmlspecialchars( $text->getTitle() ) ).'</h1>' . "\n";
    }
        
    $output .= displayGlossaryMenu();
    
    // TODO rewrite to use claro_disp_msg_arr or claro_disp_msg_box
    if ( true == $dispError )
    {
        // display error
        $errorMessage =  '<h2>'
            . ( ( true == $fatalError ) 
                ? get_lang( 'Fatal Error' ) 
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
                . '?page=text">['.get_lang('Back').']</a></p>'
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
        if ( true === $dispConfirmDelWord )
        {
            $confirmDelWord = '<p>'
                . get_lang( 'You are going to delete the word "%word"'
                    , array( '%word' => htmlspecialchars($word) ) )
                . '<br />'
                . "\n"
                ;
                
            $confirmDelWord .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=text&amp;action=exDelWord&amp;textId='
                . (int) $textId . '&amp;word='.urlencode( $word )
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=text&amp;action=showText&amp;textId='.$textId.'">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
             $output .= MessageBox::Question( $confirmDelWord );
        }
        
        if ( true === $dispWordAdded )
        {
            $output .= '<h2>'.get_lang('Add Word').'</h2>' . "\n";
            
            $msg = '<p>' . get_lang( '"%word" added successfuly'
                , array( '%word' => htmlspecialchars($word) )  )
                . '</p>' . "\n"
                ;
                
            $output .= MessageBox::Question( $msg );
        }
        
        if ( true == $dispCheckWord )
        {
            $output .= '<h2>'.get_lang('Add Word').'</h2>' . "\n";
            
            if ( $wordInDict )
            {
                $output .= '<p>';
                $output .= get_lang( '"%word" is in the dictionary.'
                    , array('%word' => htmlspecialchars($word) ) );
                $output .= '</p>' . "\n";
                
                $output .= 'Definitions found for "' . htmlspecialchars($word) . '" : <br :>' . "\n";
                $output .= '<ul>' . "\n";
                
                foreach ( $definitionList as $definition )
                {
                    $output .= '<li>' . htmlspecialchars($definition['definition']) . '</li>' . "\n";
                }
                
                $output .= '</ul>' . "\n";
                
                $addWordForm = '<p>' . get_lang( 'Add this word ?' );
                
                // No div class="formContainer" here
                $addWordForm .= '<form method="post" action="'.$_SERVER['PHP_SELF']
                    .'?page=text&amp;action=exAddWord&amp;textId='
                    .(int) $textId .'" name="addWordForm">' . "\n"
                    . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                    . '<input type="hidden" name="word" id="word" value="'.htmlspecialchars($word).'" />' . "\n"
                    . (!is_null($dictionaryId)
                    	? '<input type="hidden" name="dictionaryId" value="'.(int)$dictionaryId.'" />' :'' )
                    . '<input value="'.get_lang('Ok').'" type="submit" name="submitAddDefForm" />' . "\n"
                    . '<input value="'.get_lang('Cancel').'" onclick="window.location=\''
                    . $_SERVER['PHP_SELF']
                    . '?page=text&amp;action=showText&amp;textId='
                    . (int) $textId.'\'" type="button" name="cancelAddWordForm" />' . "\n"
                    . '</form>'
                    . "\n"
                    ;
                $addWordForm .= '</p>';
                
                $output .= MessageBox::Question( $addWordForm );
            }
            else
            {
                $output .= '<p>';
                $output .= get_lang( '"%word" does not seem to be in the dictionary.'
                    , array('%word' => htmlspecialchars($word) ) );
                $output .= '<br /><br />' . "\n";
                $output .= get_lang( 'You have to add a definition for this word by filling in the following form.' )
                    . '<br />' . "\n"
                    . '<br />' . "\n"
                    . get_lang( 'Press Add to add the word.' )
                    . '<br />' . "\n"
                    . get_lang( 'Press Cancel to cancel the operation.' )
                    ;
                $output .= '</p>' . "\n";
                
                $addDefForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                    .'?page=text&amp;action=exAddWord&amp;textId='
                    .(int) $textId .'" name="addDefForm">' . "\n"
                    . '<div class="row">' . "\n"
                    . '<label for="definition">'.get_lang('Definition').'&nbsp;:&nbsp;</label>' . "\n"
                    . '<textarea name="definition" id="definition" cols="60" rows="5"></textarea>' . "\n"
                    . '</div>' . "\n"
                    . '<div class="btnrow">' . "\n"
                    . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                    . '<input type="hidden" name="word" id="word" value="'.htmlspecialchars($word).'" />' . "\n"
                    . (!is_null($dictionaryId)
                    	? '<input type="hidden" name="dictionaryId" value="'.(int)$dictionaryId.'" />' :'' )
                    . '<input value="'.get_lang('Add').'" type="submit" name="submitAddDefForm" />' . "\n"
                    . '<input value="'.get_lang('Cancel').'" onclick="window.location=\''
                    . $_SERVER['PHP_SELF']
                    . '?page=text&amp;action=showText&amp;textId='
                    . (int) $textId.'\'" type="button" name="cancelAddDefForm" />' . "\n"
                    . '</div>' . "\n"
                    . '</form></div>'
                    . "\n"
                    ;
                $output .= $addDefForm;
            }
        }
        
        if ( true == $dispAddTextForm )
        {
            // $output .= '<h2>'.get_lang('Add Text').'</h2>' . "\n";
            
            $addTextForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=text&amp;action=exAddText" name="addTextForm" id="addWordForm">' . "\n"
                . '<fieldset>' . "\n"
                . '<legend>'.get_lang('Add Text').'</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="title">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label>'
                . '<input name="title" value="'.htmlspecialchars($title).'" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="dictionaryId">'.get_lang( 'Choose a dictionary' ).'&nbsp;:&nbsp;</label>'
                . dictionarySelector( $dictionaryList, $defaultDictionary )
                . '</div>'
                . '<div class="row">' . "\n"
                . '<label for="content">'.get_lang( 'Content' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="content" cols="80" rows="15">'.$content.'</textarea>' . "\n"
                . '</div>'
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Add').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=text\'" />' . "\n"
                . '</div>'
                . '</fieldset>' . "\n"
                . '</form></div>'
                . "\n"
                ;
                
            $output .= $addTextForm;
        }
        
        if ( true == $dispEditTextForm )
        {
            // $output .= '<h2>'.get_lang('Edit Text').'</h2>' . "\n";
            
            $ediTextForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=text&amp;action=exEditText&amp;textId='
                . (int) $textId . '" name="editTextForm" id="editTextForm">' . "\n"
                . '<fieldset>' . "\n"
                . '<legend>'.get_lang('Edit Text').'</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="title">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label>'
                . '<input name="title" value="'.$title.'" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="dictionaryId">'.get_lang( 'Choose dictionary' ).'&nbsp;:&nbsp;</label>'
                . dictionarySelector( $dictionaryList, $dictionaryId )
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="content">'.get_lang( 'Content' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="content" cols="80" rows="15">'.$content.'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Save').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF']
                . '?page=text&amp;action=showText&amp;textId='.$textId.'\'" />' . "\n"
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>'
                . "\n"
                ;
                
            $output .= $ediTextForm;
        }
        
        if ( true == $dispConfirmDelText )
        {
            // $output .= '<h2>'.htmlspecialchars( $title ).'</h2>' . "\n";
            $output .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=text">['.get_lang('Back to text list').']</a></p>'
                . "\n"
                ;
                
            $output .= '<h2>' . get_lang( 'Delete text' ) . '</h2>' . "\n";
                
            $confirmDelText = '<p>'
                . get_lang( 'You are going to delete the text :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelText .= htmlspecialchars( $title ) . '<br /><br />';
                
            $confirmDelText .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=text&amp;action=exDelText&amp;textId='
                . (int) $textId . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=text&amp;action=showText&amp;textId='.$textId.'">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelText );
        }
        
        if ( true == $dispText )
        {
            $title = $text->getTitle();
            // $output .= '<h2>'.htmlspecialchars( $title ).'</h2>' . "\n";
                            
            if ( $isAllowedToEdit )
            {
                $wordList = $text->getWordList();
                    
                $output .= '<h3>' . get_lang( 'Word list' ) . '</h3>' . "\n";
                    
                if ( count( $wordList ) > 0 )
                {
                    $list = array();
                    foreach ( $wordList as $word )
                    {
                        $list[] = htmlspecialchars( $word )
                            . ' '
                            . '<a href="'. $_SERVER['PHP_SELF'] 
                            . '?page=text&amp;action=rqDelWord&amp;word='
                            . rawurlencode( $word )
                            . '&amp;textId=' . $textId
   							. (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                            . '">'
                            . '<img src="'.get_icon('delete.gif').'" alt="['
                            . get_lang( 'Delete' ) . ']"/>'
                            . '</a>'
                            ;
                    }
                    
                    $output .= '<p>'.implode( ', ', $list ).'</p>' . "\n";
                }
                    
                $output .= '<script type="text/javascript" src="'
                    .$GLOBALS['moduleJavascriptRepositoryWeb'].'/glossary.js"></script>'
                    ;
                
                // No div class="formContainer" here
                $output .= '<form method="post" action="'.$_SERVER['PHP_SELF']
                    . '?page=text&amp;action=rqAddWord&amp;textId='
                    . (int) $textId .'" name="addWordForm">' . "\n"
                    . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                    . (!is_null($dictionaryId)
                    	? '<input type="hidden" name="dictionaryId" value="'.(int)$dictionaryId.'" />' :'' )
                    . '<input value="'.get_lang('Get selected text >>')
                    . '" onmousedown="addToList()" type="button" name="getSelection" />' . "\n"
                    . '<input type="text" name="word" id="word" value="" />' . "\n"
                    . '<input value="'.get_lang('Add to word list')
                    . '" type="submit" name="submitAddWordForm" />' . "\n"
                    . '</form>'
                    . "\n"
                    ;
            }
            
            // highlighter
            $content = $text->getContent();
            $wordList = $text->getWordList();
            $content = $san->sanitize( $content );
            
            if (! empty( $wordList ) )
            {
                $callback = $_SERVER['PHP_SELF'] 
                    . '?page=dict'
                    . '&amp;action=showDefs'
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.$dictionaryId:'')
                    ;
                    
                $content = Glossary_Highlighter::highlightList( $content
                    , $wordList
                    , $callback );
            }
            
            // display text
            
            $output .= '<h3>' . get_lang( 'Text' ) . '</h3>' . "\n";
                    
            if ( $isAllowedToEdit )
            {
                $output .= '<p class="claroCmd">'
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=text">'
                    . '<img src="'.get_icon('glossary_text.gif').'" alt="['
                    . get_lang( 'Text list' ) . ']"/>'
                    . '&nbsp;' . get_lang('Text list').'</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=text&amp;action=rqEditText&amp;textId='
                    . (int) $textId
                    . '">'
                    . '<img src="'.get_icon('edit.gif').'" alt="['
                    . get_lang( 'Edit' ) . ']"/>'
                    . '&nbsp;' . get_lang( 'Edit' )
                    . '</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=text&amp;action=rqDelText&amp;textId='
                    . (int) $textId
                    . '">'
                    . '<img src="'.get_icon('delete.gif').'" alt="['
                    . get_lang( 'Delete' ) . ']"/>'
                    . '&nbsp;' . get_lang( 'Delete' )
                    . '</a>'
                    . '</p>' . "\n"
                    ;
            }
            
            $output .= '<p class="glossaryText">'
                . nl2br( $content )
                . '</p>'
                . "\n"
                ;
        }
        
        if ( true == $dispTextList )
        {
            $table = new HTML_Datagrid_Table;
            
            $dataFields = array(
                'title' => get_lang( 'Title' ),
            );
            
            $table->setDataFields( $dataFields );
            
            $dataUrls = array(
                'title' => '<a href="' . $_SERVER['PHP_SELF'] 
                        . '?page=text&amp;action=showText&amp;textId='
                        . '%ACTION_FIELD%'
                        . '">'
                        . '%title%'
                        . '</a>'
            );
            
            $table->setDataUrls( $dataUrls );
            
            $table->displayActionField = false;
            
            if ( true === $isAllowedToEdit )
            {
                $actionFields = array(
                    'edit' => get_lang( 'Edit' ),
                    'delete' => get_lang( 'Delete' )
                );
                
                $table->setActionFields( $actionFields );
                
                $actionUrls = array(
                    'edit' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=text&amp;action=rqEditText&amp;textId='
                        . '%ACTION_FIELD%'
                        . '"'
                        . ' title="'.get_lang('Click here to edit').'">'
                        . '<img src="'.get_icon('edit.gif').'" alt="['
                        . get_lang( 'Edit' ) . ']"/>'
                        . '</a>',
                    'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=text&amp;action=rqDelText&amp;textId='
                        . '%ACTION_FIELD%'
                        . '"'
                        . ' title="'.get_lang('Click here to delete').'">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/>'
                        . '</a>'
                );
                
                $table->setActionUrls( $actionUrls );
                
                $footer = '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=text&amp;action=rqAddText"'
                    . ' title="'.get_lang('Click here to add a new text').'">'
                    . '<img src="'.get_icon('newtext.gif').'" alt="'
                    . get_lang('Click here to add a new text').'" />'
                    . '&nbsp;'.get_lang('Add a new text').'</a>'
                    ;
                    
                $table->setFooter( $footer );
            }
            
            $table->setData( $textList );
            
            $output .= $table->render();
        }
    }
    // fatal error
    else
    {
        // fatal error nothing else to do...
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php', 'name' => get_lang("Glossary"));
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=text', 'name' => get_lang("Texts"));
    
    if ( !is_null( $textId ) && false === $dispTextList )
    {
        $text->load();
        $title = htmlspecialchars( $text->getTitle() );
        $GLOBALS['interbredcrump'][]= array ( 'url' => null, 'name' => $title );
    }
    
    // send output to dispatcher
    $this->setOutput($output);
}
// }}}
?>