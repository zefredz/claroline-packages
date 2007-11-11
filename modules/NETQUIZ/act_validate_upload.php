<?php // $Id$

    // vim: expandtab sw=4 ts=4 sts=4:
	
    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision: 159 $
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     * This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
     * as published by the FREE SOFTWARE FOUNDATION. The GPL is available
     * through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
     *
     * @author KOCH Gregory <gregk84@gate71.be>
     *
     * @package NETQUIZ
     */

if( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead');
}

if($is_allowedToAdmin == false) 
{
    claro_die( get_lang('Not allowed action !') );
}

foreach($_POST as $key=>$val) 
{
	$_POST[$key] = trim($val);
	if(!get_magic_quotes_gpc()) 
    {
		$_POST[$key] = addslashes($val);
	}
}

$error = 0;
$error_message = "";

// Validation du  fichier uploade
if( array_key_exists('frm_file',$_FILES) )
{
    if( $_FILES['frm_file']['error'] == 4 )
    {
        $error = 1;
        $error_message .= '<li>'.get_lang("Vous n'avez pas sélectionné de fichier !").'</li>';
    }
    else
    {
        if( $_FILES['frm_file']['size'] > 0 )
        {
            // id unique de l'exercice
			$id = uniqid();
			
			// Build archive in tmp course folder
            $destinationDirectory = get_path('rootSys') . get_conf('tmpPathSys', 'tmp') . 'unzip';
            $destinationFileName = strtolower( $_FILES['frm_file']['name'] );
            $destinationPath = $destinationDirectory . '/' . $destinationFileName;
            
            // repertoire de dézippage
			$extractDirectory = $destinationDirectory . '/' . $id;
			
			// repertoire de destination
            $moveDataDirectory = get_path('rootSys') . 'courses/' . claro_get_course_path() .'/modules/' . get_current_module_label() . '/data';

			// Create the temp dir if it doesn't exist
            // or do a cleanup before creating the zipfile
            if(!is_dir($destinationDirectory))
            {
                claro_mkdir($destinationDirectory, CLARO_FILE_PERMISSIONS, true);
            }
            else
            {
                // Delete les fichiers qui ont plus de 2 heures
                $handle = opendir( $destinationDirectory );
                while ( false !== ( $file = readdir( $handle ) ) )
                {
                    if ( $file != "." && $file != ".." && $file != "" && !preg_match('/^\./', $file) && $file != "__MACOSX" )
                    {
                        $fileCreationTimeInHour = ( time() - filemtime( $destinationDirectory . '/' . $file ) )/60/60;
                        // If file is old of 2 hours delete it
                        if ($fileCreationTimeInHour > 2) claro_delete_file( $destinationDirectory . '/' . $file );
                    }
                }
                closedir($handle);
            }
            
            // copie le fichier dans le repertoire => tmp/unzip
            $uploader = new FileUploader( $_FILES['frm_file'] );
        
            if ( ! $uploader->uploadFailed() )
            {
                $uploader->moveToDestination( $destinationDirectory, $destinationFileName );
            }
            else
            {
                $error = 1;
                $error_message .= '<li>'.$uploader->getFileUploadErrorMessage().'</li>';
            }
            
            // verifie si le fichier est bien un fichier zip sur base des caractères magiques
            if ( is_zip_file( $destinationPath ) )
            {
                // verifie si le repertoire existe si non le crée
                if( !is_dir( $extractDirectory ) )
                {
                    claro_mkdir($extractDirectory, CLARO_FILE_PERMISSIONS, true);
                }
                    
                // class pclzip : extrait le zip dans un repertoire le repertoire de decompression
                $archive = new PclZip( $destinationPath );
                if ( $archive->extract( PCLZIP_OPT_PATH, $destinationDirectory . '/' . $id ) == 0 )
                {
                    $error = 1;
                    $error_message .= '<li>'.get_lang( $archive->errorInfo( true ) ).'</li>';
                    claro_delete_file( $destinationPath );
                    claro_delete_file( $extractDirectory );
                }
				
				// validation du contenu du repertoire decompressé
				$netquizInstaller = new netquizInstaller();
				$netquizInstaller->setExtractDirectory( $extractDirectory );
				
				if ( false === ( $fileStruct = $netquizInstaller->chkFileStruct() ) )
				{
					// supression du repertoire decompressé
					claro_delete_file( $extractDirectory );	
					
					$error = 1;
					$error_message .= '<li>'.get_lang("Votre archive n'est pas valide !").'</li>';
				}
				else
				{
					// validation du fichier xml
					$netquizInstaller->setFileStruct( $fileStruct['path'] . '/' . $fileStruct['xml'] );
					if ( false === ( $netquizInstaller->chkValidXml() ) )
					{
						// supression du repertoire decompressé
						claro_delete_file( $extractDirectory );	
						
						$error = 1;
						$error_message .= '<li>'.get_lang("Votre fichier XML n'est pas valide !").'</li>';
					}
					else
					{
						// renomage du repertoire et du fichier xml
						claro_rename_file( $fileStruct['path'] . '/' . $fileStruct['data'] . '/', $fileStruct['path'] . '/' . $id);
						claro_rename_file( $fileStruct['path'] . '/' . $fileStruct['xml'], $fileStruct['path'] . '/' . $id . '.xml' );
						
						// verifie si le repertoire existe si non le crée
		                if( !is_dir( $moveDataDirectory ) )
		                {
		                    claro_mkdir( $moveDataDirectory, CLARO_FILE_PERMISSIONS, true );
		                }

						// déplacement du repertoire
						if( claro_move_file( $fileStruct['path'] . '/' . $id, $moveDataDirectory ))
                        {
                            // installation du xml
                            $sFilePath = $fileStruct['path']. '/' . $id . '.xml';
                            
                            //Try to open it using xmldom
                            //If it fails, return error
                            if(!$xml = simplexml_load_file($sFilePath))
                            {
                                $error = 1;
                                $error_message .= '<li>'.get_lang("Chargement du XML non réussi !").'</li>';
                            }
                            else
                            {
                                //Get quiz info
                                $oQuestions = $xml->quiz->questions->question;
                                $sQuizIdent = utf8_decode( $xml->quiz->quizident );
                                $sQuizVersion = utf8_decode( $xml->quiz->quizversion );	
                                $sQuizTitre = html_entity_decode( utf8_decode( $xml->quiz->quiztitre ) );
                                $iNbQuestions = count($oQuestions);
                                //$sVersionDate = now();
                                $sPassword = '';
                                $sQuizAuteur = html_entity_decode( utf8_decode( $xml->quiz->quizauteur ) );
                                $sActif = 1;
                                
                                // Declaration de la Class netquiz		
                                $netquiz = new netquiz();
                                
                                // Class netquiz : recuperation de IdQuiz	
                                $netquiz->setQuizVersion( $sQuizVersion );
                                $netquiz->setQuizIdent( $sQuizIdent );
                                if ( $netquiz->fetchIdQuiz() )
                                {
                                    $error = 1;
                                    $error_message .= '<li>'.get_lang("L'exercice est déjà installé !").'</li>';
                                }
                                else
                                {
                                    // Class netquiz : insertion du quiz
                                    $netquiz->setRepQuizId( $id );
                                    $netquiz->setQuizIdent( $sQuizIdent );
                                    $netquiz->setQuizVersion( $sQuizVersion );
                                    $netquiz->setQuizName( $sQuizTitre );
                                    $netquiz->setNbQuestions( $iNbQuestions );
                                    $netquiz->setPassword( $sPassword );	
                                    $netquiz->setAuteur( $sQuizAuteur );
                                    $netquiz->setActif( $sActif );
                                    
                                    if ( $netquiz->insertQuiz() )
                                    {
                                        $iIDQuiz = netquiz::lastIdQuiz();
                                        
                                        //Loop for each questions
                                        $iNoQuestion = 0;
                                        foreach($oQuestions as $oQuestion){
                                        
                                        //Insert question information in DB
                                        $sQAtt = $oQuestion->attributes();
                                        $iType = intval($sQAtt["type"]);
                                            if($iType > 0){
                                                $sType = $sTypeLabel[$iType];
                                                
                                            }
                                            else
                                            {
                                                if($oQuestion->reponse->isrepmultiple == "true")
                                                {
                                                    $sType = $sTypeLabel[0]["reponses"];
                                                    
                                                }
                                                else
                                                {
                                                    if(count($oQuestion->reponse->liste_choix->choix) == 2)
                                                    {
                                                        $sType = $sTypeLabel[0]["vraifaux"];
                                                    }
                                                    else
                                                    {
                                                        $sType = $sTypeLabel[0]["choix"];
                                                    }
                                                }
                                            }
                                            
                                            $sTitre = utf8_decode( $oQuestion->titre );
                                            $sEnonce = utf8_decode( $oQuestion->enonce );
                                            $iPonderation = utf8_decode( $oQuestion->ponderation );
                                            $sReponseXML = utf8_decode( $oQuestion->reponse->asXML() );
                                                    
                                            // Class netquiz : insertion des questions
                                            $netquiz->setTitre( $sTitre );
                                            $netquiz->setType( $iType );
                                            $netquiz->setTypeTd( $sType );
                                            $netquiz->setPonderation( $iPonderation );
                                            $netquiz->setEnonce( $sEnonce );
                                            $netquiz->setReponseXML( $sReponseXML );	
                                            $netquiz->setIdQuiz( $iIDQuiz );
                                            $netquiz->setNoQuestion( $iNoQuestion );
                                            
                                            if ( !$netquiz->insertQuestions() )
                                            {
                                                $error = 1;
                                                $error_message .= '<li>'.get_lang("Questions is not insert !").'</li>';
                                            }

                                            $iNoQuestion++;
                                        }

                                        // Modification de l'url dans main.js
                                        $fileDirectory = get_path('rootSys') . 'courses/' . claro_get_course_path() .'/modules/' . get_current_module_label() . '/data/' . $id . '/includes/main.js';
                                        
                                        if ( $fileContents = file_get_contents($fileDirectory) )
                                        {
                                            $regexp = '/(var\s+sMGURL\s*=\s*"[^"]*"\s*;)/';
                                            $urlNetquiz = get_path('url') . '/module/' . get_current_module_label() . '/netquiz';
                                            $rep = 'var sMGURL = "'. $urlNetquiz .'";';

                                            $tmpFile = preg_replace($regexp, $rep, $fileContents);
                                            
                                            if ( file_put_contents($fileDirectory, $tmpFile) )
                                            {
                                                $confirm = '<li>'.get_lang("L'exercice à correctement été ajouté !").'</li>';
                                            }
                                            else
                                            {
                                                $error = 1;
                                                $error_message .= '<li>'.get_lang("Le fichier main.js n'a pas été modifié !").'</li>';
                                            }
                                        }
                                        else
                                        {
                                            $error = 1;
                                            $error_message .= '<li>'.get_lang("On ne sait pas lire le fichier main.js !").'</li>';
                                        }
                                    }
                                    else
                                    {
                                        $error = 1;
                                        $error_message .= '<li>'.get_lang("Quiz is not insert !").'</li>';
                                        
                                        // supression du répertoire du quiz
                                        claro_delete_file( $moveDataDirectory . '/' . $id );
                                    }
                                }

                                // supression du fichier zip
                                claro_delete_file( $destinationPath );
                                
                                // supression du répertoire et du fichier xml
                                claro_delete_file( $fileStruct['path'] );
                                
                            }
                        }
                        else
                        {
                            // supression du fichier zip
                            claro_delete_file( $destinationPath );
                            
                            // supression du répertoire et du fichier xml
                            claro_delete_file( $fileStruct['path'] );
                        }
					}
				}
            }
            else
            {
                $error = 1;
                $error_message .= '<li>'.get_lang("Le fichier n'est pas une archive ZIP !").'</li>';
                claro_delete_file( $destinationPath );
                claro_delete_file( $extractDirectory );
            }
        }
        else
        {
            $error = 1;
            $error_message .= '<li>'.get_lang("Le fichier est trop volumineux !").'</li>';
        }
    }
}
else
{
    $error = 1;
    $error_message .= '<li>'.get_lang("Le fichier n'a pas été uploadé pour une raison inconnue !").'</li>';
}

if($error == 1) 
{
    // En cas d'échec de la validation, on déprotège les valeurs en vue de les réafficher dans le formulaire
	foreach($_POST as $key=>$val) 
    {
        $_POST[$key] = stripslashes($val);
	}
}
?>