/**
 * PopupWindow helper class
 * @author Frederic Minne <zefredz@gmail.com>
 * @version 1.0
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 */
 
function PopupWindow( title, url ){
	this.title = title;
	this.url = url;
	this.params = '';
};

PopupWindow.prototype.addParam = function( name, value ){
	if ( this.params.length > 0 )
	{
		this.params += ',';
	}
	
	if ( typeof( value ) != 'undefined' )
	{
		this.params += name + '=' + value;
	}
	else
	{
		this.params += name;
	}
};

PopupWindow.prototype.setSize = function( width, height ){
	this.addParam( 'width', width );
	this.addParam( 'height', height );
};

PopupWindow.prototype.setPosition = function( top, left ){
	this.addParam( 'top', top );
	this.addParam( 'left', left );
	this.addParam( 'screenX', left );
	this.addParam( 'screenY', top );
};

PopupWindow.prototype.setUrl = function( url ){
	this.url = url;
};

PopupWindow.prototype.setResizable = function(){
	this.addParam( 'resizable', 'yes' );
};

PopupWindow.prototype.setNotResizable = function(){
	this.addParam( 'resizable', 'no' );
};

PopupWindow.prototype.hideStatus = function(){
	this.addParam( 'status', 'no' );
};

PopupWindow.prototype.showStatus = function(){
	this.addParam( 'status', 'yes' );
};

PopupWindow.prototype.showMenubar = function(){
	this.addParam( 'menubar', 'yes' );
};

PopupWindow.prototype.showScrollbar = function(){
	this.addParam( 'scrollbars', 'yes' );
};

PopupWindow.prototype.hideScrollbar = function(){
	this.addParam( 'scrollbars', 'no' );
};

PopupWindow.prototype.hideMenubar = function(){
	this.addParam( 'menubar', 'no' );
};

PopupWindow.prototype.showToolbar = function(){
	this.addParam( 'toolbar', 'yes' );
};

PopupWindow.prototype.hideToolbar = function(){
	this.addParam( 'toolbar', 'no' );
};

PopupWindow.prototype.showLocation = function(){
	this.addParam( 'location', 'yes' );
};

PopupWindow.prototype.hideLocation = function(){
	this.addParam( 'location', 'no' );
};

PopupWindow.prototype.show = function(){
	if ( this.params.length > 0 )
	{
		// console.log(this.params);
		return window.open( this.url, this.title, this.params );
	}
	else
	{
		return window.open( this.url, this.title );
	}
};

function popup( url, name, width, height ){
	var pw = new PopupWindow( name, url );
	pw.setSize( width, height );
	pw.showScrollbar();
	pw.show();
	
	// return false;
}