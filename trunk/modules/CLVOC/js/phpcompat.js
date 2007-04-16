    function in_array( item, arr ) {
        for ( var i = 0; i < arr.length; i++ ) {
            if ( arr[i] == item ) return true;
        }
        
        return false;
    }
    
    function array_search( item, arr ) {
        for ( var i = 0; i < arr.length; i++ ) {
            if ( arr[i] == item ) return i;
        }

        return false;
    }
    
    function strcasecmp(x,y) { 
      var a = String(x).toUpperCase(); 
      var b = String(y).toUpperCase(); 
      if (a > b) 
         return 1 
      if (a < b) 
         return -1 
      return 0; 
    }
    
    function array_key_exists( key, assocArray ) {
        for ( var x in assocArray ) {
            if ( x == key ) return true;
        }
        
        return false;
    }