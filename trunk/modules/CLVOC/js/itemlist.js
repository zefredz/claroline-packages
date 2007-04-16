    function ItemListObject(id){
        this._itemList = [];
        this._id = id;
    }

    ItemListObject.prototype = {
        addNewItem:function() {
            var v = document.getElementById('add_'+this._id).value;

            if ( v ) {
                if ( ! in_array( v, this._itemList ) ) {
                    this._itemList.push(v);
                    this._itemList.sort(strcasecmp);
                }
                else {
                    alert( v+' already in list' );
                }
            }
        },
        setItemList: function( itemList ) {
            this._itemList = itemList;
        },
        getItemList: function() {
            return this._itemList;
        },
        renderList: function( objName ) {
            var targetElem = document.getElementById(this._id);

            targetElem.innerHTML = '';

            if ( this._itemList.length == 0 ){
                targetElem.innerHTML += '<tr><td>' + get_lang('Empty') + '</td></tr>';
            }

            for ( var i = 0; i < this._itemList.length; i++ ) {
                targetElem.innerHTML += '<tr><td><input type="hidden" name="synList['+i+']" value="'+this._itemList[i]+'" />'+this._itemList[i]+'</td><td>'+this._deleteItemButton(this._itemList[i], objName)+'</td></tr>';
            }
        },
        _deleteItemButton: function(idx, objName) {
            return '<a href="" onclick="'+objName+'.deleteItem(\''+idx+'\', \''+objName+'\');'+objName+'.renderList(\''+objName+'\');return false;">'+get_icon('delete.gif')+'</a>';
        },
        deleteItem: function(idx) {
            var tmp = [];

            for ( var i = 0; i < this._itemList.length; i++ ) {
                if ( this._itemList[i] != idx ) {
                    tmp.push( this._itemList[i] );
                }
            }

            this._itemList = tmp;
        },
        addItemButton: function( objName ) {
            document.write('<input type="text" name="add_'+this._id+'" id="add_'+this._id+'" value="" /><input type="button" onclick="'+objName+'.addNewItem();'+objName+'.renderList(\''+objName+'\');" name="btnAdd_'+this._id+'" value="Add" />');
        }
    }