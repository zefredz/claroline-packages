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
                                                                . "?cmd=userViewTrackModule"
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
                                                     . "?cmd=classViewTrackModule"
                                                     . "&classId=$this->classId"
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
                                                     . "?cmd=classViewTrackModule"
                                                     . "&classId=$this->classId"
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
                                                     . "?cmd=classViewTrackModule"
                                                     . "&classId=$this->classId"
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
                        $totalWidth += 4;
                        break;

                    default:
                        $totalWidth += 3;
                        break;
                }
            }
            echo $totalWidth;
        ?>
        ">
            <img src="<?php echo get_icon_url( 'course' ); ?>" alt=""/>
            <?php echo $this->courseName . " : "; ?>
            <img src="<?php echo get_module_icon_url( 'CLLNP', 'learnpath' ); ?>" alt=""/>
            <?php echo $this->learnPathName; ?>
        </th>
    </tr>
    <tr class="headerX">
        <th>
            <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php echo get_lang( 'Student' ); ?>
        </th>
        <?php foreach( $this->infoModuleList as $infoModule ) : ?>
            <th colspan="
            <?php
                $moduleWidth = 0;
                switch( $infoModule->getModuleContentType() )
                {
                    case 'EXERCISE':
                        $moduleWidth = 4;
                        break;

                    default:
                        $moduleWidth = 3;
                        break;
                }
                echo $moduleWidth;
            ?>
            ">
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
                
            <?php echo $infoModule->getModuleName(); ?>
                
            </th>
        <?php endforeach; ?>
    </tr>
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        <?php foreach( $this->infoModuleList as $infoModule ) : ?>
                    
            <th> <?php echo get_lang( 'Last connection' ); ?> </th>
            <th> <?php echo get_lang( 'Spent time' ); ?> </th>
            <th> <?php echo get_lang( 'Progress' ); ?> </th>
            
            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
            <th> <?php echo get_lang( 'Best score' ); ?> </th>
            <?php endif; ?>
            
        <?php endforeach; ?>
    </tr>
    
    <?php foreach( $this->infoUserList as $infoUser ) : ?>
    <?php $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() ); ?>
    <tr <?php if( $this->mode == 2 || $this->mode == 3 ) echo 'class="detailsModeToggle"'; ?>>
        <td class="userLabel">
            <img src="<?php echo get_icon_url( 'user' ); ?>" alt=""/>
            <?php if( $this->mode == 2 || $this->mode == 3 ) : ?>
            <a href="#">
            <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName();  ?>
            </a>
            <?php else : ?>
            <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName();  ?>
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
                <td class="warning bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                    <td class="warning bigCell">
                        <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                    </td>
                    <?php endif; ?>
                <?php else : ?>
                <td class="bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                    <td class="bigCell">
                        <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                    </td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <td class="emptyCell bigCell">-</td>
                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
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
                    <td class="detailTable" colspan="<?php echo ( ( $infoModule->getModuleContentType() == 'EXERCISE' ) ? 4 : 3 ); ?>">
                        <table class="claroTable emphaseLine detailTable" width="100%" border="0" cellspacing="2">
                            <tr class="header">
                                <th> <?php echo get_lang( 'Date' ); ?> </th>
                                <th> <?php echo get_lang( 'Time' ); ?> </th>
                                <th> <?php echo get_lang( 'Progress' ); ?> </th>
                                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
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
                                <td class="warning"><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                                    <td class="warning"><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                    <?php endif; ?>
                                <?php else : ?>
                                <td><?php echo $trackingEntry->getDate(); ?></td>
                                <td><?php echo $trackingEntry->getTime(); ?></td>
                                <td><?php echo $trackingEntry->getProgress() . '%'; ?></td>
                                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                                    <td><?php echo $trackingEntry->getScoreRaw() . '/' . $trackingEntry->getScoreMax(); ?></td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                            <tr><td>&nbsp;</td></tr>
                        </table>
                    </td>
 
                <?php else : ?>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                        <td class="emptyCell" colspan="4">&nbsp;</td>
                    <?php else : ?>
                        <td class="emptyCell" colspan="3">&nbsp;</td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        
        <?php endif; ?>
    
    <?php endforeach; ?>
    
</table>
