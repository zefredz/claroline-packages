<?php // $Id$
/**
 * Subscriptions for Claroline
 *
 * @version     ICSUBSCR 0.1 $Revision$ - Claroline 1.11
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICSUBSCR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class TimeSlotController extends PluginController
{
    private function validate( $date )
    {
        if( empty( $data['title'] ) )
        {
            $this->addMsg( self::ERROR , 'Missing Title' );
            return;
        }
        
        $date = $data['date'];
        $startHour = $data['startHour'];
        $endTHour = $data['endHour'];
        
        $startDate = $this->dateUtil->in( $date , $startHour );
        $endDate = $this->dateUtil->in( $date , $endDate );
        
        if( ! $startDate || ! $endDate )
        {
            $this->addMsg( self::ERROR , 'Invalid date' );
            return;
        }
        else
        {
            $data['startDate'] = $startDate;
            $data['endDate'] = $endDate;
            unset( $data['date'] , $data['startHour'] , $data['endHour'] );
        }
        
        return $data;
    }
}