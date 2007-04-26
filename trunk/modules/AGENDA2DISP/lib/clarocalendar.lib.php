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

require_once 'claroevent.lib.php';
//require_once 'clarodate.lib.php';


/**
* Class Calendar
*/

class monthView
{
    /**
    * function that display the month view of $referenceDate with $eventList
    * @param $referenceDate : object Date
    * @param $eventList : eventlist to display in the view to paint
    * @param $view : set the type of view needed by function MonthView
     */
    
    function monthViewDisplay( $referenceDate, $eventList, $format, $view, $refMonth=null, $refYear=null)
    {
        $titleName = get_lang('Month view');

        if ($view != 'yearview')
        {
            echo '<h3>'.$titleName.'</h3>';
        }
        if(!isset($refYear))
            $refYear = clarodate::getYearFromTimeStamp($referenceDate);
        if(!isset($refMonth))
            $refMonth = clarodate::getMonthFromTimeStamp($referenceDate);
        if ( isset($refMonth) && isset($refYear) )
            $referenceDate = mktime(0, 0, 0, $refMonth, 1, $refYear);
        $lastDay = claroDate::getLastDayFromTimeStamp($referenceDate);
        $dayCountInMonth = claroDate::getLastDayFromTimeStamp($referenceDate);
        $monthStartDate = clarodate::monthStartDateFromTimeStamp($referenceDate);
        
        $monthEndDate = clarodate::monthEndDateFromTimeStamp($referenceDate);
        
        if( !empty($eventList) ) 
            $monthEventList = claroEvent::filterListByDate($eventList, $monthStartDate, $monthEndDate);

        $tableWidth = ($format == 'SHORT') ?  '' : ' width="100%"';
        
       
        $backwardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.($refMonth==1 ? 12 : $refMonth-1)
            .'&amp;refYear='.($refMonth==1 ? $refYear-1 : $refYear) . '&amp;cmd=monthview';

        $forewardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.($refMonth==12 ? 1 : $refMonth+1)
            .'&amp;refYear='.($refMonth==12 ? $refYear+1 : $refYear)  . '&amp;cmd=monthview' ;

        echo  '<table class="claroTable" border="1" '.$tableWidth.'>'. "\n"
            . '<tr>'  . "\n";
       

        if ($view != 'yearview') 
        {
            echo '<th class="superHeader" valign="top"><center>'
            .    '<a href="' . $backwardsURL . '">&lt;&lt;</a>'
            .    '</center>'
            .    '</th>';
        }

        echo '<th  class="superHeader" colspan="' . ( ($view != 'yearview') ? '5' : '7' ) . '"'
            . ' valign="top"><center>' . claroDate::getMonthNameFromTimeStamp($monthStartDate).' '.$refYear.'</center></th>'. "\n";
        

        if ($view!='yearview')
        {
            echo '<th class="superHeader" valign="top"><center>'
            .    '<a href="' . $forewardsURL . '">&gt;&gt;</a>'
            .    '</center>'
            .    '</th>';
        }
        
        echo "</tr>"  . "\n";
        
        $dayNameFormat = ( $format == 'SHORT' ) ?  CAL_DOW_SHORT : CAL_DOW_LONG;
        $dayNameList = claroDate::getDayNameList($dayNameFormat);

        for ($i=0; $i < 7; $i ++) // HEADER
        {
            echo ('<th class="superHeader" valign="top">' . $dayNameList[$i] . '</th>');
        }

        echo '</tr>' . "\n";

        $startDayCountDisplay = false;
        $dayNumber            = false;

        while ($dayNumber <= $dayCountInMonth)
        {
            echo '<tr class="headerX" valign="top">';
   
            for ($i = 0; $i < 7; $i++)
            {
                if ( (!$dayNumber) && (claroDate::getNbrDayofWeekFromTimeStamp($monthStartDate) == $i) )
                {
                    $dayNumber = 1;
                }

                if ( $dayNumber && $dayNumber <= $dayCountInMonth)
                {
                    $dayDate = claroDate::rollDaysFromTimeStamp($monthStartDate,($dayNumber-1));

                     if( isset($eventList) )
                     {
                        $dayEventList = 
                        claroEvent::sortList(
                            claroEvent::filterListByDate( 
                                $monthEventList, 
                                $dayDate, 
                                claroDate::rollSecondsFromTimeStamp(
                                        $dayDate,24 * 60 * 60 -1
                                        ) 
                            ) 
                       );
                                        }
                    if ( $format == 'SHORT' ) 
                    {
                        if ( isset($eventList) && count($dayEventList) > 0 )
                            $content = '<b style="color:blue">'.$dayNumber.'</b>';
                        else
                            $content = $dayNumber;
                    }
                    else
                    {
                        $content = '<p>'.$dayNumber.'</p>';
                        if(isset($dayEventList)){
                             foreach($dayEventList as $thisEvent)
                             {
                                 $content .= '<p>'.$thisEvent->getTitle().'</p>';
                             }
                        }
                    }
                    $dayNumber++;
                }
                else
                {
                    $content = '&nbsp;';
                }
                echo '<td width="14%">'.$content.'</td>';
            }
            echo '</tr>' . "\n";
        }
        echo '</table>'. "\n";
    }
        
    
}

