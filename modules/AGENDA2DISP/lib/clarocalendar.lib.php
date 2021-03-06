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
 * @autor Michel Carbone <michel_c12@yahoo.fr>
 */

// TODO : remake the month view from the original calendar of Claroline : use of the exacts
//        CSS tags and i.e. highlight for the current date

require_once 'claroevent.lib.php';


/**
* Class Calendar
*/

class monthView
{

    /**
    * function that display a month view of the agenda
    *
    * @param integer $referenceDate : timestamp
    * @param array $eventList : eventlist to display in the view
    * @param string $format : short or long view of a month
    * @param string $view : set the type of view needed
    * @param integer $refMonth
    * @param integer $refYear
    * @return echo html
     */
    function monthViewDisplay( $referenceDate, $eventList, $format, $view, $refMonth=null, $refYear=null)
    {
        if(!isset($_REQUEST['cmd']))
        $_REQUEST['cmd']='monthview';
        
        $monthEventList = array();
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

        $tableWidth = ($format == 'SHORT') ?  'width="100%"' : ' width="100%"';
        
       
        $backwardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.($refMonth==1 ? 12 : $refMonth-1)
            .'&amp;refYear='.($refMonth==1 ? $refYear-1 : $refYear) . '&amp;cmd=monthview';

        $forewardsURL = $_SERVER['PHP_SELF']
            .'?refMonth='.($refMonth==12 ? 1 : $refMonth+1)
            .'&amp;refYear='.($refMonth==12 ? $refYear+1 : $refYear)  . '&amp;cmd=monthview' ;

        echo  '<table class="claroTable" border="1" '.$tableWidth.'>'. "\n"
            . '<tr class="superHeader">'  . "\n";
       

        if ($view != 'yearview') 
        {
            echo '<th><center>'
            .    '<a href="' . $backwardsURL . '">&lt;&lt;</a>'
            .    '</center>'
            .    '</th>';
        }

        echo '<th colspan="' . ( ($view != 'yearview') ? '5' : '7' ) . '"'
            . ' valign="top"><center>' . claroDate::getMonthNameFromTimeStamp($monthStartDate).' '.$refYear.'</center></th>'. "\n";
        

        if ($view!='yearview')
        {
            echo '<th><center>'
            .    '<a href="' . $forewardsURL . '">&gt;&gt;</a>'
            .    '</center>'
            .    '</th>';
        }
        
        echo "</tr>"  . "\n";
        
        $dayNameFormat = ( $format == 'SHORT' ) ?  CAL_DOW_SHORT : CAL_DOW_LONG;
        $dayNameList = claroDate::getDayNameList($dayNameFormat);
        
        echo "<tr class=\"headerX\">";
        
        for ($i=0; $i < 7; $i ++) // HEADER
        {
            echo ('<th width="14%">' . $dayNameList[$i] . '</th>');
        }

        echo '</tr>';

        $startDayCountDisplay = false;
        $dayNumber            = false;
        
        $nbrweekinmonth=0;
        while ($dayNumber <= $dayCountInMonth)
        {
            $nbrweekinmonth++;
           
            echo '<tr>'."\n";
   
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
                        if ( isset($eventList) && count($dayEventList) > 0 ){
                            $content = '<b style="color:blue">'.'<a href="' . $_SERVER['PHP_SELF'] .'?cmd=dayview&amp;refYear='.$refYear.'&amp;refMonth='.$refMonth.'&amp;refDay='.$dayNumber.'">';
                            $content .= $dayNumber.'</a>'.'</b>';
                            }
                        else
                            $content = $dayNumber;
                    }
                    else
                    {
                        $content = '<table>';
                        
                        $content .='<tr valign="top"';
                        $content .= 'class="';
                        if($i<5){
                            $content .= 'workingWeek">';
                            }
                            else $content .= 'weekEnd">';
                        $content .= '<br><b>'. $dayNumber .'</b></tr></td>';
                        if(isset($dayEventList)){
                             foreach($dayEventList as $thisEvent)
                             {
                                 $content .= '<tr><p>'.'<a href="' . $_SERVER['PHP_SELF'] .'?cmd=dayview&amp;refYear='.$refYear.'&amp;refMonth='.$refMonth.'&amp;refDay='.$dayNumber.'">';
                                 $content .= wordwrap($thisEvent->getTitle() , 12, "\n", 1) .'</a>'.'</p></tr>';
                                
                             }
                        }
                         $content .= '</table>';
                    }

                    $dayNumber++;
                }
                else
                {
                    $content = '&nbsp;';
                }
                echo '<td width="14%">'. $content .'</td>'."\n";
            }
            echo '</tr>' . "\n";
        }
        
        // if month has less than 6 weeks to display, display of one or 2 rows of blank cells
        if('yearview' == $_REQUEST['cmd'])
        for ( $nbrweekinmonth<6; $nbrweekinmonth<6; $nbrweekinmonth++)
            { 
                echo '<tr>';
                for ($i = 0; $i < 7; $i++)
                    {
                        echo '<td width="14%" valign="top">'. '&nbsp;' .'</td>';
                    }
                echo '</tr>';
            }

        echo '</table>'. "\n";
        # echo claroDate::getLastDayFromTimeStamp(mktime());
        # $date = new claroDate();
        # $date1 = new claroDate($date+100);
        # var_dump($date->after($date1));
        # var_dump($date->before($date1));
    }
}

