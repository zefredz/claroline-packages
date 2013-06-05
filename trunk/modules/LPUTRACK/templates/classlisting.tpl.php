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

<br>
<br>
<h2><?php echo get_lang( 'User list' ); ?></h2>

<form action="#">
    <label for="search"><?php echo get_lang('Search an user') ?></label>
    <input type="text" value="" name="searchuser" id="searchuser" />
    <input type="submit" value="<?php echo get_lang('Ok') ?>" />
</form>

<br>

<table  class="claroTable emphaseLine">
    <tr class="headerX">
        <th>
            <?php echo get_lang( 'Username' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'Name' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'First name' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'Email' ); ?>
        </th>
        <th>
            <?php echo get_lang( 'Course' ); ?>
        </th>
    </tr>

    <?php
        foreach( $this->userList as $user ) :
            $userId = $user['user_id'];
            $hasCourses = ($user['nbcours'] > 0);
    ?>
    <tr>
        <td>
            <?php if( $hasCourses ) : ?>
            <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . "?cmd=uniqueGlobalViewTrackCourse&userId=$userId" ) ); ?>">
            <?php endif; ?>
                <?php echo $user['username']; ?>
            <?php if( $hasCourses ) : ?>
            </a>
            <?php endif; ?>
        </td>
        <td><?php echo $user['nom']; ?></td>
        <td><?php echo $user['prenom']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td>
            <?php
                if( isset( $user['nbcours'] ) )
                {
                    echo $user['nbcours'];
                }
                else
                {
                    echo '0';
                }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>