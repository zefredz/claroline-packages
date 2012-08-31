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

class DateUtil
{
    const INPUT_DATE = 'input_date';
    const HOUR = 'hour';
    const DATETIME = 'datetime';
    
    protected $dateFormat;
    protected $dateFields;
    
    public function __construct( $dateFormat = 'm/d/Y' )
    {
        $this->dateFormat = $dateFormat;
        $this->dateFields = explode( '/' , $dateFormat );
    }
    
    public function in( $date = null , $hour = null )
    {
        if( ! $date )
        {
            $dateTime = date( 'Y-m-d H:i:s' );
        }
        elseif( $this->validate( $date , self::INPUT_DATE ) )
        {
            $datePart = explode( '/' , $date );
            $date = array();
            
            foreach( $this->dateFields as $index => $formatPart )
            {
                $date[ $formatPart ] = $datePart[ $index ];
            }
            
            if( ! $hour || ! $this->validate( $hour , self::HOUR ) )
            {
                $hour = '00:00';
            }
            
            $dateTime = $date['Y']
                . '-' . $date['m']
                . '-' . $date['d']
                . ' ' . $hour . ':00' ;
        }
        else
        {
            $dateTime = false;
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
        if( $this->validate( $dateTime , self::DATETIME ) )
        {
            $offset = $secOffset
                + $minOffset*60
                + $hourOffset*3600
                + $dayOffset*86400
                + $weekOffset*604800;
            
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
    
    public function validate( $string , $type )
    {
        switch( $type )
        {
            case self::DATETIME:
                return date( 'Y-m-d H:i:s' , strtotime( $string ) ) == $string;
            
            case self::HOUR:
                $hourParts = explode( ':' , $string );
                
                if( count( $hourParts ) != 2 )
                    return false;
                
                foreach( $hourparts as $index => $part )
                {
                    $max = $index == 0 ? 24 : 60;
                    
                    if( ! is_numeric( $parts ) )
                        return false;
                    
                    if( strlen( $part ) != 2 )
                        return false;
                    
                    if( (int)$part > $max )
                        return false;
                }
                
                return true;
            
            case self::INPUT_DATE:
                $dateParts = explode( '/' , $string );
                
                if( count( $dateParts ) != 3 ) return false;
                
                foreach( $dateParts as $index => $field )
                {
                    if( ! is_numeric( $field ) ) return false;
                    
                    switch( $this->dateFields[ $index ] )
                    {
                        case 'Y':
                            if( strlen( $dateParts[ $index ] ) != 4 )
                                return false;
                            break;
                        
                        case 'm':
                            if( strlen( $dateParts[ $index ] ) != 2
                                && (int)$dateParts[ $index ] > 12 )
                                return false;
                            break;
                        
                        case 'd':
                            if( strlen( $dateParts[ $index ] ) != 2
                                && (int)$dateParts[ $index ] > 31 )
                                return false;
                            break;
                        
                        default:
                            throw new Exception( 'Error while parsing date' );
                    }
                }
                
                return true;
            
            default:
                throw new Exception('Bad date string type');
        }
    }
}