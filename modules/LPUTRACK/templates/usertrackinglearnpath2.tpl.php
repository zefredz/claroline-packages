<?php if( !$this->excelExport ) : ?>

    <form action="" method="post">
        <input type="Submit" name="excelexport" value="<?php echo get_lang( 'xls export' ) ?>">
    </form>
    <br />

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackCourse"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'Class view' ); ?>
    </a>
    <br />
    <br />

    <table border="0" cellspacing="10">
        <tr>
            <td class="simpleLabel"><?php echo get_lang( 'View type' ) . ' :'; ?></td>
            <td class="simpleLabel">
                <input type="radio" id="course_view" name="view_type" value="course"
                       onchange="location.href='<?php 
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=userViewTrackCourse"
                                                     . "&classId=$this->classId"
                                                     . "&mode=$this->mode" ) );
                                                ?>'"
                >
                <label for="course_view">
                    <?php echo get_lang( 'Course' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="learnpath_view" name="view_type" value="learnpath" checked
                   onchange="location.href='<?php 
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=userViewTrackLearnPath"
                                                     . "&classId=$this->classId"
                                                     . "&mode=$this->mode" ) );
                                            ?>'"
                >
                <label for="learnpath_view">
                    <?php echo get_lang( 'LearnPath' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="module_view" name="view_type" value="module"
                   onchange="location.href='<?php 
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=userViewTrackModule"
                                                     . "&classId=$this->classId"
                                                     . "&mode=$this->mode" ) );
                                            ?>'"
                >
                <label for="module_view">
                    <?php echo get_lang( 'Module' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td class="simpleLabel"><?php echo get_lang( 'Detail level' ) . ' :'; ?></td>
            <td class="simpleLabel">
                <input type="radio" id="general_detail" name="detail_level" value="general" <?php if( $this->mode == 1 ) echo 'checked'; ?>
                   onchange="location.href='<?php 
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=userViewTrackLearnPath"
                                                     . "&classId=$this->classId"
                                                     . "&mode=1" ) );
                                            ?>'"
                >
                <label for="general_detail">
                    <?php echo get_lang( 'General tracking' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="daily_detail" name="detail_level" value="daily" <?php if( $this->mode == 2 ) echo 'checked'; ?>
                   onchange="location.href='<?php 
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=userViewTrackLearnPath"
                                                     . "&classId=$this->classId"
                                                     . "&mode=2" ) );
                                            ?>'"
                >
                <label for="daily_detail">
                    <?php echo get_lang( 'Daily tracking' ); ?>
                </label>
            </td>
        </tr>
    </table>
    <br />

<?php endif; ?>

<?php foreach( $this->infoUserList as $infoUser ) : ?>
<?php $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() ); ?>

<h1> <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName(); ?> </h1>

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <tr class="headerX">
        <th> <?php echo get_lang( 'Course' ); ?> </th>
        <th> <?php echo get_lang( 'LearnPath' ); ?> </th>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Total time' ); ?> </th>
        <th> <?php echo get_lang( 'Progress' ); ?> </th>
    </tr>
    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
    <?php
        $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
        $trackinCoursegEntry = $trackingCourse->getGeneralTracking();
    ?>
        <tr>
            <td class="courseLabel" colspan="2">
                <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
                <?php echo $infoCourse->getCourseCode() . ' - ' . $infoCourse->getCourseName(); ?>
            </td>
            <?php if( !is_null( $trackinCoursegEntry ) ) : ?>
                <?php if( $trackinCoursegEntry->getWarning() ) : ?>
                <td class="warning biggerCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="warning biggerCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="warning biggerCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php else : ?>
                <td class="biggerCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="biggerCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="biggerCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell biggerCell">-</td>
                <td class="emptyCell biggerCell">-</td>
                <td class="emptyCell biggerCell">-</td>
            <?php endif; ?>
        </tr>
        <?php foreach( $infoCourse->getInfoLearnPathList() as $infoLearnPath ) : ?>
        <?php
            $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoLearnPath->getLearnPathId() );
            $trackingLearnPathEntry = $trackingLearnPath->getGeneralTracking();
        ?>
            <tr>
                <td class="emptyCell">&nbsp;</td>
                <td class="learnpathLabel">
                    <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
                    <?php echo $infoLearnPath->getLearnPathName(); ?>
                </td>
                <?php if( !is_null( $trackingLearnPathEntry ) ) : ?>
                    <?php if( $trackingLearnPathEntry->getWarning() ) : ?>
                    <td class="warning bigCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php else : ?>
                    <td class="bigCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                <?php else : ?>
                    <td class="emptyCell bigCell">-</td>
                    <td class="emptyCell bigCell">-</td>
                    <td class="emptyCell bigCell">-</td>
                <?php endif; ?>
            </tr>
            <?php if( $this->mode == 2 && !is_null( $trackingLearnPathEntry ) ) : ?>
            <tr><td class="emptyCell">&nbsp;</td></tr>
            <tr class="headerX">
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
            </tr>
                <?php foreach( $trackingLearnPath->getTrackingList() as $trackingEntry ) : ?>
                <tr>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <?php if( $trackingLearnPathEntry->getWarning() ) : ?>
                    <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                    <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                    <?php else : ?>
                    <td> <?php echo $trackingEntry->getDate(); ?> </td>
                    <td> <?php echo $trackingEntry->getTime(); ?> </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <tr><td class="emptyCell">&nbsp;</td></tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
    <?php endforeach; ?>
</table>

<?php endforeach; ?>