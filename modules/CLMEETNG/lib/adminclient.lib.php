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

class CLMEETNG_Admin extends CLMEETNG_OpenMeetingsClient
{
    public function AddUser( $userName , $password , $firstname , $lastname , $email , $lang = 1 )
    {
        return $this->_callService( 'user' ,
                                    'addNewUser' ,
                                    array( 'username' => $userName ,
                                           'userpass' => $password ,
                                           'firstname' => $firstname ,
                                           'lastname' => $lastname ,
                                           'email' => $email ,
                                           'language_id' => $lang ) );
    }
}