class YearView
{

    /**
    * function that display a year view of the events of the agenda
    * Use the monthViewDisplay function for the display of each month of the calendar
    *
    * @param integer $referenceDate : time stamp  for choose of the year to display
    * @param array $eventList
    * @return echo html
    */
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


            echo  '<table class="claroTable" border="0" width="100%">'. "\n"
            . '<tr>'  . "\n";

        
            echo '<th class="superHeader" valign="top" width="10%"><center>'
            .    '<a href="' . $backwardsURL . '">&lt;&lt;</a>'
            .    '</center>'
            .    '</th>';
        

        echo '<th  class="superHeader" '
            . ' valign="top" width="80%"><center>' .$refYear.'</center></th>'. "\n";
       

                
            echo '<th class="superHeader" valign="top" width="10%"><center>'
            .    '<a href="' . $forewardsURL . '">&gt;&gt;</a>'
            .    '</center>'
            .    '</th>';
        
        
        echo "</tr></table>"  . "\n";

        for ($i=0; $i < 12; $i++)
        {
            if ( $i%3 == 0 ) echo '<table width="100%" border="0"><tr valign="top">' . "\n";

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

    /**
    * function that display a week view of the events of the agenda
    *
    * @param integer $referenceDate : time stamp  for choose of the week to display
    * @param array $eventList
    * @return echo html
    */
    function weekViewDisplay($referenceDate, $eventList)
    {
        $refYear = claroDate::getYearFromTimeStamp($referenceDate);
        $refDay = claroDate::getDayFromTimeStamp($referenceDate);
        $refMonth = claroDate::getMonthFromTimeStamp($referenceDate);
        $referenceDateForwards = mktime(0, 0, 0, $refMonth, $refDay+7, $refYear);
        $referenceDateBackwards = mktime(0, 0, 0, $refMonth, $refDay-7, $refYear);
        
        echo '<h3>'. get_lang('Week View').'</h3>';

        
        $weekStartDate = claroDate::rollDaysFromTimeStamp($referenceDate,(claroDate::getNbrDayofWeekFromTimeStamp($referenceDate) - 1 )* -1);
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
        echo '<table class="claroTable" border="1" width="100%">';
        echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $backwardsURL . '">&lt;&lt;</a></center>';

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
                . '<td><b>' . "\n"
                . claroDate::getFormateddateFromTimeStamp($dayDate,'Y-M-d') . "\n"
                .'</b></td>' . "\n"
                . '<td>' . "\n";

            if ( !empty($dayEventList) )
            {
                foreach($dayEventList as $thisEvent)
                { 
                    echo '<p><b>'.claroDate::getFormatedDateFromTimeStamp($thisEvent->getStartDate()->getTimeStamp(), 'H:i').'  </b>'.$thisEvent->getTitle().'</p>' . "\n";
                }
            }
            else
            {
                echo '&nbsp;';
            }

            echo  '</td>' . "\n". '<td>';
                foreach($dayEventList as $thisEvent)
                {
                    echo '<p>'. $thisEvent->getUrl(). '</p>';
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
    * function that display a day view of the events of the agenda
    *
    * @param integer $referenceDate : reference time stamp for choose of the day to display
    * @param array $eventList
    * @return echo html
    */
    function dayViewDisplay($referenceDate, $eventList)
    {
        /// initialisation of the array to avoid notice
        $dayEventList=array();
        $hourEventList=array();
        
        /// display the title
        echo '<h3>'. get_lang('Day View').'</h3>';

        /// define the variables of time
        $refYear  = claroDate::getYearFromTimeStamp($referenceDate);
        $refMonth = claroDate::getMonthFromTimeStamp($referenceDate);
        $refDay   = claroDate::getDayFromTimeStamp($referenceDate);

        $referenceDateForwards = mktime(0, 0, 0, $refMonth, $refDay+1, $refYear);
        $referenceDateBackwards = mktime(0, 0, 0, $refMonth, $refDay-1, $refYear);
        
        $dayStart = mktime(0, 0, 0, $refMonth, $refDay, $refYear);
        $dayEnd   = claroDate::rollSecondsFromTimeStamp($dayStart, 24 * 60 * 60 - 1 );

        /// creation of the url
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
        
        /// if start hour is smaller than default start hour
        //  TODO : preference in tools panel for setting the default start and end hour by the user
        
        if ( isset($dayEventList[0]) && $dayEventList[0]->getStartDate()->getFormatedDate('H') < 8 )
        {
            $startViewHour = $dayEventList[0]->getStartDate()->getTimeStamp();
        }

        /// default start hour of the day view
        else
        {
        	$startViewHour = mktime(8, 0, 0, $refMonth, $refDay, $refYear);
        }
        
        /// if start hour is smaller than default start hour
        if ( isset($dayEventList[count($dayEventList)-1]) && $dayEventList[count($dayEventList)-1]->getStartDate()->getFormatedDate('H') > 18 )
        {
            $endViewHour = $dayEventList[count($dayEventList)-1]->getStartDate()->getTimeStamp();
        }

        /// default end hour of the day view
        else
        {
        	$endViewHour = mktime(18, 0, 0, $refMonth, $refDay, $refYear);
        }

        /// display the first row with title and navigation url
        echo "\n".'<table class="claroTable" border="1" width="100%">' . "\n";
              echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $backwardsURL . '">&lt;&lt;</a></center>'.'</th>';
        echo '<th class="superHeader" valign="top"><center>'  . "\n"
             .claroDate::getFormatedDateFromTimeStamp($referenceDate, 'Y M d') . "\n"
             .'</center></th>'."\n";
        echo '<th class="superHeader" width="15%" valign="top">'.'<center><a href="' . $forewardsURL . '">&gt;&gt;</a></center>';
        echo'</th></tr>'."\n";
        
        /// display the hours and the events
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
            echo  '</td>'. '<td width="15%">';
            if( !empty($hourEventList) )
            {
                foreach ($hourEventList as $thisEvent)
                {
                    echo '<p>'. $thisEvent->getUrl(). '</p>'; 
                       
                }
            }
            else
            {
                 echo '&nbsp;';
            }
            echo'</td>'.'</tr></tbody>';
        }
        echo '</table>';
       
    }
}

/**
* Class ListView
*/

class ListView
{

    /**
    * function that display a list view of the events of the agenda
    *
    * @param integer $referenceDate : time stamp
    * @param array $eventList
    * @return echo html
    */
    function listViewDisplay($referenceDate, $eventList)
    {
    
        $now = mktime();

        $monthDateBar  = mktime(0,0,0, 01, 01, 1970); // Epoch
        $nowBarPainted = false;
        $eventList = claroEvent::sortList($eventList) ;

        echo '<h3>List View</h3>';

        echo '<table class="claroTable" border="1" width="100%">' . "\n";

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
                    .    '<th class="superHeader" width="100%" valign="top"><i>' . "\n"
                    .    claroDate::getMonthNameFromTimeStamp($monthDateBar)
                    .    ' '
                    .    clarodate::getYearFromTimeStamp($monthDateBar)
                    .    '</i></th>' . "\n"
                    .    '</tr>' . "\n"
                    ;
                }

                if (  $thisEventDate > $now && ! $nowBarPainted )
                {
                    //echo '<tr><td align="center"> -- '.get_lang('NOW').' -- </td></td>';
                echo '<tr>' . "\n"
				.    '<td align="center">' . "\n"
				.    '<img src="' . get_path('imgRepositoryWeb') . 'pixel.gif" width="20" alt=" " />'
				.    '<span class="highlight">'
				.    '<a name="today">'
				.    '<i>'
				.    ucfirst(claro_html_localised_date( get_locale('dateFormatLong'))) . ' '
				.    ucfirst(strftime( get_locale('timeNoSecFormat')))
				.    ' -- '
				.    get_lang('Now')
				.    '</i>'
				.    '</a>'
				.    '</span>' . "\n"
				.    '</td>' . "\n"
				.    '</tr>' . "\n"
				;

                    $nowBarPainted = true;
                }

                echo'<tr>' . "\n"
                .   '<th class="headerX">'.claroDate::getFormatedDateFromTimeStamp($thisEventDate, 'd M Y H:i').'</th>' . "\n"
                .   '</tr>' . "\n";

                echo'<tr>' . "\n"
                .   '<td>'
                .   '<h4>'.htmlspecialchars($thisEvent->getTitle()) .'</h4>'
                .   $thisEvent->getComment()
                .   '<p>'.$thisEvent->getUrl() . '</p>'
                .   '</td>' . "\n"
                .   '</tr>' . "\n";
            }
        }
        else
        {
            echo (get_lang('No event to display'));
        }
        echo '</table>' . "\n";
    }
}