class YearView
{
    function yearViewDisplay($referenceDate, $eventList)
    {
    
    $titleName = get_lang('Year view');

    echo '<h3>'.$titleName.'</h3>';

        
     if(!isset($refYear))
        {
        $refYear = clarodate::getYearFromTimeStamp($referenceDate);
        }

        $backwardsURL = $_SERVER['PHP_SELF']
            .'?refYear='.($refYear-1) . '&amp;cmd=yearview';

        $forewardsURL = $_SERVER['PHP_SELF']
            .'?refYear='.($refYear+1) . '&amp;cmd=yearview';


            echo  '<table class="claroTable" border="0" >'. "\n"
            . '<tr>'  . "\n";

        
            echo '<th class="superHeader" valign="top"><center>'
            .    '<a href="' . $backwardsURL . '">&lt;&lt;</a>'
            .    '</center>'
            .    '</th>';
        

        echo '<th  class="superHeader" '
            . ' valign="top"><center>' .$refYear.'</center></th>'. "\n";
       

                
            echo '<th class="superHeader" valign="top"><center>'
            .    '<a href="' . $forewardsURL . '">&gt;&gt;</a>'
            .    '</center>'
            .    '</th>';
        
        
        echo "</tr>"  . "\n";

        for ($i=0; $i < 12; $i++)
        {
            if ( $i%3 == 0 ) echo '<tr valign="top">' . "\n";

            $refMonthDate = new ClaroDate($refYear . '-' . ($i+1) . '-01');
            
            echo '<td>'  . "\n";
            monthView::monthViewDisplay($referenceDate, $eventList, 'SHORT', 'yearview', $i+1, $refYear);
            echo '</td>' . "\n";

            if ( ($i+1) %3 == 0 ) echo '</tr>' . "\n";
        }
        
        echo '</table>';
    }
}

