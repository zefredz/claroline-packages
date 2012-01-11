<?php
FromKernel::uses('utils/validator.lib');

class DateValidator implements Claro_Validator
{
    private static $_instance = NULL;
    
    private function __construct(){}
    
    public static function getInstance()
    {
        if(empty(self::$_instance))
        {
            self::$_instance = new DateValidator();
        }
        
        return self::$_instance;
    }
    
    public function isValid($value)
    {
        if(!preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{2})$/",$value,$parts))
        {
            return false;
        }
        
        return checkdate($parts[2],$parts[1],$parts[3]);
    }
    
    public function getTimeStamp($date)
    {
        if(preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{2})$/",$date,$parts))
        {
            return mktime(0,0,0, intval($parts[2]),intval($parts[1]), intval($parts[3]));
        }
        else
        {
            return 0;
        }
    }
}