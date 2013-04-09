<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.8 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class TicketManager
{
    const SAVE_MODE_INSERT = false;
    const SAVE_MODE_UPDATE = true;
    
    protected $data = array();
    
    public function __construct( $ticketId = null )
    {
        $tbl = get_module_main_tbl( array( 'ichelp_log' ) );
        $this->tbl = $tbl[ 'ichelp_log' ];
        
        if( $ticketId )
        {
            $this->load( $ticketId );
        }
        elseif( isset( $_SESSION[ 'ICHELP_data' ] )
            && ! empty( $_SESSION[ 'ICHELP_data' ] ) )
        {
            $this->data = $_SESSION[ 'ICHELP_data' ];
        }
        else
        {
            $this->data[ 'submissionDate' ] = date( 'Y-m-d H:i:s' );
            $this->data[ 'userAgent' ] = $_SERVER['HTTP_USER_AGENT'];
            //$this->data[ 'httpReferer' ] = $_SERVER['HTTP_REFERER'];
            //$this->data[ 'cookieEnabled' ] = isset( $_SERVER['HTTP_COOKIE'] );
            $this->data[ 'mailSent' ] = 0;
            
            $this->data[ 'ticketId' ] = md5(
                $this->data[ 'userAgent' ] .
                //$this->data[ 'httpReferer' ] .
                $this->data[ 'submissionDate' ] .
                rand() ) .
                '-' .
                substr( md5( rand() ) , substr( $this->data[ 'submissionDate' ] , 11 , 2 ) , 8 );
            
            $this->update();
        }
    }
    
    public function load( $ticketId = null )
    {
        if( ! $ticketId )
        {
            if( $this->get( 'ticketId' ) )
            {
                $ticketId = $this->get( 'ticketId' );
            }
            else
            {
                throw new Exception( 'Missing id' );
            }
        }
        
        $data = Claroline::getDatabase()->query( "
            SELECT
                userId,
                courseId,
                submissionDate,
                userAgent,
                urlOrigin,
                userInfos,
                issueDescription,
                shortDescription,
                mailSent,
                autoMailSent /*,status */
            FROM
                `{$this->tbl}`
            WHERE
                ticketId = " . Claroline::getDatabase()->quote( $ticketId )
        )->fetch( Database_ResultSet::FETCH_ASSOC );
        
        if( ! empty( $data ) )
        {
            $this->data = $data;
            
            return $this->data[ 'ticketId' ] = $ticketId;
        }
    }
    
    public function save( $mode = self::SAVE_MODE_INSERT )
    {
        $sql1 = implode( ',' , array_keys( $this->data ) );
        $sql2 = implode( "','" , $this->data );
        $sql3 = "( " . $sql1 ." )\nVALUES ( '" . $sql2 . "' )";
        
        if( $mode == self::SAVE_MODE_UPDATE )
        {
            $sql = "UPDATE `{$this->tbl}`\n" . $sql3
                . "\nWHERE ticketId = " . Claroline::getDatabase()->quote( $this->data[ 'ticketId' ] );
        }
        else
        {
            $sql = "INSERT INTO `{$this->tbl}`\n" . $sql3;
        }
        
        if( Claroline::getDatabase()->exec( $sql ) )
        {
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
        
        if( isset( $_SESSION[ 'ICHELP_data' ] ) && ! is_null( $_SESSION[ 'ICHELP_data' ] ) )
        {
            $this->update();
        }
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    private function update()
    {
        $_SESSION[ 'ICHELP_data' ] = $this->data;
    }
}