class weekView
{
    function weekViewDisplay($referenceDate, $eventList)
    {
        $refYear = claroDate::getYearFromTimeStamp($referenceDate);
        $refDay = claroDate::getDayFromTimeStamp($referenceDate);
        $refMonth = claroDate::getMonthFromTimeStamp($referenceDate);
        $referenceDateForwards = mktime(0, 0, 0, $refMonth, $refDay+7, $refYear);
        $referenceDateBackwards = mktime(0, 0, 0, $refMonth, $refDay-7, $refYear);
        
        echo '<h3>'. get_lang('Week View').'</h3>';

        
        $weekStartDate = claroDate::rollDaysFromTimeStamp($referenceDate,(claroDate::getNbrDayofWeekFromTimeStamp($referenceDate) - 1 )* -1);
        echo claroDate::getDayofWeekFromTimeStamp($referenceDate);
        $weekEndDate   = claroDate::rollDaysFromTimeStamp($weekStartDate,6);

        $weekEventList = claroEvent::filterListByDate($eventList, $weekStartDate, $weekEndDate);
       

        $backwardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.(claroDate::getMonthFromTimeStamp($referenceDateBackwards) )
            .'&amp;refYear='.(claroDate::getYearFromTimeStamp($referenceDateBackwards) )
            .'&amp;cmd=weekview'
            .'&amp;refDay='.(claroDate::getDayFromTimeStamp($referenceDateBackwards) );
            
        $forewardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.(claroDate::getMonthFromTimeStamp($referenceDateForwards) )
            .'&amp;refYear='.(claroDate::getYearFromTimeStamp($referenceDateForwards) )
            .'&amp;cmd=weekview'
            .'&amp;refDay='.(claroDate::getDayFromTimeStamp($referenceDateForwards) );
            
        echo  "\n";
        echo '<table class="claroTable" border="1">';
        echo'<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $backwardsURL . '">&lt;&lt;</a></center>';

        echo '<th class="superHeader" width="70%" valign="top"><center>'.get_lang('Week').' n&deg;'. claroDate::getWeekofYearFromTimeStamp($referenceDate).'</center></th>';

        echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $forewardsURL . '">&gt;&gt;</a></center>'.'</th>';

        for ($i = 0; $i < 7; $i++)
        {
            $dayDate      = claroDate::rollDaysFromTimeStamp($weekStartDate, $i);
            if( isset($weekEventList) )
            {
                $dayEventList = claroEvent::sortList(claroEvent::filterListByDate($weekEventList, $dayDate, claroDate::rollSecondsFromTimeStamp($dayDate, 24 * 60 * 60 -1) ) );
            }

            echo  '<tr>' . "\n"
                . '<td>' . "\n"
                . claroDate::getFormateddateFromTimeStamp($dayDate,'Y-M-d') . "\n"
                .'</td>' . "\n"
                . '<td>' . "\n";

            if ( !empty($dayEventList) )
            {
                foreach($dayEventList as $thisEvent)
                {
                    echo '<p>'.$thisEvent->getTitle().'</p>' . "\n";
                }
            }
            else
            {
                echo '&nbsp;';
            }

            echo  '</td>' . "\n";
                foreach($dayEventList as $thisEvent)
                {
                    echo '<td>'. $thisEvent->getUrl(). '</td>';
                }
                
                echo '</tr>' . "\n";
        }

        echo '</table>' . "\n";

    }
}
    
    /**
* Class DayView
*/

class DayView
{

    /**
    * function that display the day view of $referenceDate with $eventList
    */
    function dayViewDisplay($referenceDate, $eventList)
    {
        echo '<h3>'. get_lang('Day View').'</h3>';

        $refYear  = claroDate::getYearFromTimeStamp($referenceDate);
        $refMonth = claroDate::getMonthFromTimeStamp($referenceDate);
        $refDay   = claroDate::getDayFromTimeStamp($referenceDate);

        $referenceDateForwards = mktime(0, 0, 0, $refMonth, $refDay+1, $refYear);
        $referenceDateBackwards = mktime(0, 0, 0, $refMonth, $refDay-1, $refYear);
        
        $dayStart = mktime(0, 0, 0, $refMonth, $refDay, $refYear);
        $dayEnd   = claroDate::rollSecondsFromTimeStamp($dayStart, 24 * 60 * 60 - 1 );


         $backwardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.(claroDate::getMonthFromTimeStamp($referenceDateBackwards) )
            .'&amp;refYear='.(claroDate::getYearFromTimeStamp($referenceDateBackwards) )
            .'&amp;cmd=dayview'
            .'&amp;refDay='.(claroDate::getDayFromTimeStamp($referenceDateBackwards) );
            
        $forewardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.(claroDate::getMonthFromTimeStamp($referenceDateForwards) )
            .'&amp;refYear='.(claroDate::getYearFromTimeStamp($referenceDateForwards) )
            .'&amp;cmd=dayview'
            .'&amp;refDay='.(claroDate::getDayFromTimeStamp($referenceDateForwards) );

        if( !empty($eventList) )
        {
            $dayEventList = claroEvent::sortList(claroEvent::filterListByDate($eventList, $dayStart, $dayEnd));
        }

        if ( isset($dayEventList[0]) && $dayEventList[0]->getStartDate()->getFormatedDate('H') < 8 )
        {
            $startViewHour = $dayEventList[0]->getStartDate()->getTimeStamp();
        }

        /// default start hour of the day view
        else
        {
        	$startViewHour = mktime(8, 0, 0, $refMonth, $refDay, $refYear);
        }

        if ( isset($dayEventList[count($dayEventList)-1]) && $dayEventList[count($dayEventList)-1]->getStartDate()->getFormatedDate('H') > 18 )
        {
            $endViewHour = $dayEventList[count($dayEventList)-1]->getStartDate()->getTimeStamp();
        }

        /// default end hour of the day view
        else
        {
        	$endViewHour = mktime(18, 0, 0, $refMonth, $refDay, $refYear);
        }

        echo "\n".'<table class="claroTable" border="1">' . "\n";
              echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $backwardsURL . '">&lt;&lt;</a></center>'.'</th>';
        echo '<th class="superHeader" valign="top"><center>'  . "\n"
             .claroDate::getFormatedDateFromTimeStamp($referenceDate, 'Y M d') . "\n"
             .'</center></th>'."\n";
        echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $forewardsURL . '">&gt;&gt;</a></center>';
        echo'</th></tr>'."\n";

        for ($i =  claroDate::getHourFromTimeStamp($startViewHour); $i <= claroDate::getHourFromTimeStamp($endViewHour); $i++)
        {	
            echo  '<tr valign="top">'  . "\n". '<td>';
            
            echo str_pad($i,2,"0", STR_PAD_LEFT); //display of a "0" if $i < 10

            echo' : 00 </td>' . "\n". '<td>'. "\n";
            

            $hourStart = mktime($i, 0, 0, $refMonth, $refDay, $refYear);

            $hourEnd = claroDate::rollSecondsFromTimeStamp($hourStart, 60*60 - 1);

            if ( !empty($dayEventList))
            {
                $hourEventList = claroEvent::sortList(claroEvent::filterListByDate($dayEventList, $hourStart, $hourEnd));
                if( !empty($hourEventList) )
                {
                    foreach($hourEventList as $thisEvent)
                    {
                        echo '<p>'.$thisEvent->getTitle().'</p>';
                    }
                }
                else
                {
                    echo  '&nbsp;';
                }
            }
            echo  '</td>'. '<td width="15%">'.'&nbsp;'.'</td>'.'</tr>';
        }
        echo '</table>';
       
    }
}

