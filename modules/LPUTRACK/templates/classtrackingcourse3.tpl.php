<?php if( !$this->excelExport ) : ?>

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

    <form action="" method="post">
        <input type="Submit" name="excelexport" value="<?php echo get_lang( 'xls export' ) ?>">
    </form>
    <br />

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackCourse"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'User view' ); ?>
    </a>
    <br />
    <br />

    <table border="0" cellspacing="10">
        <tr>
            <td class="simpleLabel"><?php echo get_lang( 'Detail level' ) . ' :'; ?></td>
            <td class="simpleLabel">
                <input type="radio" id="general_detail" name="detail_level" value="general" <?php if( $this->mode == 1 ) echo 'checked'; ?>
                   onchange="location.href='<?php
                                                echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                     . "?cmd=classViewTrackCourse"
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
                                                     . "?cmd=classViewTrackCourse"
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

<?php if( $this->excelExport ) : ?>
    <p> <?php echo get_lang( 'Platform name' ) . ' : ' . $this->platformName; ?> </p>
    <?php if( trim($this->institutionName) !== '' ) : ?>
        <p> <?php echo get_lang( 'Institution' ) . ' : ' . $this->institutionName; ?> </p>
    <?php endif; ?>
    <p> <?php echo get_lang( 'Class' ) . ' : ' . $this->className; ?> </p>
    <br>
<?php endif; ?>

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <th>
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo get_lang( 'Student' ); ?>
        </th>

        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>

        <th colspan="<?php if( $this->displayProgress ) : ?> 5 <?php else : ?> 4 <?php endif; ?>">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
            <?php endif; ?>
            <a href="
                <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                        . "?cmd=classViewTrackLearnPath"
                                                                        . "&classId="
                                                                        . $this->classId
                                                                        . "&courseCode="
                                                                        . $infoCourse->getCourseCode() ) ); ?>
            ">
                <?php echo $infoCourse->getCourseCode() . ' - ' . $infoCourse->getCourseName(); ?>
            </a>
        </th>

        <?php endforeach; ?>

    </tr>

    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>

        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>

        <th> <?php echo get_lang( 'First connection' ); ?> </th>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Spent time' ); ?> </th>
        <?php if( $this->displayProgress ) : ?>
            <th> <?php echo get_lang( 'Progress' ); ?> </th>
        <?php endif; ?>
        <th> <?php echo get_lang( 'Inactivity (day)' ); ?> </th>

        <?php endforeach; ?>

    </tr>

    <?php foreach( $this->infoUserList as $infoUser ) : ?>
    <?php
        $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() );
    ?>
    <tr <?php if( $this->mode == 2 ) echo 'class="detailsModeToggle"'; ?>>
        <td class="userLabel">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php if( $this->mode == 2 ) : ?>
            <a href="#">
            <?php echo $infoUser->getLastName() . " " . $infoUser->getFirstName();  ?>
            </a>
            <?php else : ?>
            <?php echo $infoUser->getLastName() . " " . $infoUser->getFirstName();  ?>
            <?php endif; ?>
        </td>

        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
            <?php
                $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
                $trackingEntry = $trackingCourse->getGeneralTracking();
            ?>

            <?php if( !is_null( $trackingEntry ) ) : ?>
                <?php if( $trackingEntry->getWarning() ) : ?>
                <td class="warning bigCell"> <?php echo $trackingEntry->getFirstConnection(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <?php if( $this->displayProgress ) : ?>
                    <td class="warning bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>

                <td class="warning bigCell">
                    <?php
                    $entryDate = new DateTime($trackingEntry->getDate() );
                    $today = new DateTime( date('Y-m-d') );
                    $interval = $today->diff( $entryDate, true );
                    echo $interval->format('%a');
                    ?>
                </td>
                <?php else : ?>
                <td class="bigCell"> <?php echo $trackingEntry->getFirstConnection(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <?php if( $this->displayProgress ) : ?>
                    <td class="bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                <?php endif; ?>
                <td class="bigCell">
                    <?php
                    $entryDate = new DateTime($trackingEntry->getDate() );
                    $today = new DateTime( date('Y-m-d') );
                    $interval = $today->diff( $entryDate, true );
                    echo $interval->format('%a');
                    ?>
                </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <?php if( $this->displayProgress ) : ?>
                    <td class="emptyCell bigCell">-</td>
                <?php endif; ?>
            <?php endif; ?>

        <?php endforeach; ?>
    </tr>

        <?php if( $this->mode == 2 ) : ?>
        <tr class="detailsMode">
            <td class="emptyCell">&nbsp;</td>
            <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
                <?php
                    $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
                    $trackingEntry = $trackingCourse->getGeneralTracking();
                ?>
                <?php if( !is_null( $trackingEntry ) ) : ?>
                    <td class="detailTable" colspan="<?php if( $this->displayProgress ) : ?> 4 <?php else : ?> 3 <?php endif; ?>">
                        <table class="claroTable emphaseLine detailTable" width="100%" border="0" cellspacing="2">
                            <tr class="header">
                                <th> <?php echo get_lang( 'Date' ); ?> </th>
                                <th> <?php echo get_lang( 'Time' ); ?> </th>
                                <?php if( $this->displayProgress ) : ?>
                                    <th> <?php echo get_lang( 'Progress' ); ?> </th>
                                <?php endif; ?>
                            </tr>
                            <?php foreach( $trackingCourse->getTrackingList() as $trackingEntry ) : ?>
                            <tr>
                                <?php if( $trackingEntry->getWarning() ) : ?>
                                    <td class="warning"><?php echo $trackingEntry->getDate(); ?></td>
                                    <td class="warning"><?php echo $trackingEntry->getTime(); ?></td>
                                    <?php if( $this->displayProgress ) : ?>
                                        <td class="warning"><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <td><?php echo $trackingEntry->getDate(); ?></td>
                                    <td><?php echo $trackingEntry->getTime(); ?></td>
                                    <?php if( $this->displayProgress ) : ?>
                                        <td><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </td>
                    <td class="emptyCell">&nbsp;</td>
                <?php else : ?>
                    <td class="emptyCell" colspan="<?php if( $this->displayProgress ) : ?> 5 <?php else : ?> 4 <?php endif; ?>">&nbsp;</td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>

</table>