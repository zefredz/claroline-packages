<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.2 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2011 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<table class="claroTable emphaseLine" style=" width: 100%;">
    <thead>
        <tr class="headerX">
            <th>
                <?php echo get_lang( 'Poll'); ?>
            </th>
            <?php if ( claro_is_allowed_to_edit() ) : ?>
            <th>
                <?php echo get_lang( 'Modify' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Purge' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Delete' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Status' ); ?>
            </th>
            <th>
                <?php echo get_lang( 'Visibility' ); ?>
            </th>
            <?php else : ?>
            <th>
                <?php echo get_lang( 'Open/close' ); ?>
            </th>
            <?php endif; ?>
            <th>
                <?php echo get_lang( 'Statistics' ); ?>
            </th>
        </tr>
    </thead>
    <tbody>
<?php if ( $this->pollList->numRows() ) : ?>
    <?php foreach ( $this->pollList as $poll ) : ?>
        <?php if ( $poll['visibility'] == Poll::VISIBLE || claro_is_allowed_to_edit() == true ) : ?>
        <tr>
            <td>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pollId='. $poll['id'] ) );?>">
                    <?php echo $poll[ 'title' ]; ?>
                </a>
                <br />
                <small><?php echo $poll[ 'question' ]; ?></small>
            </td>
                <?php if ( claro_is_allowed_to_edit() ) : ?>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditPoll&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Modify' ); ?>"/>
                </a>
            </td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqPurgePoll&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'sweep' ); ?>" alt="<?php echo get_lang( 'Purge' ); ?>"/>
                </a>
            </td>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeletePoll&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete' ); ?>"/>
                </a>
            </td>
            <td align="center">
                    <?php if ( $poll['status'] == Poll::OPEN_VOTE ) : ?>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exClose&tpl=polllist&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Open' ); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exOpen&tpl=polllist&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Closed'); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
            <td align="center">
                    <?php if ( $poll['visibility'] == Poll::VISIBLE ) : ?>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&tpl=polllist&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                </a>
                    <?php else: ?>
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&&tpl=polllist&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible' ); ?>"/>
                </a>
                    <?php endif; ?>
            </td>
                <?php else : ?>
            </td>
            <td align="center">
                    <?php if ( $poll['status'] == Poll::OPEN_VOTE ) : ?>
                <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Open'); ?>"/>
                    <?php else: ?>
                <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Closed'); ?>"/>
                    <?php endif; ?>
            </td>
                <?php endif; ?>
            <td align="center">
                <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewStats&pollId='. $poll['id'] ) );?>">
                    <img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="<?php echo get_lang( 'Statistics'); ?>"/>
                </a>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
        <tr>
            <td class="empty" colspan="<?php echo claro_is_allowed_to_edit() ? 7 : 3; ?>"><?php echo get_lang( 'No poll for this course yet' ); ?></td>
        </tr>
<?php endif; ?>
    </tbody>
</table>