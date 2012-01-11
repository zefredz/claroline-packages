<?php

class Functions
{
    static function idOf($modelObject)
    {
        if(method_exists($modelObject, 'getId'))
        {
            return $modelObject->getId();
        }
        else
        {
            return $modelObject->id;
        }
    }
    
    static function textOf($modelObject)
    {
        if(method_exists($modelObject, 'getText'))
        {
            return $modelObject->getText();
        }
        else
        {
            return $modelObject->text;
        }
    }
}