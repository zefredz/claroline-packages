<?php 

// $Id$

/**
 * CLAROLINE
 *
 * @version 0.1 $Revision$
 * @copyright (c) 2013 Universite catholique de Louvain (UCL)
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package ICEPC
 * @author Frederic Minne <zefredz@claroline.net>
 *
 */

/**
 * Get current academic year start year (ex.: 2012 if we are between 1st september 2012 and 31 august 2013)
 * @return string
 */
function epc_get_current_acad_year()
{
    $currentMonth = (int) date ( 'n' );
    
    if ( $currentMonth < 9 )
    {
        return (int) date ( 'Y' ) - 1;
    }
    else
    {
        if ( claro_debug_mode() )
        {
            return (int) date ( 'Y' ) - 1;
        }
        else
        {
            return date ( 'Y' );
        }
    }
}
