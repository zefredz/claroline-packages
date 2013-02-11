<?php if( !$this->excelExport ) : ?>

    <form action="" method="post">
        <input type="Submit" name="excelexport" value="<?php echo get_lang( 'xls export' ) ?>">
    </form>
    </br>

    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=userViewTrackCourse"
                                                                . "&classId=$this->classId" ) ); ?>
    ">
        <?php echo get_lang( 'User view' ); ?>
    </a>
    </br>
    </br>

    <div> <?php echo get_lang( 'Detail level' ); ?> </div>
    <?php if( $this->mode != 1 ) : ?>
    <a href="
       <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                . "?cmd=classViewTrackCourse"
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
                                                                . "?cmd=classViewTrackCourse"
                                                                . "&classId=$this->classId"
                                                                . "&mode=2" ) ); ?>
    ">
        <?php echo get_lang( 'Daily tracking' ); ?>
    </a>
    </br>
    <?php endif; ?>
    </br>

<?php endif; ?>

<table class="claroTable emphaseLine">
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        
        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
        
        <th colspan="4">
            <a href="
                <?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF']
                                                                        . "?cmd=classViewTrackLearnPath"
                                                                        . "&classId="
                                                                        . $this->classId
                                                                        . "&courseCode="
                                                                        . $infoCourse->getCourseCode() ) ); ?>
            ">
                <?php echo $infoCourse->getCourseName(); ?>
            </a>
        </th>
        
        <?php endforeach; ?>
                
    </tr>
    
    <tr class="headerX">
        <td class="emptyCell">&nbsp;</td>
        
        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>

        <th> <?php echo get_lang( 'Last connection' ); ?> </th> 
        <th> <?php echo get_lang( 'Spent time' ); ?> </th>      
        <th> <?php echo get_lang( 'Progress' ); ?> </th>
        <th> <?php echo get_lang( 'Inactivity (day)' ); ?> </th> 
        
        <?php endforeach; ?>
        
    </tr>
    
    <?php foreach( $this->infoUserList as $infoUser ) : ?>
        <?php 
            $trackingUser = $this->trackingController->getTrackingUser( $infoUser->getUserId() );
        ?>
    <tr class="headerX">
        <th class="trackingUserName">
        <!--<a href="">-->
        <?php echo $infoUser->getFirstName() . " " . $infoUser->getLastName();  ?>
        <!--</a>-->
        </th>
    
        <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
            <?php 
                $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
                $trackingEntry = $trackingCourse->getGeneralTracking();
            ?>

            <?php if( !is_null( $trackingEntry ) ) : ?>
                <?php if( $trackingEntry->getWarning() ) : ?>
                <td class="warning bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <td class="warning bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                <td class="warning bigCell"> 
                    <?php
                    $entryDate = new DateTime($trackingEntry->getDate() );
                    $today = new DateTime( date('Y-m-d') );
                    $interval = $today->diff( $entryDate, true );
                    echo $interval->format('%a');
                    ?>
                </td>
                <?php else : ?>
                <td class="bigCell"> <?php echo $trackingEntry->getDate(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getTime(); ?> </td>
                <td class="bigCell"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
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
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
                <td class="emptyCell">&nbsp;</td>
            <?php endif; ?>
        
        <?php endforeach; ?>
    </tr>
    
        <?php if( $this->mode == 2 ) : ?>
        <?php
            $continueDisplay = false;
            $indexInTrackingList = 0;
        ?>
        <tr class="headerX">
            <td class="emptyCell">&nbsp;</td>
            <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
                <?php
                    $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
                    $trackingEntry = $trackingCourse->getGeneralTracking();
                ?>
                <?php if( !is_null( $trackingEntry ) ) : ?>
                    <?php $continueDisplay = true; ?>
                    <th class="subTableHeader"> <?php echo get_lang( 'Date' ); ?> </th>
                    <th class="subTableHeader"> <?php echo get_lang( 'Time' ); ?> </th>
                    <th class="subTableHeader"> <?php echo get_lang( 'Progress' ); ?> </th>
                    <td class="emptyCell">&nbsp;</td>
                <?php else : ?>
                    <?php $continueDisplay |= false; ?>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                    <td class="emptyCell">&nbsp;</td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
            <?php while( $continueDisplay ) : ?>
                <?php $continueDisplay = false; ?>
                <tr>
                    <td class="emptyCell">&nbsp;</td>
                    <?php foreach( $this->infoCourseList as $infoCourse ) : ?>
                        <?php
                            $trackingCourse = $trackingUser->getTrackingCourse( $infoCourse->getCourseCode() );
                            $courseTrackingList = null;
                            if( is_array( $trackingCourse->getTrackingList() ) )
                            {
                                $courseTrackingList = array_values( $trackingCourse->getTrackingList() );
                            }
                        ?>
                        <?php if( is_array( $courseTrackingList ) && isset( $courseTrackingList[ $indexInTrackingList ] ) ) : ?>
                            <?php 
                                $continueDisplay = true;
                                $trackingEntry = $courseTrackingList[ $indexInTrackingList ];
                            ?>
                            <?php if( $trackingEntry->getWarning() ) : ?>
                            <td class="warning"> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td class="warning"> <?php echo $trackingEntry->getTime(); ?> </td>
                            <td class="warning"> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                            <?php else : ?>
                            <td> <?php echo $trackingEntry->getDate(); ?> </td>
                            <td> <?php echo $trackingEntry->getTime(); ?> </td>
                            <td> <?php echo $trackingEntry->getProgress() . "%"; ?> </td>
                            <?php endif; ?>
                            <td class="emptyCell">&nbsp;</td>
                        <?php else : ?>
                            <?php $continueDisplay |= false; ?>
                            <td class="emptyCell">&nbsp;</td>
                            <td class="emptyCell">&nbsp;</td>
                            <td class="emptyCell">&nbsp;</td>
                            <td class="emptyCell">&nbsp;</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php $indexInTrackingList++; ?>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    
    <?php endforeach; ?>
    
</table>