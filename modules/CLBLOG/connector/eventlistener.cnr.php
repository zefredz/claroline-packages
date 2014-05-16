<?php // $Id$

Claroline::getInstance()->notification->addListener( 'post_added',             'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'post_modified',          'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'post_deleted',           'modificationDelete' );
Claroline::getInstance()->notification->addListener( 'comment_added',          'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'comment_modified',       'modificationDefault' );
Claroline::getInstance()->notification->addListener( 'comment_deleted',        'modificationDelete' );
