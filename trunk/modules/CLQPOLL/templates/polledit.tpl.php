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
            <tr valign="top">
                <td align="right">
                    <label>
                        <?php echo get_lang( 'Status' ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
                <td>
                    <input type="radio" name="status"
                                        value="open"
                                        <?php if ( $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                    <img src="<?php echo get_icon_url( 'unlock' ); ?>" alt="<?php echo get_lang( 'Open'); ?>"/>
                    <?php echo get_lang( 'The votes are open' ); ?><br />
                    <input type="radio" name="status"
                                        value="closed"
                                        <?php if ( ! $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                    <img src="<?php echo get_icon_url( 'locked' ); ?>" alt="<?php echo get_lang( 'Closed'); ?>"/>
                    <?php echo get_lang( 'The votes are closed' ); ?>
                </td>
            </tr>
            <tr valign="top">
                <td align="right">
                    <label>
                        <?php echo get_lang( 'Visibility' ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
                <td>
                    <input type="radio" name="visibility"
                                        value="visible"
                                        <?php if ( $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                    <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                    <input type="radio" name="visibility"
                                        value="invisible"
                                        <?php if ( ! $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                    <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
                </td>
            </tr>
        </table>
    </fieldset>
    
    <fieldset id="pollOptions">
        <legend><?php echo get_lang( 'Options' ); ?></legend>
        <table>
        <?php foreach( $this->poll->getOptionList() as $item => $value ) : ?>
            <tr valign="top">
                <td align="right">
                    <label>
                        <?php echo get_lang( $item ); ?>
                        &nbsp;:&nbsp;
                    </label>
                </td>
            <?php if ( $this->change_allowed[ $item ] ) : ?>
                <td>
                        <?php $optionValueList = $this->poll->getOptionValueList( $item ); ?>
                        <?php if ( ! is_int( $optionValueList[ 0 ] ) ) : ?>
                        <?php foreach( $this->poll->getOptionValueList( $item ) as $option ) : ?>
                        <input type="radio" name="<?php echo $item; ?>"
                                            value="<?php echo $option; ?>"
                                            <?php if ( $this->poll->getOption( $item ) == $option ) : ?>checked="checked"<?php endif; ?>/>
                        <?php echo get_lang( $option ); ?>
                        <br />
                        <?php endforeach; ?>
                        <?php else : ?>
                        <input type="text" name="<?php echo $item; ?>" value="<?php echo $this->poll->getOption( $item ); ?>" size="2"/>
                        <?php echo get_lang( 'Maximum amount of votes for each choice ( set to 0 for no limit )' ); ?>
                        <br />
                        <?php endif; ?>
                </td>
            <?php else : ?>
                <td>
                    <span class="disabled"><?php echo get_lang( $this->poll->getOption( $item ) ) . get_lang( '#locked' ); ?></span>
                </td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        
        </table>
    </fieldset>
    
    <fieldset id="pollChoices">
        <legend><?php echo get_lang( 'Poll choices' ); ?></legend>
        <ul id="choiceList">
        <?php if ( $this->poll->getChoiceList() ) : ?>
            <?php foreach( $this->poll->getChoiceList() as $choiceId => $label ) : ?>
            <li>
                <input id="choice<?php echo $choiceId; ?>" type="text" name="mod[<?php echo $choiceId; ?>]" value="<?php echo $label; ?>" size="40" />
                <?php if ( ! $this->poll->getAllVoteList() ) : ?>
                <a id="del<?php echo $choiceId; ?>" class="delChoice claroCmd" href="#choice<?php echo $choiceId; ?>">
                    <?php echo get_lang( 'Delete' ); ?>
                </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li></li>
        <?php endif; ?>
        </ul>
        <?php if ( ! $this->poll->getAllVoteList() ) : ?>
        <a id="addChoice" href="#addChoice">
            <img src="<?php echo get_icon_url( 'quiz_new' ); ?>" alt="<?php echo get_lang( 'create a new choice' ); ?>"/>
            <span class="claroCmd"><?php echo get_lang( 'Add a new choice' ); ?></span>
        </a>
        <?php endif; ?>
    </fieldset>
    
    <input id="submitPollProperties" type="submit" name="submitPoll" value="<?php echo get_lang( 'OK' ); ?>" />
    <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
</form>
