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
 */

require_once 'clarodate.lib.php';

class claroEvent
{
    function claroEvent($startDate, $title, $comment = null, $endDate = null, $author = null, $url = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->title      = $title;
        $this->comment   = $comment;
        $this->url = $url;
        $this->author = $author;
    }

    /**
    * return the url of an event
    */
    function getUrl()
    {
        return $this->url;
    }

    /**
    * return the start date of an event
    */
    function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
    * return the title of an event
    */
    function getTitle()
    {
        return $this->title;
    }

    /**
    * return the comment of an event
    */
    function getComment()
    {
        return $this->comment;
    }

    /**
    * create an abstract of an event in max 20 caracters
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

    /**
    * return the end date of an event
    */
    function getEndDate()
    {
        return $this->endDate;
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
