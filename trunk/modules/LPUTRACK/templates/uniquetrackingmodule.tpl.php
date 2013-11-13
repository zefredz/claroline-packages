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
                                                                . "?cmd=uniqueUserViewTrackModule"
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
                                                     . "?cmd=uniqueGlobalViewTrackModule"
                                                     . "&userId=$this->userId"
                                                     . "&mode=1"
                                                     . "&courseCode=$this->courseCode"
                                                     . "&learnPathId=$this->learnPathId" ) );
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
                                                     . "?cmd=uniqueGlobalViewTrackModule"
                                                     . "&userId=$this->userId"
                                                     . "&mode=2"
                                                     . "&courseCode=$this->courseCode"
                                                     . "&learnPathId=$this->learnPathId" ) );
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
                                                     . "?cmd=uniqueGlobalViewTrackModule"
                                                     . "&userId=$this->userId"
                                                     . "&mode=3"
                                                     . "&courseCode=$this->courseCode"
                                                     . "&learnPathId=$this->learnPathId" ) );
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

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <th colspan="
        <?php
            $totalWidth = 0;
            foreach( $this->infoModuleList as $infoModule )
            {
                switch ( $infoModule->getModuleContentType() )
                {
                    case 'EXERCISE' :
                    case 'SCORM' :
                        if( $this->displayProgress )
                        {
                            $totalWidth += 5;
                        }
                        else
                        {
                            $totalWidth += 4;
                        }
                        break;

                    default:
                        if( $this->displayProgress )
                        {
                            $totalWidth += 4;
                        }
                        else
                        {
                            $totalWidth += 3;
                        }
                        break;
                }
            }
            echo $totalWidth;
        ?>
        ">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo $this->courseName . " : "; ?>
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo $this->learnPathName; ?>
        </th>
    </tr>
    <tr class="headerX">
        <th>
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php echo get_lang( 'Student' ); ?>
        </th>
        <?php foreach( $this->infoModuleList as $infoModule ) : ?>
            <th colspan="
            <?php
                $moduleWidth = 0;
                switch( $infoModule->getModuleContentType() )
                {
                    case 'EXERCISE':
                    case 'SCORM' :
                        if( $this->displayProgress )
                        {
                            $moduleWidth += 5;
                        }
                        else
                        {
                            $moduleWidth += 4;
                        }
                        break;

                    default:
                        if( $this->displayProgress )
                        {
                            $moduleWidth += 4;
                        }
                        else
                        {
                            $moduleWidth += 3;
                        }
                        break;
                }
                echo $moduleWidth;
            ?>
            ">
            <?php if( !$this->excelExport ) : ?>
                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                    <img src="<?php echo get_icon_url( 'quiz', 'CLQWZ' ); ?>" alt=""/>
                <?php else : ?>
                    <img src="
                        <?php
                            $modulePath = TrackingUtils::getPathFromModule( $this->courseCode, $infoModule->getModuleId() );
                            echo get_icon_url( choose_image( basename( $modulePath ) ) );
                        ?>
                        " alt=""
                    />
                <?php endif; ?>
            <?php endif; ?>

            <?php echo $infoModule->getModuleName(); ?>

            </th>
        <?php endforeach; ?>
    </tr>
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <?php foreach( $this->infoModuleList as $infoModule ) : ?>

            <th> <?php echo get_lang( 'First connection' ); ?> </th>
            <th> <?php echo get_lang( 'Last connection' ); ?> </th>
            <th> <?php echo get_lang( 'Spent time' ); ?> </th>
            <?php if( $this->displayProgress ) : ?>
                <th> <?php echo get_lang( 'Progress' ); ?> </th>
            <?php endif; ?>

            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                $infoModule->getModuleContentType() == 'SCORM' ) :
            ?>
                <th> <?php echo get_lang( 'Best score' ); ?> </th>
            <?php endif; ?>

        <?php endforeach; ?>
    </tr>

    <?php $trackingUser = $this->trackingController->getTrackingUser( $this->infoUser->getUserId() ); ?>
    <tr <?php if( $this->mode == 2 || $this->mode == 3 ) echo 'class="detailsModeToggle"'; ?>>
        <td class="userLabel">
            <?php if( !$this->excelExport ) : ?>
                <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php endif; ?>
            <?php if( $this->mode == 2 || $this->mode == 3 ) : ?>
            <a href="#">
            <?php echo $this->infoUser->getFirstName() . " " . $this->infoUser->getLastName();  ?>
            </a>
            <?php else : ?>
            <?php echo $this->infoUser->getFirstName() . " " . $this->infoUser->getLastName();  ?>
            <?php endif; ?>
        </td>
        <?php foreach( $this->infoModuleList as $infoModule ) : ?>
            <?php
                $trackingCourse = $trackingUser->getTrackingCourse( $this->courseCode );
                $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $this->learnPathId );
                $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                $trackingEntry = $trackingModule->getGeneralTracking();
            ?>
            <?php if( !is_null( $trackingEntry ) ) : ?>
                <?php if( $trackingEntry->getWarning() ) : ?>
                    <td class="warning bigCell"> <?php echo $trackingEntry->getFirstConnection(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                    <td class="warning bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                    <?php if( $this->displayProgress ) : ?>
                        <td class="warning bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                        $infoModule->getModuleContentType() == 'SCORM' ) :
                    ?>
                        <td class="warning bigCell">
                            <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                        </td>
                    <?php endif; ?>
                <?php else : ?>
                    <td class="bigCell"> <?php echo $trackingEntry->getFirstConnection(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                    <td class="bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                    <?php if( $this->displayProgress ) : ?>
                        <td class="bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                    <?php endif; ?>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                        $infoModule->getModuleContentType() == 'SCORM' ) :
                    ?>
                        <td class="bigCell">
                            <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                        </td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <?php if( $this->displayProgress ) : ?>
                    <td class="emptyCell bigCell">-</td>
                <?php endif; ?>
                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                    $infoModule->getModuleContentType() == 'SCORM' ) :
                ?>
                    <td class="emptyCell">-</td>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>

        <?php if( $this->mode == 2 || $this->mode == 3 ) : ?>
        <tr class="detailsMode">
            <td class="emptyCell">&nbsp;</td>
            <?php foreach( $this->infoModuleList as $infoModule ) : ?>
                <?php
                    $trackingCourse = $trackingUser->getTrackingCourse( $infoModule->getCourseCode() );
                    $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoModule->getLearnPathId() );
                    $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                    $trackingEntry = $trackingModule->getGeneralTracking();
                ?>
                <?php if( !is_null( $trackingEntry ) ) : ?>
                    <td class="detailTable" colspan="<?php if( $this->displayProgress ) echo ( ( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) ? 5 : 4 ); else echo ( ( $infoModule->getModuleContentType() == 'EXERCISE' || $infoModule->getModuleContentType() == 'SCORM' ) ? 4 : 3 ); ?>">
                        <table class="claroTable emphaseLine detailTable" width="100%" border="0" cellspacing="2">
                            <tr class="header">
                                <th> <?php echo get_lang( 'Date' ); ?> </th>
                                <th> <?php echo get_lang( 'Time' ); ?> </th>
                                <?php if( $this->displayProgress ) : ?>
                                    <th> <?php echo get_lang( 'Progress' ); ?> </th>
                                <?php endif; ?>
                                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                                    $infoModule->getModuleContentType() == 'SCORM' ) :
                                ?>
                                    <?php if( $this->mode == 2 ) : ?>
                                    <th>
                                        <?php echo get_lang( 'Best score' ); ?>
                                    </th>
                                    <?php elseif( $this->mode == 3 ) : ?>
                                    <th>
                                        <?php echo get_lang( 'Score' ); ?>
                                    </th>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php foreach( $trackingModule->getTrackingList() as $trackingEntry ) : ?>
                            <tr>
                                <?php if( $trackingEntry->getWarning() ) : ?>
                                    <td class="warning"><?php echo $trackingEntry->getDate(); ?></td>
                                    <td class="warning"><?php echo $trackingEntry->getTime(); ?></td>
                                    <?php if( $this->displayProgress ) : ?>
                                        <td class="warning"><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php endif; ?>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                                        $infoModule->getModuleContentType() == 'SCORM' ) :
                                    ?>
                                        <td class="warning"><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <td><?php echo $trackingEntry->getDate(); ?></td>
                                    <td><?php echo $trackingEntry->getTime(); ?></td>
                                    <?php if( $this->displayProgress ) : ?>
                                        <td><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php endif; ?>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                                        $infoModule->getModuleContentType() == 'SCORM' ) :
                                    ?>
                                        <td><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </td>

                <?php else : ?>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ||
                        $infoModule->getModuleContentType() == 'SCORM' ) :
                    ?>
                        <td class="emptyCell" colspan="<?php if( $this->displayProgress ) : ?> 5 <?php else : ?> 4 <?php endif; ?>">&nbsp;</td>
                    <?php else : ?>
                        <td class="emptyCell" colspan="<?php if( $this->displayProgress ) : ?> 4 <?php else : ?> 3 <?php endif; ?>">&nbsp;</td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>

        <?php endif; ?>

</table>
