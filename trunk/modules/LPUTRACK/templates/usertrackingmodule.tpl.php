<?php if( !$this->excelExport ) : ?>

    <form action="" method="post">
        <input type="Submit" name="excelexport" value="<?php echo get_lang( 'xls export' ) ?>">
    </form>
    </br>

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackCourse"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'Class view' ); ?>
    </a>
    </br>
    </br>

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackCourse"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'Course' ); ?>
    </a>
    </br>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackLearnPath"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'LearnPath' ); ?>
    </a>
    </br>
    </br>

    <div> <?php echo get_lang( 'Detail level' ); ?> </div>
    <?php if( $this->mode != 1 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=1" ) ); ?>
    ">
        <?php echo get_lang( 'General tracking' ); ?>
    </a>
    </br>
    <?php endif; ?>
    <?php if( $this->mode != 2 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=2" ) ); ?>
    ">
        <?php echo get_lang( 'Daily tracking' ); ?>
    </a>
    </br>
    <?php endif; ?>
    <?php if( $this->mode != 3 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=3" ) ); ?>
    ">
        <?php echo get_lang( 'Complete tracking' ); ?>
    </a>
    </br>
    <?php endif; ?>
    </br>

<?php endif; ?>

<?php foreach( $this->infoUserList as $infoUser ) : ?>
<?php $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() ); ?>

<h1> <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName(); ?> </h1>

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <td class="emptyCell">&nbsp;</td>
        <td class="emptyCell">&nbsp;</td>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Total time' ); ?> </th>
        <th> <?php echo get_lang( 'Progress' ); ?> </th>
        <th> <?php echo get_lang( 'Best score' ); ?> </th>
    </tr>
    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
    <?php
        $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
        $trackinCoursegEntry = $trackingCourse->getGeneralTracking();
    ?>
        <tr class="headerX">
            <th>
                <?php echo $infoCourse->getCourseName() . " "; ?>
            </th>
            <?php if( !is_null( $trackinCoursegEntry ) ) : ?>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <?php if( $trackinCoursegEntry->getWarning() ) : ?>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="warning biggestCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php else : ?>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getDate(); ?> </td>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getTime(); ?> </td>
                <td class="biggestCell"> <?php echo $trackinCoursegEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
            <?php endif; ?>
        </tr>
        <?php foreach( $infoCourse->getInfoLearnPathList() as $infoLearnPath ) : ?>
        <?php
            $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoLearnPath->getLearnPathId() );
            $trackingLearnPathEntry = $trackingLearnPath->getGeneralTracking();
        ?>
            <tr class="headerX">
                <td class="emptyCell">&nbsp;</td>
                <th>
                    <?php echo $infoLearnPath->getLearnPathName(); ?>
                </th>
                <?php if( !is_null( $trackingLearnPathEntry ) ) : ?>
                    <td class="emptyCell">&nbsp;</td>
                    <?php if( $trackingLearnPathEntry->getWarning() ) : ?>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="warning biggerCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php else : ?>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getDate(); ?> </td>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getTime(); ?> </td>
                    <td class="biggerCell"> <?php echo $trackingLearnPathEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                <?php else : ?>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                <?php endif; ?>
            </tr>
            <?php foreach( $infoLearnPath->getInfoModuleList() as $infoModule ) : ?>
            <?php
                $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                $trackingModuleEntry = $trackingModule->getGeneralTracking();
            ?>
                <tr class="headerX">
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <th> <?php echo $infoModule->getModuleName(); ?> </th>
                    <?php if( !is_null( $trackingModuleEntry ) ) : ?>
                        <?php if( $trackingModuleEntry->getWarning() ) : ?>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getDate(); ?> </td>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getTime(); ?> </td>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getProgress() . "%"; ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                            <td class="warning bigCell"> <?php echo $trackingModuleEntry->getScoreRaw() . "/" . $trackingModuleEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php else : ?>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getDate(); ?> </td>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getTime(); ?> </td>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getProgress() . "%"; ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                            <td class="bigCell"> <?php echo $trackingModuleEntry->getScoreRaw() . "/" . $trackingModuleEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <td class="emptyCell">&nbsp;</td>
                        <td class="emptyCell">&nbsp;</td>
                        <td class="emptyCell">&nbsp;</td>
                    <?php endif; ?>
                </tr>

                <?php if( ( $this->mode == 2 || $this->mode == 3 ) && !is_null( $trackingModuleEntry ) ) : ?>
                <tr><td class="emptyCell">&nbsp;</td></tr>
                <tr class="headerX">
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                    <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                        <th> <?php echo get_lang( 'Score' ); ?> </th>
                    <?php endif; ?>
                </tr>
                    <?php foreach( $trackingModule->getTrackingList() as $trackingEntry ) : ?>
                    <tr>
                        <td class="emptyCell">&nbsp;</td>
                        <td class="emptyCell">&nbsp;</td>
                        <td class="emptyCell">&nbsp;</td>
                        <?php if( $trackingEntry->getWarning() ) : ?>
                            <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                            <td class="warning"> <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php else : ?>
                            <td> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td> <?php echo $trackingEntry->getTime(); ?> </td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                            <td> <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?> </td>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <tr><td class="emptyCell">&nbsp;</td></tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <!--<tr><td class="emptyCell">&nbsp;</td></tr>-->
        <?php endforeach; ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
    <?php endforeach; ?>
</table>

<?php endforeach; ?>