<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=rqSelect' ) ); ?>" >
    <fieldset>
        <legend><?php echo get_lang( 'add_single_user' ); ?> :</legend>
        <dl>
            <dt>
                <label for="resourceType">
                    <?php echo get_lang( 'last_name' ); ?>
                    <span class="required">*</span></label>&nbsp;:
                </label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][nom]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType">
                    <?php echo get_lang( 'first_name' ); ?>
                    <span class="required">*</span></label>&nbsp;:
                </label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][prenom]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType">
                    <?php echo get_lang( 'email' ); ?>
                    <span class="required">*</span></label>&nbsp;:
                </label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][email]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'official_code' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][officialCode]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'official_code_prefix' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][officialCodePrefix]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'username' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][username]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'password' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][password]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'institute' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][institution]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'study_year' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[annee_etude]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'phone_number' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][phoneNumber]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'comment' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="userData[0][remarques]"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'auth_source' ); ?> :</label>
            </dt>
            <dd>
                <select name="userData[0][authSource]">
                    <option value="external">external</option>
                    <option value="mailbox">mailbox</option>
                </select>
            </dd>
        </dl>
    </fieldset>
    <fieldset>
        <legend><?php echo get_lang( 'add_from_csv' ); ?> :</legend>
        <dl>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'upload_file' ); ?> :</label>
            </dt>
            <dd>
                <input type="file"
                       name="CsvFile" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'official_code_prefix' ); ?> :</label>
            </dt>
            <dd>
                <input type="text"
                       name="officialCodePrefix"
                       value="" />
            </dd>
            <dt>
                <label for="resourceType"><?php echo get_lang( 'auth_source' ); ?> :</label>
            </dt>
            <dd>
                <select name="authSource">
                    <option value="external">external</option>
                    <option value="mailbox">mailbox</option>
                </select>
            </dd>
        </dl>
    </fieldset>
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo get_path( 'rootWeb' ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>
