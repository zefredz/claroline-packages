<?php // $Id$
/**
 * CLSURVEY
 *
 * @version 1.0.0
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLSURVEY
 *
 * @author Christophe Gesch� <moosh@claroline.net>
 * @author Philippe Dekimpe <dkp@ecam.be>
 * @author Claro Team <cvs@claroline.net>
 *
 */
if ( count( get_included_files() ) == 1 ) die( '---' );
/**
    //survey tool events
*/
    $claro_notifier->addListener( 'update',       "survey_visible");
    $claro_notifier->addListener( 'update',       "survey_added");
    $claro_notifier->addListener( 'delete_notif', "survey_deleted");
    $claro_notifier->addListener( 'delete_notif', "survey_invisible");