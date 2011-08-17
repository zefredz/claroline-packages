<?php // $Id$
/**
 * Examination report
 *
 * @version     UCREPORT 2.1.0 $Revision$ - Claroline 1.9
 * @copyright   2001-2009 Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     UCEXAM/UCREPORT
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<span>
    <a class="claroCmd" href="<?php echo 'report.php'; ?>">
        <img src="<?php echo get_icon_url( 'go_left' ); ?>" alt="back" />
        <?php echo get_lang( 'Back to the report list' ); ?>
    </a>
</span>
<?php if ( $this->has_result ) : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqResult') ); ?>">
        <img src="<?php echo get_icon_url( 'icon' ); ?>" alt="<?php echo get_lang( 'result' ); ?>"/>
        <?php echo get_lang( 'See my examination result details' ); ?>
    </a>
<?php endif; ?>
<?php if ( claro_is_allowed_to_edit() ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqCreate') ); ?>">
        <img src="<?php echo get_icon_url( 'new_exam' ); ?>" alt="<?php echo get_lang( 'create a new session' ); ?>"/>
        <?php echo get_lang( 'Create a new session' ); ?>
    </a>
</span>
<?php endif; ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Session' ); ?>
            </th>
            <?php if ( claro_is_allowed_to_edit() ) : ?>
            <th>
                <?php echo get_lang( 'Modify' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Delete' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Visibility' ); ?>
            </th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->examinationList->numRows() ) : ?>
    <?php foreach ( $this->examinationList as $session ) : ?>
        <?php if ( $session['visibility'] == AssetList::VISIBLE || claro_is_allowed_to_edit() == true ) : ?>
        <tr>
            <td>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqShow&sessionId='. $session['id'] ) );?>">
                    <?php echo $session[ 'title' ]; ?>
                </a>
            </td>
                <?php if ( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEdit&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Modify'); ?>"/>
                </a>
            </td>
            <td align="center">
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDelete&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete'); ?>"/>
                </a>
            </td>
            <td align="center">
                    <?php if ( $session['visibility'] == AssetList::VISIBLE ) : ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&sessionId='. $session['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&sessionId='. $session['id'] ) );?>">
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
            <td style="color: #888888; text-align: center; font-style: italic;"colspan="<?php echo claro_is_allowed_to_edit() ? 6 : 2; ?>"><?php echo get_lang( 'No session for this course yet' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>