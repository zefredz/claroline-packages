<?php 

class Guest extends Claro_User
{
    
    public $userId = null;
    public $firstName = "Anon";
    public $lastName = "Ymous";
    public $email = "guest@nowhere.com";
    public $isCourseCreator = false;
    public $isPlatformAdmin = false;
    
    
    public function __construct() {
        parent::__construct($this->userId);
    }
    

}

