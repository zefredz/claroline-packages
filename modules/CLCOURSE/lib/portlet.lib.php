<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

if ( count( get_included_files() ) == 1 )
{
    die( 'The file ' . basename(__FILE__) . ' cannot be accessed directly, use include instead' );
}

/**
* CLAROLINE
*
* User desktop portlet classes
*
* @version      1.9 $Revision$
* @copyright    (c) 2001-2008 Universite catholique de Louvain (UCL)
* @license      http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
* @package      DESKTOP
* @author       Claroline team <info@claroline.net>
*
*/

abstract class Portlet
{
    // render title
    abstract public function renderTitle();

    // render content
    abstract public function renderContent();

    // render all
    public function render()
    {
        return '<div class="claroBlock portlet">' . "\n"
        .   '<div class="claroBlockHeader">' . "\n"
        .   $this->renderTitle() . "\n"
        .   '</div>' . "\n"
        .   '<div class="claroBlockContent">' . "\n"
        .   $this->renderContent()
        .   '</div>' . "\n" 
        .   '</div>' . "\n\n";
    }
}
?>
