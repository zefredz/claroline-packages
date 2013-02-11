<table  class="claroTable emphaseLine">
    <tr class="headerX">
        <th>
            <?php echo get_lang( 'Class' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'User' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'Course' ); ?>
        </th>
    </tr>
    
    <?php foreach( $this->trackingController->getClassList() as $classId => $className ) : ?>
    
    <tr>
        <td>
            <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . "?cmd=classViewTrackCourse&classId=$classId" ) ); ?>" />
                <?php echo "$className"; ?>
            </a>
        </td>
        <td>
            <?php echo $this->trackingController->getNbUserFromClass( $classId ); ?>
        </td>
        <td>
            <?php echo $this->trackingController->getNbCourseFromClass( $classId ); ?>
        </td>
    </tr>
    
    <?php endforeach; ?>
    
</table>