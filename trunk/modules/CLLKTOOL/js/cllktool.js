
function linkAddOption( zoneId, labelName, labelValue, labelDelete )
{
    
    zoneId = '#' + zoneId;
    
    var div = $('<div></div>');
    div.css('padding', '2px');
    div.attr('id', 'option_' + optionsNb );
    var iName = $('<input type="text" name="options[params][' + optionsNb + '][name]" />').attr( 'id', 'name_' + optionsNb );    
    var iVars = $('<select></select>').append( selectOptionsList ).attr( 'id', 'option_' + optionsNb ).attr('name','options[params][' + optionsNb + '][var]');
    iVars.attr('onchange','linkLoadOptionValue( this, ' + optionsNb + ' );');
    var aDel = $('<a href="#" onclick="linkDelOption(\'option_' + optionsNb + '\');"></a>');
    var imgDel = $('<img src="./img/brick_delete.png" alt="' + labelDelete + '" />');
    aDel.append(imgDel);
    div.append( labelName + ' : ');
    div.append( iName );
    div.append( ' ' + labelValue + ' : ');
    div.append( iVars );
    div.append( ' ' );
    div.append( aDel );
    $(zoneId).append(div);
    
    optionsNb++;
}

function linkLoadOptionValue( selectZone, id )
{
    $("#value_" + id ).remove();
    if( $( selectZone ).val() == 'freeValue' )
    {
        var iValue = $('<input type="text" name="options[params][' + id + '][value]" style="width: 100px;" />');
    }
    else
    {
        var iValue = $('<input type="hidden" name="options[params][' + id + '][value]" />');
    }
    iValue.attr( 'id', 'value_' + id );
    
    $( selectZone ).after( iValue ).after(' ');
}

function linkDelOption( zoneId )
{
    $('#' + zoneId ).remove();
}