<?php
require_once get_path('incRepositorySys') . '/lib/fileManage.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileDisplay.lib.php';
require_once get_path('incRepositorySys') . '/lib/fileUpload.lib.php';
require_once get_path('incRepositorySys') . '/lib/file.lib.php';
// for handling of error messages
require_once get_path('incRepositorySys') . '/lib/backlog.class.php';


class ScormImporter
{
    var $uploadedZipFile;
    var $baseDir = '';
    var $uploadDir = '';
    
    var $backlog;
    
    function ScormImporter(&$uploadedZipFile)
    {
        $this->uploadedZipFile = $uploadedZipFile;    
        
        // paths
        $this->baseDir = get_path('rootSys') . get_conf('tmpPathSys') . '/upload/';

        // errors logging
        $backlog = new Backlog();
    }
    
    function import()
    {
        // create tmp dir
        $this->createUploadDir();
        
        // extract uploaded file to tmp dir
        $this->unzip();
        
        // find manifest file
        
        // parser le manifest
        //  - trouver manifestes secondaire
        //  - les parser
        //  - vérifier si les ressources citées existes dans le tmp
        //  - sauver  
        // créer un objet path, le valider et le sauver
        // pour chaque module ou chapitre 
        //  - créer un objet item, le valider et le sauver
        
        // créer répertoire définitif
        // copier les fichiers de tmp vers répertoire définitif
        // effacer le répertoire temporaire
        
        // renvoyer succès ou non
        
    }
    
    function createUploadDir()
    {
        $this->uploadDir = claro_mkdir_tmp($this->baseDir, 'path_');
    }
    
    function unzip()
    {
        // upload path is the difference between baseDir to uploadDir
        $uploadPath = str_replace($this->baseDir,'',$this->uploadDir);
        
        if( !isset($this->uploadedZipFile) || !is_uploaded_file($this->uploadedZipFile['tmp_name']))
        {
            // upload failed
            return false;
        }

        include_once get_path('incRepositorySys') . '/lib/pclzip/pclzip.lib.php';


        if ( preg_match('/.zip$/i', $this->uploadedZipFile['name']) 
            && treat_uploaded_file($this->uploadedZipFile, $this->baseDir, $uploadPath, get_conf('maxFilledSpace' , 10000000), 'unzip', true))
        {
            if (!function_exists('gzopen'))
            {
                claro_delete_file($uploadDir);
                return false;
            }
            
            return true;
        }
        else
        {
            claro_delete_file($uploadDir);
            return false;
        }
    }
    
    function buildFileList()
    {
        $this->zipContent = listDirContent($this->uploadDir);
    }
    
    function listDirContent()
    {
        $files = array();
        if( is_dir($start_dir) ) 
        {
            $fh = opendir($start_dir);
            while( ( $file = readdir($fh) ) !== false ) 
            {
                // loop through the files, skipping . and .., and recursing if necessary
                if( $file == '.' || $file == '..' ) continue;
                
                $filepath = $start_dir . '/' . $file;
                
                if ( is_dir($filepath) )
                {
                    $files = array_merge($files, listdir($filepath));
                }
                else
                {
                    array_push($files, $filepath);
                }
            }
            closedir($fh);
        } 
        else
        {
            // false if the function was called with an invalid non-directory argument
            $files = false;
        }
        return $files;
    }
       
    function clean()
    {
    
    }
}
?>
