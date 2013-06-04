<script language="javascript" type="text/javascript">
    $( document ).ready(
        function()
        {
            $( '.detailsMode' ).hide();
            $( '.detailsModeToggle' ).click(
                function()
                {
                    $( this ).next( ".detailsMode" ).toggle();
                    return false;
                }
            );
        }
    );
</script>

<div>
    <select
        onchange="location.href='currentuser.php?userId=' + <?php echo $_GET['userId']; ?>
                + '&courseCode=' + this.options[ this.selectedIndex ].value
                + '&mode=<?php echo $_GET['mode']; ?>'"
    >
    <?php foreach( $this->courseList as $course ) : ?>
        <option value="<?php echo $course['code']; ?>" <?php if( $course['code'] == $_GET['courseCode'] ) echo 'selected'; ?> >
            <?php echo $course['code'] . ' - ' . $course['intitule']; ?>
        </option>
    <?php endforeach; ?>
    </select>
</div>

<table border="0" cellspacing="10">
    <tr>
        <td><?php echo get_lang( 'Detail level' ) . ' :'; ?></td>
        <td>
            <input type="radio" id="daily_detail" name="detail_level" value="daily" <?php if( $this->mode == 2 ) echo 'checked'; ?>
                   onchange="location.href='currentuser.php?userId=' + <?php echo $_GET['userId']; ?>
                                + '&courseCode=' + '<?php echo $_GET['courseCode']; ?>'
                                + '&mode=2'"
            >
            <label for="daily_detail">
                <?php echo get_lang( 'Daily tracking' ); ?>
            </label>
        </td>
        <td>
            <input type="radio" id="complete_detail" name="detail_level" value="complete" <?php if( $this->mode == 3 ) echo 'checked'; ?>
                   onchange="location.href='currentuser.php?userId=' + <?php echo $_GET['userId']; ?>
                                + '&courseCode=' + '<?php echo $_GET['courseCode']; ?>'
                                + '&mode=3'"
            >
            <label for="complete_detail">
                <?php echo get_lang( 'Complete tracking' ); ?>
            </label>
        </td>
    </tr>
</table>

<br />

<?php
$generalCourseTracking = $this->trackingCourse->getGeneralTracking();
if( is_null( $generalCourseTracking ) )
{
    $progress = 0;
    $spentTime = '-';
    $date = '-';
    $warning = false;
}
else
{
    $progress = $generalCourseTracking->getProgress();
    $spentTime = $generalCourseTracking->getTime();
    $date = $generalCourseTracking->getDate();
    $warning = $generalCourseTracking->getWarning();
}
?>

<table class="courseTrackingSummary" border="0">
    <tr>
        <th class="trackingLabel"><?php echo get_lang( 'Progress' ) . ' :'; ?></th>
        <td class="trackingValue" nowrap><?php echo claro_html_progress_bar( $progress, 1 ); ?></td>
        <td class="trackingValue <?php if( $warning ) echo 'warning'; ?>" nowrap><?php echo $progress . '%'; ?></td>
    </tr>
    <tr>
        <th class="trackingLabel"><?php echo get_lang( 'Spent time' ) . ' :'; ?></th>
        <td class="trackingValue <?php if( $warning ) echo 'warning'; ?>" nowrap><?php echo $spentTime; ?></td>
    </tr>
    <tr>
        <th class="trackingLabel"><?php echo get_lang( 'Last connection' ) . ' :'; ?></th>
        <td class="trackingValue <?php if( $warning ) echo 'warning'; ?>" nowrap><?php echo $date; ?></td>
    </tr>
</table>

<br />

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <tr class="headerX" align="center" valign="top">
        <th><?php echo get_lang( 'LearnPath' ); ?></th>
        <th colspan="2" nowrap><?php echo get_lang( 'Progress' ); ?></th>
        <th nowrap><?php echo get_lang( 'Spent time' ); ?></th>
        <th nowrap><?php echo get_lang( 'Last connection' ); ?></th>
    </tr>

