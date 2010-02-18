<?php echo claro_html_tool_title( $this->pageTitle ); ?>

<?php if ( $this->poll->getId() ) : ?>
    <span>
        <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewPoll&pollId=' . $this->poll->getId() ) ); ?>">
            <img src="<?php echo get_icon_url( 'poll' ); ?>" alt="<?php echo get_lang( 'View poll'); ?>"/>
            <?php echo get_lang( 'View poll' ); ?>
        </a>
    </span>
    <?php if ( $this->poll->getAllVoteList() ) : ?>
    <span>
        <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqViewStats&pollId=' . $this->poll->getId() ) ); ?>">
            <img src="<?php echo get_icon_url( 'statistics' ); ?>" alt="<?php echo get_lang( 'View stats'); ?>"/>
            <?php echo get_lang( 'View stats' ); ?>
        </a>
    </span>
    <span>
        <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqPurgePoll&pollId=' . $this->poll->getId() ) ); ?>">
            <img src="<?php echo get_icon_url( 'sweep' ); ?>" alt="<?php echo get_lang( 'Purge this poll'); ?>"/>
            <?php echo get_lang( 'Purge this poll' ); ?>
        </a>
    </span>
    <?php endif; ?>
    <span>
        <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeletePoll&pollId=' . $this->poll->getId() ) ); ?>">
            <img src="<?php echo get_icon_url( 'delete' ); ?>" alt="<?php echo get_lang( 'Delete this poll'); ?>"/>
        <?php echo get_lang( 'Delete this poll' ); ?>
        </a>
    </span>
<?php endif; ?>

<?php echo $this->dialogBox->render(); ?>

<?php $cmd = $this->poll->getId() ? 'exEditPoll' : 'exCreatePoll'; ?>
<form method="post" action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>">
    <fieldset id="pollProperties">
        <legend><?php echo get_lang( 'Poll properties' ); ?></legend>
    <?php if ( $this->poll ) : ?>
        <input type="hidden" name="pollId" value="<?php echo $this->poll->getId(); ?>" />
    <?php endif; ?>
        <table>
            <tr valign="top">
                <td align="right">
                    <label for="title">
                        <?php echo get_lang( 'Title' ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
                <td>
                    <?php $title = $this->poll->getId() ? $this->poll->getTitle() : get_lang( 'Put the title here' ); ?>
                    <input id="title" type="text" name="title" value="<?php echo $title; ?>" size="40" />
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label for="question">
                        <?php echo get_lang( 'Question' ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
                <td>
                    <?php $question = $this->poll->getId() ? $this->poll->getQuestion() : get_lang( 'Put your question here' ); ?>
                    <input id="question" type="text" name="question" value="<?php echo $question; ?>" size="60" />
                </td>
            </tr>
        </table>
        <?php if ( $this->poll->getId() ) : ?>
        <div id="pollStatus">
            <?php if ( $this->poll->getStatus() == Poll::OPEN_VOTE ) : ?>
            <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exClose&tpl=polledit&pollId=' . $this->poll->getId() ) );?>">
                <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Open'); ?>"/>
                <?php echo get_lang( 'Close poll' ); ?>
            </a>
            <?php else: ?>
            <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exOpen&tpl=polledit&pollId=' . $this->poll->getId() ) );?>">
                <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Closed'); ?>"/>
                <?php echo get_lang( 'Open poll' ); ?>
            </a>
            <?php endif; ?>
        </div>
        <div id="changeVisibility">
            <?php if ( $this->poll->isVisible() ) : ?>
            <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkInvisible&tpl=polledit&pollId=' . $this->poll->getId() ) );?>">
                <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                <?php echo get_lang( 'Make invisible' ); ?>
            </a>
            <?php else : ?>
            <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=exMkVisible&tpl=polledit&pollId=' . $this->poll->getId() ) );?>">
                <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                <?php echo get_lang( 'Make visible' ); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </fieldset>
    
    <fieldset id="pollOptions">
        <legend><?php echo get_lang( 'Options' ); ?></legend>
        <table>
        <?php foreach( $this->poll->getOptionList() as $item => $value ) : ?>
        <?php if ( $this->change_allowed[ $item ] ) : ?>
            <tr valign="top">
                <td align="right">
                    <label>
                        <?php echo get_lang( $item ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
                <td>
                        <?php foreach( $this->poll->getOptionValueList( $item ) as $option ) : ?>
                        <input type="radio" name="<?php echo $item; ?>"
                                            value="<?php echo $option; ?>"
                                            <?php if ( $this->poll->getOption( $item ) == $option ) : ?>checked="checked"<?php endif; ?>/>
                        <?php echo get_lang( $option ); ?>
                        <br />
                        <?php endforeach; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php endforeach; ?>
        
        </table>
    </fieldset>
    
    <fieldset id="pollChoices">
        <legend><?php echo get_lang( 'Poll choices' ); ?></legend>
        <?php if ( $this->poll->getChoiceList() ) : ?>
        <ul id="choiceList">
            <?php foreach( $this->poll->getChoiceList() as $choiceId => $label ) : ?>
            <li>
                <input type="text" name="choice<?php echo $choiceId; ?>" value="<?php echo $label; ?>" size="40" />
                <a class="claroCmd" href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'].'?cmd=rqDeleteChoice&pollId=' . $this->poll->getId() .'&choiceId=' . $choiceId ) ); ?>">
                    <?php echo get_lang( 'Delete' ); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if ( ! $this->poll->getAllVoteList() ) : ?>
        <div id="newChoice"></div>
        <a id="addChoice" href="#newChoice">
            <img src="<?php echo get_icon_url( 'quiz_new' ); ?>" alt="<?php echo get_lang( 'create a new choice' ); ?>"/>
            <span class="claroCmd"><?php echo get_lang( 'Add a new choice' ); ?></span>
        </a>
        <?php endif; ?>
    </fieldset>
    
    <input id="submitPollProperties" type="submit" name="submitPoll" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
