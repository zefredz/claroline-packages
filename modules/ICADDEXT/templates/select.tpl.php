<script type="text/javascript">
    $(document).ready(function(){
        $("#selectAll" ).click(function(){
            var is_checked=$(this).attr('checked');
            $(".itemSelect").attr('checked',is_checked);
        });
    });
</script>
<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exAdd' ) ); ?>" >
    <?php foreach( $this->controller->importer->toAdd as $index => $userData ) : ?>
        <?php foreach( $userData as $field => $value ) : ?>
    <input type="hidden"
           name="userData[<?php echo $index; ?>][<?php echo $field; ?>]"
           value="<?php echo $value; ?>" />
        <?php endforeach; ?>
    <?php endforeach; ?>
    
    <?php if( ! empty( $this->controller->importer->incomplete ) ) : ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'missing_values' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th><?php echo get_lang( 'force' ); ?></th>
                    <?php foreach( ICADDEXT_Importer::$check_conflict_fields as $field ) : ?>
                    <th align="center"><?php echo ucwords( get_lang( $field ) ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->incomplete as $index => $userData ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]" />
                    </td>
                    <?php foreach( ICADDEXT_Importer::$check_conflict_fields as $field ) : ?>
                    <td>
                        <?php if( empty( $userData[ $field ] ) ) : ?>
                        <input type="text"
                               name="toForce[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo get_lang( 'missing_value' ); ?>"
                               style="color: #f00; width: 300px;" />
                        <?php else : ?>
                        <input type="hidden"
                               name="toForce[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $userData[ $field ]; ?>" />
                        <?php echo $userData[ $field ]; ?>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
    <br />
    <?php endif; ?>
    
    <?php if( ! empty( $this->controller->importer->conflict ) ) : ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'force_conflict' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th><?php echo get_lang( 'force' ); ?></th>
                    <?php foreach( ICADDEXT_Importer::$check_conflict_fields as $field ) : ?>
                    <th align="center"><?php echo ucwords( get_lang( $field ) ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->conflictFields as $index => $userData ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]" />
                    </td>
                    <?php foreach( ICADDEXT_Importer::$check_conflict_fields as $field ) : ?>
                    <td>
                        <?php if( array_key_exists( $field , $userData ) ) : ?>
                        <input type="text"
                               name="toForce[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $field == 'username'
                                                        ? ICADDEXT_Importer::username(
                                                                    $this->controller->importer->conflict[ $index ][ 'prenom' ]
                                                                  , $this->controller->importer->conflict[ $index ][ 'nom' ] )
                                                        : $userData[ $field ]; ?>"
                               style="color: #f00; width: 300px;" />
                        <?php else : ?>
                        <strong><?php echo $this->controller->importer->conflict[ $index ][ $field ]; ?></strong>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
    <br />
    <?php endif; ?>
    
    <?php if( ! empty( $this->controller->importer->toAdd ) ) : ?>
    <fieldset>
        <legend><?php echo get_lang( 'ready_to_add' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th align="center"><!--<?php echo get_lang( 'Select' ); ?>--><input id="selectAll" type="checkbox" checked="checked" /></th>
                <?php foreach( $this->controller->importer->csvParser->titles as $field ) : ?>
                    <th align="center"><?php echo get_lang( $field ); ?></th>
                <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->toAdd as $index => $userData ) : ?>
                <tr>
                    <td align="center">
                        <input class="itemSelect"
                               type="checkbox"
                               name="selected[<?php echo $index; ?>]"
                               checked="checked" />
                    </td>
                    <?php foreach( $this->controller->importer->csvParser->titles as $field ) : ?>
                    <td>
                        <?php echo $userData[ $field ]; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
    <br />
    <?php endif; ?>
    <input type="checkbox" name="send_mail" checked="checked" /><strong><?php echo get_lang( 'send_mail' ); ?></strong><br />
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>