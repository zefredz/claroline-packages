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

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackLearnPath"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'LearnPath' ); ?>
    </a>
    <br />
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackModule"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'Module' ); ?>
    </a>
    <br />
    <br />

    <div> <?php echo get_lang( 'Detail level' ); ?> </div>
    <?php if( $this->mode != 1 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackCourse"
                                                                . "&classId=$this->classId"
                                                                . "&mode=1" ) ); ?>
    ">
        <?php echo get_lang( 'General tracking' ); ?>
    </a>
    <br />
    <?php endif; ?>
    <?php if( $this->mode != 2 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackCourse"
                                                                . "&classId=$this->classId"
                                                                . "&mode=2" ) ); ?>
    ">
        <?php echo get_lang( 'Daily tracking' ); ?>
    </a>
    <br />
    <?php endif; ?>
    <br />

<?php endif; ?>

<?php foreach( $this->infoUserList as $infoUser ) : ?>
<?php $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() ); ?>

<h1> <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName(); ?> </h1>

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Total time' ); ?> </th>
        <th> <?php echo get_lang( 'Progress' ); ?> </th>
    </tr>
    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
    <?php
        $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
        $trackingCourseEntry = $trackingCourse->getGeneralTracking();
    ?>
        <tr class="headerX">
            <th> <?php echo $infoCourse->getCourseName() . " "; ?> </th>
            <?php if( !is_null( $trackingCourseEntry ) ) : ?>
                <?php if( $trackingCourseEntry->getWarning() ) : ?>
                <td class="warning bigCell"> <?php echo $trackingCourseEntry->getDate(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingCourseEntry->getTime(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingCourseEntry->getProgress() . "%"; ?> </td>
                <?php else : ?>
                <td class="bigCell"> <?php echo $trackingCourseEntry->getDate(); ?> </td>
                <td class="bigCell"> <?php echo $trackingCourseEntry->getTime(); ?> </td>
                <td class="bigCell"> <?php echo $trackingCourseEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
            <?php endif; ?>
        </tr>
        <?php if( $this->mode == 2 && !is_null( $trackingCourseEntry ) ) : ?>
        <tr><td class="emptyCell">&nbsp;</td></tr>
        <tr class="headerX">
            <td class="emptyCell">&nbsp;</td>
            <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
            <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
        </tr>
            <?php foreach( $trackingCourse->getTrackingList() as $trackingEntry ) : ?>
            <tr>
                <td class="emptyCell">&nbsp;</td>
                <?php if( $trackingEntry->getWarning() ) : ?>
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
        <tr><td class="emptyCell">&nbsp;</td></tr>
    <?php endforeach; ?>
</table>

<?php endforeach; ?>