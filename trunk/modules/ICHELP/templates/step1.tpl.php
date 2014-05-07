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
           name="data[courseCode]"
           value="<?php echo $this->userData[ 'courseCode' ]; ?>" />
    <input type="hidden"
           name="data[isCourseCreator]"
           value="<?php echo $this->userData[ 'isCourseCreator' ]; ?>" />
    <input type="hidden"
           name="data[firstName]"
           value="<?php echo $this->userData[ 'firstName' ]; ?>" />
    <input type="hidden"
           name="data[lastName]"
           value="<?php echo $this->userData[ 'lastName' ]; ?>" />
    <input type="hidden"
           name="data[mail]"
           value="<?php echo $this->userData[ 'mail' ]; ?>" />
    <noscript>
        <input type="hidden"
               name="data[jsEnabled]"
               value="0" />
    </noscript>
    <input type="hidden"
           name="data[urlOrigin]"
           value="<?php echo $this->userData[ 'urlOrigin' ]; ?>" />
    <input type="hidden"
           name="step"
           value="2" />
    
    <fieldset>
        <legend><?php echo get_lang( 'Personnal infos' ); ?></legend>
        <dl>
        <?php if( empty( $this->userData['userId'] ) ) : ?>
            <dt><strong><?php echo get_lang( 'First name' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[firstName]" value="<?php echo $this->userData[ 'firstName' ]; ?>" /></dd>
            
            <dt><strong><?php echo get_lang( 'Last name' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[lastName]" value="<?php echo $this->userData[ 'lastName' ]; ?>" /></dd>
    
            <dt><strong><?php echo get_lang( 'eMail' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[mail]" value="<?php echo $this->userData[ 'mail' ]; ?>" /></dd>

            <dt><strong><?php echo get_lang( 'Username' ); ?></strong></dt>
            <dd><input type="text" name="data[username]" value="<?php echo $this->userData[ 'username' ]; ?>" /></dd>
    
        <?php endif; ?>
            <dt><strong><?php echo get_lang( 'UCL member' ); ?><span class="required">*</span>&nbsp;</strong></dt>
            <dd>
                <input  id = "isMember"
                        type="radio"
                        name="data[UCLMember]"
                        value="1"
                        <?php if ( $this->userData['UCLMember'] === '1' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'Yes' ); ?>
            </dd>
            <dt></dt>
            <dd>
                <input  id = "notMember"
                        type="radio"
                        name="data[UCLMember]"
                        value="0"
                        <?php if ( $this->userData['UCLMember'] === '0' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'No' ); ?></strong>
            </dd>
            
            <dt><strong><?php echo get_lang( 'FGS' ); ?></strong> <span style="font-size: small; color: grey; font-style : italic;">(<?php echo get_lang( 'useful for authentification problem' ); ?>)</span></dt>
            <dd><input type="text" name="data[officialCode]" value="<?php echo $this->userData[ 'officialCode' ]; ?>" /></dd>
            
            <dt><strong><?php echo get_lang( 'You are' ); ?><span class="required">*</span>&nbsp;</strong>
            </dd></dt>
            <dd>
                <input  id="notManager"
                        type="radio"
                        name="data[isManager]"
                        value="0"
                        <?php if ( $this->userData['isManager'] === '0' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'Student' ); ?>
            </dd>
            <dt></dt>
            <dd>
                <input  id="isManager"
                        type="radio"
                        name="data[isManager]"
                        value="1"
                        <?php if ( $this->userData['isManager'] === '1' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'Teacher' ); ?></strong>
        </dl>
    </fieldset>
    
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'Next' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo claro_htmlspecialchars( $this->userData[ 'urlOrigin' ] ? $this->userData[ 'urlOrigin' ] : get_path( 'rootWeb' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>