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
    protected $ticketList = array();
    
    public function __construct()
    {
        $tbl = get_module_main_tbl( array( 'ichelp_log' ) );
        $this->tbl = $tbl[ 'ichelp_log' ];
        
        $this->load();
    }
    
    public function load()
    {
        $ticketList = Claroline::getDatabase()->query( "
            SELECT
                ticketId,
                submissionDate,
                shortDescription,
                issueDescription,
                userInfos
            FROM
                `{$this->tbl}`
            WHERE
                mailSent = 0"
        );
        
        foreach( $ticketList as $ticket )
        {
            $data = json_decode( $ticket[ 'userInfos' ] );
            
            $ticketData = array(
                'submissionDate' => $ticket[ 'submissionDate' ],
                'shortDescription' => $ticket[ 'shortDescription' ],
                'issueDescription' => $ticket[ 'issueDescription' ],
                'userName' => $data->firstName . ' ' . $data->lastName,
                'mail' => $data->mail );
            
            $this->ticketList[ $ticket[ 'ticketId' ] ] = $ticketData;
        }
    }
    
    public function getTicketList()
    {
        return $this->ticketList;
    }
    
    public function ticketExists( $ticketId )
    {
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