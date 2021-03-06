<?php // $Id$

/**
 * Claroline Poll Tool
 *
 * @version     CLQPOLL 1.2.2 $Revision$ - Claroline 1.11.5
 * @copyright   2001-2012 Universite Catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @package     CLQPOLL
 * @author      Frederic Fervaille <frederic.fervaille@uclouvain.be>
 */ ?>

<div id="poll">
    <!-- BEGIN Displays poll title and question -->
    <h2>
        <?php echo $this->poll->getTitle(); ?>
    </h2>
    <h3>
        <?php echo $this->poll->getQuestion(); ?>
    </h3>
    <!-- END Displays poll title and question -->
<?php if ( count( $this->poll->getChoiceList() ) != 0 ) : ?>
    <?php if ( $this->poll->getAllVoteList() ) : ?>
    <h4><?php echo get_lang( 'Number of votes' ) . ' : ' . count( $this->poll->getAllVoteList() ); ?></h4>
    <?php else : ?>
    <h4><?php echo get_lang( 'No vote for this poll' ); ?></h4>
    <?php endif; ?>
    
    <?php if ( $this->userRights[ 'vote' ] ) : ?>
    <form action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exSubmitVote' ) ); ?>" method="post">
    <?php endif; ?>
    
    <table>
        <thead>
    <!-- BEGIN Display the voting form -->
    <?php if ( $this->userRights[ 'vote' ] ) : ?>
                <input id="pollId" type="hidden" value="<?php echo $this->poll->getId(); ?>" name="pollId" />
                <tr class="userline">
                    <td class="name">
                        <input class="submit" type="submit" value="<?php echo get_lang( $this->userVote->voteExists( true ) ? 'Modify your vote' : 'Vote' ); ?>"/>
                    </td>
        <?php if ( $this->poll->getOption( '_type' ) == '_multi' ) : ?>
        <!-- MULTIPLE VOTE -->
            <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                    <td>
                        <input id="option<?php echo $choiceId; ?>" type="checkbox" name="<?php echo 'choice' . $choiceId; ?>" />
                    </td>
            <?php endforeach; ?>
        <?php else : ?>
        <!-- SINGLE VOTE -->
            <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                    <td>
                        <input id="choice<?php echo $choiceId; ?>" type="radio" name="choiceId" value="<?php echo $choiceId; ?>" />
                    </td>
            <?php endforeach; ?>
        <?php endif; ?>
                </tr>

    <?php endif; ?>
    <!-- END Display the voting form -->
    <!-- BEGIN Displays the current user vote -->
    <?php if ( claro_get_current_user_id() && $this->userVote->load()->voteExists() ) : ?>
            <tr id="current" >
                <th><?php echo get_lang( 'Your vote' ); ?></th>
        <?php foreach ( $this->userVote->getVote( true ) as $vote ) : ?>
            <?php if ( $vote == UserVote::CHECKED ) : ?>
                <td class="checked"><?php echo get_lang( 'YES' ); ?></td>
            <?php elseif ( $vote == UserVote::NOTCHECKED ) : ?>
                <td class="notchecked"><?php echo get_lang( 'NO' ); ?></td>
            <?php else : ?>
                <td class="disabled"><?php echo get_lang( 'No vote' ); ?></td>
            <?php endif; ?>
        <?php endforeach; ?>
            </tr>
    <?php endif; ?>
    <!-- END Displays the current user vote -->
    <!-- BEGIN Displays the option list -->
            <tr>
                <th class="invisible">
                <?php echo get_lang( 'Choices' ); ?>
                </th>
    <?php foreach ( $this->poll->getChoiceList() as $label ) : ?>
                <th class="option"><?php echo claro_htmlspecialchars( $label ); ?></th>
    <?php endforeach; ?>
            </tr>
        </thead>
    <!-- END Displays the option list -->
        <tbody>
            <!-- BEGIN Displays the statistics -->
    <?php if ( $this->userRights[ 'see_stats' ] ) : ?>
            <tr class="stats">
                <th class="name">Statistiques</th>
        <?php foreach ( $this->pollStat->getResult() as $optionVoteCount ) : ?>
                <td><?php echo $optionVoteCount; ?></td>
        <?php endforeach; ?>
            </tr>
    <?php endif; ?>
            <!-- END Displays the statistics -->
            <!-- BEGIN Displays the poll votes -->
    <?php if ( $this->poll->getAllVoteList() ) : ?>
        <?php if ( $this->userRights[ 'see_names' ] ) : ?>
             <?php foreach ( $this->voteList->getPage( $this->pageNb ) as $vote ) : ?>
            <tr>
                <th class="name">
            <?php echo $vote[ 'lastName' ] . ' ' . $vote[ 'firstName' ]; ?>
                <?php if ( claro_is_platform_admin() ) : ?>
                        <a href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteVote&pollId='. $this->poll->getId() . '&userId=' . $vote[ 'user_id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete user\'s vote' ); ?>"/>
                        </a>
                <?php endif; ?>
                </th>
                <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                    <?php if ( ! isset( $vote[ $choiceId ] ) ) : ?>
                <td class="disabled"><?php echo get_lang( 'No vote' ); ?></td>
                    <?php elseif ( $vote[ $choiceId ]  == UserVote::CHECKED ) : ?>
                <td class="checked"><?php echo get_lang( 'YES' ); ?></td>
                    <?php elseif ( $vote[ $choiceId ] == UserVote::NOTCHECKED ) : ?>
                <td class="notchecked"><?php echo get_lang( 'NO' ); ?></td>
                    <?php else: ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else : ?>
            <tr>
                <td class="novote" colspan="<?php echo count( $this->poll->getChoiceList() ) + 1; ?>"><?php echo get_lang( 'No vote' ); ?></td>
            </tr>
    <?php endif; ?>
            <!-- END Displays the poll votes -->
        </tbody>
    </table>
    
    <?php if ( $this->userRights[ 'vote' ] ) : ?>
    </form>
    <?php endif; ?>
    
    <?php if ( $this->voteList->getPageCount() > 1 && $this->userRights[ 'see_names'] ) : ?>
    <div id="pagerNav" class="pager">
        <?php if ( $this->pageNb > 0 ) : ?>
        <a class="pagerButton" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=0&pollId=' . $this->poll->getId() ) );?>">
            <span class="enabled">&lt;&lt;</span>
        </a>
        <a class="pagerButton" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->pageNb - 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
            <span class="enabled">&lt;</span>
        </a>
        <?php else : ?>
            <span class="pagerButton">&lt;&lt;</span>
            <span class="pagerButton">&lt;</span>
        <?php endif; ?>
        <?php for ( $i = $this->pageNb - 3; $i <= $this->pageNb + 3; $i++ ) : ?>
            <?php if ( $i >= 0 && $i < $this->voteList->getPageCount() ) : ?>
                <?php if ( $this->pageNb != $i ) : ?>
        <a class="pagerButton" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . $i . '&pollId=' . $this->poll->getId() ) );?>">
            <span class="enabled"><?php echo $i + 1; ?></span>
        </a>
            <?php else : ?>
        <span class="pagerButton"><?php echo $i + 1; ?></span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ( $this->pageNb < $this->voteList->getPageCount() - 1 ) : ?>
        <a class="pagerButton" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->pageNb + 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
            <span class="enabled">&gt;</span>
        </a>
        <a  class="pagerButton" href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->voteList->getPageCount() - 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
            <span class="enabled">&gt;&gt;</span>
        </a>
        <?php else : ?>
            <span class="pagerButton">&gt;</span>
            <span class="pagerButton">&gt;&gt;</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
<?php else: ?>
<h3><?php echo get_lang( 'No option for this poll yet' ); ?></h3>
<?php endif; ?>
</div>