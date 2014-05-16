<?php // $Id$

// vim: expandtab sw=4 ts=4 sts=4:

/**
 * CLAROLINE
 *
 * Event listeners for the blog
 *
 * @version     $Revision$
 * @copyright   (c) 2001-2014, Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author      claroline Team <cvs@claroline.net>
 * @package     CLBLOG
 */

Claroline::getInstance()->notification->addListener( 'post_added',             'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'post_modified',          'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'post_deleted',           'modificationDelete' );
Claroline::getInstance()->notification->addListener( 'comment_added',          'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'comment_modified',       'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'comment_deleted',        'modificationDelete' );
