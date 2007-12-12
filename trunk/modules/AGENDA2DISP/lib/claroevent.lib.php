<?php # -$Id$

/**
 * CLAROLINE
 *
 *
 * @version 1.8 $Revision$
 *
 * @copyright (c) 2001-2007 Universite catholique de Louvain (UCL)
 *
 * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 *
 * @package CLDATE
 *
 * @author Claro Team <cvs@claroline.net>
 * @author Michel Carbone <michel_c12@yahoo.fr>
 */

require_once 'clarodate.lib.php';

class claroEvent
{
    /**
    * Contructor of an event
    *
    * @param integer $startDate : timestamp
    * @param string $title
    * @param string $comment
    * @param integer $endDate : timestamp
    * @param string $author
    * @param string $url
    */
    function claroEvent($startDate, $title, $comment = null, $endDate = null, $author = null, $url = null)
    {
        $this->startDate = $startDate;
        $this->title      = $title;
        $this->comment   = $comment;
        $this->endDate   = $endDate;
        $this->author = $author;
        $this->url = $url;

    }
    
    /**
    * @return integer : the start date of an event
    */
    function getStartDate()
    {
        return $this->startDate;
    }
    
        
    /**
    * @return string :the title of an event
    */
    function getTitle()
    {
        return $this->title;
    }


    /**
    * @return string : the comment of an event
    */
    function getComment()
    {
        return $this->comment;
    }
    
    /**
    * @return integer : the end date of an event
    */
    function getEndDate()
    {
        return $this->endDate;
    }

    /**
    * @return integer : the ID of the author of an event
    */
    function getAuthor()
    {
        return $this->author;
    }
    
    /**
    * @return string : the url of an event
    */
    function getUrl()
    {
        return $this->url;
    }

    /**
    * create an abstract of an event in max 20 caracters
    * @return string : the abstract
    */
    function getAbstract()
    {
        $abstract = strip_tags($this->title);

        if ( trim($this->title) == '')
        {
            $maxCharCount = 20;

            $abstract = trim(strip_tags($this->comment));

            if ( strlen($abstract) > $maxCharCount)
            {
                $abstract = substr($this->comment, 0, $maxCharCount) . ' ...';
            }
        }

        return $abstract;
    }

    function sortList($eventList)
    {
        if(isset($eventList))
        usort($eventList, 
              create_function('$event1, $event2', 
                              'return 
                               $event1->getStartDate()->compare($event2->getStartDate());'
                             ) 
             );

        return $eventList;
    }

    
    
    /**
    * filter a list of event by the date with a precision define in sec, min, hour, day
    * @param array $eventList 
    * @param integer $startDate : timestamp
    * @param integer $endDate : timestamp
    * @param string $precision : define the precision for filter the list of event
    * @return array : array filtered of the eventlist
    */
    function filterListByDate($eventList, $startDate, $endDate, $precision = 'SEC')
    {


        $filterStartTime = $startDate;
        $filterEndTime   = $endDate;

        $functionStr = '$eventTime = $event->getStartDate()->getTimeStamp();' . "\n"
                     . 'return ! ($eventTime < '.$filterStartTime.' || $eventTime > '.$filterEndTime.');';
                     
        if(isset($eventList)){
        return array_filter($eventList, create_function('$event', $functionStr)) ;
        }
        else {
            return array_filter ($eventList = array(), create_function('$event', $functionStr));
        }
    }
}


?>
