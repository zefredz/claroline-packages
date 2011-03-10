<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.2.7 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqCreateLibrary') ); ?>">
        <img src="<?php echo get_icon_url( 'courseadd' ); ?>" alt="<?php echo get_lang( 'Add' ); ?>" />
        <?php echo get_lang( 'Create a new library' ); ?>
    </a>
</span>

<?php foreach( $this->resourceList as $category => $resourceList ) : ?>
<fieldset id="<?php echo $category; ?>Library">
    <legend><?php echo get_lang( $category . ' library' ); ?></legend>
    <table class="claroTable emphaseLine" style=" width: 100%;">
        <thead>
            <tr class="headerX">
                <th>
                    <?php echo get_lang( 'Title'); ?>
                </th>
                <th>
                    <?php echo get_lang( 'Librarians' ); ?>
                </th>
    <?php if ($category == 'user') : ?>
                <th>
                    <?php echo get_lang( 'Commands' ); ?>
                </th>
    <?php endif; ?>
            </tr>
        </thead>
        <tbody>
    <?php if ( $resourceList->numRows() ) : ?>
        <?php foreach ( $resourceList as $library ) : ?>
            <tr>
                <td>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?context=Catalogue&libraryId='. $library[ 'id' ] ) );?>">
                        <?php echo $library[ 'title' ]; ?>
                    </a>
                </td>
                <td> 
                    <?php echo '-'; ?>
                </td>
            <?php if ($category == 'user') : ?>
                <td align="center">
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteLibrary&context=LibraryList&libraryId='. $library[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLibrary&libraryId='. $library[ 'id' ] ) );?>">
                        <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                    </a>
                </td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
            <tr>
                <td class="empty" colspan="3"><?php echo get_lang( 'Empty bibliography' ); ?></td>
            </tr>
    <?php endif; ?>
        </tbody>
    </table>
</fieldset>
<?php endforeach; ?>