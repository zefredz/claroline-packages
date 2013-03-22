<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.1 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class TicketManager
{
    protected $data = array();
    
    public function __construct()
    {
        $tbl = get_module_main_tbl( array( 'ichelp_log' ) );
        $this->tbl = $tbl[ 'ichelp_log' ];
        
        if( isset( $_SESSION[ 'ICHELP_data' ] )
            && ! empty($_SESSION[ 'ICHELP_data' ] ) )
        {
            $this->data = $_SESSION[ 'ICHELP_data' ];
        }
        else
        {
            $this->data[ 'submissionDate' ] = date( 'Y-m-d H:i:s' );
            $this->data[ 'userAgent' ] = $_SERVER['HTTP_USER_AGENT'];
            $this->data[ 'httpReferer' ] = $_SERVER['HTTP_REFERER'];
            //$this->data[ 'cookieEnabled' ] = isset( $_SERVER['HTTP_COOKIE'] );
            $this->data[ 'mailSent' ] = 0;
            
            $this->data[ 'ticketId' ] = md5(
                $this->data[ 'userAgent' ] .
                $this->data[ 'httpReferer' ] .
                $this->data[ 'submissionDate' ] .
                rand() ) .
                '-' .
                substr( md5( rand() ) , substr( $this->data[ 'submissionDate' ] , 11 , 2 ) , 8 );
            
            $_SESSION[ 'ICHELP_data' ] = $this->data;
        }
    }
    
    public function save()
    {
        $sql1 = implode( ',' , array_keys( $this->data ) );
        $sql2 = implode( "','" , $this->data);
        $sql = "( " . $sql1 ." )\nVALUES ( '" . $sql2 . "' )";
        
        if( Claroline::getDatabase()->exec( "
                INSERT INTO `{$this->tbl}`" . $sql ) )
        {
            $this->flush();
            
            return $this->get( 'ticketId' );
        }
        else
        {
            return false;
        }
    }
    
    public function flush()
    {
        unset( $_SESSION[ 'ICHELP_data' ] );
    }
    
    public function get( $name )
    {
        if( array_key_exists( $name , $this->data ) )
        {
            return $this->data[ $name ];
        }
        else
        {
            return false;
        }
    }
    
    public function set( $name , $value )
    {
        $this->data[ $name ] = $value;
    }
    
    public function getData()
    {
        return $this->data;
    }
}