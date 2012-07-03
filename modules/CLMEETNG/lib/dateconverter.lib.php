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
    
    static $translator = array( 'MM' => 'm',
                                'DD' => 'd',
                                'YYYY' => 'Y' );
    
    protected $dateFormat;
    
    public function __construct( $dateFormat = 'MM/DD/YYYY' )
    {
        $this->dateFormat = $this->_translate( $dateFormat );
    }
    
    public function in( $data )
    {
        
    }
    
    public function out( $date = null )
    {
        if( ! $date )
        {
            $date = date( 'Y-m-d H:i:s' );
        }
    }
    
    private function _translate( $date , $mode = self::MODE_IN )
    {
        $glue = $mode == self::MODE_IN ? '/' : ':';
        
        $explTpl = explode( $glue , $data );
        
        foreach( $explTpl as $index => $part )
        {
            $explTpl[ $index ] = $mode == self::MODE_IN
                                ? self::$translator[ $part ]
                                : array_search( $part , self::$translator );
        }
    }
}