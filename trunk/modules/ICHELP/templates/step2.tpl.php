<script type="text/javascript">
    $(document).ready(function(){
        $("#goback").click(function(){
            $("#step").attr({value:"1"});
        });
    <?php foreach( array_keys($this->addedFields ) as $fieldId ) : ?>
        if( $(".hasRequired<?php echo $fieldId; ?> li input").attr('checked') )
        {
            $("#required<?php echo $fieldId; ?>").show();
        }
        else
        {
            $("#required<?php echo $fieldId; ?>").hide();
        }
        $(".hasRequired<?php echo $fieldId; ?> li input").click(function(){
            $(".required").hide();
            $("#required<?php echo $fieldId; ?>").show();
        });
    <?php endforeach; ?>
        $(".noRequired li input").click(function(){
            $(".required").hide();
        });
        $(".hiddenUntilClick").hide();
        $(".issueCase").click(function(){
            $(".hiddenUntilClick").show();
        })
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
           name="data[urlOrigin]"
           value="<?php echo $this->userData[ 'urlOrigin' ]; ?>" />
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
            <?php if( $categoryId != 99 ) : ?>
        <div>
            <span style="font-weight: bold; color: #336699;"><?php echo get_lang( $this->categoryList[ $categoryId ] ); ?></span>
            <ul class="<?php if( array_key_exists( $categoryId , $this->addedField ) ) : ?> hasRequired<?php echo $this->addedField[ $categoryId ]; ?><?php else : ?> noRequired<?php endif; ?>"
                style="list-style-type: none;">
            <?php foreach( $issue as $label => $description ) : ?>
                <li>
                    <input class="issueCase"
                           type="radio"
                        <?php if ( $this->userData[ 'issueType' ] == $label ) : ?>
                           checked="checked"
                        <?php endif; ?>
                           name="data[issueType]"
                           value="<?php echo $label; ?>" />
                    <?php echo $description; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
            <?php else : ?>
        <div class="collapsible collapsed">
            <a class="doCollapse" href="#"><span><?php echo get_lang( $this->categoryList[ $categoryId ] ); ?></span></a>
            <ul class="collapsible-wrapper <?php if( array_key_exists( $categoryId , $this->addedField ) ) : ?> hasRequired<?php echo $this->addedField[ $categoryId ]; ?><?php else : ?> noRequired<?php endif; ?>"
                style="display: none; list-style-type: none;">
            <?php foreach( $issue as $label => $description ) : ?>
                <li>
                    <input class="issueCase"
                           type="radio"
                        <?php if ( $this->userData[ 'issueType' ] == $label ) : ?>
                           checked="checked"
                        <?php endif; ?>
                           name="data[issueType]"
                           value="<?php echo $label; ?>" />
                    <?php echo $description; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </fieldset>
        <fieldset class="hiddenUntilClick">
        <legend><?php echo get_lang( 'Additionnal informations' ); ?></legend>
        <span style="font-weight: bold; color: #336699;"><?php echo get_lang( 'Enter the course code' ); ?></span>
        <span id="required0" class="required" style="font-weight: bold;">*</span><br />
        <input type="text"
                   name="data[courseCode]"
                   value="<?php echo $this->userData['courseCode']; ?>" /><br /><br />
        <span style="font-weight: bold; color: #336699;"><?php echo get_lang( 'Describe your problem' ); ?></span>
        <span id="required1" class="required" style="font-weight: bold;">*</span><br />
<textarea   rows="20" cols="120" name="data[issueDescription]">
<?php echo $this->userData['issueDescription']; ?>
</textarea>
    </fieldset>
    <input class="hiddenUntilClick" id="submit" type="submit" name="submit" value="<?php echo get_lang( 'Submit' ); ?>" />
    <input id="goback" type="submit" name="submit" value="<?php echo get_lang( 'Go back' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo claro_htmlspecialchars( $this->userData[ 'urlOrigin' ] ? $this->userData[ 'urlOrigin' ] : get_path( 'rootWeb' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>