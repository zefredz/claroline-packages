<?php

if ( count ( get_included_files () ) == 1 )
    die ( '---' );

From::Module( 'LPUTRACK' )->uses(
    'trackingUtils.lib',
    'trackingData.class',
    'trackingUser.class',
    'trackingCourse.class',
    'trackingEntry.class'
);

class LPUTRACK_Portlet extends UserDesktopPortlet
{
    private $displayProgress;

    public function __construct( $label )
    {
        parent::__construct( $label );

        $this->name = 'My learnPath tracking';
        $this->label = 'LPUTRACK_Portlet';

        $this->displayProgress = get_conf('LPUTRACK_display_progress_widget');
    }

    public function renderContent()
    {
        $output = '';
        $currentUserInfo = TrackingUtils::getUserFromUserId( claro_get_current_user_id() );
        $userCourseList = TrackingUtils::getAllCourseFromUser( claro_get_current_user_id() );

        $trackingData = TrackingData::getInstance();
        $trackingData->addUser( $currentUserInfo['user_id'] );

        $courseList = array();

        foreach( $userCourseList as $course )
        {
            $trackingData->addCourse( $course['code'] );
            $courseList[ $course['code'] ] = $course['intitule'];
        }

        $trackingData->generateData();
        $trackingUser = new TrackingUser( $currentUserInfo['user_id'],
                                          $currentUserInfo['prenom'],
                                          $currentUserInfo['nom']);
        $trackingUser->generateTrackingCourseList( array_keys( $courseList ) );
        $trackingUser->generateCourseTrackingList( 1 );

        $output .= '<div class="portlet collapsible collapsed">'
                 . '<a href="#" class="doCollapse">' . get_lang( 'Show/Hide' ) . '</a>'
                 . '<div class="content collapsible-wrapper">'
                 . '<table class="claroTable" width="100%" border="0" cellspacing="2">'
                 . '<thead>'
                 . '<tr align="center" valign="top">'
                 . '<th colspan=2>' . get_lang( 'Course' ) . '</th>';

        if( $this->displayProgress )
        {
            $output .= '<th colspan="2">' . get_lang( 'Progress' ) . '</th>';
        }
        $output.= '<th>' . get_lang( 'Spent time' ) . '</th>'
                . '<th>' . get_lang( 'Last connection' ) . '</th>'
                . '</tr>'
                . '</thead>'
                . '<tbody>';

        $totalTime = '00:00:00';

        foreach( $courseList as $courseCode => $courseIntitule )
        {
            $trackingCourse = $trackingUser->getTrackingCourse( $courseCode );
            $courseGeneralEntry = $trackingCourse->getGeneralTracking();

            if( is_null( $courseGeneralEntry ) )
            {
                $progress = 0;
                $date = '-';
                $spentTime = '-';
            }
            else
            {
                $progress = $courseGeneralEntry->getProgress();
                $date = claro_html_localised_date( get_locale( 'dateFormatLong' ), strtotime( $courseGeneralEntry->getDate() ) );
                $spentTime = $courseGeneralEntry->getTime();
                $totalTime = TrackingUtils::addTime( $totalTime, $spentTime);
            }
            $courseTrackingUrl = Url::Contextualize( get_module_url( 'LPUTRACK' ) . '/currentuser.php' );

            $output .= '<tr align="center" valign="top">'
                     . '<td><img src="'. get_icon_url( 'course' ) . '" alt=""/></td>'
                     . '<td align="left">'
                     . '<a href="' . $courseTrackingUrl
                                   . '?userId=' . $trackingUser->getUserId()
                                   . '&courseCode=' . $courseCode
                                   . '&mode=2'
                     . '">'
                     . $courseIntitule
                     . '</a>'
                     . '</td>';

            if( $this->displayProgress )
            {
                $output .= '<td nowrap>' . claro_html_progress_bar( $progress, 1 ) . '</td>'
                        . '<td nowrap><small>' . $progress . '%</small></td>';
            }
            $output .= '<td nowrap>' . $spentTime . '</td>'
                     . '<td nowrap>' . $date . '</td>'
                     . '</tr>';
        }

        $output .= '<tr>'
                 . '<th colspan=2>&nbsp;</th>';

        if( $this->displayProgress )
        {
            $output .= '<th colspan=2>&nbsp;</th>';
        }
        $output .= '<th>&nbsp;</th>'
                 . '<th>&nbsp;</th>'
                 . '</tr>'
                 . '<tr align="center" valign="top">'
                 . '<td colspan=2><strong>' . get_lang( 'Total' ) . '</strong></td>';

        if( $this->displayProgress )
        {
            $output .= '<td colspan=2><strong>-</strong></td>';
        }
        $output .= '<td><strong>' . $totalTime . '</strong></td>'
                 . '<td><strong>-</strong></td>'
                 . '</tr>';
        $output .= '</tbody>'
                 . '</table>'
                 . '</div>'
                 . '</div>';

        return $output;
    }

    public function renderTitle()
    {
        if ($this->displayProgress)
        {
            return get_lang( 'My learnPath tracking' );
        }
        else
        {
            return get_lang( 'My learnPath time tracking' );
        }
    }
}