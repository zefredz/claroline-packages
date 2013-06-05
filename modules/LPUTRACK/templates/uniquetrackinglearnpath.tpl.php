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
                                                                . "?cmd=uniqueUserViewTrackLearnPath"
                                                                . "&userId=$this->userId" ) ); ?>
    ">
        <?php echo get_lang( 'Alternative view' ); ?>
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
                                                     . "?cmd=uniqueGlobalViewTrackLearnPath"
                                                     . "&userId=$this->userId"
                                                     . "&mode=1"
                                                     . "&courseCode=$this->courseCode" ) );
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
                                                     . "?cmd=uniqueGlobalViewTrackLearnPath"
                                                     . "&userId=$this->userId"
                                                     . "&mode=2"
                                                     . "&courseCode=$this->courseCode" ) );
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

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <th colspan="<?php echo ( $this->nbLearnPath * 4 ); ?>">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo $this->courseCode . ' - ' . $this->courseName; ?>
        </th>
    </tr>
    <tr class="headerX">
        <th>
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo get_lang( 'Student' ); ?>
        </th>
        <?php foreach( $this->infoLearnPathList as $infoLearnPath ) : ?>
        <th colspan="4">
            <a href="
                <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                        . "?cmd=uniqueGlobalViewTrackModule"
                                                                        . "&userId="
                                                                        . $this->userId
                                                                        . "&courseCode="
                                                                        . $infoLearnPath->getCourseCode()
                                                                        . "&learnPathId="
                                                                        . $infoLearnPath->getLearnPathId() ) ); ?>
            ">
                <?php if( !$this->excelExport ) : ?>
                    <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
                <?php endif; ?>
                <?php echo $infoLearnPath->getLearnPathName(); ?>
            </a>
        </th>
        <?php endforeach; ?>
    </tr>

    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
    <?php foreach( $this->infoLearnPathList as $infoLearnPath ) : ?>
        <th> <?php echo get_lang( 'First connection' ); ?> </th>
        <th> <?php echo get_lang( 'Last connection' ); ?> </th>
        <th> <?php echo get_lang( 'Spent time' ); ?> </th>
        <th> <?php echo get_lang( 'Progress' ); ?> </th>
    <?php endforeach; ?>
    </tr>

    <tr <?php if( $this->mode == 2 ) echo 'class="detailsModeToggle"'; ?>>
        <td class="userLabel">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php if( $this->mode == 2 ) : ?>
            <a href="#">
            <?php echo $this->infoUser->getFirstName() . " " . $this->infoUser->getLastName();  ?>
            </a>
            <?php else : ?>
            <?php echo $this->infoUser->getFirstName() . " " . $this->infoUser->getLastName();  ?>
            <?php endif; ?>
        </td>

        <?php foreach( $this->infoLearnPathList as $infoLearnPath ) : ?>
            <?php
                $trackingUser = $this->trackingController->getTrackingUser( $this->infoUser->getUserId() );
                $trackingCourse = $trackingUser->getTrackingCourse( $infoLearnPath->getCourseCode() );
                $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoLearnPath->getLearnPathId() );
                $trackingEntry = $trackingLearnPath->getGeneralTracking();
            ?>
            <?php if( !is_null( $trackingEntry ) ) : ?>
                <?php if( $trackingEntry->getWarning() ) : ?>
                <td class="warning bigCell">
                    <?php echo $trackingEntry->getFirstConnection(); ?>
                </td>
                <td class="warning bigCell">
                    <?php echo $trackingEntry->getDate(); ?>
                </td>
                <td class="warning bigCell">
                    <?php echo $trackingEntry->getTime(); ?>
                </td>
                <td class="warning bigCell">
                    <?php echo $trackingEntry->getProgress() . "%"; ?>
                </td>
                <?php else : ?>
                <td class="bigCell">
                    <?php echo $trackingEntry->getFirstConnection(); ?>
                </td>
                <td class="bigCell">
                    <?php echo $trackingEntry->getDate(); ?>
                </td>
                <td class="bigCell">
                    <?php echo $trackingEntry->getTime(); ?>
                </td>
                <td class="bigCell">
                    <?php echo $trackingEntry->getProgress() . "%"; ?>
                </td>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>

    <?php if( $this->mode == 2 ) : ?>
    <tr class="detailsMode">
        <td class="emptyCell">&nbsp;</td>
        <?php foreach( $this->infoLearnPathList as $infoLearnPath ) : ?>
            <?php
                $trackingCourse = $trackingUser->getTrackingCourse( $infoLearnPath->getCourseCode() );
                $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoLearnPath->getLearnPathId() );
                $trackingEntry = $trackingLearnPath->getGeneralTracking();
            ?>
            <?php if( !is_null( $trackingEntry ) ) : ?>
                <td class="detailTable" colspan="4">
                    <table class="claroTable emphaseLine detailTable" width="100%" border="0" cellspacing="2">
                        <tr class="header">
                            <th> <?php echo get_lang( 'Date' ); ?> </th>
                            <th> <?php echo get_lang( 'Time' ); ?> </th>
                            <th> <?php echo get_lang( 'Progress' ); ?> </th>
                        </tr>
                        <?php foreach( $trackingLearnPath->getTrackingList() as $trackingEntry ) : ?>
                        <tr>
                            <?php if( $trackingEntry->getWarning() ) : ?>
                            <td class="warning"><?php echo $trackingEntry->getDate(); ?></td>
                            <td class="warning"><?php echo $trackingEntry->getTime(); ?></td>
                            <td class="warning"><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                            <?php else : ?>
                            <td><?php echo $trackingEntry->getDate(); ?></td>
                            <td><?php echo $trackingEntry->getTime(); ?></td>
                            <td><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <tr><td>&nbsp;</td></tr>
                    </table>
                </td>
            <?php else : ?>
                <td class="emptyCell" colspan="4">&nbsp;</td>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>

    <?php endif; ?>

</table>