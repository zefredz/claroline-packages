<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class Article extends Resource
{
    protected $authorizedFileType = array( 'pdf' , 'rtf' , 'odt' , 'doc' , 'txt' , 'htm' , 'html' );
    protected $defaultMetadataList = array( 'author'
                                          , 'publication'
                                          , 'issue'
                                          , 'pages'
                                          , 'publication date'
                                          , 'publisher'
                                          , 'ISSN' );
}