/**
* Class ListView
*/

class ListView
{
    function listViewDisplay($referenceDate, $eventList)
    {
        echo '<h3>List View</h3>';

        $now = mktime();

        $monthDateBar  = mktime(0,0,0, 01, 01, 1970); // Epoch
        $nowBarPainted = false;

        echo '<table class="claroTable" border="1">' . "\n";

        if ( isset($eventList) )
        {
            foreach( $eventList as $thisEvent)
            {
                $thisEventDate = $thisEvent->getStartDate()->getTimeStamp();

                if ( claroDate::getMonthFromTimeStamp($monthDateBar) != claroDate::getMonthFromTimeStamp($thisEventDate) )
                {
                    $monthDateBar = mktime(0, 0, 0, claroDate::getMonthFromTimeStamp( $thisEventDate), 01, claroDate::getYearFromTimeStamp($thisEventDate) );

                    $dayNameList = claroDate::getDayNameList();

                    // MONTH BAR
                    echo '<tr>' . "\n"                    
                    .    '<th class="superHeader" colspan="7" valign="top">' . "\n"
                    .    claroDate::getMonthNameFromTimeStamp($monthDateBar)
                    .    '</th>' . "\n"
                    .    '</tr>' . "\n"
                    ;
                }

                if (  $thisEventDate > $now && ! $nowBarPainted )
                {
                    echo '<tr><td align="center"> -- '.get_lang('NOW').' -- </td></td>';
                    $nowBarPainted = true;
                }

                echo  '<tr>' . "\n"
                    . '<th class="headerX">'.claroDate::getFormatedDateFromTimeStamp($thisEventDate, 'd M Y H:m').'</th>' . "\n"
                    . '</tr>' . "\n";

                echo  '<tr>' . "\n"
                    . '<td>'
                    . '<h4>'.htmlspecialchars($thisEvent->getTitle()) .'</h4>'
                    . $thisEvent->getComment()
					. $thisEvent->getUrl()
                    . '</td>' . "\n"
                    . '</tr>' . "\n";
            }
        }
        else
        {
            echo (get_lang('No event to display'));
        }
        echo '</table>' . "\n";
    }
}
