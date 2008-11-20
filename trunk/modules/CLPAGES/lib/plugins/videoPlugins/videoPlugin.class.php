<?php

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

interface VideoPlugin
{
    /**
    * Return the specific video plugin identifiers array
    * 
    * @return array The automatic identifiers array (see documentation for API)
    * ex : ['default' => 'Url',
    *       'identifiers' => ['Url' => 'Video Internet Adress',
    *                         'Id'=> 'Video identification', ...]]
    */
    public function getIdentifiers();
    
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
    public function getParameters();
    
    /**
    * Set the differents video plugin properties
    *
    * @param array $data The specific video plugin data needed to set the web player
    * ex : $data => ['input' => 'youtube' ,
    *                'inputType' => 'Url' ,
    *                'parameters' => ['Size' => 'medium, ...]
    *               ]
    */
    public function setData($data);
    
    /**
    * Test the submited inputs to define the validation status
    * Return true if inputs are validate and error message if the validation abord
    *
    * @return string The validation result, true or an error message
    */
    public function validate();
    
    /**
    * Create and return video plugin player html code
    *
    * @return string The video plugin player html code
    */
    public function setPlayer();
    
    /**
    * Check if the input url is a valid video plug in url
    *
    * @param string $url An Url
    * @return bool true if input is a valid url, false if it isn't
    */
    public function isValidUrl($url);
    
}
?>