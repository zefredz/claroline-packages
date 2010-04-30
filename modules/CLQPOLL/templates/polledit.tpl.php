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
<form   method="post"
        action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>" >
<?php if ( $this->poll ) : ?>
    <input  type="hidden"
            name="pollId"
            value="<?php echo $this->poll->getId(); ?>" />
<?php endif; ?>
    <fieldset id="pollDescription">
        <legend><?php echo get_lang( 'Poll properties' ); ?></legend>
        <dl>
            <dt><label for="title" ><?php echo get_lang( 'Title' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="title"
                        type="text"
                        name="title"
                        value="<?php echo $this->poll->getTitle(); ?>"
                        size="40" />
            </dd>
        </dl>
        <dl>
            <dt><label for="question" ><?php echo get_lang( 'Question' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="question"
                        type="text"
                        name="question"
                        value="<?php echo $this->poll->getQuestion(); ?>"
                        size="60" />
            </dd>
        </dl>
    </fieldset>
    
    <fieldset id="choices">
        <legend><?php echo get_lang( 'Poll choices' ); ?></legend>
        <ul id="choiceList">
        <?php if ( $this->poll->getChoiceList() ) : ?>
            <?php foreach( $this->poll->getChoiceList() as $choiceId => $label ) : ?>
            <li>
                <input id="choice<?php echo $choiceId; ?>"
                       type="text"
                       name="mod[<?php echo $choiceId; ?>]"
                       value="<?php echo $label; ?>"
                       size="40" />
                <a id="del<?php echo $choiceId; ?>" class="delChoice claroCmd" href="#choice<?php echo $choiceId; ?>">
                    <?php echo get_lang( 'Delete' ); ?>
                </a>
            </li>
            <?php endforeach; ?>
        <?php else : ?>
            <li>
                <span class="disabled"><?php echo get_lang( 'No choices' ); ?></span>
            </li>
        <?php endif; ?>
        </ul>
        <a id="addChoice" href="#claroBody">
            <img src="<?php echo get_icon_url( 'quiz_new' ); ?>" alt="<?php echo get_lang( 'create a new choice' ); ?>"/>
            <span class="claroCmd"><?php echo get_lang( 'Add a new choice' ); ?></span>
        </a>
    </fieldset>
    
    <fieldset>
        <legend><?php echo get_lang( 'Access' ); ?></legend>
        <dl>
            <dt><label><?php echo get_lang( 'Status' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  type="radio"
                        name="status"
                        value="open"
                        <?php if ( $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'unlock' ); ?>"
                        alt="<?php echo get_lang( 'Open'); ?>"/>
                <?php echo get_lang( 'The votes are open' ); ?><br />
                <input  type="radio"
                        name="status"
                        value="closed"
                        <?php if ( ! $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'locked' ); ?>"
                        alt="<?php echo get_lang( 'Closed'); ?>"/>
                <?php echo get_lang( 'The votes are closed' ); ?>
            </dd>
        </dl>
        <dl>
            <dt><label><?php echo get_lang( 'Visibility' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  type="radio"
                        name="visibility"
                        value="visible"
                       <?php if ( $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                <img src="<?php echo get_icon_url( 'visible' ); ?>" alt="<?php echo get_lang( 'Visible'); ?>"/>
                <input  type="radio"
                        name="visibility"
                        value="invisible"
                                    <?php if ( ! $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                <img src="<?php echo get_icon_url( 'invisible' ); ?>" alt="<?php echo get_lang( 'Invisible'); ?>"/>
            </dd>
        </dl>
    </fieldset>
    
    <fieldset>
        <legend><?php echo get_lang( 'Settings' ); ?></legend>
        <?php foreach( $this->poll->getOptionList() as $item => $value ) : ?>
        <dl>
            <dt><label><?php echo get_lang( $item ); ?></label>&nbsp;:</dt>
            <dd>
            <?php $optionValueList = $this->poll->getOptionValueList( $item ); ?>
            <?php if ( $this->change_allowed[ $item ] ) : ?>
                <?php foreach( $this->poll->getOptionValueList( $item ) as $option ) : ?>
                <input  type="radio"
                        name="<?php echo $item; ?>"
                        value="<?php echo $option; ?>"
                        <?php if ( $this->poll->getOption( $item ) == $option ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( $option ); ?>
                <br />
                <?php endforeach; ?>
            <?php else : ?>
                <span class="disabled"><?php echo get_lang( $this->poll->getOption( $item ) ) . get_lang( '#locked' ); ?></span>
            <?php endif; ?>
            </dd>
        </dl>
        <?php endforeach; ?>
    </fieldset>
    
    <dl>
        <dt>
            <input id="submitPollProperties" type="submit" name="submitPoll" value="<?php echo get_lang( 'OK' ); ?>" />
            <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
        </dt>
    </dl>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>