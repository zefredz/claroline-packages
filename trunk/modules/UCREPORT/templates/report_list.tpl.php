<?php // $Id$
/**
 * Student Report for Claroline
 *
 * @version     UCREPORT 2.2.1 $Revision$ - Claroline 1.9
 * @copyright   2001-2010 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Report' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Publication date' ); ?>
            </th>
<?php if ( $this->is_allowed_to_edit ) : ?>
            <th>
                <?php echo get_lang( 'Delete' ); ?>
            </th>
    <?php if ( get_conf( 'UCREPORT_public_allowed' ) ) : ?>
            <th>
                <?php echo get_lang( 'Public / private' ); ?>
            </th>
    <?php endif; ?>
            <th>
                <?php echo get_lang( 'Visibility' ); ?>
            </th>
<?php endif; ?>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->reportList->numRows() ) : ?>
    <?php foreach ( $this->reportList as $report ) : ?>
        <?php if ( $report['visibility'] == AssetList::VISIBLE || $this->is_allowed_to_edit ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqView&id='. $report['id'] ) );?>">
                    <?php echo $report[ 'title' ]; ?>
                </a>
            </td>
            <td>
                    <?php echo $report[ 'publication_date' ]; ?>
            </td>
                <?php if ( $this->is_allowed_to_edit ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDelete&id='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete'); ?>"/>
                </a>
            </td>
                    <?php if ( get_conf( 'UCREPORT_public_allowed' ) ) : ?>
            <td align="center">
                        <?php if ( $report['confidentiality'] == AssetList::ACCESS_PRIVATE ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkPublic&id='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'user' ); ?>" alt="<?php echo get_lang( 'Private : click to open' ); ?>"/>
                </a>
                        <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkPrivate&id='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'group' ); ?>" alt="<?php echo get_lang( 'Public : click to close' ); ?>"/>
                </a>
                        <?php endif; ?>
            </td>
                    <?php endif; ?>
            <td align="center">
                    <?php if ( $report['visibility'] == AssetList::VISIBLE ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&id='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&id='. $report['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
                <?php endif; ?>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td class="empty" colspan="<?php echo $this->is_allowed_to_edit ? 6 : 2; ?>"><?php echo get_lang( 'No report available' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>