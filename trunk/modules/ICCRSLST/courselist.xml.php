<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<response>
    <user>
        <firstname><?php echo $this->user['firstname']; ?></firstname>
        <lastname><?php echo $this->user['lastname']; ?></lastname>
        <officialCode><?php echo $this->user['officialCode']; ?></officialCode>
        <email><?php echo $this->user['email']; ?></email>
    </user>
    <courses><?php if ( count( $this->courses ) ):
    foreach ( $this->courses as $course ) : ?>
        <course id="<?php echo $course['id']; ?>">
            <title><?php echo $course['title']; ?></title>
            <code><?php echo $course['officialCode']; ?></code>
            <manager><?php echo $course['isCourseManager'] == 1 ? 'true' : 'false'; ?></manager>
            <profile><?php echo claro_get_profile_name( $course['profileId'] ); ?></profile>
        </course>
    <?php endforeach;
    endif;?></courses>
</response>
