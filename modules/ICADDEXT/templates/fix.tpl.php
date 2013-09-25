<script type="text/javascript">
    $(document).ready(function(){
        $("#selectAll" ).click(function(){
            var is_checked=$(this).attr('checked');
            $(".itemSelect").attr('checked',is_checked);
        });
    });
</script>
<?php $cmd = $this->controller->importer->is_ok() ? 'exAdd' : 'exFix'; ?>
<form method="post"
      enctype="multipart/form-data"
      action="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=' . $cmd ) ); ?>" >
    <?php foreach( $this->controller->importer->toAdd as $index => $userData ) : ?>
        <?php foreach( $userData as $field => $value ) : ?>
    <input type="hidden"
           name="userData[<?php echo $index; ?>][<?php echo $field; ?>]"
           value="<?php echo claro_htmlspecialchars( $value ); ?>" />
        <?php endforeach; ?>
    <?php endforeach; ?>
    
    <?php if( ! empty( $this->controller->importer->incomplete ) ) : ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'missing_values' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th><?php echo get_lang( 'fix' ); ?></th>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <th align="center"><?php echo ucwords( get_lang( $field ) ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( array_keys( $this->controller->importer->incomplete ) as $index ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]" />
                    </td>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <td>
                        <?php if( empty( $this->controller->importer->csvParser->data[$index][ $field ] ) ) : ?>
                        <input type="text"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo get_lang( 'missing_value' ); ?>"
                               style="color: #f00; width: 300px;" />
                        <?php elseif( $field == 'email' && ! ICADDEXT_Importer::is_mail( $this->controller->importer->csvParser->data[$index][ $field ] ) ) : ?>
                        <input type="text"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>"
                               style="color: #f00; width: 300px;" />
                        <?php else : ?>
                        <input type="hidden"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>" />
                        <?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>
                            <?php if( $this->controller->importer->isAutoGen( $field , $index ) ) : ?>
                        <img src="<?php echo get_icon_url( 'magic' ); ?>" alt="<?php echo get_lang( 'auto_generated' ); ?>"/>
                            <?php endif; ?>
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
    
    <?php if( ! empty( $this->controller->importer->invalid ) ) : ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'invalid_mail' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th><?php echo get_lang( 'fix' ); ?></th>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <th align="center"><?php echo ucwords( get_lang( $field ) ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( array_keys( $this->controller->importer->invalid ) as $index ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]" />
                    </td>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <td>
                        <?php if( $field == 'email' ) : ?>
                        <input type="text"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>"
                               style="color: #f00; width: 300px;" />
                        <?php else : ?>
                        <input type="hidden"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>" />
                        <?php echo $this->controller->importer->csvParser->data[$index][ $field ]; ?>
                            <?php if( $this->controller->importer->isAutoGen( $field , $index ) ) : ?>
                        <img src="<?php echo get_icon_url( 'magic' ); ?>" alt="<?php echo get_lang( 'auto_generated' ); ?>"/>
                            <?php endif; ?>
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
    <?php foreach( array_intersect_key( $this->controller->importer->csvParser->data , $this->controller->importer->conflict ) as $index => $userData ) : ?>
        <?php foreach( $userData as $field => $value ) : ?>
            <?php if( ! array_key_exists( $field , ICADDEXT_Importer::$display_fields ) ) : ?>
    <input type="hidden"
           name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
           value="<?php echo claro_htmlspecialchars( $value ); ?>" />
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'conflict_found' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th><?php echo get_lang( 'fix' ); ?></th>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <th align="center"><?php echo ucwords( get_lang( $field ) ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->conflict as $index => $userData ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]" />
                    </td>
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <td>
                        <?php if( array_key_exists( $field , $userData ) ) : ?>
                        <input type="text"
                               name="toFix[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $field == 'username'
                                                        ? ICADDEXT_Importer::username(
                                                                    $this->controller->importer->csvParser->data[ $index ][ 'prenom' ]
                                                                  , $this->controller->importer->csvParser->data[ $index ][ 'nom' ] )
                                                        : $userData[ $field ]; ?>"
                               style="color: #f00; width: 300px;" />
                        <?php else : ?>
                        <?php echo $this->controller->importer->csvParser->data[ $index ][ $field ]; ?>
                            <?php if( $this->controller->importer->isAutoGen( $field , $index ) ) : ?>
                        <img src="<?php echo get_icon_url( 'magic' ); ?>" alt="<?php echo get_lang( 'auto_generated' ); ?>"/>
                            <?php endif; ?>
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
                <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
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
                    <?php foreach( ICADDEXT_Importer::$display_fields as $field ) : ?>
                    <td>
                        <?php echo $userData[ $field ]; ?>
                        <?php if( $this->controller->importer->isAutoGen( $field , $index ) ) : ?>
                        <img src="<?php echo get_icon_url( 'magic' ); ?>" alt="<?php echo get_lang( 'auto_generated' ); ?>"/>
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
    <p style="font-weight: bold; font-style: italic; color: grey;">
        <img src="<?php echo get_icon_url( 'magic' ); ?>" alt="<?php echo get_lang( 'auto_generated' ); ?>"/>
        <?php echo get_lang( 'autogen' ); ?>
    </p>
    <?php if( $this->controller->importer->is_ok() ) : ?>
    <input type="checkbox" name="send_mail" checked="checked" /><strong><?php echo get_lang( 'send_mail' ); ?></strong><br />
    <?php endif; ?>
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo claro_htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>