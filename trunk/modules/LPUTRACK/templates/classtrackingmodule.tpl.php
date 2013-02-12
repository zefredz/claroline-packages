<?php if( !$this->excelExport ) : ?>

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

    <div> <?php echo get_lang( 'Detail level' ); ?> </div>
    <?php if( $this->mode != 1 ) : ?>
    <a href="
        <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=1"
                                                                . "&courseCode=$this->courseCode"
                                                                . "&learnPathId=$this->learnPathId" ) ); ?>
    ">
        <?php echo get_lang( 'General tracking' ); ?>
    </a>
    <br />
    <?php endif; ?>
    <?php if( $this->mode != 2 ) : ?>
    <a href="
        <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=2"
                                                                . "&courseCode=$this->courseCode"
                                                                . "&learnPathId=$this->learnPathId" ) ); ?>
    ">
        <?php echo get_lang( 'Daily tracking' ); ?>
    </a>
    <br />
    <?php endif; ?>
    <?php if( $this->mode != 3 ) : ?>
    <a href="
        <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackModule"
                                                                . "&classId=$this->classId"
                                                                . "&mode=3"
                                                                . "&courseCode=$this->courseCode"
                                                                . "&learnPathId=$this->learnPathId" ) ); ?>
    ">
        <?php echo get_lang( 'Complete tracking' ); ?>
    </a>
    <br />
    <?php endif; ?>
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
            <?php echo $this->courseName . " : " . $this->learnPathName; ?>
        </th>
    </tr>
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
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
            <?php
                $moduleDisplayName = $infoModule->getModuleName();
                switch( $infoModule->getModuleContentType() )
                {
                    case 'EXERCISE':
                        $moduleDisplayName .= " (" . get_lang( 'Exercise' ) . ")";
                        break;

                    default:
                        break;
                }
                echo $moduleDisplayName
            ?>
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
    <tr class="headerX">
        <th class="trackingUserName">
        <!--<a href="">-->
        <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName(); ?>
        <!--</a>-->
        </th>
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
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                    <td class="emptyCell">&nbsp;</td>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </tr>
    
        <?php if( $this->mode == 2 || $this->mode == 3 ) : ?>
        <?php
            $continueDisplay = false;
            $indexInTrackingList = 0;
        ?>
        <tr class="headerX">
            <td class="emptyCell">&nbsp;</td>
            <?php foreach( $this->infoModuleList as $infoModule ) : ?>
                <?php
                    $trackingCourse = $trackingUser->getTrackingCourse( $infoModule->getCourseCode() );
                    $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoModule->getLearnPathId() );
                    $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                    $trackingEntry = $trackingModule->getGeneralTracking();
                ?>
                <?php if( !is_null( $trackingEntry ) ) : ?>
                    <?php $continueDisplay = true; ?>
                    <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                    <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
                    <th class="subTableHeader"> <?php echo get_lang( 'Progress' ); ?> </th>
                    
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
                    
                <?php else : ?>
                    <?php $continueDisplay |= false; ?>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                        <td class="emptyCell">&nbsp;</td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
            <?php while( $continueDisplay ) : ?>
                <?php $continueDisplay = false; ?>
                <tr>
                    <td class="emptyCell">&nbsp;</td>
                    <?php foreach( $this->infoModuleList as $infoModule ) : ?>
                        <?php
                            $trackingCourse = $trackingUser->getTrackingCourse( $infoModule->getCourseCode() );
                            $trackingLearnPath = $trackingCourse->getTrackingLearnPath( $infoModule->getLearnPathId() );
                            $trackingModule = $trackingLearnPath->getTrackingModule( $infoModule->getModuleId() );
                            $moduleTrackingList = null;
                            if( is_array( $trackingModule->getTrackingList() ) )
                            {
                                $moduleTrackingList = array_values( $trackingModule->getTrackingList() );
                            }
                        ?>
                        <?php if( is_array( $moduleTrackingList ) && isset( $moduleTrackingList[ $indexInTrackingList ] ) ) : ?>
                            <?php 
                                $continueDisplay = true;
                                $trackingEntry = $moduleTrackingList[ $indexInTrackingList ];
                            ?>
                            <?php if( $trackingEntry->getWarning() ) : ?>
                            <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                            <td class="warning"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                                <td class="warning">
                                    <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                                </td>
                                <?php endif; ?>
                            <?php else : ?>
                            <td> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td> <?php echo $trackingEntry->getTime(); ?> </td>
                            <td> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                                <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                                <td>
                                    <?php echo $trackingEntry->getScoreRaw() . "/" . $trackingEntry->getScoreMax(); ?>
                                </td>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php $continueDisplay |= false; ?>
                            <td class="emptyCell">&nbsp;</td>
                            <td class="emptyCell">&nbsp;</td>
                            <td class="emptyCell">&nbsp;</td>
                            <?php if( $infoModule->getModuleContentType() == 'EXERCISE' ) : ?>
                                <td class="emptyCell">&nbsp;</td>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php $indexInTrackingList++; ?>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    
    <?php endforeach; ?>
    
</table>
