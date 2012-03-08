<form method="post"
      enctype="multipart/form-data"
      action="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] . '?cmd=exAdd' ) ); ?>" >
    <fieldset>
        <legend><?php echo get_lang( 'ready_to_add' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <th align="center"><?php echo get_lang( 'select' ); ?></th>
                <?php foreach( $this->controller->importer->csvParser->titles as $field ) : ?>
                    <th align="center"><?php echo get_lang( $field ); ?></th>
                <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->toAdd as $index => $userData ) : ?>
                <tr>
                    <td align="center">
                        <input type="checkbox"
                               name="selected[<?php echo $index; ?>]"
                               checked="checked" />
                    </td>
                    <?php foreach( $userData as $field => $value ) : ?>
                    <td>
                        <input type="hidden"
                               name="userData[<?php echo $index; ?>][<?php echo $field; ?>]"
                               value="<?php echo $value; ?>" />
                        <?php echo $value; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
    <?php if( $this->controller->importer->conflict ) : ?>
    <br />
    <fieldset>
        <legend><?php echo get_lang( 'conflict_found' ); ?> :</legend>
        <table class="claroTable emphaseLine" style="width: 100%;">
            <thead>
                <tr class="headerX">
                    <?php foreach( $this->controller->importer->getConflictFields() as $field ) : ?>
                    <th align="center"><?php echo $field; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $this->controller->importer->conflict as $index => $userData ) : ?>
                <tr>
                    <?php foreach( $this->controller->importer->getConflictFields() as $field ) : ?>
                        <?php if( array_key_exists( $field , $userData ) ) : ?>
                    <td style="color: #f00;" >
                        <?php echo $userData[ $field ]; ?>
                        <?php else : ?>
                    <td>
                        <?php echo $this->controller->importer->csvParser->data[ $index ][ $field ]; ?>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </fieldset>
    <?php endif; ?>
    <input id="submit" type="submit" name="submit" value="<?php echo get_lang( 'OK' ); ?>" />
    <a style="text-decoration: none;"
       href="<?php echo htmlspecialchars( Url::Contextualize( $_SERVER['PHP_SELF'] ) ); ?>">
        <input type="button" name="cancel" value="<?php echo get_lang( 'Cancel' ); ?>" />
    </a>
</form>