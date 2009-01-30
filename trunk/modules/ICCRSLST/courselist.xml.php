<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<response>
    <user>
        <firstname><?php echo $this->user['firstname']; ?></firstname>
        <lastname><?php echo $this->user['lastname']; ?></lastname>
        <officialCode><?php echo $this->user['officialCode']; ?></officialCode>
        <email><?php echo $this->user['email']; ?></email>
    </user>
    <courses><?php foreach ( $this->courses as $course ) : ?>
        <course code="<?php echo $course['officialCode']; ?>" id="<?php echo $course['id']; ?>">
            <title><?php echo $course['title']; ?></title>
        </course>
    <?php endforeach; ?></courses>
</response>
