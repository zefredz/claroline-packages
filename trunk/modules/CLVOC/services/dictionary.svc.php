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
    $isAllowedToEdit = claro_is_allowed_to_edit();
    
    // set diplay mode
    $dispDictionary         = true;  // display dictionary
    $dispAddWordForm        = false; // display add word form
    $dispAddWordLink        = false; // display link to add word
    $dispEditForm           = false; // display edit form
    $dispEditLink           = false; // display link to edit word
    $dispEditWordForm       = false; // display edit word form
    $dispAddDefForm         = false; // display add definition form
    $dispConfirmDelWordDef  = false; // display delete entry confirmation message
    $dispEditDefForm        = false; // display definition edition form
    $dispConfirmDelWord     = false; // display delete word confirmation message
    $dispConfirmDelDef      = false; // display delete definition confirmation message
    $dispWordDefs           = false;
    $dispAdminWordLink      = false;
    $dispToolBar            = true;
    $dispBackToDictionary   = true;
    
    // set service state
    $loadDictionary         = true;
    
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
}
// }}}
    
// {{{ MODEL
{
    $connection = new Claroline_Database_Connection;
    $dictionary = new Glossary_Dictionary( $connection, $GLOBALS['glossaryTables'] );
    $dictionaryList = new Glossary_Dictionary_List( $connection, $GLOBALS['glossaryTables'] );
}
// }}}

