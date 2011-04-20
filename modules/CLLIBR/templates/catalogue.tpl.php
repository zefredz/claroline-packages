<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.3.5 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLibrarian&context=librarylist&libraryId=' . $this->libraryId ) ); ?>">
        <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'librarians' ); ?>" />
        <?php echo get_lang( 'Manage librarians' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddResource&libraryId=' . $this->libraryId ) ); ?>">
        <img src="<?php echo get_icon_url( 'courseadd' ); ?>" alt="<?php echo get_lang( 'Add' ); ?>" />
        <?php echo get_lang( 'Add a resource' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&libraryId=' . $this->libraryId ) ); ?>">
        <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Add selection to bookmark' ); ?>" />
        <?php echo get_lang( 'Add selection to my bookmark' ); ?>
    </a>
</span>
<?php if ( $this->courseId && $this->is_allowed_to_edit ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAdd&libraryId=' . $this->libraryId ) ); ?>">
        <img src="<?php echo get_icon_url( 'course' ); ?>" alt="<?php echo get_lang( 'Add selection in bibliography' ); ?>" />
        <?php echo get_lang( 'Add selection in course\'s bibliography' ); ?>
    </a>
</span>
<?php endif; ?>
<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Title'); ?>
            </th>
            <th>
                <?php echo get_lang( 'Author(s)' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Collection(s)' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Selected' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Commands' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( ! empty( $this->resourceList  ) ) : ?>
    <?php foreach ( $this->resourceList as $resourceId => $objects ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resourceId ) );?>">
                    <?php echo $objects[0]->getTitle(); ?>
                </a>
            </td>
            <td> 
                <?php echo implode( ', ' , $objects[1]->get( 'author' ) ); ?>
            </td>
            <td> 
                <?php echo implode( ', ' , $objects[1]->get( 'collection' ) ); ?>
            </td>
            <td align="center">
                <input type="checkbox" name="select[<?php echo $resourceId; ?>]" />
            </td>
            <td align="center">
                <?php if ( $this->courseId && $this->is_allowed_to_edit ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAdd&resourceId='. $resourceId . '&courseId=' . $this->courseId ) );?>">
                    <img src="<?php echo get_icon_url( 'course' ); ?>" alt="<?php echo get_lang( 'Add to the course\' bibliography' ); ?>"/>
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Add to my bookmarks' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
        <?php if ( $this->is_allowed_to_edit ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDelete&libraryId=' . $this->libraryId . '&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditResource&resourceId='. $resourceId . '&libraryId=' . $this->libraryId ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                </a>
        <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td class="empty" colspan="6"><?php echo get_lang( 'Empty bibliography' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>