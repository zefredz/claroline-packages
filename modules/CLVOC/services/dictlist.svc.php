<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    // vim>600: set foldmethod=marker:

    if ( count( get_included_files() ) == 1 )
    {
        die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
    }
    
// {{{ SCRIPT INITIALISATION
{
    $isAllowedToEdit = claro_is_allowed_to_edit();
    
    // local variables
    $dispToolBar            = true;
    $dispToolBarSearchInportExport      = true;
    $dispError              = false;
    $fatalError             = false;
    
    $dispDictionary         = false;
    $dispDictionaryList     = true;
    $dispDictionaryAddForm  = false;
    $dispDictionaryEditForm = false;
    $dispDelDictMsg         = false;
    $dispBackToParent       = false;
    
    // success dialog
    $dispSuccess            = false;
    $successMsg             = '';
    
    // error dialog
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = false; // display back button on error
    $err                    = '';    // error string
    
    $dispTextSearch = false;

    // load modules and libraries
    require_once dirname(__FILE__) . '/../lib/glossary/dictionary.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/dictionarylist.class.php';
}
// }}}
// {{{ MODEL
{
    $connection = new Claroline_Database_Connection;
    $list = new Glossary_Dictionary_List( $connection, $GLOBALS['glossaryTables'] );
    $dictionary = new Glossary_Dictionary( $connection, $GLOBALS['glossaryTables'] );
}
// }}}
// {{{ CONTROLLER
{
    if ( $isAllowedToEdit == true )
    {
        $allowedActions = array(
              'showList'
            , 'showDict'
            , 'rqAddDict'
            , 'exAddDict'
            , 'rqEditDict'
            , 'exEditDict'
            , 'rqDelDict'
            , 'exDelDict'
            , 'searchText'
        );
    }
    else
    {
        $allowedActions = array( 
               'showList'
             , 'showDict'
            , 'searchText'
        );
    }
    
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'showDict' // showList
        ;
        
    $title = isset( $_REQUEST['title'] )
        ? trim( $_REQUEST['title'] )
        : ''
        ;
            
    $description = isset( $_REQUEST['description'] )
        ? trim( $_REQUEST['description'] )
        : ''
        ;
        
    $dictionaryId = isset( $_REQUEST['dictionaryId'] )
        ? (int) $_REQUEST['dictionaryId']
        : null
        ;
        
    $parentId = isset( $_REQUEST['parentId'] )
        ? (int) $_REQUEST['parentId']
        : null
        ;
        
    $rootId =  ( is_null( $dictionaryId ) )
        ? 0
        : $dictionaryId
        ;
    
    $param = ( isset( $_REQUEST['page'] ) ) 
    ? $_REQUEST['page']
    : 'list'
    ;

    if ( 'searchText' == $action )
    {                
        $frm_search = isset( $_REQUEST['frm_search'] )
        ? $_REQUEST['frm_search']
        : NULL
        ;
        
        if( strlen( trim ( $frm_search ) ) )
        {
            $dispTextSearch = true;
            $dispTextList = true;
        }
        else
        {
            $dispError = true;
            $errorMsg = get_lang( 'the field of research is empty' );
            $dispTextList = true;
        }
    }
    
    if ( !is_null( $dictionaryId ) && $dictionaryId !== 0 )
    {
        $dictionaryExists = $list->dictionaryExists( $dictionaryId );
        
        if ( $dictionaryExists )
        {
            $dictionaryInfo = $list->getDictionaryInfo( $dictionaryId );
        }
        else
        {
            // Make sure we cannot do anything else
            $dictionaryInfo = null;
            $dictionaryId = null;
            $action = 'noAction';
            
            $err = 'Cannot find dictionary : %s'; 
            $reason = 'invalid id';
    
            $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
            
            $dispError = true;
            $fatalError = true;
            $dispErrorBoxBackButton = false;
        }
    }
    else
    {
        $dictionaryInfo = null;
    }
    
    if ( !is_null($dictionaryInfo) )
    {
        $parentId = $dictionaryInfo['parentId'];
    }
        
    if ( ! is_null( $parentId ) && $parentId !== 0 )
    {
        $parentInfo = $list->getDictionaryInfo( $parentId );
        $dispBackToParent = true;
    }
    else
    {
        if ( !is_null($dictionaryInfo) && 0 !== $dictionaryInfo['parentId'] )
        {
            $parentInfo = $list->getDictionaryInfo( $dictionaryInfo['parentId'] );
            $dispBackToParent = true;
        }
        else
        {
            $parentInfo = null;
        }
    }
    
    // set default dictionary as parent
    if ( is_null( $parentInfo ) )
    {
        $parentInfo = array(
            'id' => 0,
            'name' => get_lang('Default'),
            'description' => '',
            'parentId' => null,
            'itemId' => null
        );
    }
    
    if ( ( 'showDict' === $action || 'showList' === $action ) 
        && is_null( $dictionaryId ) )
    {
        $dictionaryId = 0;
    }    
        
    if ( 'rqAddDict' === $action )
    {
        $dispDictionaryAddForm  = true;
    }
    
    if ( 'exAddDict' === $action )
    {
        if ( ! empty( $title ) )
        {
            $list->createDictionary( $title, $description, null, $parentId );
            
            if ( $connection->hasError() )
            {
                $dispError = true;
                $err = 'Cannot add dictionary : %s'; 
                $reason = $connection->getError();
        
                $errorMsg .= sprintf( $err, $reason ) . "\n";
            }
            else
            {
                $dispSuccess = true;
                $successMsg = get_lang( 'Dictionary added' );
            }
        }
        else
        {
            $dispError = true;
            $err = 'Cannot add dictionary : %s'; 
            $reason = 'Empty title';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
    }
    
    if ( 'rqEditDict' === $action )
    {
        if ( is_null( $dictionaryId ) )
        {
            $dispError = true;
            $err = 'Cannot edit dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        else
        {
            $dictionaryInfo = $list->getDictionaryInfo( $dictionaryId );
            
            if ( !is_null( $dictionaryInfo ) )
            {
                $title = $dictionaryInfo['name'];
                $description = $dictionaryInfo['description'];     
                $dispDictionaryEditForm  = true;
            }
            else
            {
                $dispError = true;
        
                $errorMsg .= 'Dictionary not found' . "\n";
            }
        }
    }
    
    if ( 'exEditDict' === $action )
    {
        if ( is_null ( $dictionaryId ) )
        {
            $dispError = true;
            $err = 'Cannot edit dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        else
        {
            if ( ! empty( $title ) )
            {
                $list->updateDictionary( $dictionaryId, $title, $description );
                
                if ( $connection->hasError() )
                {
                    $dispError = true;
                    $err = 'Cannot edit dictionary : %s'; 
                    $reason = $connection->getError();
            
                    $errorMsg .= sprintf( $err, $reason ) . "\n";
                }
                else
                {
                    $dispSuccess = true;
                    $successMsg = get_lang( 'Dictionary updated' );
                }
            }
            else
            {
                $dispError = true;
                $err = 'Cannot edit dictionary : %s'; 
                $reason = 'Empty title';
        
                $errorMsg .= sprintf( $err, $reason ) . "\n";
            }
        }
    }
    
    if ( 'rqDelDict' === $action )
    {
        if ( is_null( $dictionaryId ) )
        {
            $dispError = true;
            $err = 'Cannot delete dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        else
        {
            $dict = $list->getDictionaryInfo( $dictionaryId );
            
            if ( !is_null( $dict ) )
            {
                $title = $dict['name'];
                $description = $dict['description'];     
                $dispDelDictMsg = true;
            }
            else
            {
                $dispError = true;
        
                $errorMsg .= 'Dictionary not found' . "\n";
            }
        }
    }
    
    if ( 'exDelDict' === $action )
    {
        if ( is_null( $dictionaryId ) )
        {
            $dispError = true;
            $err = 'Cannot delete dictionary : %s'; 
            $reason = 'missing id';
    
            $errorMsg .= sprintf( $err, $reason ) . "\n";
        }
        else
        {
            $list->deleteDictionary( $dictionaryId );
            
            if ( $connection->hasError() )
            {
                $dispError = true;
                $err = 'Cannot delete dictionary : %s'; 
                $reason = $connection->getError();
        
                $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
            }
            else
            {
                $dispSuccess = true;
                $successMsg = get_lang( 'Dictionary deleted' );
            }
        }
    }
    
    
    $list->setRootId( $rootId );
    
    $dictionaryList = $list->getDictionaryList();
    //var_dump($dictionaryList);
    if ( $connection->hasError() )
    {
        $dispError = true;
        $fatalError = true;
        
        $err = 'Dictionary list cannot be loaded : %s'; 
        $reason = $connection->getError();
        
        $errorMsg .= sprintf( $err, $reason ) . "<br />\n";
    }
    
    if ( 'showDict' === $action )
    {
        /* Show Dictionary */    
        if ( !is_null( $dictionaryId ) )
        {
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
                $dispDictionary = true;
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
}
// }}}
// {{{ VIEW
{
    $output = '';
    
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
    
    if ( true == $dispToolBar )
    {
        $output .= displayGlossaryMenu();
    }
    
    if ( true == $dispToolBarSearchInportExport )
    {
        $output .= displayGlossaryMenuSearchInportExport($param, $isAllowedToEdit, $dictionaryId);
    }
    
    if ( true == $dispTextSearch )
    {
        $i = 1;
        
        $output .= '<p>' . get_lang('Your search is : ') . '<strong>' . $frm_search . '</strong></p>';
        
        $search = new search();
        $search->setSearch( $frm_search );
        $searchText = $search->searchText();
        $nbrResult = count($searchText);

        $output .= '<p>' . get_lang('The results found are : ') . '<strong>' . $nbrResult . '</strong></p>';
        foreach($searchText as $row)
        {
            $output .= '<p>' . $i . ') <a href="#" onclick="popup( \'entry.php?page=dict&amp;action=showDefs&amp;dictionaryId='.$row['dictionaryId'].'&amp;word='.rawurlencode($row['name']).'&amp;inPopup=true\', \''.rawurlencode($row['name']).'\', 300,300);return false;">'.$row['name'].'</a></p>';
            $i++;
        }
        
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
        // $output .= var_export( $dictionaryList, true );
        
        if ( true === $dispBackToParent )
        {
            $output .= '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list&amp;action=showDict'
                . '&amp;dictionaryId=' . (int) $parentInfo['id']
                . '" class="claroCmd">'
                . '<img src="'.get_icon('parent.gif').'" alt="[back]" />'
                . sprintf( get_lang('Back to %s'), $parentInfo['name'] )
                . '</a>'
                ;
        }
        
        if ( true === $dispDictionaryAddForm )
        {
            $form = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=list&amp;action=exAddDict'
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '" name="addDictForm" id="addDictForm">' . "\n"
                . '<fieldset id="addDict">' . "\n"
                . '<legend>'.get_lang('Add a new dictionary').'</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="title">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<input name="title" id="title" value="'.htmlspecialchars( $title ).'" type="text" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="description">'.get_lang( 'Description' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="description" id="description" cols="60" rows="5">'.htmlspecialchars( $description ).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=list&amp;action=showDict'
                . '&amp;dictionaryId='.(!is_null($parentId)?(int)$parentId:0) 
                . '\'" />' . "\n"
                . '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            
            $output .= $form;
        }
        
        if ( true === $dispDictionaryEditForm )
        {
            $form = '<div class="formContainer"><form method="post" action="'.$_SERVER['PHP_SELF']
                . '?page=list&amp;action=exEditDict" name="editDictForm'
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                . '" id="editDictForm">' . "\n"
                . '<fieldset id="editDict">' . "\n"
                . '<legend>'.get_lang('Edit dictionary').'</legend>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="title">'.get_lang( 'Title' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<input name="title" id="title" value="'.htmlspecialchars( $title ).'" type="text" />' . "\n"
                . '</div>' . "\n"
                . '<div class="row">' . "\n"
                . '<label for="description">'.get_lang( 'Description' ).'&nbsp;:&nbsp;</label>' . "\n"
                . '<textarea name="description" id="description" cols="60" rows="5">'.htmlspecialchars( $description ).'</textarea>' . "\n"
                . '</div>' . "\n"
                . '<div class="btnrow">' . "\n"
                . '<input type="hidden" name="claroFormId" value="' . uniqid('') . '" />'
                . '<input name="dictionaryId" value="'.(int) $dictionaryId.'" type="hidden" />' . "\n"
                . '<input name="submit" value="'.get_lang('Ok').'" type="submit" />&nbsp;'
                . '<input name="cancel" value="'.get_lang('Cancel').'" type="button" '
                . 'onclick="window.location=\''.$_SERVER['PHP_SELF'].'?page=list&amp;action=showDict'
                . '&amp;dictionaryId='.(!is_null($parentId)?(int)$parentId:0) 
                . '\'" />' . "\n"
                .  '</div>' . "\n"
                . '</fieldset>' . "\n"
                . '</form></div>' . "\n"
                ;
            
            $output .= $form;
        }
        
        if ( true === $dispDelDictMsg )
        {
            $confirmDelDict = '<p>'
                . get_lang( 'You are going to delete the following dictionary :' )
                . '<br /><br />'
                . "\n"
                ;
                
            $confirmDelDict .= $title . ' : ' . $description . '<br /><br />';
                
            $confirmDelDict .= get_lang( 'Continue ?' ) . '<br /><br />' . "\n"
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list&amp;action=exDelDict&amp;dictionaryId='
                . (int) $dictionaryId
                . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'')  
                . '">'
                . '[' 
                . get_lang( 'Yes' ) 
                . ']</a>&nbsp;' 
                . '<a href="'
                . $_SERVER['PHP_SELF']
                . '?page=list">[' 
                . get_lang( 'No' ) 
                . ']</a>' . '</p>'
                . "\n"
                ;
                
            $output .= MessageBox::Question( $confirmDelDict );
        }
        
        if ( true === $dispDictionaryList )
        {            
            $table = new HTML_Datagrid_Table;
            
            $table->setTitle( get_lang('Dictionaries') );
            
            $dataFields = array(
                'name' => get_lang( 'Title' ),
                'description' => get_lang( 'Description' )
            );
            
            
            $table->setDataFields( $dataFields );
            
            $dataUrls = array(
                'name' => '<a href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=list&amp;action=showDict&amp;dictionaryId='
                    . '%ACTION_FIELD%'
                    . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                    . '">'
                    . '%name%'
                    . '</a>'
            );
            
            $table->setDataUrls( $dataUrls );
            
            if ( true === $isAllowedToEdit )
            {
                
                
                $actionFields = array(
                    'edit' => get_lang( 'Edit' ),
                    'delete' => get_lang( 'Delete' )
                );
                
                $table->setActionFields( $actionFields );
                
                $table->displayActionField = false;
                
                $actionUrls = array(
                    'edit' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=list&amp;action=rqEditDict&amp;dictionaryId='
                        . '%ACTION_FIELD%'
                        . '&amp;parentId='.(!is_null($parentId)?(int)$parentId:0) 
                        . '">'
                        . '<img src="'.get_icon('edit.gif').'" alt="['
                        . get_lang( 'Edit' ) . ']"/></a>',
                    'delete' => '<a href="'
                        . $_SERVER['PHP_SELF']
                        . '?page=list&amp;action=rqDelDict&amp;dictionaryId='
                        . '%ACTION_FIELD%'
                        . (!is_null($parentId)?'&amp;parentId='.(int)$parentId:'') 
                        . '">'
                        . '<img src="'.get_icon('delete.gif').'" alt="['
                        . get_lang( 'Delete' ) . ']"/></a>'
                );
                
                $table->setActionUrls( $actionUrls );
                
                $footer = '<a class="claroCmd" href="'
                    . $_SERVER['PHP_SELF']
                    . '?page=list&amp;action=rqAddDict'
                    . (!is_null($dictionaryId)?'&amp;parentId='.(int)$dictionaryId:'')
                    . '" title="'.get_lang('Click here to add a new dictionary').'">'
                    . '<img src="'.get_icon('new.gif').'" alt="'
                    . get_lang('Click here to add a new dictionary').'" />'
                    . '&nbsp;'.get_lang('Add a new dictionary').'</a>'
                    ;
                    
                $table->setFooter( $footer );
            }
            
            $table->setData( $dictionaryList );
            
            $output .= $table->render();
        }
        
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
            
            //$output .= ('Entrée du dictionnaire');
                        
            $output .= '<p class="claroCmd icoPrint">'
            
            . '<a href="entry.php?page=export&amp;action=exportDict&amp;format=yml&amp;dictionaryId='.$dictionaryId.'"'
            . '>'
            . '<img src="'.get_icon('clvoc_export.png').'" alt="' . get_lang( 'export' ) . '" title="' . get_lang( 'Export' ) . '" /> ' . get_lang('Export dictionary') . ''
            . '</a>'
            . '&nbsp;&nbsp;|&nbsp;&nbsp;'
            /*
            . '<a href="entry.php?page=export&amp;action=exportDict&amp;format=text&amp;dictionaryId='.$dictionaryId.'"'
            . '>'
            . '<img src="'.get_icon('clvoc_export.png').'" alt="' . get_lang( 'export' ) . '" title="' . get_lang( 'Export' ) . '" /> ' . get_lang('Export dictionary to text') . ''
            . '</a>'
            . '&nbsp;&nbsp;|&nbsp;&nbsp;'
            */
            . '<a href="#"'
            .'onclick="popup( \'entry.php?page=print&amp;action=printDict&amp;dictionaryId='.$dictionaryId.'&amp;inPopup=true\', \'Print\', 600,600);return false;"'
            . '>'
            . '<img src="'.get_icon('print').'" alt="' . get_lang( 'Print' ) . '" title="' . get_lang( 'Print' ) . '" /> Imprimer'
            . '</a>'
            . '</p>' . "\n"
            ; 

        }
    }
    else
    {
        // fatal error - nothing to do at this time
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php', 'name' => get_lang("Glossary"));
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=list&amp;action=showDict&amp;dictionaryId=0', 'name' => get_lang("Dictionary"));
    
    if ( !is_null( $dictionaryInfo) )
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=list&amp;action=showDict&amp;dictionaryId='.(int)$dictionaryId
            , 'name' => $dictionaryInfo['name']);
    }
    else
    {
        $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=list&amp;action=showDict&amp;dictionaryId=0'
            , 'name' => get_lang("Default"));
    }
    
    // send output to dispatcher
    $this->setOutput($output);
}
// }}}
?>