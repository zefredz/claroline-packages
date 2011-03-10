<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.6 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Picture extends Resource
{
    protected $authorizedFileType = array( 'png' , 'jpg' , 'jpeg' , 'gif' );
}