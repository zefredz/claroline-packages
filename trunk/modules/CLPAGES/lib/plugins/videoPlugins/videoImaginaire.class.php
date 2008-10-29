<?php // $Id$

if ( count( get_included_files() ) == 1 ) die( basename(__FILE__) );

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2008 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 *
 */
// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class videoImaginaireComponent
{
    
     private $input;
     private $inputType;
     private $status;
     private $password;
     private $login;
     private $sex;
     
     /**
     * Check if input equals correct and active YouTube url
     */
    
    public function isValidUrl($url)
    {
        return true;
    }
    
    
     /**
     * Set YouTube web player from YouTube url or id
     */
    
    public function setPlayer()
    {
        $player =
        '<div class="validationTest">
            <p>La valeur d\'input entrée est : '.$this->input.'</p></br>
            <p>Le type d\'input entré est : '.$this->inputType.'</P></br>
            <p>le status entré est : '.$this->status.'</p></br>
            <p>Le password entré est : '.$this->password.'</p></br>
            <p>Le login entré est : '.$this->login.'</p></br>
            <p>Le sexe de l\'utilisateur est : '.$this->sex.'</p></br>';
        return $player;
    }

    public function validate()
    {
        return true;
    }
    
    
    /*
     * 
     */
    public function getReferences()
    {
        $references =  array('Url' => 'Video Internet Adress','NOMA' => 'Matricule Number');
        return array('default' => 'NOMA', 'references' => $references);
    }
    
    /*
     * 
     */
    public function getParameters()
    {
        $sex = array('type' => 'radio', 'display' => get_lang('Sex'), 'default' => 'male','data' => array('male' => 'Male', 'female' => 'Female'));
        $status = array('type' => 'select','display' => get_lang('Status'), 'default' => 'student','data' => array('student' => 'Student', 'teacher' => 'Teacher', 'director' => 'Director'));
        $login = array('type' => 'textBox','display' => get_lang('Login'), 'default' => '', 'data' => array('type' => 'text'));
        $password = array('type' => 'textBox','display' => get_lang('Password'), 'default' => '', 'data' => array('type' => 'password'));
        return array('Sex' => $sex, 'Status' => $status , 'Login' => $login, 'Password' => $password);
    }
    
    public function setData($data)
    {
    $this->input = $data['input'];
    $this->inputType = $data['inputType'];
    $parameters = $data['parameters'];
    $this->status = $parameters['Status'];
    $this->password = $parameters['Password'];
    $this->login = $parameters['Login'];
    $this->sex = $parameters['Sex'];
    }
}

//VideoRegistry::register('imaginaire',get_lang('Imaginaire'),'videoImaginaireComponent', 'Externals');
