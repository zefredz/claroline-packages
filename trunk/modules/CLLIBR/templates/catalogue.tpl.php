<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Title'); ?>
            </th>
            <th>
                <?php echo get_lang( 'Author(s)' ); ?>
            </th>
            <!--
            <th>
                <?php echo get_lang( 'Selected' ); ?>
            </th>
            -->
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
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resourceId . '&libraryId=' . $this->libraryId ) );?>">
                    <?php echo $objects[1]->get( Metadata::TITLE ); ?>
                </a>
            </td>
            <td> 
                <?php echo $objects[1]->get( 'author' ); ?>
            </td>
            <!--
            <td align="center">
                <input type="checkbox" name="select[<?php echo $resourceId; ?>]" />
            </td>
            -->
            <td align="center">
                <?php if ( $this->courseId && $this->edit_allowed ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAdd&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'book' ); ?>" alt="<?php echo get_lang( 'Add to the course\'s bibliography' ); ?>"/>
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'bookmark' ); ?>" alt="<?php echo get_lang( 'Add to my bookmarks' ); ?>"/>
                </a>
                <!--
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
                -->
        <?php if ( $this->edit_allowed ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&option=move&libraryId=' . $this->libraryId . '&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'move' ); ?>" alt="<?php echo get_lang( 'Move' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteResource&libraryId=' . $this->libraryId . '&resourceId='. $resourceId ) );?>">
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
            <td class="empty" colspan="5"><?php echo get_lang( 'Empty catalogue' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>