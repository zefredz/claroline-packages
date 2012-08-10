<?php // $Id$
/**
 * Online Meetings for Claroline
 *
 * @version     CLMEETNG 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2012 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLMEETNG
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class CLMEETNG_DateConverter
{
    const MODE_IN = 'IN';
    const MODE_OUT = 'OUT';
    
    protected $dateFormat;
    
    public function __construct( $dateFormat = 'm/d/Y' )
    {
        $this->dateFormat = $dateFormat;
    }
    
    public function in( $date = null , $hour = null )
    {
        if( ! $date )
        {
            $dateTime = date( 'Y-m-d H:i:s' , time() + $offset );
        }
        else
        {
            $datePart = explode( '/' , $date );
            $date = array();
            $format = explode( '/' , $this->dateFormat );
            
            foreach( $format as $index => $formatPart )
            {
                $date[ $formatPart ] = $datePart[ $index ];
            }
            
            if( ! $hour )
            {
                $hour = '00:00';
            }
            
            $dateTime = $date['Y'] . '-' . $date['m'] . '-' . $date['d'] . ' ' . $hour . ':00' ;
        }
        
        return $dateTime;
    }
    
    public function out( $dateTime = null
        , $secOffset = 0
        , $minOffset = 0
        , $hourOffset = 0
        , $dayOffset = 0
        , $weekOffset = 0 )
    {
        $offset = $secOffset + $minOffset*60 + $hourOffset*3600 + $dayOffset*86400 + $weekOffset*604800;
        
        if( ! $dateTime )
        {
            $dateTime = date( 'Y-m-d H:i:s' , time() + $offset );
        }
        
        $time = strtotime( $dateTime ) + $offset;
        $date = date( $this->dateFormat , $time );
        $hour = date( 'H:i' , $time );
        
        return array( 'date' => $date , 'hour' => $hour );
    }
}