<script type="text/javascript">
    $(document).ready(function(){
        $("#goback").click(function(){
            $("#step").attr({value:"1"});
        });
    });
</script>
<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>" >
    <input type="hidden"
           name="data[jsEnabled]"
           value="1" />
    <input type="hidden"
           name="data[courseId]"
           value="<?php echo $this->userData[ 'courseId' ]; ?>" />
    <input type="hidden"
           name="data[isCourseCreator]"
           value="<?php echo $this->userData[ 'isCourseCreator' ]; ?>" />
    <noscript>
        <input type="hidden"
               name="data[jsEnabled]"
               value="0" />
    </noscript>
    <input type="hidden"
           name="data[ticketId]"
           value="<?php echo $this->ticket->get( 'ticketId' ); ?>" />
    <input type="hidden"
           name="data[urlOrigin]"
           value="<?php echo $this->ticket->get( 'urlOrigin' ); ?>" />
    <input id="step"
           type="hidden"
           name="step"
           value="3" />
    <input type="hidden"
           name="data[firstName]"
           value="<?php echo $this->userData[ 'firstName' ]; ?>" />
    <input type="hidden"
           name="data[lastName]"
           value="<?php echo $this->userData[ 'lastName' ]; ?>" />
    <input type="hidden"
           name="data[mail]"
           value="<?php echo $this->userData[ 'mail' ]; ?>" />
    <input type="hidden"
           name="data[username]"
           value="<?php echo $this->userData[ 'username' ]; ?>" />
    <input type="hidden"
           name="data[officialCode]"
           value="<?php echo $this->userData[ 'officialCode' ]; ?>" />
    <input  type="hidden"
            name="data[UCLMember]"
            value="<?php echo $this->userData[ 'UCLMember' ]; ?>" />
    <input  type="hidden"
            name="data[isManager]"
            value="<?php echo $this->userData[ 'isManager' ]; ?>" />
    
    <fieldset>
        <legend><?php echo get_lang( 'Issue infos' ); ?>&nbsp;<span class="required">*</span></legend>
        <?php foreach( $this->issueList as $categoryId => $issue ) : ?>
        <div class="collapsible collapsed">
            <a class="doCollapse" href="#"><strong><?php echo get_lang( $this->categoryList[ $categoryId ] ); ?></strong></a>
            <ul class="collapsible-wrapper" style="display: none; list-style-type: none;">
            <?php foreach( $issue as $label => $description ) : ?>
                <li>
                    <input type="radio"
                           name="data[issueType]"
                           value="<?php echo $label; ?>" />
                    <?php echo $description; ?>
                </li>
            <?php endforeach; ?>
            <ul>
            <?php if( isset( $this->addedFields[ $categoryId ] ) ) : ?>
            <br />
            <span style="font-weight: bold; color: #A55;"><?php echo get_lang( $this->addedFields[ $categoryId ][ 'label' ] ); ?></span>
                <?php if( $this->addedFields[ $categoryId ][ 'required' ] == 1 ) : ?>&nbsp;<span class="required">*</span><?php endif; ?>
                <br />
                <?php if( $this->addedFields[ $categoryId ][ 'type' ] == 'text' ) : ?>
                <input type="text"
                           name="data[<?php echo $this->addedFields[ $categoryId ][ 'name' ]; ?>]"
                           value="<?php echo $this->userData[ 'courseCode' ]; ?>" />
                <?php elseif( $this->addedFields[ $categoryId ][ 'type' ] == 'textarea' ) : ?>
                <textarea   type="text"
                            rows="20"
                            cols="120"
                            name="data[<?php echo $this->addedFields[ $categoryId ][ 'name' ]; ?>]"></textarea>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </fieldset>
    
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'Submit' ); ?>" />
    <input id="goback" type="submit" name="submit" value="<?php echo get_lang( 'Go back' ); ?>" />
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>