<?php // $Id$

/**
 * Subscription
 *
 * @version     CLSUBSCR 0.2 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLSUBSCR
 * @author      Dimitri Rambout <dim@claroline.net>
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

/**
 * retruns a string with a starting date and an end date
 * @param string $dateFrom
 * @param int $dateTo
 * @return string a circumstanced strings with dates
 */
function availability_date( $dateFrom = null , $dateTo = null )
{
    if ( $dateFrom )
    {
        if ( $dateTo )
        {
            return get_lang( 'from %dateFrom to %dateTo'
                            , array( '%dateFrom' => claro_date( 'Y/m/d - H:i', $dateFrom ) ,
                                    '%dateTo'   => claro_date( 'Y/m/d - H:i', $dateTo ) ) );
        }
        else
        {
            return get_lang( 'from %dateFrom'
                            , array( '%dateFrom' => claro_date( 'Y/m/d - H:i', $dateFrom ) ) );
        }
    }
    elseif( $dateTo )
    {
        return get_lang( 'until %dateTo'
                        , array( '%dateTo' => claro_date( 'Y/m/d - H:i', $dateTo ) ) );
    }
}