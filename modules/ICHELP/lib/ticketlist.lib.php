<?php // $Id$

/** Online Help Form
 *
 * @version     ICHELP 0.8 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2013 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     ICHELP
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */

class TicketList
{
    public $chronologicOrder = false;
    public $showFailedOnly = false;
    
    protected $ticketList = false;
    
    public function __construct()
    {
        $tbl = get_module_main_tbl( array( 'ichelp_log' ) );
        $this->tbl = $tbl[ 'ichelp_log' ];
        
        //$this->load();
    }
    
    public function load( $showFailedOnly = false , $chronologicOrder = false , $all = false )
    {
        $this->showFailedOnly = (boolean)$showFailedOnly;
        $this->chronologicOrder = (boolean)$chronologicOrder;
        
        $sqlString = "SELECT
                ticketId,
                submissionDate,
                shortDescription,
                issueDescription,
                userInfos,
                mailSent
            FROM
                `{$this->tbl}`";
        
        if( ! $all )
        {
            $sqlString .= "\nWHERE
                status = 'pending'";
        
            if( $this->showFailedOnly )
            {
                $sqlString .= "\nAND
                    mailSent = 0";
            }
        }
        
        if( ! $this->chronologicOrder )
        {
            $sqlString .= "\nORDER BY
                submissionDate DESC";
        }
        
        $ticketList = Claroline::getDatabase()->query( $sqlString );
        
        $this->ticketList = array();
        
        foreach( $ticketList as $ticket )
        {
            $data = unserialize( $ticket[ 'userInfos' ] );
            
            $ticketData = array(
                'submissionDate' => $ticket[ 'submissionDate' ],
                'shortDescription' => $ticket[ 'shortDescription' ],
                'issueDescription' => $ticket[ 'issueDescription' ],
                'userName' => $data[ 'firstName' ] . ' ' . $data[ 'lastName' ],
                'mail' => $data[ 'mail' ] );
            
            $this->ticketList[ $ticket[ 'ticketId' ] ] = $ticketData;
        }
    }
    
    public function getTicketList( $refresh = false )
    {
        if( is_null( $this->ticketList ) || $refresh )
        {
            $this->load();
        }
        
        return $this->ticketList;
    }
    
    public function ticketExists( $ticketId )
    {
        if( is_null( $this->ticketList ) )
        {
            $this->load();
        }
        
        return array_key_exists( $ticketId , $this->ticketList );
    }
    
    public function getTicket( $ticketId )
    {
        if( $this->ticketExists( $ticketId ) )
        {
            return new TicketManager( $ticketId );
        }
    }
}