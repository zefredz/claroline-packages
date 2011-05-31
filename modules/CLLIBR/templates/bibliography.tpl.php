<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.4.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php if ( $this->userId ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarylist'); ?>">
        <img src="<?php echo get_icon_url( 'icon' ); ?>" alt="<?php echo get_lang( 'Libraries' ); ?>" />
        <?php echo get_lang( 'Libraries' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowBookmark') ); ?>">
        <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Bookmark' ); ?>" />
        <?php echo get_lang( 'My bookmark' ); ?>
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
<?php if ( ! empty( $this->resourceList ) ) : ?>
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
        <?php if ( $this->userId ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Add to my bookmarks' ); ?>"/>
                </a>
        <?php endif; ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
        <?php if ( $this->edit_allowed ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemove&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditResource&resourceId='. $resourceId ) );?>">
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