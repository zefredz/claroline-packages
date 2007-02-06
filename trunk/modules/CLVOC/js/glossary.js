// define extended String class    
String.prototype.ltrim = function()
{
	return this.replace(/^\s*/, '');
};

String.prototype.rtrim = function()
{
	return this.replace(/\s*$/, '');
};

String.prototype.trim = function() {
    return this.rtrim().ltrim();
};

String.prototype.stripHTML = function() {
    var reTag = /<[^>]*?>/;
    return this.replace( reTag, '' );
};

String.prototype.getWordList = function() {
    var reWords = /\b(\s+?)\b/g;
    return this.match( reWords );
};

String.prototype.nl2Space = function() {
    var reTag = /(\n|\r\n|\r)/g;
    return this.replace( reTag, " " );
};

// define Selection functions
function getSelectedText()
{
    var txt = '';
    // Mozilla, Safari
    if (window.getSelection)
    {
        txt = window.getSelection();
    }
    // Mozilla, IE 5.2 Mac, Opera, Netscape
    else if (document.getSelection)
    {
        txt = document.getSelection();
    }
    // IE 5, IE 6
    else if (document.selection)
    {
        txt = document.selection.createRange().text;
    }
    else
    {
        alert('Sorry : your browser does not seem to support\nany of the selection methods used by this script.');
        txt = '';
    }
    return txt;
}

function valueToLowerCase( sId )
{
    var oInput = document.getElementById( sId );
    oInput.value = oInput.value.toLowerCase();
}

function valueToUpperCase( sId )
{
    var oInput = document.getElementById( sId );
    oInput.value = oInput.value.toUpperCase();
}

function filterSelection( oSelection )
{
		var txt = oSelection.toString();
    
		txt = txt.stripHTML().nl2Space().trim();
		
		return txt;
}

// define script function
function addToList()
{
    var txt = getSelectedText();
    var oInput = document.getElementById( 'word' );
    
    txt = filterSelection( txt );
    
    oInput.value = txt;
}

function selectionToLowerCase()
{
    valueToLowerCase('word');
}

function selectionToUpperCase()
{
    valueToUpperCase('word');
}

function emptySelection()
{
    var oInput = document.getElementById( 'word' );
    oInput.value = '';
}