// {{{ CONTROLLER
{    
    // set access right
    if ( $isAllowedToEdit == true )
    {
        $allowedActions = array(
            // all users
              'showDict'        // list words and definitions 
            // manager only
            , 'rqAddWord'       // display form to add a word
            , 'exAddWord'       // add word to dictionary
            , 'rqAddDef'        // display add definition form
            , 'exAddDef'        // add definition to dictionary
            , 'rqEdit'          // display word edit form
            , 'rqEditWord'      // display edit word form
            , 'exEditWord'      // modify word
            , 'rqEditDef'       // display edit definition form
            , 'exEditDef'       // save definition
            , 'rqDelWord'       // display are you sure box
            , 'exDelWord'       // delete word from dictionary
            , 'rqDelDef'        // display are you sure
            , 'exDelDef'        // delete definition from dictionary
            , 'rqDelWordDef'    // display are you sure
            , 'exDelWordDef'    // delete a def-word line from dictionary
            , 'showDefs'        // show definitions of a word
        );
        
        $dispAddWordLink = true;
        $dispEditLink = true;
    }
    else
    {
        $allowedActions = array( 
              'showDict'              // list words and definitions
            , 'showDefs'        // show definitions of a word
        );
    }
    
    // get request variables
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'showDict'
        ;

    $word = isset( $_REQUEST['word'] )
        ? trim( $_REQUEST['word'] )
        : ''
        ;
            
    $def = isset( $_REQUEST['def'] )
        ? trim( $_REQUEST['def'] )
        : ''
        ;

    /*$synList = isset( $_REQUEST['synonymList'] )
            && trim( $_REQUEST['synonymList'] ) != ''
        ? explode( ',', $_REQUEST['synonymList'] )
        : NULL
        ; */
        
    $synList = isset( $_REQUEST['synList'] )
            && is_array( $_REQUEST['synList'] )
        ? $_REQUEST['synList']
        : NULL
        ;
        
    $wordId = isset( $_REQUEST['wordId'] )
        ? (int) $_REQUEST['wordId']
        : NULL
        ;
        
    $defId = isset( $_REQUEST['defId'] )
        ? (int) $_REQUEST['defId']
        : NULL
        ;
        
    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : null
        ;
        
    $parentId = isset( $_REQUEST['parentId'] )
        ? (int) $_REQUEST['parentId']
        : null
        ;
        
    $dictionaryIdList = isset( $_REQUEST['dictionaryIdList'] )
        ? explode( ',', $_REQUEST['dictionaryIdList'] )
        : array( $dictionaryId )
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
    
    // resolve actions against the model (update model) and update view
    if ( 'showDefs' == $action )
    {
        if ( empty( $word ) )
        {
            $dispError = true;
            $errorMsg = get_lang( 'Word is empty' );
            $dispToolTitle = false;
        }
        else
        {
            $wordId = $dictionary->getWordId( $word );
            
            if ( !is_null( $wordId ) )
            {
                $word = $dictionary->getWord( $wordId );
                $definitionList = $dictionary->getDefinitionList( $wordId );
                $synonymList = array();
                foreach ( $definitionList as $definition )
                {
                    $defId = $definition['definitionId'];
                    $tmp = $dictionary->getSynonymList( $defId );
                    if ( count ( $tmp ) > 1 )
                    {
                        $synonymList[ $defId ] = implode (', ', $tmp );
                    }
                }
                
                $dispWordDefs = true;
                $dispDictionary = false;
                 
                if ( $isAllowedToEdit && ! $GLOBALS['inPopup'] )
                {
                    $dispAdminWordLink = true;
                }
            }
            else
            {
                $dispError = true;
                $errorMsg = get_lang( 'Word "%word"not found in dictionary'
                    , array( '%word' => htmlspecialchars( $word ) ) );
                $fatalError = true;
                $dispDictionary = false; 
                $loadDictionary = false;
            }
            
            // $inPopup = true;
        }
    }
    
    if ( 'rqDelDef' == $action )
    {
        $dispDictionary = false;
        $dispConfirmDelDef = true;
        $loadDictionary = false;
        
        if ( ! is_null( $defId ) )
        {
            $def = $dictionary->getDefinition( $defId );
            
            // set connection error message
            $err = get_lang( 'Cannot find definition : %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete word :  missing definition id' );
            
            $dispErrorBoxBackButton = true;
            $fatalError = true;
        }
    }
    
    if ( 'exDelDef' == $action )
    {
        $dispDictionary = true;
        
        if ( ! is_null( $defId ) )
        {
            $dictionary->deleteDefinition( $defId );
            
            // set connection error message
            $err = get_lang( 'Cannot delete definition :  %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete definition :  missing definitio id' );
            
            $dispErrorBoxBackButton = true;
            $fatalError = true;
        }
    }
    
    if ( 'exDelWord' == $action )
    {
        $dispDictionary = true;
        
        if ( ! is_null( $wordId ) )
        {
            $dictionary->deleteWord( $wordId );
            
            // set connection error message
            $err = get_lang( 'Cannot delete word :  %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete word :  missing word id' );
            
            $dispErrorBoxBackButton = true;
            $fatalError = true;
        }
    }
    
    if ( 'rqDelWord' == $action )
    {
        $dispDictionary = false;
        $dispConfirmDelWord = true;
        $loadDictionary = false;
        
        if ( ! is_null( $wordId ) )
        {
            $word = $dictionary->getWord( $wordId );
            
            // set connection error message
            $err = get_lang( 'Cannot find word : %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete word :  missing word id' );
            
            $dispErrorBoxBackButton = true;
            $fatalError = true;
        }
    }
    
    // delete entry confirmation message
    if ( 'rqDelWordDef' == $action )
    {
        $dispDictionary = false;
        $loadDictionary = false;
        $dispConfirmDelWordDef = true;
        
        if ( ! is_null( $wordId ) && ! is_null( $defId ) )
        {
            $word = $dictionary->getWord( $wordId );
            $def = $dictionary->getDefinition( $defId );
            
            // set connection error message
            $err = get_lang( 'Cannot entry definition : %s' );
        }
        else
        {
            $dispError = true;
            $err = get_lang( 'Cannot delete entry :  %s' );
            
            $reason = is_null( $defId )
                ? ( is_null( $wordId ) 
                    ? 'missing definition and word id'
                    : 'missing definition id' )
                : 'missing word id'
                ;
            
            $errorMsg .= sprintf( $err, $reason );
            $dispErrorBoxBackButton = true;
            $fatalError = true;
        }
    }
    
    // edit definition
    if ( 'rqEditDef' == $action )
    {
        if ( ! empty( $defId ) )
        {
            $dispDictionary = false;
            $loadDictionary = false;
            $dispEditDefForm = true;
            
            $def = $dictionary->getDefinition( $defId );
            
            $tmp = $dictionary->getSynonymList( $defId );
            if ( !empty ( $tmp ) )
            {
                $synonymList = $tmp;
            }
            else
            {
                $synonymList = '';
            }
            
            // set connection error message
            $err = get_lang( 'Cannot find definition : %s' );
        }
        else
        {
            $dispDictionary = true;
            
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot modify definition :  missing definition id' );
            $dispErrorBoxBackButton = false;
        }
    }
    
    // save definition modification in database
    if ( 'exEditDef' == $action )
    {
        $dispDictionary = true;
        
        if ( ! is_null( $defId ) && ! empty( $def ) )
        {
            $dictionary->modifyDefinition( $defId, $def );
            
            if ( !empty( $synList ) )
            {
                $dictionary->addSynonymList( $defId, $synList, $def );
            }
            
            // set connection error message
            $err = get_lang( 'Cannot modify definition :  %s' );
        }
        else
        {
            $dispError = true;
            $err = get_lang( 'Cannot modify definition :  %s' );
            
            $reason = is_null( $defId )
                ? ( empty( $def ) 
                    ? 'missing definition id and definition empty'
                    : 'missing definition id' )
                : 'definition empty'
                ;
            
            $errorMsg .= sprintf( $err, $reason );
            $dispErrorBoxBackButton = false;
        }
    }
    
    if ( 'rqEditWord' == $action )
    {
        $dispEditForm = true;
        $dispDictionary = false;
        $loadDictionary = false;
        $dispEditWordForm = true;
    }
    
    // add def form
    if ( 'rqAddDef' == $action )
    {
        $dispEditForm = true;
        $dispDictionary = false;
        $loadDictionary = false;
        $dispAddDefForm = true;
    }
    
    // add word form
    if ( 'rqAddWord' == $action )
    {
        $action = 'show';
        $dispAddWordForm = true;
        $dispAddWordLink = false;
    }
    
    // delete dictionary entru
    if ( 'exDelWordDef' == $action )
    {
        $dispDictionary = true;
        
        if ( ! is_null( $wordId ) && ! is_null( $defId ) )
        {
            $dictionary->deleteWordDefinition( $wordId, $defId );
            
            // set connection error message
            $err = get_lang( 'Cannot delete entry :  %s' );
            
            if ( ! $connection->hasError() )
            {
                /*
                 * If there is still a definition for this word in the dictionary
                 *  display edit form
                 * Else back to dictionary
                 */
                if ( $dictionary->wordExists( $wordId ) )
                {
                    $dispEditForm = true;
                    $dispDictionary = false;
                    $action = 'rqEdit';
                }
                else
                {
                    header( "Location: " . $_SERVER['PHP_SELF'] 
                        . '?page=list&action=showDict&dictionaryId=' 
                        . (int) $dictionaryId );
                }
            }
            
        }
        else
        {
            $dispError = true;
            $err = get_lang( 'Cannot delete entry :  %s' );
            
            $reason = is_null( $defId )
                ? ( is_null( $wordId ) 
                    ? 'missing definition and word id'
                    : 'missing definition id' )
                : 'missing word id'
                ;
            
            $errorMsg .= sprintf( $err, $reason );
            $dispErrorBoxBackButton = false;
        }
    }
    
    // modify a word in dictionary
    if ( 'exEditWord' == $action )
    {
        if ( ! is_null( $wordId ) )
        {
            $dispEditForm = true;
            $dispDictionary = false;
            $loadDictionary = false;
            
            if ( ! empty( $word ) )
            {
                $dictionary->modifyWord( $wordId, $word );
                
                // set connection error message
                $err = get_lang( 'Cannot add definition : %s' );
            }
            else
            {
                $dispError = true;
                $errorMsg .= get_lang( 'Cannot modify word :  word empty' );
                $dispErrorBoxBackButton = false;
            }
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot modify word :  missing word id' );
            $dispErrorBoxBackButton = false;
        }
    }
    
    // add word or definition
    if ( 'exAddWord'  == $action )
    {
        if ( (!empty( $word )) && (!empty($def)) )
        {
            $wordId = $dictionary->addWord( $word, $def );
            $defId = $dictionary->getDefinitionId( $def );
            
            if ( !empty( $synList ) )
            {
                if ( ! in_array( $word, $synList ) )
                {
                    $synList[] = $word;
                }
                
                $dictionary->addSynonymList( $defId, $synList, $def );
            }
            
            // set connection error message
            $err = get_lang( 'Word cannot be added : %s' );
            
            $dispEditForm = true;
            $dispDictionary = false;
        }
        else
        {
            $dispError = true;
            
            $err = get_lang( 'Word cannot be added : %s' );
            
            $reason = ( empty( $word ) 
                ? ( empty( $def ) 
                    ? get_lang('form empty') 
                    : get_lang('missing word') ) 
                : get_lang('missing definition') )
                ;
                
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
            
            $dispAddWordForm = true;
            $dispErrorBoxBackButton = false;
        }
    }
    
    // add definition to a word
    if ( 'exAddDef' == $action )
    {
        if ( ! is_null( $wordId ) )
        {
            $dispEditForm = true;
            $dispDictionary = false;
            $loadDictionary = false;
            
            $wordName = $dictionary->getWord( $wordId );
            
            if ( empty ( $def ) )
            {
                $dispError = true;
                $dispAddDefForm = true;
                $errorMsg .= get_lang( 'Cannot add definition :  missing definition' );
                $dispErrorBoxBackButton = false;
            }
            else
            {
                $wordId = $dictionary->addWord( $wordName, $def );
                $defId = $dictionary->getDefinitionId( $def );
                
                if ( !empty( $synList ) )
                {
                    $synList[] = $wordName;
                    $dictionary->addSynonymList( $defId, $synList, $def );
                }
                
                $dispEditForm = true;
                $dispDictionary = false;
                
                // set connection error message
                $err = get_lang( 'Cannot add definition : %s' );
            }
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot add definition :  missing word id' );
            $dispErrorBoxBackButton = false;
        }
    }
    
    // get definition list for a word
    if ( 'rqEdit' == $action 
        || 'rqEditWord' == $action 
        || 'exEditWord' == $action
        || 'rqAddDef' == $action
        || 'exAddDef' == $action
        || 'exAddWord' == $action )
    {        
        if ( ! is_null( $wordId ) )
        {
            $dispEditForm = true;
            $dispDictionary = false;
            $loadDictionary = false;
            
            $wordName = $dictionary->getWord( $wordId );
            
            $definitionList = $dictionary->getDefinitionList( $wordId );
            
            $synonymList = array();
            
            foreach ( $definitionList as $definition )
            {
                $defId = $definition['definitionId'];
                $tmp = $dictionary->getSynonymList( $defId );
                if ( count ( $tmp ) > 1 )
                {
                    $synonymList[ $defId ] = implode (', ', $tmp );
                }
            }
            
            // set connection error message
            $err = get_lang( 'Cannot load definitions : %s' );
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot edit word :  missing word id' );
            $dispErrorBoxBackButton = false;
        }
    }
    
    // delete a word
    if ( 'exDelWord' == $action )
    {            
        if ( ! is_null( $wordId ) )
        {
            $dictionary->deleteWord( $wordId );
            
            // set connection error message
            $err = 'Word cannot be deleted : %s';
        }
        else
        {
            $dispError = true;
            $errorMsg .= get_lang( 'Cannot delete word : missing word id' );
            $dispErrorBoxBackButton = false;
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
    if ( true != $fatalError && true === $loadDictionary )
    {
        $dict = $dictionary->getDictionary();
        
        if ( $connection->hasError() )
        {
            $dispError = true;
            $fatalError = true;
            
            $err = 'Dictionary cannot be loaded : %s'; 
            $reason = $connection->getError();
            
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
        }
    }
    
    if ( true === $GLOBALS['inPopup'] )
    {
        $dispToolBar = false;
        $dispBackToDictionary = false;
    }
}
// }}}

// {{{ VIEW
{
    $output = '';
    
    if ( is_null( $dictionaryId ) || 0 === $dictionaryId )
    {
        $output .= '<h1>'.get_lang('Dictionary').'</h1>' . "\n";
    }
    else
    {
        $output .= '<h1>'.sprintf(get_lang('Dictionary : %s')
            , htmlspecialchars($dictionaryInfo['name'])).'</h1>' . "\n";
    }
    
    if ( true == $dispToolBar )
    {
        $output .= displayGlossaryMenu();
    }
    
    if ( true === $dispBackToDictionary )
    {
        $output .= '<a href="'
            . $_SERVER['PHP_SELF'] 
            . '?page=list&amp;action=showDict&amp;dictionaryId='
            . (int) $dictionaryId
            . '" class="claroCmd">'
            . '<img src="'.get_icon('parent.gif').'" alt="[back]" />'
            . get_lang( 'Back to dictionary' )
            . '</a>'
            ;
    }
    
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
        // display definition list in popup
        if( true == $dispWordDefs )
        {
            $output .= '<h2>' . htmlspecialchars( $word ) . '</h2>';
            
            if ( $dispAdminWordLink )
            {
                $output .= '<p>' . '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=rqEdit&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '"'
                    . ' title="'.get_lang('Click here to edit').'">'
                    . '<img src="'.get_icon('edit.gif').'" alt="['
                    . get_lang( 'Edit' ) . ']"/> '
                    . get_lang( 'Edit' )
                    . '</a>'
                    . '</p>' . "\n"
                    ;
            }
            
            $output .= '<ul>' . "\n";
            
            if ( !empty( $definitionList ) )
            {
                foreach ( $definitionList as $definition )
                {
                    $defId = $definition['definitionId'];
                    $synList = array_key_exists( $defId, $synonymList )
                        ? '<br />(' . get_lang( 'Variants' ) 
                            . ' : ' . htmlspecialchars( $synonymList[$defId] ) . ')'
                        : ''
                        ;
                    $output .= '<li>' . htmlspecialchars($definition['definition']) 
                        . $synList . '</li>' 
                        . "\n"
                        ;
                }
            }
            else
            {
                $output .= '<li>' . get_lang( 'No definition found' ) . '</li>';
            }
            
            $output .= '</ul>' . "\n";
        }
        
        // display add word form
        if ( true == $dispAddWordForm )
        {
            $addWordForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=dict&amp;action=exAddWord'
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '" name="addWordForm" id="addWordForm">' . "\n"
                . '<fieldset id="addWord">' . "\n"
                . '<legend>'.get_lang( 'Add Word' ).'</legend>'
                . '<div class="row">' . "\n"
                . '<label for="word">'.get_lang( 'Word' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<input name="word" value="'.$word.'" type="text" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="def">'.get_lang( 'Definition' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="def" cols="60" rows="5">'.$def.'</textarea>' . "\n"
                . '</div>' . "\n"
                /*. '<div class="row">' . "\n"
                . '<label for="synonymList">'.get_lang( 'Variants' ).'&nbsp;:&nbsp;'
                . '</label>' . "\n"
                . '<small><em>(' .get_lang('Coma separated word list'). ')</em></small><br />'
                . '<textarea name="synonymList" cols="60" rows="5"></textarea>' . "\n"
                . '</div>' . "\n"  */
                . '<div class="row">' . "\n"
                . '<label for="add_synList">'.get_lang( 'Variants' ).'&nbsp;:&nbsp;</label>'
                . '<script type="text/javascript">'  . "\n"
                . 'var synList = new ItemListObject(\'synList\');' . "\n"
                . 'synList.addItemButton(\'synList\');' . "\n"
                . '</script>' . "\n"
                . '</div>'
                . '<div class="row">' . "\n"
                . '<label for="synList">&nbsp;</label>'
                . '<table class="claroTable"><tbody id="synList">' . "\n"
                . '</tbody></table>' . "\n"
                . '<script type="text/javascript">synList.renderList(\'synList\');</script>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Add').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=list&amp;action=showDict'
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '\'" />' . "\n"
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>'
                . "\n"
                ;

            $output .= $addWordForm;
        }
        
        // display edit word form
        if ( true == $dispEditForm )
        {
            $editForm = '<h2>'.get_lang( 'Edit Dictionary Entry' ).'</h2>'
                . "\n"
                ;
                
            if ( true == $dispEditWordForm )
            {
                $editForm .= '<div class="formContainer"><form method="POST" action="'
                    . $_SERVER['PHP_SELF'] . '?page=dict&amp;action=exEditWord&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '">' . "\n"
                    . '<fieldset id="addDef">'
                    . '<legend>'.get_lang( 'Edit Word' ).'</legend>'
                    . '<div class="row">' . "\n"
                    . '<label for="def">'.get_lang( 'Word' ).'&nbsp;:&nbsp;</label>'
                    . '<input type="text" name="word" id="word" value="'
                    . htmlspecialchars( $wordName ) . '" />' 
                    . '</div>' . "\n"
                    . '<div class="btnrow">' . "\n"
                    . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                    . '<input type="submit" name="submit" value="Save" />'
                    . '&nbsp;<input type="button" name="cancel" value="Cancel"'
                    . ' onclick="window.location=\''
                    . $_SERVER['PHP_SELF'].'?page=dict&action=rqEdit&wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    .'\'" />'
                    . '</div>' . "\n"
                    . '</fieldSet>'
                    . '</form></div>'
                    . "\n"
                    ;
            }
            else
            {
                $editForm .= '<h3>'.get_lang('Word').'</h3>'
                    . "\n"
                    . '<div class="glossaryWord">'
                    . $wordName
                    . '</div>'
                    ;
                    
                $editForm .= '<p>'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=rqEditWord&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    .'">'
                    . '<img src="'.get_icon('edit.gif').'" alt="['
                    . get_lang( 'Edit word' ) . ']"/>&nbsp;'
                    . get_lang( 'Edit this word' ) . '</a>'
                    . '&nbsp;|&nbsp;<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=rqDelWord&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    .'">'
                    . '<img src="'.get_icon('delete.gif').'" alt="['
                    . get_lang( 'Delete word' ) . ']"/>&nbsp;'
                    . get_lang( 'Delete this word' )
                    . '</a></p>'
                    . "\n"
                    ;
            }
            
            if ( true == $dispAddDefForm )
            {
                $addDefForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=exAddDef&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '" name="addDefForm" id="addDefForm">'
                    . '<fieldset id="addDef">'
                    . '<legend>'.get_lang( 'Add Definition' ).'</legend>'
                    . '<div class="row">' . "\n"
                    . '<label for="def">'.get_lang( 'Definition' ).'&nbsp;:&nbsp;</label>'
                    . '<textarea name="def" cols="60" rows="5"></textarea>'
                    . '</div>' . "\n"
                    . '<div class="row">' . "\n"
                    . '<label for="synonymList">'.get_lang( 'Variants' ).'&nbsp;:&nbsp;</label>'
                    . '<small><em>(' .get_lang('Coma separated word list'). ')</em></small><br>'
                    . '<textarea name="synonymList" cols="60" rows="5"></textarea>'
                    . '</div>' . "\n"
                    . '<div class="btnrow">' . "\n"
                    . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                    . '<input name="submit" value="'.get_lang('Add').'" type="submit" />&nbsp;'
                    . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                    . 'onclick="window.location=\''.$_SERVER['PHP_SELF']
                    . '?page=dict&action=rqEdit&wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '\'" />'
                    . '</div>' . "\n"
                    . '</fieldset>'
                    . '</form></div>'
                    . "\n"
                    ;

                $editForm .= $addDefForm;
            }
                
            $output .= $editForm;
            
            if ( count ( $definitionList ) > 0 )
            {
                // reformat array for output
                // TODO extract from view
                foreach ( $definitionList as $key => $definition )
                {
                    if ( array_key_exists( $definition['definitionId'], $synonymList ) )
                    {
                        $definitionList[$key]['variants'] = $synonymList[$definition['definitionId']];
                    }
                    else
                    {
                        $definitionList[$key]['variants'] = null;
                    }
                }
                
                $table = new HTML_Datagrid_Table;
                
                $table->setTitle( get_lang('Definitions')  );
                
                $table->setData( $definitionList );
                
                $dataFields = array(
                    'definition' => get_lang( 'Definition' ),
                    'variants' => get_lang( 'Variants' )
                );
                
                $table->setDataFields( $dataFields );
                
                $table->setActionField( 'definitionId' );
                
                $table->displayActionField = false;
                
                $actionFields = array(
                    'edit' => get_lang( 'Edit' ),
                    'delete' => get_lang( 'Delete' )
                );
                
                $table->setActionFields( $actionFields );
                
                $actionUrls = array(
                    'edit' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=dict&amp;action=rqEditDef&amp;defId='
                        . '%ACTION_FIELD%'
                        . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        . '">'
                        . '<img src="'.get_icon('edit.gif').'" alt="['
                        . get_lang( 'Edit' ) . ']"/></a>',
                    'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=dict&amp;action=rqDelWordDef&amp;wordId='
                        . (int) $wordId . '&amp;defId='
                        . '%ACTION_FIELD%'
                        . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        . '">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/></a>'
                );
                
                $table->setActionUrls( $actionUrls );
                
                $footer = '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=rqAddDef&amp;wordId='
                    . (int) $wordId
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '"'
                    . ' title="'.get_lang('Click here to add a new word').'">'
                    . '<img src="'.get_icon('new.gif').'" alt="'
                    . get_lang('Click here to add a new definition').'" />'
                    . '&nbsp;'.get_lang('Add a new definition').'</a>'
                    ;
                    
                $table->setFooter( $footer );
                
                $output .= $table->render();
            }
        } 

        // display confirm delete entry
        if ( true == $dispConfirmDelWordDef )
        {
            $output .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=rqEdit&amp;wordId='
                . (int) $wordId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
                
            $output .= '<h2>' . get_lang( 'Delete entry' ) . '</h2>' . "\n";
                
            $confirmDelWordDef = '<p>'
                . get_lang( 'You are going to delete the following entry :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelWordDef .= $word . ' : ' . $def . '<br /><br />';
                
            $confirmDelWordDef .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=exDelWordDef&amp;wordId='
                . (int) $wordId . '&amp;defId='
                . (int) $defId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'')  
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=rqEdit&amp;wordId='
                . (int) $wordId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelWordDef );
        }
        
        if ( true == $dispConfirmDelWord )
        {
            $output .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=rqEdit&amp;wordId='
                . (int) $wordId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
                
            $output .= '<h2>' . get_lang( 'Delete word' ) . '</h2>' . "\n";
                
            $confirmDelWord = '<p>'
                . get_lang( 'You are going to delete the following word :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelWord .= $word . '<br /><br />';
                
            $confirmDelWord .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=exDelWord&amp;wordId='
                . (int) $wordId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=rqEdit&amp;wordId='
                . (int) $wordId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'')  
                . '">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelWord );
        }
        
        if ( true == $dispConfirmDelDef )
        {
            $output .= '<h2>' . get_lang( 'Delete definition from dictionary' ) . '</h2>' . "\n";
                
            $confirmDelDef = '<p>'
                . get_lang( 'You are going to delete the following definition :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelDef .= $def . '<br /><br />';
                
            $confirmDelDef .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=exDelDef&amp;defId='
                . (int) $defId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict'
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelDef );
        }
        
        if ( true == $dispEditDefForm )
        {
            $editDefForm = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=dict&amp;action=exEditDef&amp;defId='
                . (int) $defId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '" name="editDefForm" id="editDefForm">'
                . '<fieldset id="addDef">'
                . '<legend>' . get_lang( 'Edit definition' ) . '</legend>'
                . '<div class="row">' . "\n"
                . '<label for="def">'.get_lang( 'Definition' ).'&nbsp;:&nbsp;</label>'
                . '<textarea name="def"  id="def" cols="60" rows="5">'.$def.'</textarea>'
                . '</div>' . "\n"
                /*. '<div class="row">' . "\n"
                . '<label for="synonymList">'.get_lang( 'Variants' ).'&nbsp;:&nbsp;</label>'
                . '<small><em>(' .get_lang('Coma separated word list'). ')</em></small><br>'
                . '<textarea name="synonymList" id="synonymList" cols="60" rows="5">'
                . htmlspecialchars( implode(', ', $synonymList ) ) . '</textarea>'
                . '</div>' . "\n"        */
                . '<div class="row">' . "\n"
                . '<label for="add_synList">'.get_lang( 'Variants' ).'&nbsp;:&nbsp;</label>'
                . '<script type="text/javascript">'  . "\n"
                . 'var synList = new ItemListObject(\'synList\');' . "\n"
                . 'synList.setItemList([\''. implode('\',\'', $synonymList ).'\'])' . "\n"
                . 'synList.addItemButton(\'synList\');' . "\n"
                . '</script>' . "\n"
                . '</div>'
                . '<div class="row">' . "\n"
                . '<label for="synList">&nbsp;</label>'
                . '<table class="claroTable"><tbody id="synList">' . "\n"
                . '</tbody></table>' . "\n"
                . '<script type="text/javascript">synList.renderList(\'synList\');</script>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Save').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF']
                . '?page=dict'
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '\'" />'
                . '</div>' . "\n"
                . '</fieldset>'
                . '</form>'
                . '<a class="claroCmd" href="'
                . $_SERVER['PHP_SELF']
                . '?page=dict&amp;action=rqDelDef&amp;defId='
                . (int) $defId
                . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                .'">'
                . '<img src="'.get_icon('delete.gif').'" alt="['
                . get_lang( 'Delete definition' ) . ']"/>&nbsp;'
                . get_lang( 'Delete this definition from dictionary' )
                . '</a>'
                . '</div>'
                . "\n"
                ;
                
            $output .= $editDefForm;
        }
        
        // display dictionary
        if ( true == $dispDictionary )
        {
            $table = new HTML_Datagrid_Table;
            
            $table->setTitle( sprintf( get_lang('Entries in dictionary %s'), htmlspecialchars($dictionaryInfo['name'] ) )  );
            
            $dataFields = array(
                'name' => get_lang( 'Word' ),
                'definition' => get_lang( 'Definition' )
            );
            
            $table->setDataFields( $dataFields );
            
            $table->setActionField( 'wordId' );
            
            $dataUrls = array(
                'name' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=dict&amp;action=showDefs&amp;word='
                        . '%uu(name)%'
                        . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'') 
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        .'">'
                        . '%name%'
                        . '</a>'
            );
            
            $table->setDataUrls( $dataUrls );
            
            $table->displayActionField = false;
            
            if ( true === $isAllowedToEdit )
            {
                $actionFields = array(
                    'edit' => get_lang( 'Edit' ),
                    // 'delete' => get_lang( 'Delete' )
                );
                
                $table->setActionFields( $actionFields );
                
                $actionUrls = array(
                    'edit' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=dict&amp;action=rqEdit&amp;wordId='
                        . '%ACTION_FIELD%'
                        . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        . '">'
                        . '<img src="'.get_icon('edit.gif').'" alt="['
                        . get_lang( 'Edit' ) . ']"/></a>',
                    /* 'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=dict&amp;action=rqDelWordDef&amp;wordId='
                        . '%ACTION_FIELD%'
                        . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        . '">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/></a>' */
                );
                
                $table->setActionUrls( $actionUrls );
                
                $footer = '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=dict&amp;action=rqAddWord'
                    . (!is_null($dictionaryId)?'&amp;dictionaryId='.(int)$dictionaryId:'')
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '"'
                    . ' title="'.get_lang('Click here to add a new word').'">'
                    . '<img src="'.get_icon('new.gif').'" alt="'
                    . get_lang('Click here to add a new word').'" />'
                    . '&nbsp;'.get_lang('Add a new word').'</a>'
                    ;
                    
                $table->setFooter( $footer );
            }
            
            $table->setData( $dict );
            
            $output .= $table->render();
        }
    }
    // fatal error
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