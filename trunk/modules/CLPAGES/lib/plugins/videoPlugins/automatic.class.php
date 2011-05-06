<?php // $Id$

/**
 * CLAROLINE
 *
 * $Revision$
 * @copyright (c) 2001-2011 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package CLPAGES
 * @author Claroline team <info@claroline.net>
 *
 * This is not really a available video plugin
 * It just allow the automatic setting and send the specific 'automatic ' identifiers and parameters
 */

// vim: expandtab sw=4 ts=4 sts=4 foldmethod=marker:

class VideoAutomaticComponent
{
    
   /**
    * Return the specific video plugin identifiers array
    * 
    * @return array The automatic identifiers array
    * ['default' => 'Url',
    *  'identifiers' => ['Url' => 'Video Internet Adress',
    *                    'Id'=> 'Video identification', ...]]
    */
    public function getIdentifiers()
    {
        return array('default' => 'Url',
                     'identifiers' => array('Url' => 'Video internet address'));
    }

   /**
    * Return the specific video plugin parameters informations array
    *
    * @return array The automatic parameters array
    * ['Size' => ['type' => 'radio',
    *             'display' => get_lang('Size'),
    *             'default' => 'medium',
    *             'data' => ['small' => 'Small',
    *                        'medium' => 'Medium',
    *                        'large' => 'Large']] ,
    *   ...]
    */
    public function getParameters()
    {
        return array('Size' => array( 'type' => 'radio',
                                      'display' => 'Size',
                                      'default' => 'medium',
                                      'data' => array('small' => 'Small', 'medium' => 'Medium', 'large' => 'Large')));
    }

}

VideoPluginRegistry::register('automatic','Automatic','VideoAutomaticComponent', 'Externals');
