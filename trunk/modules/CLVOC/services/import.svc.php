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
    $dispToolBar            = true;
    $dispDisplayForm        = true;
    $dispImport             = false;
        
    // error dialog
    $dispError              = false; // display error box
    $fatalError             = false; // if set to true, the script ends after 
                                     // displaying the error
    $errorMsg = '';                  // error message to display
    $dispErrorBoxBackButton = false; // display back button on error
    $err                    = '';    // error string
    
    // Success dialog
    $dispSuccess            = false;
    $dispSuccessBoxBackButton = false;
    $successMsg             = '';
    
    // load modules and libraries
    require_once dirname(__FILE__) . '/../lib/glossary/dictionary.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/dictionarylist.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/text.class.php';
    require_once dirname(__FILE__) . '/../lib/glossary/highlighter.class.php';
    require_once dirname(__FILE__) . '/../lib/html/sanitizer.class.php';
    require_once dirname(__FILE__) . '/../lib/import/fileuploader.lib.php';
    require_once dirname(__FILE__) . '/../lib/import/import.class.php';
    require_once dirname(__FILE__) . '/../lib/yml/yaml.lib.php';
    
    
    //require_once get_path('includePath') . '/lib/file.lib.php';
    require_once get_path('includePath') . '/lib/fileManage.lib.php';
    //require_once get_path('includePath') . '/lib/fileDisplay.lib.php';
    //require_once get_path('includePath') . '/lib/fileUpload.lib.php';
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
        'display',
        'import'
    );
        
    // get request variables
    $action = ( isset( $_REQUEST['action'] ) 
            && in_array( $_REQUEST['action'], $allowedActions ) )
        ? $_REQUEST['action']
        : 'display'
        ;
    
    if( 'import' == $action )
    {
        $dispDisplayForm = false;
        $dispImport = true;
        
        // Validation du  fichier uploade
        if( array_key_exists('frm_file',$_FILES) )
        {
            if( $_FILES['frm_file']['error'] == 4 )
            {
                $dispError = true;
                $dispErrorBoxBackButton = true;
                $errorMsg = "Vous n'avez pas sélectionné de fichier !" . "\n";
            }
            else
            {
                
                if( $_FILES['frm_file']['size'] > 0 )
                {
        			// Build archive in tmp course folder
                    $tmpDirectory = get_path('rootSys') . get_conf('tmpPathSys', 'tmp') . 'yml';
                    $tmpFileName = strtolower( $_FILES['frm_file']['name'] );
                    $tmpPath = $tmpDirectory . '/' . $tmpFileName;

        			// Create the temp dir if it doesn't exist
                    if(!is_dir($tmpDirectory))
                    {
                        claro_mkdir($tmpDirectory, CLARO_FILE_PERMISSIONS, true);
                    }

                    // copie le fichier dans le repertoire => tmp/yml
                    $uploader = new FileUploader( $_FILES['frm_file'] );
               
                    if ( $uploader->uploadFailed() )
                    {
                        $dispError = true;
                        $dispErrorBoxBackButton = true;
                        $errorMsg = $uploader->getFileUploadErrorMessage() . "\n";
                        // supression du repertoire temporaire de telechargement du fichier yml
                        claro_delete_file( $tmpDirectory );
                    }
                    else
                    {
                        if ( ! $uploader->moveToDestination( $tmpDirectory, $tmpFileName ) )
                        {
                            $dispError = true;
                            $dispErrorBoxBackButton = true;
                            $errorMsg = "Votre fichier YML n'a pas été déplacé !" . "\n";
                            // supression du repertoire temporaire de telechargement du fichier yml
                            claro_delete_file( $tmpDirectory );
                        }
                        else
                        {
                            // validation du fichier Yml
                            $importYml = new importYml;
                            if ( false === ( $importYml->chkValidYml() ) )
                            {    						
                                $dispError = true;
                                $dispErrorBoxBackButton = true;
                                $errorMsg = "Votre fichier YML n'est pas valide !" . "\n";
                                // supression du repertoire temporaire de telechargement du fichier yml
                                claro_delete_file( $tmpDirectory );
                            }
                            else
                            {
                                // Parsage du fichier Yml
                                if( ! $yml = parse_yaml_file( $tmpPath ) )
                                {
                                    $dispError = true;
                                    $dispErrorBoxBackButton = true;
                                    $errorMsg = "Chargement du fichier YML non réussi !" . "\n";
                                    // supression du repertoire temporaire de telechargement du fichier yml
                                    claro_delete_file( $tmpDirectory );
                                }
                                else
                                {
                                    
                                    //print('<pre>');
                                    //var_dump($yml);
                                    //print('</pre>');
                                    
                                    //if( array_key_exists('Name',$yml) && array_key_exists('Description',$yml) )
                                    if( array_key_exists('Name',$yml['Dictionary']) && array_key_exists('Description',$yml['Dictionary']) && array_key_exists('Content',$yml['Dictionary']) )
                                    {
                                        // Création du dictionnaire
                                        $name = '<h3>Dictionary imported :</h3>';
                                        $title = $yml['Dictionary']['Name'];
                                        $description = $yml['Dictionary']['Description'];

                                        // importation dans la db du titre et de la description
                                        if( is_null( $parentId ) )
                                        {
                                            $parentId = 0;
                                        }
                                        
                                        $idDictionary = $list->createDictionary( $title, $description, null, $parentId );
                                        $dictionary->setId( $idDictionary );
                                        
                                        
                                        /*
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
                                        */
                                        // Insertion des Tags 
                                        #foreach( $yml['Dictionary']['Tags'] as $tagsImported ) 
                                        #{
                                        #}
                                        
                                         
                                        

                                        
                                        // Insertion du contenu
                                        $content = '';
                                        
                                        $content .= '<dl>';
                                        foreach( $yml['Dictionary']['Content']  as $wordImported )
                                        {
                                            $content .= '<dt><strong>' . $wordImported['Word'] . ' : </strong></dt>';
                                            
                                            
                                            foreach( $wordImported['Definitions'] as $definitionImported )
                                            {
                                                $content .= '<dd>' . $definitionImported['Definition'] . '</dd>';
                                                $dictionaryImported[] = array($wordImported['Word'], $definitionImported['Definition']);
                                                $dictionary->import( $dictionaryImported );
                                                //$dictionary->addWord( $wordImported['Word'], $definitionImported['Definition'] );
                                            }
                                        }
                                        $content .= '</dl>';
                                        
                                       // print('<pre>');
                                        //var_dump($test);
                                        //print('</pre>');

                                 
/*                                        
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
*/                                        
                                        
                                        
                                        // importation dans la db
                                        
                                        // confirmation
                                        $dispSuccess = true;
                                        $dispSuccessBoxBackButton = true;
                                        $successMsg = "L'importation du dictionnaire a réussi !" . "\n";
                                        // supression du repertoire temporaire de telechargement du fichier yml
                                        claro_delete_file( $tmpDirectory );
                                    }
                                    else
                                    {
                                        $dispError = true;
                                        $dispErrorBoxBackButton = true;
                                        $errorMsg = "Fichier YML non valide !" . "\n";
                                        // supression du repertoire temporaire de telechargement du fichier yml
                                        claro_delete_file( $tmpDirectory );
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $dispError = true;
                    $dispErrorBoxBackButton = true;
                    $errorMsg = "Le fichier est trop volumineux !" . "\n";
                    // supression du repertoire temporaire de telechargement du fichier yml
                    claro_delete_file( $tmpDirectory );
                }
            }
        }
        else
        {
            $dispError = true;
            $dispErrorBoxBackButton = true;
            $errorMsg = "Le fichier n'a pas été uploadé pour une raison inconnue !!" . "\n";
            // supression du repertoire temporaire de telechargement du fichier yml
            claro_delete_file( $tmpDirectory );
        }
    }
}
// }}}

// {{{ VIEW
{
    $output = '';

    $output .= '<h1>'.get_lang('Import a dictionary') .'</h1>' . "\n";
    
    if ( true == $dispToolBar )
    {
        $output .= displayGlossaryMenu();
    }
    
    if ( true == $dispSuccess )
    {
        // display success
        $successMessage =  '<h2>'
            . get_lang( 'Success' )
            . '</h2>'
            . "\n"
            ;
        
        $successMessage .= '<p>'
            . htmlspecialchars($successMsg) . '</p>' 
            . "\n"
            ;
        // display back link    
        // but back to where ???? (in case of fatal error)
        if ( true == $dispSuccessBoxBackButton )
        {
            $successMessage .= '<p><a href="'
                . $_SERVER['PHP_SELF']
                . '?page=import'
                .'">['.get_lang('Back').']</a></p>'
                . "\n"
                ;
        }
        
        $output .= MessageBox::Success( $successMessage );
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
                . '?page=import'
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
    
        if( true == $dispDisplayForm )
        {
            $output .= '<form enctype="multipart/form-data"'
            . 'action="entry.php?page=import&amp;action=import" '
            . 'method="post">'
            . claro_form_relay_context()
            . '<table>'
            . '<tr>'
            . '<td><label for="frm_file" >'.get_lang('Upload file').'</label> : </td>'
            . '<td><input id="frm_file" type="file" name="frm_file" /></td>'
            . '</tr>'
            . '<tr>'
            . '<td>'
            . '<a href="entry.php?page=list">'
            . '<input class="buttom" type="button" value="'.get_lang("Cancel").'" onclick="document.location=\'entry.php?page=list\'" />'
            . '</a>'
            . '<input class="buttom" type="submit" value="'.get_lang("Save").'" />'
            . '</td>'
            . '</tr>'
            . '</table>' 
            . '</form>'
            ;
        }
        
        if( true == $dispImport && false == $dispError)
        {
            // Affichage du contenu en dessous du message de réussite 
            
            $output .= $name;
            $output .= '<p>Name : ' . $title . '</p>';
            $output .= '<p>Description : ' . $description . '</p>';
            $output .= $content;

        }
    
    // fatal error
    }
    else
    {
        // fatal error nothing else to do...
    }
    
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php'
        , 'name' => get_lang("Glossary") );
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=list'
        , 'name' => get_lang("Dictionary") );
    $GLOBALS['interbredcrump'][]= array ( 'url' => 'entry.php?page=import'
        , 'name' => get_lang("Import") );
    
    // send output to dispatcher
    $this->setOutput($output);
}
// }}}
?>