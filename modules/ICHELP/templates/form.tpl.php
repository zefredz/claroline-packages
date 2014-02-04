<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>" >
    <input type="hidden"
           name="data[jsEnabled]"
           value="1" />
    <input type="hidden"
           name="data[courseId]"
           value="<?php echo $this->userData[ 'courseId' ]; ?>" />
    <noscript>
        <input type="hidden"
               name="data[jsEnabled]"
               value="0" />
    </noscript>
    <input type="hidden"
           name="data[urlOrigin]"
           value="<?php echo $this->ticket->get( 'urlOrigin' ); ?>" />
    <fieldset>
        <legend><?php echo get_lang( 'Personnal infos' ); ?> :</legend>
        <dl>
            <dt><strong><?php echo get_lang( 'First name' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[firstName]" value="<?php echo $this->userData[ 'firstName' ]; ?>" /></dd>
            
            <dt><strong><?php echo get_lang( 'Last name' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[lastName]" value="<?php echo $this->userData[ 'lastName' ]; ?>" /></dd>
    
            <dt><strong><?php echo get_lang( 'eMail' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><input type="text" name="data[mail]" value="<?php echo $this->userData[ 'mail' ]; ?>" /></dd>

            <dt><strong><?php echo get_lang( 'Username' ); ?></strong></dt>
            <dd><input type="text" name="data[username]" value="<?php echo $this->userData[ 'username' ]; ?>" /></dd>
    
            <dt><strong><?php echo get_lang( 'FGS' ); ?></strong> <span style="font-size: small; color: grey; font-style : italic;">(<?php echo get_lang( 'useful for authentification problem' ); ?>)</span></dt>
            <dd><input type="text" name="data[officialCode]" value="<?php echo $this->userData[ 'officialCode' ]; ?>" /></dd>

            <dt><strong><?php echo get_lang( 'UCL member' ) . ' ?'; ?><span class="required">*</span>&nbsp;</strong></dt>
            <dd>
                <input  id = "isMember"
                        type="radio"
                        name="data[UCLMember]"
                        value="1"
                        <?php if ( claro_is_user_authenticated() && substr( $this->userData[ 'mail' ] , -12 ) == 'uclouvain.be' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'Yes' ); ?>
                <input  id = "notMember"
                        type="radio"
                        name="data[UCLMember]"
                        value="0"
                        <?php if ( claro_is_user_authenticated() && substr( $this->userData[ 'mail' ] , -12 ) != 'uclouvain.be' ) : ?>checked="checked"<?php endif; ?>/>
                <?php echo get_lang( 'No' ); ?></strong>
            </dd>
            
            <dt><strong><?php echo get_lang( 'Course manager' ) . ' ?'; ?></strong><span class="required">*</span>&nbsp;
            </dd></dt>
            <dd>
                <input  id="isManager"
                        type="radio"
                        name="data[courseManager]"
                        value="1" />
                <?php echo get_lang( 'Yes' ); ?>
                <input  id="notManager"
                        type="radio"
                        name="data[courseManager]"
                        value="0" />
                <?php echo get_lang( 'No' ); ?></strong>
        </dl>
    </fieldset>
    
    <fieldset>
        <legend><?php echo get_lang( 'Issue infos' ); ?></legend>
        <dl>
            <dt><strong><?php echo get_lang( 'Your issue is related to' ) . ' :'; ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd><ul style="list-style-type: none;">
            <?php foreach( $this->checkList as $index => $check ) : ?>
                <?php if( (int)$check[ 'issueCategory' ] > 1 || ! $this->userData[ 'userId' ] ) : ?>
                <li class="issueType<?php echo $check[ 'issueCategory' ]; ?>">
                    <input type="radio"
                           name="data[issueType]"
                           value="<?php echo $index; ?>" />
                    <?php echo $check[ 'description' ]; ?>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
            <ul></dd>
        </dl>
        <dl>
            <dt><strong><?php echo get_lang( 'Related course code (if relevant)' ); ?></strong></dt>
            <dd><input type="text"
                       name="data[courseCode]"
                       value="<?php echo $this->userData[ 'courseCode' ]; ?>"</dd>
        </dl>
        <dl>
            <dt><strong><?php echo get_lang( 'Describe your problem' ); ?></strong><span class="required">*</span>&nbsp;</dt>
            <dd>
                <textarea   type="text"
                            rows="20"
                            cols="120"
                            name="data[issueDescription]"></textarea>
            </dd>
        </dl>
    </fieldset>
    
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo claro_htmlspecialchars( $this->ticket->get( 'urlOrigin' ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>
<p><small><?php echo get_lang( '<span class="required">*</span> denotes required field' ); ?></small></p>