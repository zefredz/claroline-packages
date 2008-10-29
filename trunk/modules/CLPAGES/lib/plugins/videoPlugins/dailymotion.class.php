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

class videoDailyMotionComponent
{
     /**
     * Check if input equals correct and active DailyMotion url 
     */
     
     private $input;
     private $inputType;
     private $width;
     private $height;
    
    public function isValidUrl($url)
    {
        if (preg_match('/dailymotion.com/',$url))
        {
            return true;
        }
        else
        {   
            return false;
        } 
    }
    
    public function setSizes($sizes)
    {
        //default dimensions = small
        $this->width = 380;
        $this->height = 320;
        
        //size dimensions
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
    
     /**
     * Check if input equals correct and active DailyMotion url 
     */
    
    private function defineUrlType($url)
    {
        if (preg_match('/swf/',$url))
        {
            return 'videoUrl';
        }
        else if (preg_match('/video/',$url))
        {   
            return 'webPageUrl';
        }
        else
        {
            return 'error';
        }
    }
    
    /**
     * Set DailyMotion web player from correct DailyMotion id, webPage url or video url
     */
    
    public function setPlayer()
    {
        $id ='';
        if ('Id' == $this->inputType)
        {
            $id = $this->input;
        }
        else
        {
            $type = $this->defineUrlType($this->input);
        
            if ('videoUrl' == $type)
            {
                $id = $this->input;
                $id = preg_replace('/(.*)www.dailymotion.com\/swf\//','',$id);
                $id = preg_replace('/&(.*)/','',$id);
            }
            else if ('webPageUrl' == $type)
            {
                $id = $this->input;
                $id = preg_replace('/(.*)www.dailymotion.com\/(.*)\/video\//','',$id);
                $id = preg_replace('/_(.*)/','',$id);
            }                      
        }    
    
        return '<center>' . "\n"
            .    '<object type="application/x-shockwave-flash" style="width:'.$this->width.'px;height:'.$this->height.'px;" data="http://www.dailymotion.com/swf/'.$id.'&related=0" allowFullScreen="true" allowScriptAccess="always">' . "\n" 
            .    '<param name="movie" value="http://www.dailymotion.com/swf/'.htmlspecialchars($id).'&related=0"></param>' . "\n"
            .    '<param name="allowFullScreen" value="true"></param>' . "\n" 
            .    '<param name="allowScriptAccess" value="always"></param>' . "\n" 
            .    '</object>' . "\n"
            .    '</center>' . "\n";
    }

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
                    $message = get_lang('Not a correct and valid DailyMotion video web address').' :  <br> '.htmlspecialchars($input) ;
                    return $message;
                }
            }
            else
            {
                return false;
            }
        }
        //allow id input type        
        return true;
    }
    
    public function getReferences()
    {
        $references =  array('Url' => 'Video Internet Adress', 'Id'=> 'Video identification');
        return array('default' => 'Url', 'references' => $references);
    }

    /*
     * 
     */
    public function getParameters()
    {
        $size = array('type' => 'radio', 'display' => get_lang('Size'),'default' => 'medium','data' => array('small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'));
        return array('Size' => $size);
    }

    
    public function setData($data)
    {
    $this->input = $data['input'];
    $this->inputType = $data['inputType'];
    $parameters = $data['parameters'];
    $this->setSizes($parameters['Size']);
    }
}

VideoRegistry::register('dailymotion',get_lang('DailyMotion'),'videoDailyMotionComponent', 'Externals');
