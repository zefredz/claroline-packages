<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.8.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php include( 'selector.tpl.php' ); ?>
<?php include( dirname(__FILE__) . '/searchform.tpl.php' ); ?>

<form method="post"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) );?>">
    <table class="claroTable emphaseLine" style=" width: 100%;">
        <thead>
            <tr class="headerX">
                <th>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowCatalogue&libraryId=' . $this->libraryId ) ); ?>">
                    <?php echo get_lang( 'Title'); ?>
                    </a>
                </th>
                <th>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( get_module_url( 'CLLIBR' ) .'/index.php?cmd=rqShowCatalogue&sort=author&libraryId=' . $this->libraryId ) ); ?>">
                    <?php echo get_lang( 'Author(s)' ); ?>
                    </a>
                </th>
            <?php if ( count( $this->resourceList ) > 1 ) : ?>
                <th>
                    <input id="selectAll" type="checkbox" />
                </th>
            <?php endif; ?>
                <th>
                    <?php echo get_lang( 'Actions' ); ?>
                </th>
            </tr>
        </thead>
        <tbody>
    <?php if ( ! empty( $this->resourceList ) ) : ?>
        <?php foreach ( $this->resourceList as $resource ) : ?>
            <tr>
                <td>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resource[ 'id' ] . '&libraryId=' . $this->libraryId ) );?>">
                        <?php echo $resource[ 'title' ]; ?>
                    </a>
                </td>
                <td> 
                    <?php echo $resource[ 'author' ]; ?>
                </td>
            <?php if ( count( $this->resourceList ) > 1 ) : ?>
                <td align="center">
                    <input class="resourceSelect" type="checkbox" name="resource[<?php echo $resource[ 'id' ]; ?>]" />
                </td>
            <?php endif; ?>
                <td align="center">
                    <?php if ( $this->courseId && $this->edit_allowed ) : ?>
                    <a title="<?php echo get_lang( 'Add to the course\'s bibliography' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exAdd&resourceId='. $resource[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'book' ); ?>" alt="<?php echo get_lang( 'Add to the course\'s bibliography' ); ?>"/>
                    </a>
                    <?php endif; ?>
                    <a title="<?php echo get_lang( 'Add to my bookmark' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resource[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'bookmark' ); ?>" alt="<?php echo get_lang( 'Add to my bookmark' ); ?>"/>
                    </a>
                    <!--
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resource[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                    </a>
                    -->
            <?php if ( $this->edit_allowed ) : ?>
                    <a title="<?php echo get_lang( 'Move this resource to another library' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&option=move&libraryId=' . $this->libraryId . '&resourceId='. $resource[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'move' ); ?>" alt="<?php echo get_lang( 'Move' ); ?>"/>
                    </a>
                    <a title="<?php echo get_lang( 'Delete this resource' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteResource&libraryId=' . $this->libraryId . '&resourceId='. $resource[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                    <a title="<?php echo get_lang( 'Edit resource\'s metadatas' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditResource&resourceId='. $resource[ 'id' ] . '&libraryId=' . $this->libraryId ) );?>">
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
    <?php if ( count( $this->resourceList ) > 1 ) : ?>
    <div id="onSelected">
        <span class="claroCmd"><?php echo get_lang( 'On selected items' ); ?>:</span>
        <select name="cmd">
        <?php if ( $this->courseId ) : ?>
            <option value="exAdd"><?php echo get_lang( 'Add to the course\'s bibliography' ); ?></option>
        <?php endif; ?>
            <option value="exBookmark"><?php echo get_lang( 'Add to my bookmark' ); ?></option>
        </select>
        <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    </div>
    <?php endif; ?>
</form>