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
                <?php foreach( $this->controller->importer->getToAdd() as $index => $userData ) : ?>
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
    <?php if( $this->controller->importer->getConflict() ) : ?>
    <fieldset>
        <legend><?php echo get_lang( 'conflict_found' ); ?> :</legend>
        <table class="claroTable emphaseLine" style=" width: 100%;">
            <thead>
                <tr class="headerX">
                    <?php foreach( $this->controller->importer->csvParser->titles as $field ) : ?>
                    <td align="center"><?php echo $field; ?></td>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
            <tbody>
                <?php foreach( $this->controller->importer->getConflict() as $index => $userData ) : ?>
                <tr>
                    <?php foreach( $userData as $field => $value ) : ?>
                        <?php if( array_key_exists( $value , $this->controller->importer->conflict[ $index ] ) ) : ?>
                    <td style="background-color: #faa;" >
                        <?php else : ?>
                    <td>
                        <?php endif; ?>
                        <?php echo $value; ?>
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