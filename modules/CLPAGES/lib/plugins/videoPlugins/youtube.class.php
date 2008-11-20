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

class VideoYouTube implements VideoPlugin
{
//video plugin properties
     private $input;
     private $inputType;
     private $width;
     private $height;
     
//Interface methods implementation
    
    /**
    * Return the specific video plugin identifiers array
    * 
    * @return array The automatic identifiers array (see documentation for API)
    * ex : ['default' => 'Url',
    *       'identifiers' => ['Url' => 'Video Internet Adress',
    *                         'Id'=> 'Video identification', ...]]
    */
    public function getIdentifiers()
    {
        return array('default' => 'Url',
                     'identifiers' => array('Url' => 'Video internet address',
                                            'Id'=> 'Video identification'));
    }
    
    /**
    * Return the specific video plugin parameters informations array
    *
    * @return array The automatic parameters array (see documentation for API)
    * ex : ['Nom' => ['type' => 'radio',
    *                 'display' => get_lang('Size'),
    *                 'default' => 'medium',
    *                 'data' => ['small' => 'Small',
    *                            'medium' => 'Medium',
    *                            'large' => 'Large']] ,
    *   ...]
    */
    public function getParameters()
    {
        return array('Size' => array( 'type' => 'radio',
                                      'display' => 'Size',
                                      'default' => 'medium',
                                      'data' => array('small' => 'Small',
                                                      'medium' => 'Medium',
                                                      'large' => 'Large')));
    }
    
    /**
    * Set the differents video plugin properties
    *
    * @param array $data The specific video plugin data needed to set the web player
    * ex : $data => ['input' => 'youtube' ,
    *                'inputType' => 'Url' ,
    *                'parameters' => ['Size' => 'medium, ...]
    *               ]
    */
    public function setData($data)
    {
    $this->input = $data['input'];
    $this->inputType = $data['inputType'];
    $parameters = $data['parameters'];
    $this->setSizes($parameters['Size']);
    }
    
    /**
    * Test the submited inputs to define the validation status
    * Return true if inputs are validate and error message if the validation abord
    *
    * @return string The validation result, true or an error message
    */
    public function validate()
    {
        if('Url' == $this->inputType)
        {
            $input = $this->input;
            if ($input != null)
            {
                if($this->isValidUrl($input))
                {
                    return 'true';
                }
                else
                {
                    $message = get_lang('Not a correct available%videoType video web address : <br> %wrongInput',array("%videoType" => ' YouTube', "%wrongInput" => htmlspecialchars($input)));
                    return $message;
                }
            }
            else
            {
                $message = get_lang('Not a correct available%videoType video web address : <br> %wrongInput',array("%videoType" => ' YouTube', "%wrongInput" => htmlspecialchars($input)));
                return $message;
            }
        }
        //allow id input type         
        return 'true';
    }
    
    /**
    * Create and return video plugin player html code
    *
    * @return string The video plugin player html code
    */
    public function setPlayer()
    {
        $id='';
        if('Id' == $this->inputType)
        {
            $id=$this->input;
        }
        else if('Url' == $this->inputType)
        {
            $id = $this->input;
            $id = preg_replace('/(.*)youtube.com\/watch\?v=/','',$id);
            $id = preg_replace('/&(.*)/','',$id);
        }
    
        return '<center>' . "\n"
            .    '<object type="application/x-shockwave-flash" style="width:'.$this->width.'px;height:'.$this->height.'px;" data="http://www.youtube.com/v/'.$id.'&rel=0">' . "\n"
            .    '<param name="movie" value="http://www.youtube.com/v/'.htmlspecialchars($id).'&rel=0">' . "\n"
            .    '</object>' . "\n"
            .     '</center>' . "\n";
    }
    
    /**
    * Check if the input url is a valid YouTube url
    *
    * @param string $url An Url
    * @return bool true if input is a valid url, false if it isn't
    */
    public function isValidUrl($url)
    {
        if (preg_match('/youtube.com\/watch\?v=/',$url))
        {
            return true;
        }
        else
        {   
            return false;
        }
    }
    
//Specific video plugin methods
    
    /**
    * Set the width and height YouTube player from the size parameter
    * Set as small by default
    * 
    * @param string $sizes The wanted video sizes
    */
    private function setSizes($sizes)
    {
        $this->width = 380;
        $this->height = 320;
        
        if('medium' == $sizes)
        {
            $this->width = 550;
            $this->height = 460;
        }
        if('large' == $sizes)
        {
            $this->width = 750;
            $this->height = 625;
        }
    }
    
}

VideoPluginRegistry::register('youtube','YouTube','VideoYouTube', 'Externals');
