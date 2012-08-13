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
                <input  id="startDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[startDate]"
                        value="<?php echo date( get_lang( '_date' ) , strtotime( $this->model->get( $this->id , 'startDate' ) ? $this->model->get( $this->id , 'startDate' ) : 'now' ) ); ?>"
                        size="8" />
            </dd>
        </dl>
        <dl>
            <dt><label for="endDate" ><?php echo get_lang( 'End date' ); ?></label>&nbsp;:</dt>
            <dd>
                <input  id="endDate"
                        class="auto-kal"
                        type="text"
                        lang="<?php echo get_lang( '_lang_code' ); ?>"
                        name="data[endDate]"
                        value="<?php echo date( get_lang( '_date' ) , strtotime( $this->model->get( $this->id , 'endDate' ) ? $this->model->get( $this->id , 'endDate' ) : '+1 month' ) ); ?>"
                        size="8" />
            </dd>
        </dl>
    </fieldset>
    
    <dl>
        <dt>
            <input id="submitSessionProperties" type="submit" name="submitSession" value="<?php echo get_lang( 'OK' ); ?>" />
            <?php echo claro_html_button( htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ) , get_lang( 'Cancel' ) ); ?>
        </dt>
    </dl>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>