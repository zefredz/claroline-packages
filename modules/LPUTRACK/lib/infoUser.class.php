<?php

/**
 * Information about a user
 * 
 * @version LPUTRACK 1.0
 * @package LPUTRACK
 * @author Anh Thao PHAM <anhthao.pham@claroline.net>
 */
class InfoUser
{
    private $userId;
    private $firstName;
    private $lastName;
    
    /**
     * Constructor
     * @param int $userId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct( $userId, $firstName, $lastName )
    {
        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
    
    /**
     * Get the id of the user
     * @return int The id of the user
     */
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * Get the first name of the user
     * @return string The first name of the user
    */
    public function getFirstName()
    {
        return $this->firstName;
    }
    
    /**
     * Get the last name of the user
     * @return string The last name of the user
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}

?>
