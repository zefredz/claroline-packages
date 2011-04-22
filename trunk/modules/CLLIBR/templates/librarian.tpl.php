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

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqAddLibrarian&libraryId=' . $this->libraryId ) ); ?>">
        <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'New librarian' ); ?>" />
        <?php echo get_lang( 'Add a librarian' ); ?>
    </a>
</span>
<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th><?php echo get_lang( 'Name' ); ?></th>
            <th><?php echo get_lang( 'Unsubscribe' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $this->librarianList as $librarianId => $librarian ) : ?>
        <tr>
            <td>
            <?php echo $librarian; ?>
            </td>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqRemoveLibrarian&libraryId='. $this->libraryId . '&librarianId=' . $librarianId ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>