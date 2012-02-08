<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.7.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2011 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLLIBR
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<?php include( dirname(__FILE__) . '/searchform.tpl.php' ); ?>

<div id="mainContent">
<?php foreach( $this->resourceList as $category => $resourceList ) : ?>
<fieldset id="<?php echo $category; ?>Library">
    <legend><?php echo get_lang( ucwords( $category . ' libraries' ) ); ?></legend>
    <table class="claroTable emphaseLine" style=" width: 100%;">
        <thead>
            <tr class="headerX">
                <th>
                    <?php echo get_lang( 'Title'); ?>
                </th>
                <th>
                    <?php echo get_lang( 'Librarians' ); ?>
                </th>
    <?php if ( $category == 'user' || $this->is_platform_admin ) : ?>
                <th>
                    <?php echo get_lang( 'Status' ); ?>
                </th>
                <th>
                    <?php echo get_lang( 'Actions' ); ?>
                </th>
    <?php endif; ?>
            </tr>
        </thead>
        <tbody>
    <?php if ( ! empty( $resourceList ) ) : ?>
        <?php foreach ( $resourceList as $libraryId => $library ) : ?>
            <tr>
                <td>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowCatalogue&libraryId='. $libraryId ) );?>">
                        <?php echo $library[ 'title' ]; ?>
                    </a>
                </td>
                <td>
            <?php if ( ! empty( $library[ 'librarianList' ] ) ) : ?>
                <?php echo implode( ', ' , $library[ 'librarianList' ] ); ?>
                <?php if ( $this->is_platform_admin ) : ?>
                <a title="<?php echo get_lang( 'Manage librarians' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarian&libraryId='. $libraryId ) );?>">
                    <img src="<?php echo get_icon_url( 'librarian' ); ?>" alt="<?php echo get_lang( 'Manage librarians' ); ?>"/>
                </a>
                <?php endif; ?>
            <?php else : ?>
                    -
            <?php endif; ?>
                </td>
            <?php if ( $category == 'user' || $this->is_platform_admin ) : ?>
                <td align="center">
                    <?php echo get_lang( ucwords( $library[ 'status' ] ) ); ?>
                </td>
                <td align="center">
                <?php if ( $this->courseId ) : ?>
                    <?php if ( isset( $this->courseLibraryList[ $libraryId ] ) ) : ?>
                    <img src="<?php echo get_icon_url( 'add_disabled' ); ?>" alt="<?php echo get_lang( 'Add a library' ); ?>"/>
                    <?php else : ?>
                    <a title="<?php echo get_lang( 'Add a library' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddLibrary&libraryId='. $libraryId ) );?>">
                        <img src="<?php echo get_icon_url( 'add' ); ?>" alt="<?php echo get_lang( 'Add a library' ); ?>"/>
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
                    <a title="<?php echo get_lang( 'Delete this library' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteLibrary&libraryId='. $libraryId ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                    <a title="<?php echo get_lang( 'Edit' ); ?>" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLibrary&libraryId='. $libraryId ) );?>">
                        <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit' ); ?>"/>
                    </a>
                </td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
            <tr>
                <td class="empty" colspan="4"><?php echo get_lang( 'No libraries' ); ?></td>
            </tr>
    <?php endif; ?>
        </tbody>
    </table>
</fieldset>
<?php endforeach; ?>
</div>
