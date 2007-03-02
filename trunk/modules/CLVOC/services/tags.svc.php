<?php  // $Id$
    
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
    
    // dicplay variables
    $dispToolBar            = true;
    $dispTagList            = false;
    $dispTagCloud           = false;
    $dispTag                = false;
    $dispTagForm            = false;
    $dispConfirmDelTag      = false;
    $dispConfirmDelEntry    = false;
    
    // success dialog
    $dispSuccess            = false;
    $successMsg             = '';
    
    // error dialog
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = false; // display back button on error
    // $err                    = '';    // error string
    
    require_once dirname(__FILE__) . '/../lib/glossary/tag.class.php';  
}
// }}}
// {{{ MODEL
{
    $connection = new Claroline_Database_Connection;
    $tagList = new Glossary_Tag( $connection, $GLOBALS['glossaryTables'] );
    $tagInfo = null;
}
// }}}
// {{{ CONTROLLER
{ 
    if ( $isAllowedToEdit == true )
    {
        $allowedActions = array(
              'showTagList'
            , 'showCloud'
            , 'showByTag'
            , 'showTag'
            , 'rqAddTag'
            , 'exAddTag'
            , 'rqEditTag'
            , 'exEditTag'
            , 'rqDelTag'
            , 'exDelTag'
            , 'rqDelEntry'
            , 'exDelEntry'
        );
    }
    else
    {
        $allowedActions = array( 
              'showTagList'
            , 'showCloud'
            , 'showByTag'
            , 'showTag'
        );
    }
    
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'showTagList'
        ;
        
    $tagId = isset( $_REQUEST['tagId'] )
        ? (int) $_REQUEST['tagId']
        : null
        ;
        
    $tagName = isset( $_REQUEST['tagName'] )
        ? trim( $_REQUEST['tagName'] )
        : ''
        ;
        
    $tagDescription = isset( $_REQUEST['tagDescription'] )
        ? trim( $_REQUEST['tagDescription'] )
        : ''
        ;
        
    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : null
        ;
        
    $entryId = isset( $_REQUEST['entryId'] )
        ? (int) $_REQUEST['entryId']
        : null
        ;
        
    if ( ! is_null( $tagId ) && ! $tagList->tagIdExists( $tagId ) )
    {
        $err = 'Cannot load tag : %s'; 
        $reason = ' tag not found in database';

        $errorMsg .= sprintf( $err, $reason ) . "\n";
        
        $dispError = true;
        $fatalError = true;
        
        $action = 'showTagList';
        $tag = null;
    }
    elseif ( !is_null( $tagId ) )
    {
        $tag = $tagList->getTag( $tagId );
        
        if ( is_null ( $tag ) )
        {
            $err = 'Cannot load tag : %s'; 
            $reason = $connection->getError();

            $errorMsg .= sprintf( $err, $reason ) . "\n";
        
            $dispError = true;
            $fatalError = true;
        }
    }
    else
    {
    }
    
    if ( 'exAddTag' === $action || 'exEditTag' === $action )
    {
        if ( !empty ( $tagName ) )
        {
            if ( 'exAddTag' === $action )
            {
                if ( ! $tagList->tagExists( $tagName ) )
                {
                    $tagList->addTag( $tagName, $tagDescription );
                }
                else
                {
                    $err = 'Cannot add tag : %s'; 
                    $reason = 'tag already exists';
            
                    $errorMsg .= sprintf( $err, $reason ) . "\n";
                    
                    $dispError = true;
                    $action = 'rqAddTag';
                }
            }
            else
            {
                $tagList->updateTag( $tagId, $tagName, $tagDescription );
            }
            
            if ( ! $connection->hasError() )
            {
                $action = 'showTagList';            
            }
            else
            {
                $err = 'Cannot add tag : %s'; 
                $reason = $connection->getError();
        
                $errorMsg .= sprintf( $err, $reason ) . "\n";
                
                $dispError = true;
                $fatalError = true;
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot add tag : %s'; 
            $reason = 'missing tag name';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            $action = ($action === 'exAddTAg') ? 'rqAddTag' : 'rqEditTag';
        }
    }
    
    if ( 'exDelTag' === $action )
    {
        if ( ! is_null( $tagId ) )
        {
            $tagList->deleteTag( $tagId );
            
            if ( $connection->hasError() )
            {
                $err = 'Cannot load tag : %s'; 
                $reason = $connection->getError();
    
                $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                $dispError = true;
            }
            
            $action = 'showTagList';
        }
        else
        {
            $dispError = true;
            $err = 'Cannot delete tag : %s'; 
            $reason = 'missing tag id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
    }
    
    if ( 'exDelTagEntry' === $action )
    {
        if ( !is_null( $entryId ) && !is_null( $tagId ) )
        {
            $this->tagList->deleteTagForItem( $tagId, $entryId );
            
            if ( $connection->hasError() )
            {
                $err = 'Cannot delete tag : %s'; 
                $reason = $connection->getError();
    
                $errorMsg .= sprintf( $err, $reason ) . "\n";
            
                $dispError = true;
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot delete entry : %s'; 
            $reason = 'missing entry id or tag id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        
        $action = 'showTag';
    }
    
    if ( 'rqAddTag' === $action )
    {
        $dispTagForm = true;
        $nextAction = 'exAddTag';
    }
    
    if ( 'rqEditTag' === $action )
    {
        $dispTagForm = true;
        $tagName = $tag['name'];
        $tagDescription = $tag['description'];
        $nextAction = 'exEditTag';
    }
    
    if ( 'rqDelTag' === $action )
    {
        $dispConfirmDelTag = true;
        $tagName = $tag['name'];
        $tagDescription = $tag['description'];
    }
    
    if ( 'rqDelTagEntry' === $action )
    {
        if ( !is_null( $entryId ) && !is_null( $tagId ) )
        {
            $dispConfirmDelEntry = true;
            $tagName = $tag['name'];
            $entry = $tagList->getEntry( $entryId );
            
            if ( is_null( $entry ) )
            {
                $dispError = true;
                $err = 'Cannot delete entry : %s'; 
                
                if ( $connection->hasError() )
                {
                    $reason = 'missing entry not found';
                }
                else
                {
                    $reason = 'missing entry id or tag id';
                }
        
                $errorMsg .= sprintf( $err, $reason ) . "\n";
            }
            
        }
        else
        {
            $dispError = true;
            $err = 'Cannot delete entry : %s'; 
            $reason = 'missing entry id or tag id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        
        $action = 'showTag';
    }
    
    if ( 'showTag' === $action )
    {
        if ( ! is_null( $tagId ) )
        {
            $entries = $tagList->getEntryList( $tagId );
            $dispTag = true;
            $tagName = $tag['name'];
            $tagDescription = $tag['description'];
        }
        else
        {
            $dispError = true;
            $err = 'Cannot display tag : %s'; 
            $reason = 'missing tag id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            $action = 'showTagList';
        }
    }
        
    if ( 'showTagList' === $action )
    {
        $list = $tagList->getAllTags();
        
        if ( ! $connection->hasError() )
        {
            $dispTagList = true;
        }
        else
        {
            $err = 'Cannot load tag list : %s'; 
            $reason = $connection->getError();
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            
            $dispError = true;
            $fatalError = true;
        }
    }
    
    if ( 'showCloud' === $action )
    {
        $cloud = $tagList->getTagCloud();
        
        if ( ! $connection->hasError() )
        {
            $dispTagCloud = true;
        }
        else
        {
            $err = 'Cannot load tag cloud : %s'; 
            $reason = $connection->getError();
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
            
            $dispError = true;
            $fatalError = true;
        }
    }
}
// }}}
// {{{ VIEW
{
    $output = '';
    
    $output .= '<h1>'. get_lang('Tags')
            .'</h1>' . "\n"
            ;
            
    if ( true == $dispToolBar )
    {
        $output .= displayGlossaryMenu();
        // $output .= '<p>'.helpLink('tags_browse').'</p>';
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
        if ( true === $dispErrorBoxBackButton )
        {
            $errorMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        if ( true === $fatalError )
        {
            $output .= MessageBox::FatalError( $errorMessage );
        }
        else
        {
            $output .= MessageBox::Error( $errorMessage );
        }
    }
    
    if ( true === $dispSuccess )
    {
        // display error
        $successMessage =  '<h2>'
            . get_lang( 'Success' )
            . '</h2>'
            . "\n"
            ;
        
        $successMessage .= '<p>'
            . htmlspecialchars($successMsg) . '</p>' 
            . "\n"
            ;
            
        $output .= MessageBox::Success( $successMessage );
    }
    
    // no fatal error
    if ( true != $fatalError )
    {
        // $output .= MessageBox::Info("<p>Not implemented.<br /> Coming soon...</p>");
        
        if ( true === $dispConfirmDelTag )
        {
            $confirmDelTag = '<p>'
                . get_lang( 'You are going to delete the following tag :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelTag .= $tagName . ' : ' . $tagDescription . '<br /><br />';
                
            $confirmDelTag .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=tags&amp;action=exDelTag&amp;tagId='
                . (int) $tagId
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=tags">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelTag );
        }
        
        if ( true === $dispConfirmDelEntry )
        {
            $confirmDelTagEntry = '<p>'
                . get_lang( 'You are going to delete the following entry for the tag %tagName%'
                    , array( '%tagName%' => $tagName ) )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelTagEntry .= $entry['wordName'] . ' : ' . $entry['description'] . '<br /><br />';
                
            $confirmDelTagEntry .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=tags&amp;action=exDelTagEntry&amp;tagId='
                . (int) $tagId
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=tags">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelTagEntry );
        }
        
        if ( true === $dispTagForm )
        {
            $form = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=tags&amp;action='
                . $nextAction.'" name="editTagForm" id="addDictForm">' . "\n"
                . '<fieldset id="editTag">' . "\n"
                . '<legend>'
                . ( $nextAction === 'exAddTag' ? get_lang('Add a new tag') : get_lang('Edit tag') )
                . '</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="tagName">'.get_lang( 'Tag' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<input name="tagName" value="'.htmlspecialchars( $tagName ).'" type="text" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="tagDescription">'.get_lang( 'Description' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="tagDescription" cols="60" rows="5">'.htmlspecialchars( $tagDescription ).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . ( $tagId ? '<input type="hidden" value="'.$tagId.'" name="tagId" />' : '' )
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=tags&amp;action=showTagList'
                . '\'" />' . "\n"
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            $output .= $form;
        }
        
        if ( true === $dispTag )
        {
            $output .= '<p><b>'.get_lang('Tag').' : </b>'
                .htmlspecialchars($tagName).'</p>' . "\n"
                ;
            $output .= '<p><b>'.get_lang('Description').' : </b>'
                .htmlspecialchars($tagDescription).'</p>'."\n"
                ;
                
            if ( $isAllowedToEdit )
            {
                $output .= '<p>'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=tags&amp;action=rqEditTag&amp;tagId='.$tagId.'">'
                    . '<img src="'.get_icon('edit.gif').'" alt="['
                    . get_lang( 'Edit' ) . ']"/>'. get_lang( 'Edit' ) .'</a>'
                    . '&nbsp;|&nbsp;'
                    . '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=tags&amp;action=rqDelTag&amp;tagId='.$tagId.'">'
                    . '<img src="'.get_icon('delete.gif').'" alt="['
                    . get_lang( 'Delete' ) . ']"/>'. get_lang( 'Delete' ) .'</a>'
                    . '</p>' . "\n"
                    ;
            }
            
            $table = new HTML_Datagrid_Table;
            
            $table->setTitle( get_lang('Entries matching this tag')  );
            
            $table->setData( $entries );
            
            $table->setActionField( 'relId' );
            
            $dataFields = array(
                'word' => get_lang( 'word' ),
                'definition' => get_lang( 'Definition' ),
                'dictName' => get_lang( 'Dictionary' )
            );
            
            $table->setDataFields( $dataFields );
            
            if ( $isAllowedToEdit )
            {
                $actionFields = array(
                    'delete' => get_lang( 'Delete' )
                );
            
                $table->setActionFields( $actionFields );
            
                $actionUrls = array(
                    'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=tags&amp;action=rqDelTagEntry&amp;tagId='
                        . '%tagId%&amp;entryId=%ACTION_FIELD%">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/></a>'
                );
                
                $table->setActionUrls( $actionUrls );
            }
            
            $output .= $table->render();
        }
        
        if ( true === $dispTagList )
        {
            $table = new HTML_Datagrid_Table;
            
            $table->setTitle( get_lang('Tag list')  );
                
            $table->setData( $list );
            
            $dataFields = array(
                'name' => get_lang( 'Tag' ),
                'description' => get_lang( 'Description' )
            );
            
            $table->setDataFields( $dataFields );
            
            $dataUrls = array(
                'name' => '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=tags&amp;action=showTag&amp;tagId=%ACTION_FIELD%">'
                    . '%name%'
                    . '</a>'
            );
            
            $table->setDataUrls( $dataUrls );
                
            $table->setActionField( 'id' );
            
            $table->displayActionField = false;
            
            if ( $isAllowedToEdit )
            {
                $actionFields = array(
                    'edit' => get_lang( 'Edit' ),
                    'delete' => get_lang( 'Delete' )
                );
            
                $table->setActionFields( $actionFields );
            
                $actionUrls = array(
                    'edit' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=tags&amp;action=rqEditTag&amp;tagId=%ACTION_FIELD%">'
                        . '<img src="'.get_icon('edit.gif').'" alt="['
                        . get_lang( 'Edit' ) . ']"/></a>',
                    'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=tags&amp;action=rqDelTag&amp;tagId='
                        . '%ACTION_FIELD%">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/></a>'
                );
                
                $table->setActionUrls( $actionUrls );
                
                $footer = '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=tags&amp;action=rqAddTag"'
                    . ' title="'.get_lang('Click here to add a new tag').'">'
                    . '<img src="'.get_icon('new.gif').'" alt="'
                    . get_lang('Click here to add a new tag').'" />'
                    . '&nbsp;'.get_lang('Add a new tag').'</a>'
                    ;
                    
                $table->setFooter( $footer );
            }
                
            $output .= $table->render();
        }
        
        if ( $dispTagCloud )
        {
        }
    }
    else
    {
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php', 'name' => get_lang("Glossary"));
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=tags&amp;action=showTagList', 'name' => get_lang("Tags"));
    
    if ( !is_null( $tagInfo) )
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=tags&amp;action=showTag&amp;tagId='.(int)$tagId
            , 'name' => $tagInfo['name']);
    }
    
    $this->setOutput( $output );
}
// }}}
?>