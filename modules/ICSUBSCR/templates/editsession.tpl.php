<script type="text/javascript">
    $(document).ready(function(){
        if(!$("#hasStartDate").attr("checked")){
            $("#startDate").hide();
        }
        if(!$("#hasEndDate").attr("checked")){
            $("#endDate").hide();
        }
        $("#hasStartDate").click(function(){
            if($("#hasStartDate").attr("checked")){
                $("#startDate").val('<?php echo date( get_lang( '_date' ) , strtotime( $this->model->getStartDate( $this->id ) ? $this->model->getStartDate( $this->id ) : 'now' ) ); ?>');
                $("#startDate").show();
            }else{
                $("#startDate").hide();
                $("#startDate").val('');
            }
        });
        $("#hasEndDate").click(function(){
            if($("#hasEndDate").attr("checked")){
                $("#endDate").val('<?php echo date( get_lang( '_date' ) , strtotime( $this->model->getEndDate( $this->id ) ? $this->model->getEndDate( $this->id ) : '+1 month' ) ); ?>');
                $("#endDate").show();
            }else{
                $("#endDate").hide();
                $("#endDate").val('');
            }
        });
    });
</script>

<?php $cmd = $this->id ? 'exEditSession' : 'exCreateSession'; ?>
<form   method="post"
        action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>" >
    <?php if( $this->id ) : ?>
    <input type="hidden"
           name="sessionId"
           value="<?php echo $this->id; ?>" />
    <?php endif; ?>
    <input type="hidden"
           name="data[status]"
           value="<?php echo $this->id ? $this->model->get( $this->id , 'status' ) :'open'; ?>" />
    <input type="hidden"
           name="data[visibility]"
           value="<?php echo $this->id ? $this->model->get( $this->id , 'visibility' ) :'visible'; ?>" />
    
    <fieldset id="sessionProperties">
        <legend><?php echo get_lang( 'Session properties' ); ?></legend>
        <dl>
            <dt><label for="title" ><?php echo get_lang( 'Heading' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="title"
                        type="text"
                        name="data[title]"
                        value="<?php echo $this->id ? $this->model->get( $this->id , 'title' ) : ''; ?>"
                        size="40" />
            </dd>
        </dl>
        <dl>
            <dt><label for="description" ><?php echo get_lang( 'Description' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <input  id="description"
                        type="text"
                        name="data[description]"
                        value="<?php echo $this->id ? $this->model->get( $this->id , 'description' ) : ''; ?>"
                        size="80" />
            </dd>
        </dl>
        <dl>
            <dt><label for="type" ><?php echo get_lang( 'Session type' ); ?><span class="required">*</span></label>&nbsp;:</dt>
            <dd>
                <select id="type"
                        name="data[type]" >
                    <?php foreach( $this->model->getTypeList() as $type ) : ?>
                    <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </dd>
        </dl>
        <dl>
            <dt><label for="startDate" ><?php echo get_lang( 'Start date' ); ?></label>&nbsp;:</dt>
            <dd>
                <input id="hasStartDate"
                       type="checkbox"
                       <?php if( $this->model->getStartDate( $this->id ) ) : ?>
                       checked="checked"
                       <?php endif; ?>/>
                <input  id="startDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[startDate]"
                        value="<?php echo $this->model->getStartDate( $this->id ) ? date( get_lang( '_date' ) , strtotime( $this->model->get( $this->id , 'startDate' ) ) ) : ''; ?>"
                        size="8" />
            </dd>
        </dl>
        <dl>
            <dt><label for="endDate" ><?php echo get_lang( 'End date' ); ?></label>&nbsp;:</dt>
            <dd>
                <input id="hasEndDate"
                       type="checkbox"
                       <?php if( $this->model->getEndDate( $this->id ) ) : ?>
                       checked="checked"
                       <?php endif; ?>/>
                <input  id="endDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[endDate]"
                        value="<?php echo $this->model->getEndDate( $this->id ) ? date( get_lang( '_date' ) , strtotime( $this->model->get( $this->id , 'endDate' ) ) ) : ''; ?>"
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
                    <input type="checkbox" <?php if( $this->id && $this->model->getOption( $this->id , Session::OPTION_USER_NAME_VISIBLE ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_USER_NAME_VISIBLE; ?>]" />
                    <label><?php echo get_lang( 'Users can see the name of the others voters' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->id && $this->model->getOption( $this->id , Session::OPTION_UNSUBSCRIBE_ALLOWED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_UNSUBSCRIBE_ALLOWED; ?>]" />
                    <label><?php echo get_lang( 'Users cans delete their vote' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->id && $this->model->getOption( $this->id , Session::OPTION_VOTE_MODIFICATION_ALLOWED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_VOTE_MODIFICATION_ALLOWED; ?>]" />
                    <label><?php echo get_lang( 'Users can modify their choice' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->id && $this->model->getOption( $this->id , Session::OPTION_BLANK_VOTE_ENABLED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_BLANK_VOTE_ENABLED; ?>]" />
                    <label><?php echo get_lang( 'Blank vote enabled (adds a choice)' ); ?></label><br />
                    <input type="checkbox" <?php if( $this->id && $this->model->getOption( $this->id , Session::OPTION_PREFERENCE_ENABLED ) ) echo 'checked="checked"'; ?> name="data[optionList][<?php echo Session::OPTION_PREFERENCE_ENABLED; ?>]" />
                    <label><?php echo get_lang( 'Subscription with preferences' ); ?></label><br />
                </dd>
            </dl>
            <dl>
                <dt><?php echo get_lang( 'Number of vote required' ); ?></dt>
                <dd>
                    <input id="min_num_vote" type="text" size="2" value="<?php echo $this->id && $this->model->getOption( $this->id , Session::OPTION_MINIMUM_NUMBER_OF_VOTE ) ? $this->model->getOption( $this->id , Session::OPTION_MINIMUM_NUMBER_OF_VOTE ) : 1; ?>" name="data[optionList][<?php echo Session::OPTION_MINIMUM_NUMBER_OF_VOTE; ?>]" />
                </dd>
            </dl>
            <dl>
                <dt><?php echo get_lang( 'Number of vote allowed' ); ?></dt>
                <dd>
                    <input id="max_num_vote" type="text" size="2" value="<?php echo $this->id && $this->model->getOption( $this->id , Session::OPTION_MAXIMUM_NUMBER_OF_VOTE ) ? $this->model->getOption( $this->id , Session::OPTION_MAXIMUM_NUMBER_OF_VOTE ) : 1; ?>" name="data[optionList][<?php echo Session::OPTION_MAXIMUM_NUMBER_OF_VOTE; ?>]" />
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