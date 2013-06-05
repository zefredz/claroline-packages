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
                <input type="radio" id="course_view" name="view_type" value="course"
                       onchange="location.href='<?php
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=uniqueUserViewTrackCourse"
                                                     . "&userId=$this->userId"
                                                     . "&mode=" . ( ( $this->mode == 3 ) ? 2 : $this->mode ) ) );
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
                                                     . "&mode=" . ( ( $this->mode == 3 ) ? 2 : $this->mode ) ) );
                                            ?>'"
                >
                <label for="learnpath_view">
                    <?php echo get_lang( 'LearnPath' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="module_view" name="view_type" value="module" checked
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
                                                     . "?cmd=uniqueUserViewTrackModule"
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
                                                     . "?cmd=uniqueUserViewTrackModule"
                                                     . "&userId=$this->userId"
                                                     . "&mode=2" ) );
                                            ?>'"
                >
                <label for="daily_detail">
                    <?php echo get_lang( 'Daily tracking' ); ?>
                </label>
            </td>
            <td class="simpleLabel">
                <input type="radio" id="complete_detail" name="detail_level" value="complete" <?php if( $this->mode == 3 ) echo 'checked'; ?>
                   onchange="location.href='<?php
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=uniqueUserViewTrackModule"
                                                     . "&userId=$this->userId"
                                                     . "&mode=3" ) );
                                            ?>'"
                >
                <label for="complete_detail">
                    <?php echo get_lang( 'Complete tracking' ); ?>
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

<table class="claroTable emphaseLine" width="100%" border="0" cellspacing="2">
    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
    <?php
        $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
        $trackinCoursegEntry = $trackingCourse->getGeneralTracking();
    ?>
        <tr class="headerX">
            <th> <?php echo get_lang( 'Course' ); ?> </th>
            <th> <?php echo get_lang( 'LearnPath' ); ?> </th>
            <th> <?php echo get_lang( 'Module' ); ?> </th>
            <th> <?php echo get_lang( 'First connection' ); ?> </th>
            <th> <?php echo get_lang( 'Last connection' ); ?> </th>
            <th> <?php echo get_lang( 'Total time' ); ?> </th>
            <th> <?php echo get_lang( 'Progress' ); ?> </th>
            <th> <?php echo get_lang( 'Best score' ); ?> </th>
        </tr>
        <tr>
            <td class="courseLabel" colspan="3">
                <?php if( !$this->excelExport ) : ?>
                    <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
                <?php endif; ?>
                <?php echo $infoCourse->getCourseCode() . ' - ' . $infoCourse->getCourseName(); ?>
            </td>
            <?php if( !is_null( $trackinCoursegEntry ) ) : ?>
                <?php if( $trackinCoursegEntry->getWarning() ) : ?>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getFirstConnection(); ?> </td>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php else : ?>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getFirstConnection(); ?> </td>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell biggestCell">-</td>
                <td class="emptyCell biggestCell">-</td>
                <td class="emptyCell biggestCell">-</td>
                <td class="emptyCell biggestCell">-</td>
            <?php endif; ?>
        </tr>
        <?php foreach( $infoCourse->getInfoLearnPathList() as $infoLearnPath ) : ?>
        <?php
            $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoLearnPath->getLearnPathId() );
            $trackingLearnPathEntry = $trackingLearnPath->getGeneralTracking();
        ?>
            <tr>
                <td class="emptyCell">&nbsp;</td>
                <td class="learnpathLabel" colspan="2">
                    <?php if( !$this->excelExport ) : ?>
                        <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
                    <?php endif; ?>
                    <?php echo $infoLearnPath->getLearnPathName(); ?>
                </td>
                <?php if( !is_null( $trackingLearnPathEntry ) ) : ?>
                    <?php if( $trackingLearnPathEntry->getWarning() ) : ?>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getFirstConnection(); ?> </td>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php else : ?>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getFirstConnection(); ?> </td>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                <?php else : ?>
                    <td class="emptyCell biggerCell">-</td>
                    <td class="emptyCell biggerCell">-</td>
                    <td class="emptyCell biggerCell">-</td>
                    <td class="emptyCell biggerCell">-</td>
                <?php endif; ?>
            </tr>
            <?php foreach( $infoLearnPath->getInfoModuleList() as $infoModule ) : ?>
            <?php
                $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                $trackingModuleEntry = $trackingModule->getGeneralTracking();
                if( $infoModule->getModuleContentType() == 'EXERCISE' )
                {
                    $moduleIcon = get_icon_url( 'quiz', 'CLQWZ' );
                }
                else
                {
                    $moduleIcon = get_icon_url( choose_image( basename( $trackingModule->getModulePath() ) ) );
                }
            ?>
                <tr>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="moduleLabel">
                        <?php if( !$this->excelExport ) : ?>
                            <img src="<?php echo $moduleIcon; ?>" alt=""/>
                        <?php endif; ?>
                        <?php echo $infoModule->getModuleName(); ?>
                    </td>
                    <?php if( !is_null( $trackingModuleEntry ) ) : ?>
                        <?php if( $trackingModuleEntry->getWarning() ) : ?>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getFirstConnection(); ?> </td>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getDate(); ?> </td>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getTime(); ?> </td>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getProgress() . "%"; ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) : ?>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getScoreRaw() . "/" . $trackingModuleEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php else : ?>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getFirstConnection(); ?> </td>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getDate(); ?> </td>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getTime(); ?> </td>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getProgress() . "%"; ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) : ?>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getScoreRaw() . "/" . $trackingModuleEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <td class="emptyCell bigCell">-</td>
                        <td class="emptyCell bigCell">-</td>
                        <td class="emptyCell bigCell">-</td>
                        <td class="emptyCell bigCell">-</td>
                    <?php endif; ?>
                </tr>

                <?php if( ( $this->mode == 2 || $this->mode == 3 ) && !is_null( $trackingModuleEntry ) ) : ?>
                <tr><td class="emptyCell">&nbsp;</td></tr>
                <tr>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td colspan="<?php echo ( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) ? 3 : 2; ?>">
                        <table width="100%" border="0" cellspacing="2">
                            <tr class="header">
                                <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                                <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
                                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) : ?>
                                <th class="subTableHeader"> <?php echo get_lang( 'Score' ); ?> </th>
                                <?php endif; ?>
                            </tr>
                            <?php foreach( $trackingModule->getTrackingList() as $trackingEntry ) : ?>
                            <tr>
                                 <?php if( $trackingEntry->getWarning() ) : ?>
                                <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                                <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) : ?>
                                    <td class="warning"> <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?> </td>
                                    <?php endif; ?>
                                <?php else : ?>
                                <td> <?php echo $trackingEntry->getDate(); ?> </td>
                                <td> <?php echo $trackingEntry->getTime(); ?> </td>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) : ?>
                                    <td> <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?> </td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                </tr>
                <tr><td class="emptyCell">&nbsp;</td></tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
    <?php endforeach; ?>
</table>