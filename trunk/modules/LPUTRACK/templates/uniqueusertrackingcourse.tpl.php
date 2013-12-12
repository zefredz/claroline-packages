<?php if( !$this->excelExport ) : ?>

    <form action="" method="post">
        <input type="Submit" name="excelexport" value="<?php echo get_lang( 'xls export' ) ?>">
    </form>
    <br />

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=uniqueGlobalViewTrackCourse"
                                                                . "&userId=$this->userId" ) ); ?>
    ">
        <?php echo get_lang( 'Alternative view' ); ?>
    </a>
    <br />
    <br />

    <table border="0" cellspacing="10">
        <tr>
            <td class="simpleLabel"><?php echo get_lang( 'View type' ) . ' :'; ?></td>
            <td class="simpleLabel">
                <input type="radio" id="course_view" name="view_type" value="course" checked
                       onchange="location.href='<?php
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=uniqueUserViewTrackCourse"
                                                     . "&userId=$this->userId"
                                                     . "&mode=$this->mode" ) );
                                                ?>'"
                >
                <label for="course_view">
                    <?php echo get_lang( 'Course' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="learnpath_view" name="view_type" value="learnpath"
                   onchange="location.href='<?php
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=uniqueUserViewTrackLearnPath"
                                                     . "&userId=$this->userId"
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
                                                     . "?cmd=uniqueUserViewTrackModule"
                                                     . "&userId=$this->userId"
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
                                                     . "?cmd=uniqueUserViewTrackCourse"
                                                     . "&userId=$this->userId"
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
                                                     . "?cmd=uniqueUserViewTrackCourse"
                                                     . "&userId=$this->userId"
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

<?php $trackingUser = $this->trackingController->getTrackingUser( $this->infoUser->getUserId() ); ?>

<h1>
    <?php if( !$this->excelExport ) : ?>
        <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
    <?php endif; ?>
    <?php echo $this->infoUser->getFirstName() . " " . $this->infoUser->getLastName(); ?>
</h1>

<h3>
    <?php echo get_lang( 'Total time' ); ?> :
    <?php echo $trackingUser->getTotalTime(); ?>
</h3>

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <tr class="headerX">
        <th> <?php echo get_lang( 'Course' ); ?> </th>
        <th> <?php echo get_lang( 'First connection' ); ?> </th>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Total time' ); ?> </th>
        <?php if( $this->displayProgress ) : ?>
            <th> <?php echo get_lang( 'Progress' ); ?> </th>
        <?php endif; ?>
    </tr>
    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
    <?php
        $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
        $trackingCourseEntry = $trackingCourse->getGeneralTracking();
    ?>
        <tr>
            <td class="courseLabel">
                <?php if( !$this->excelExport ) : ?>
                    <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
                <?php endif; ?>
                <?php echo $infoCourse->getCourseCode() . ' - ' . $infoCourse->getCourseName(); ?>
            </td>
            <?php if( !is_null( $trackingCourseEntry ) ) : ?>
                <?php if( $trackingCourseEntry->getWarning() ) : ?>
                    <td class="warning bigCell"> <?php echo $trackingCourseEntry->getFirstConnection(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingCourseEntry->getDate(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingCourseEntry->getTime(); ?> </td>
                    <?php if( $this->displayProgress ) : ?>
                        <td class="warning bigCell"> <?php echo $trackingCourseEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                <?php else : ?>
                    <td class="bigCell"> <?php echo $trackingCourseEntry->getFirstConnection(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingCourseEntry->getDate(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingCourseEntry->getTime(); ?> </td>
                    <?php if( $this->displayProgress ) : ?>
                        <td class="bigCell"> <?php echo $trackingCourseEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <?php if( $this->displayProgress ) : ?>
                    <td class="emptyCell bigCell">-</td>
                <?php endif; ?>
            <?php endif; ?>
        </tr>
        <?php if( $this->mode == 2 && !is_null( $trackingCourseEntry ) ) : ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
        <tr>
            <td class="emptyCell">&nbsp;</td>
            <td colspan="2">
                <table width="100%" border="0" cellspacing="2">
                    <tr class="header">
                        <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                        <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
                    </tr>
                    <?php foreach( $trackingCourse->getTrackingList() as $trackingEntry ) : ?>
                    <tr>
                        <?php if( $trackingEntry->getWarning() ) : ?>
                        <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                        <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                        <?php else : ?>
                        <td> <?php echo $trackingEntry->getDate(); ?> </td>
                        <td> <?php echo $trackingEntry->getTime(); ?> </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </td>
        </tr>
        <tr><td class="emptyCell">&nbsp;</td></tr>
        <?php endif; ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
    <?php endforeach; ?>
</table>