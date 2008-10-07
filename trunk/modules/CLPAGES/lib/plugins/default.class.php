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
 */

// vim: expandtab sw=4 ts=4 sts=4:

class DefaultComponent extends Component
{
    /**
     * @see Component
     */
    public function render()
    {
        return ( claro_is_allowed_to_edit() ? '<p>'.get_lang('Deprecated plugin, contact administrator').'</p>': '');
    }

    /**
     * @see Component
     */
    public function editor()
    {
        return '';
    }

    /**
     * @see Component
     */
    public function getEditorData()
    {
        // do nothing
    }

    /**
     * @see Component
     */
    public function setData( $data )
    {
          // do nothing
    }

    /**
     * @see Component
     */
    public function getData()
    {
        // do nothing
    }
}

// do not register this default plugin to avoid it appears in available plugin list
//PluginRegistry::register('default',get_lang('Default'),'DefaultComponent');
