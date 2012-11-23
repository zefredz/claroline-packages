<div id="dateInfo"
     startdate="<?php echo date( get_lang( '_date' ) , strtotime( $this->session->getOpeningDate() ? $this->session->getOpeningDate() : 'now' ) ); ?>"
     enddate="<?php echo date( get_lang( '_date' ) , strtotime( $this->session->getClosingDate() ? $this->session->getClosingDate() : '+1 month' ) ); ?>"></div>

<?php $cmd = $this->session->getId() ? 'exModifySession' : 'exCreateSession'; ?>
<form   method="post"
        action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>" >
    <?php if( $this->session->getId() ) : ?>
    <input type="hidden"
           name="sessionId"
           value="<?php echo $this->session->getId(); ?>" />
    <?php endif; ?>
    <input type="hidden"
           name="data[isOpen]"
           value="<?php echo $this->session->isOpen(); ?>" />
    <input type="hidden"
           name="data[isVisible]"
           value="<?php echo $this->session->isVisible(); ?>" />
    
    <fieldset id="sessionProperties">
        <legend><?php echo get_lang( 'Session properties' ); ?></legend>
        <dl>
            <dt><label for="title" ><?php echo get_lang( 'Heading' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="title"
                        type="text"
                        name="data[title]"
                        value="<?php echo $this->session->getTitle(); ?>"
                        size="40" />
            </dd>
        </dl>
        <dl>
            <dt><label for="description" ><?php echo get_lang( 'Description' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="description"
                        type="text"
                        name="data[description]"
                        value="<?php echo $this->session->getDescription(); ?>"
                        size="80" />
            </dd>
        </dl>
        <?php if( ! $this->session->getId() ) : ?>
        <dl>
            <dt><label for="context" ><?php echo get_lang( 'Context' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <select id="context"
                        name="data[context]" >
                    <option value="user" selected="selected"><?php echo get_lang( 'individual' ); ?></option>
                    <option value="group"><?php echo get_lang( 'group' ); ?></option>
                </select>
            </dd>
        </dl>
        <?php endif; ?>
        <?php if( ! $this->session->getId() ) : ?>
        <dl>
            <dt><label for="type" ><?php echo get_lang( 'Session type' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <select id="type"
                        name="data[type]" >
                    <option value="undated" <?php if( $this->session->getType() == 'undated' ) echo 'selected="selected"'; ?>><?php echo get_lang( 'undated' ); ?></option>
                    <option value="dated" <?php if( $this->session->getType() == 'dated' ) echo 'selected="selected"'; ?>><?php echo get_lang( 'dated' ); ?></option>
                    <option value="timeslot" <?php if( $this->session->getType() == 'timeslot' ) echo 'selected="selected"'; ?>><?php echo get_lang( 'timeslot' ); ?></option>
                </select>
            </dd>
        </dl>
        <?php endif; ?>
        <dl>
            <dt><label for="startDate" ><?php echo get_lang( 'Opening date' ); ?></label>&nbsp;:</dt>
            <dd>
                <input id="hasStartDate"
                       type="checkbox"
                       <?php if( $this->session->getOpeningDate() ) : ?>
                       checked="checked"
                       <?php endif; ?>/>
                <input  id="startDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[openingDate]"
                        value="<?php echo $this->session->getOpeningDate() ? date( get_lang( '_date' ) , strtotime( $this->session->getOpeningDate() ) ) : ''; ?>"
                        size="8" />
            </dd>
        </dl>
        <dl>
            <dt><label for="endDate" ><?php echo get_lang( 'Closing date' ); ?></label>&nbsp;:</dt>
            <dd>
                <input id="hasEndDate"
                       type="checkbox"
                       <?php if( $this->session->getClosingDate() ) : ?>
                       checked="checked"
                       <?php endif; ?>/>
                <input  id="endDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[closingDate]"
                        value="<?php echo $this->session->getClosingDate() ? date( get_lang( '_date' ) , strtotime( $this->session->getClosingDate() ) ) : ''; ?>"
                        size="8" />
            </dd>
        </dl>
    </fieldset>
    <fieldset class="collapsible collapsed">
        <legend>
            <a href="#" class="doCollapse"><?php echo get_lang( 'Advanced options' ); ?></a>
        </legend>
        <div class="collapsible-wrapper">
            <dl>
                <dt><?php echo get_lang( 'Global options' ); ?></dt>
                <dd>
                    <input type="checkbox" <?php if( $this->session->getOption( Session::OPTION_USER_NAME_VISIBLE ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_USER_NAME_VISIBLE; ?>]" />
                    <label><?php echo get_lang( 'Users can see the name of the others voters' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->session->getOption( Session::OPTION_UNSUBSCRIBE_ALLOWED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_UNSUBSCRIBE_ALLOWED; ?>]" />
                    <label><?php echo get_lang( 'Users can delete their vote' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->session->getOption( Session::OPTION_VOTE_MODIFICATION_ALLOWED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_VOTE_MODIFICATION_ALLOWED; ?>]" />
                    <label><?php echo get_lang( 'Users can modify their choice' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->session->getOption( Session::OPTION_BLANK_VOTE_ENABLED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_BLANK_VOTE_ENABLED; ?>]" />
                    <label><?php echo get_lang( 'Blank vote enabled (adds a choice)' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->session->getOption( Session::OPTION_PREFERENCE_ENABLED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_PREFERENCE_ENABLED; ?>]" />
                    <label><?php echo get_lang( 'Subscription with preferences' ); ?></label><br />
                </dd>
            </dl>
            <dl>
                <dt><?php echo get_lang( 'Number of vote required' ); ?></dt>
                <dd>
                    <input id="min_num_vote" type="text" size="2" value="<?php echo $this->session->getOption( Session::OPTION_MINIMUM_NUMBER_OF_VOTE ) ? $this->session->getOption( Session::OPTION_MINIMUM_NUMBER_OF_VOTE ) : 1; ?>" name="data[optionList][<?php echo Session::OPTION_MINIMUM_NUMBER_OF_VOTE; ?>]" />
                </dd>
            </dl>
            <dl>
                <dt><?php echo get_lang( 'Number of vote allowed' ); ?></dt>
                <dd>
                    <input id="max_num_vote" type="text" size="2" value="<?php echo $this->session->getOption( Session::OPTION_MAXIMUM_NUMBER_OF_VOTE ) ? $this->session->getOption( Session::OPTION_MAXIMUM_NUMBER_OF_VOTE ) : 1; ?>" name="data[optionList][<?php echo Session::OPTION_MAXIMUM_NUMBER_OF_VOTE; ?>]" />
                </dd>
            </dl>
        </div>
    </fieldset>
    <dl>
        <dt>
            <input id="submitSessionProperties" type="submit" name="submitSession" value="<?php echo get_lang( 'OK' ); ?>" />
            <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
        </dt>
    </dl>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>
<?php if( $this->session->getId() ) : ?>
<fieldset id="slotProperties">
    <legend><?php echo get_lang( 'Slots properties' ); ?></legend>
    <?php include( $this->session->getType() . 'edit.tpl.php' ); ?>
</fieldset>
<?php endif; ?>