<?php foreach( $this->trackingCourse->getTrackingLearnPathList() as $trackingLearnPath ) : ?>
<?php
$generalLearnPathTracking = $trackingLearnPath->getGeneralTracking();
if( is_null( $generalLearnPathTracking ) )
{
    $progress = 0;
    $date = '-';
    $warning = false;
    $spentTime = '-';
}
else
{
    $progress = $generalLearnPathTracking->getProgress();
    $date = $generalLearnPathTracking->getDate();
    $warning = $generalLearnPathTracking->getWarning();
    $spentTime = $generalLearnPathTracking->getTime();
}
?>

    <tr class="detailsModeToggle">
        <td class="learnpathLabel">
            <a href="#">
                <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
                <?php echo $trackingLearnPath->getLearnPathName(); ?>
            </a>
        </td>
        <td nowrap><?php echo claro_html_progress_bar( $progress, 1 ); ?></td>
        <?php if( $warning ) : ?>
        <td class="warning" nowrap><small><?php echo $progress . '%'; ?></small></td>
        <td class="warning" nowrap><?php echo $spentTime; ?></td>
        <td class="warning" nowrap><?php echo $date; ?></td>
        <?php else : ?>
        <td nowrap><small><?php echo $progress . '%'; ?></small></td>
        <td nowrap><?php echo $spentTime; ?></td>
        <td nowrap><?php echo $date; ?></td>
        <?php endif; ?>
    </tr>

    <tr class="detailsMode">
        <td>&nbsp;</td>
        <td colspan="4">
            <table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
                <tr class="header">
                    <th><?php echo get_lang( 'Module' ); ?></th>
                    <th colspan="2"><?php echo get_lang( 'Progress' ); ?></th>
                    <th><?php echo get_lang( 'Spent time' ); ?></th>
                    <th><?php echo get_lang( 'Last connection' ); ?></th>
                    <th><?php echo get_lang( 'Best score' ); ?>
                </tr>

            <?php foreach( $trackingLearnPath->getTrackingModuleList() as $trackingModule ) : ?>
            <?php
            $generalModuleTracking = $trackingModule->getGeneralTracking();
            $isExercice = ( $trackingModule->getContentType() == 'EXERCISE' );
            $isScorm = ( $trackingModule->getContentType() == 'SCORM' );
            if( $isExercice )
            {
                $moduleIcon = get_icon_url( 'quiz', 'CLQWZ' );
            }
            else
            {
                $modulePath = $trackingModule->getModulePath();
                $moduleIcon = get_icon_url( choose_image( basename( $modulePath ) ) );
            }
            if( is_null( $generalModuleTracking ) )
            {
                $progress = 0;
                $date = '-';
                $warning = false;
                $score = '-';
                $spentTime = '-';
            }
            else
            {
                $progress = $generalModuleTracking->getProgress();
                $date = $generalModuleTracking->getDate();
                $warning = $generalModuleTracking->getWarning();
                $isExercice = ( $trackingModule->getContentType() == 'EXERCISE' );
                $isScorm = ( $trackingModule->getContentType() == 'SCORM' );
                $score = ( $isExercice || $isScorm ) ? ( $generalModuleTracking->getScoreRaw() . '/' . $generalModuleTracking->getScoreMax() ) : '-';
                $spentTime = $generalModuleTracking->getTime();
            }
            ?>

            <tr class="detailsModeToggle">
                <td class="moduleLabel">
                    <a href="#">
                        <img src="<?php echo $moduleIcon; ?>" alt=""/>
                        <?php echo $trackingModule->getModuleName(); ?>
                    </a>
                </td>
                <td nowrap><?php echo claro_html_progress_bar( $progress, 1 ); ?></td>
                <?php if( $warning ) : ?>
                <td class="warning" nowrap><small><?php echo $progress . '%'; ?></small></td>
                <td class="warning" nowrap><?php echo $spentTime; ?></td>
                <td class="warning" nowrap><?php echo $date; ?></td>
                <td class="warning" nowrap><?php echo $score; ?></td>
                <?php else : ?>
                <td nowrap><small><?php echo $progress . '%'; ?></small></td>
                <td nowrap><?php echo $spentTime; ?></td>
                <td nowrap><?php echo $date; ?></td>
                <td nowrap><?php echo $score; ?></td>
                <?php endif; ?>
            </tr>

            <?php
            $trackingList = $trackingModule->getTrackingList();
            $trackingNull = is_null( $trackingList );
            ?>
            <?php if( !$trackingNull ) : ?>

            <tr class="detailsMode">
                <td>&nbsp;</td>
                <td colspan="<?php echo ( $isExercice || $isScorm ) ? 3 : 2; ?>">

                    <table width="100%" border="0" cellspacing="2">
                        <tr class="header">
                            <th><?php echo get_lang( 'Date' ); ?></th>
                            <th><?php echo get_lang( 'Spent time' ); ?></th>
                            <?php if( $isExercice || $isScorm ) : ?>
                            <th><?php echo get_lang( 'Score' ); ?></th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach( $trackingList as $trackingEntry ) : ?>
                        <tr>
                            <?php if( $trackingEntry->getWarning() ) : ?>
                                <td class="warning" nowrap><?php echo $trackingEntry->getDate(); ?></td>
                                <td class="warning" nowrap><?php echo $trackingEntry->getTime(); ?></td>
                                <?php if( $isExercice || $isScorm ) : ?>
                                <td class="warning" nowrap><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                <?php endif; ?>
                            <?php else : ?>
                                <td nowrap><?php echo $trackingEntry->getDate(); ?></td>
                                <td nowrap><?php echo $trackingEntry->getTime(); ?></td>
                                <?php if( $isExercice || $isScorm ) : ?>
                                <td nowrap><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                </td>
            </tr>

            <?php endif; ?>

            <?php endforeach; ?>

            </table>
        </td>
    </tr>
<?php endforeach; ?>

</table>