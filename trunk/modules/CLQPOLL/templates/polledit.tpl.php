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

<?php $cmd = $this->poll->getId() ? 'exEditPoll' : 'exCreatePoll'; ?>
<form   method="post"
        action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>" >
<?php if ( $this->poll ) : ?>
    <input  type="hidden"
            name="pollId"
            value="<?php echo $this->poll->getId(); ?>" />
<?php endif; ?>
    <fieldset id="pollDescription">
        <legend><?php echo get_lang( 'Poll properties' ); ?></legend>
        <dl>
            <dt><label for="title" ><?php echo get_lang( 'Heading' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="title"
                        type="text"
                        name="title"
                        value="<?php echo $this->poll->getTitle(); ?>"
                        size="40" />
            </dd>
        </dl>
        <dl>
            <dt><label for="question" ><?php echo get_lang( 'Description' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  id="question"
                        type="text"
                        name="question"
                        value="<?php echo $this->poll->getQuestion(); ?>"
                        size="80" />
            </dd>
        </dl>
    </fieldset>
    
    <fieldset id="choices">
        <legend><?php echo get_lang( 'Poll choices' ); ?></legend>
        <div id="choiceListBox">
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
                <!-- <span class="disabled"><?php echo get_lang( 'No choices' ); ?></span> -->
                <input id="choicex1" type="text" name="add[1]" value="" size="40" />
                <span class="required">*</span>
            </li>
        <?php endif; ?>
        </ul>
        <a id="addChoice" href="#claroBody">
            <img src="<?php echo get_icon_url( 'quiz_new' ); ?>" alt="<?php echo get_lang( 'create a new choice' ); ?>"/>
            <span class="claroCmd"><?php echo get_lang( 'Add a new choice' ); ?></span>
        </a>
        </div>
        <div id="choiceListHelp">
            <blockquote><?php echo get_lang( '#choiceListhelp' ); ?></blockquote>
        </div>
    </fieldset>
    
    <fieldset>
        <legend><?php echo get_lang( 'Access' ); ?></legend>
        <dl>
            <dt><label><?php echo get_lang( 'The votes are open' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  type="radio"
                        name="status"
                        value="open"
                        <?php if ( $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'unlock' ); ?>"
                        alt="<?php echo get_lang( 'Open'); ?>"/>
                <?php echo get_lang( 'Yes' ); ?><br />
                <input  type="radio"
                        name="status"
                        value="closed"
                        <?php if ( ! $this->poll->isOpen() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'locked' ); ?>"
                        alt="<?php echo get_lang( 'Closed'); ?>"/>
                <?php echo get_lang( 'No' ); ?>
            </dd>
        </dl>
        <dl>
            <dt><label><?php echo get_lang( 'Visibility' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  type="radio"
                        name="visibility"
                        value="visible"
                       <?php if ( $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'visible' ); ?>"
                        alt="<?php echo get_lang( 'Visible'); ?>"/>
                <?php echo get_lang( 'Visible'); ?><br />
                <input  type="radio"
                        name="visibility"
                        value="invisible"
                        <?php if ( ! $this->poll->isVisible() ) : ?>checked="checked"<?php endif; ?>/>
                <img    src="<?php echo get_icon_url( 'invisible' ); ?>"
                        alt="<?php echo get_lang( 'Invisible'); ?>"/>
                <?php echo get_lang( 'Invisible'); ?>
            </dd>
        </dl>
    </fieldset>
    
    <fieldset>
        <legend><?php echo get_lang( 'Settings' ); ?></legend>
        <?php foreach( $this->poll->getOptionList() as $option => $value ) : ?>
        <dl>
            <dt><label><?php echo get_lang( $option ); ?></label>&nbsp;:</dt>
            <dd>
            <?php if ( $this->change_allowed[ $option ] ) : ?>
                <?php foreach( $this->poll->getOptionValueList( $option ) as $optionValue ) : ?>
                <input  type="radio"
                        name="<?php echo $option; ?>"
                        value="<?php echo $optionValue; ?>"
                        <?php if ( $value == $optionValue ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( $optionValue ); ?>
                <br />
                <?php endforeach; ?>
            <?php else : ?>
                <span class="disabled"><?php echo get_lang( $this->poll->getOption( $option ) ) . get_lang( '#locked' ); ?></span>
            <?php endif; ?>
            </dd>
        </dl>
        <?php endforeach; ?>
    </fieldset>
    
    <dl>
        <dt>
            <input id="submitPollProperties" type="submit" name="submitPoll" value="<?php echo get_lang( 'OK' ); ?>" />
            <?php echo claro_html_button( claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
        </dt>
    </dl>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>