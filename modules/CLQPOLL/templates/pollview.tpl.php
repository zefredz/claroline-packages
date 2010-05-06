<?php echo claro_html_tool_title( get_lang( 'Poll' ) ); ?>

<!-- BEGIN ToolBar -->
<?php if ( $this->userRights[ 'edit' ]) : ?>
<span>
    <?php if ( $this->poll->getStatus() == Poll::OPEN_VOTE ) : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exClose&tpl=pollview&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Open'); ?>"/>
        <?php echo get_lang( 'Close poll' ); ?>
    </a>
    <?php else: ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exOpen&tpl=pollview&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Closed'); ?>"/>
        <?php echo get_lang( 'Open poll' ); ?>
    </a>
    <?php endif; ?>
</span>

<span>
    <?php if ( $this->poll->isVisible() ) : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&tpl=pollview&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
        <?php echo get_lang( 'Make invisible' ); ?>
    </a>
    <?php else : ?>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&tpl=pollview&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
        <?php echo get_lang( 'Make visible' ); ?>
    </a>
    <?php endif; ?>
</span>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqEditPoll&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'edit' ); ?>" alt="<?php echo get_lang( 'Edit poll'); ?>"/>
        <?php echo get_lang( 'Edit poll properties' ); ?>
    </a>
</span>

<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqPurgePoll&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'sweep' ); ?>" alt="<?php echo get_lang( 'Purge poll'); ?>"/>
        <?php echo get_lang( 'Purge this poll' ); ?>
    </a>
</span>
<?php endif; ?>

<?php if ( $this->userRights[ 'see_stats' ] ) : ?>
<span>
    <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewStats&pollId='. $this->poll->getId() ) );?>">
        <img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="<?php echo get_lang( 'View stats'); ?>"/>
        <?php echo get_lang( 'Statistics' ); ?>
    </a>
</span>
<?php endif; ?>
<!-- END Toolbar -->

<!-- BEGIN Dialog box -->
<?php echo $this->dialogBox->render(); ?>
<!-- END Dialog box -->

<!-- BEGIN Container for the poll datas display -->
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
    
    <!-- BEGIN Display the voting form -->
    <?php if ( $this->userRights[ 'vote' ] ) : ?>
        <?php if ( $this->poll->getOption( '_type' ) == '_multi' ) : ?>
        <!-- MULTIPLE VOTE -->
    <form action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exSubmitVote' ) ); ?>" method="post">
        <input id="pollId" type="hidden" value="<?php echo $this->poll->getId(); ?>" name="pollId" />
        <table>
            <tr class="userline">
                <th class="name">
                <input class="submit" type="submit" value="<?php echo get_lang( 'Vote' ); ?>"/>
                </th>
                    <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                <td>
                    <input id="option<?php echo $choiceId; ?>" type="checkbox" name="<?php echo 'choice' . $choiceId; ?>" />
                </td>
                    <?php endforeach; ?>
            </tr>
        </table>
    </form>
        <?php else : ?>
        <!-- SINGLE VOTE -->
    <form action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exSubmitVote' ) ); ?>" method="post">
        <input id="pollId" type="hidden" value="<?php echo $this->poll->getId(); ?>" name="pollId" />
        <table>
            <tr class="userline">
                <th class="name">
                    <input class="submit" type="submit" value="<?php echo get_lang( 'Vote' ); ?>"/>
                </th>
                    <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                <td>
                    <input id="choice<?php echo $choiceId; ?>" type="radio" name="choiceId" value="<?php echo $choiceId; ?>" />
                </td>
                    <?php endforeach; ?>
            </tr>
        </table>
    </form>
        <?php endif; ?>
    <?php endif; ?>
    <!-- END Display the voting form -->
    <table>
        <!-- BEGIN Displays the option list -->
        <thead>
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
            <tr>
                <th class="invisible">
                <?php if ( $this->poll->getAllVoteList() ) : ?>
                    <span class="vote"><?php echo get_lang( 'Votes' ); ?></span>
                <?php endif; ?>
                <?php echo get_lang( 'Choices' ); ?>
                </th>
            <?php foreach ( $this->poll->getChoiceList() as $label ) : ?>
                <th class="option"><?php echo htmlspecialchars( $label ); ?></th>
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
                        <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteVote&pollId='. $this->poll->getId() . '&userId=' . $vote[ 'user_id' ] ) );?>">
                            <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete user\'s vote' ); ?>"/>
                        </a>
                    <?php endif; ?>
                </th>
                        <?php foreach ( array_keys( $this->poll->getChoiceList() ) as $choiceId ) : ?>
                            <?php if ( $vote[ $choiceId ]  == UserVote::CHECKED ) : ?>
                <td class="checked"><?php echo get_lang( 'YES' ); ?></td>
                            <?php elseif ( $vote[ $choiceId ] == UserVote::NOTCHECKED ) : ?>
                <td class="notchecked"><?php echo get_lang( 'NO' ); ?></td>
                            <?php else: ?>
                <td class="disabled"><?php echo get_lang( 'No vote' ); ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
            </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else : ?>
            <tr>
                <td class="novote" colspan="<?php echo count( $this->poll->getChoiceList() ) + 1; ?>">No vote</td>
            </tr>
            <?php endif; ?>
            <!-- END Displays the poll votes -->
        </tbody>
    </table>
    <?php if ( $this->voteList->getPageCount() > 1 && $this->userRights[ 'see_names'] ) : ?>
    <div id="pagerNav" class="claroPager">
        <span class="pagerBefore">
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=0&pollId=' . $this->poll->getId() ) );?>">
                <img alt="go to first page" src="<?php echo get_icon_url( 'pager_first.png' ); ?>" />
            </a>
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->pageNb - 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
                <img alt="go to first page" src="<?php echo get_icon_url( 'pager_previous.png' ); ?>" />
            </a>
        </span>
        <span class="pagerPages">
            <?php for ( $i = $this->pageNb - 3; $i <= $this->pageNb + 3; $i++ ) : ?>
                <?php if ( $i >= 0 && $i < $this->voteList->getPageCount() ) : ?>
                    <?php if ( $this->pageNb != $i ) : ?>
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . $i . '&pollId=' . $this->poll->getId() ) );?>">
                <?php echo $i + 1; ?>
            </a>
                    <?php else : ?>
            <b><?php echo $i + 1; ?></b>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endfor; ?>
        </span>
        <span class="pagerAfter">
            <a href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->pageNb + 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
                <img alt="go to first page" src="<?php echo get_icon_url( 'pager_next.png' ); ?>" />
            </a>
            <a  href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pageNb=' . ( $this->voteList->getPageCount() - 1 ) . '&pollId=' . $this->poll->getId() ) );?>">
                <img alt="go to first page" src="<?php echo get_icon_url( 'pager_last.png' ); ?>" />
            </a>
        </span>
    </div>
    <?php endif; ?>
    
<!-- END Container for the poll datas display -->

<?php else: ?>
<h3><?php echo get_lang( 'No option for this poll yet' ); ?></h3>
<?php endif; ?>
</div>