<?php // $Id$
/**
 * Online library for Claroline
 *
 * @version     CLLIBR 0.6.0 $Revision$ - Claroline 1.9
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
<form id="searchForm" method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqSearch') ); ?>">
    <input type="submit" value="<?php echo get_lang( 'Quick search' ); ?>" />
    <input type="text" name="searchString" value="" />
    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?option=multisearch') ); ?>">
        <img src="<?php echo get_icon_url( 'plus' ); ?>" alt="<?php echo get_lang( 'Advanced search' ); ?>" />
    </a>
</form>
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
                    <?php echo get_lang( 'Commands' ); ?>
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
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShowLibrarian&libraryId='. $libraryId ) );?>">
                    <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'Manage librarians' ); ?>"/>
                </a>
            <?php else : ?>
                    -
            <?php endif; ?>
                </td>
            <?php if ( $category == 'user' || $this->is_platform_admin ) : ?>
                <td align="center">
                    <?php echo get_lang( $library[ 'status' ] ); ?>
                <td align="center">
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteLibrary&libraryId='. $libraryId ) );?>">
                        <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                    </a>
                    <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditLibrary&libraryId='. $libraryId ) );?>">
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