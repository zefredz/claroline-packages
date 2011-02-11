<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?context=LibraryList') ); ?>">
        <img src="<?php echo get_icon_url( 'icon' ); ?>" alt="<?php echo get_lang( 'Libraries' ); ?>" />
        <?php echo get_lang( 'Libraries' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?context=Bookmark') ); ?>">
        <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Bookmark' ); ?>" />
        <?php echo get_lang( 'My bookmark' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmarkSelected') ); ?>">
        <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Add' ); ?>" />
        <?php echo get_lang( 'Add selection to my bookmarks' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExportAll') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>" />
        <?php echo get_lang( 'Export all the bibliography' ); ?>
    </a>
</span>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExportSelected') ); ?>">
        <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>" />
        <?php echo get_lang( 'Export selected' ); ?>
    </a>
</span>
<?php if ( $this->is_allowed_to_edit ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditBibliography') ); ?>">
        <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>" />
        <?php echo get_lang( 'Edit course bibliography' ); ?>
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
<?php if ( $this->resourceList ) : ?>
    <?php foreach ( $this->resourceList as $resourceId => $resource ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&resourceId='. $resourceId ) );?>">
                    <?php echo $resource[ 'title' ]; ?>
                </a>
            </td>
            <td> 
                <?php /*echo implode( ', ' , $resource[ 'authorList' ] );*/ ?>
            </td>
            <td> 
                <?php echo isset( $resource[ 'collection' ] ) ? $resource[ 'collection' ] : get_lang( 'None' ); ?>
            </td>
            <td align="center">
                <input type="checkbox" name="select[<?php echo $resourceId; ?>]" />
            </td>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exBookmark&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'mycourses' ); ?>" alt="<?php echo get_lang( 'Add to my bookmarks' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exExport&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'export' ); ?>" alt="<?php echo get_lang( 'Export' ); ?>"/>
                </a>
        <?php if ( $this->is_allowed_to_edit ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemove&context=Bibliography&resourceId='. $resourceId ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exEdit&resourceId='. $resourceId ) );?>">
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