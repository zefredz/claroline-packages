//<script type='text/javascript'>

//----------------------------------------------------------------------
// CLAROLINE
//----------------------------------------------------------------------
// Copyright (c) 2001-2005 Universite catholique de Louvain (UCL)
//----------------------------------------------------------------------
// This program is under the terms of the GENERAL PUBLIC LICENSE (GPL)
// as published by the FREE SOFTWARE FOUNDATION. The GPL is available
// through the world-wide-web at http://www.gnu.org/copyleft/gpl.html
//----------------------------------------------------------------------
// Authors: see 'credits' file
//----------------------------------------------------------------------

        /**
         *	lib of Javascript function
         *
         *	@author Fallier Renaud (captren@gmail.com)
         **/

         /*-------------------------
                 variable
         ------------------------*/

        var shopping_cart = new Array();
        var serv_add = new Array();
        var serv_del = new Array();

        var servAdd = 0;
        var servDel = 0;


       /*----------------------------
                   function
        ---------------------------*/

       /**
        *  display the navigator of the linker
        *
        * @param $CRL string a crl
        **/
        function display_navigator(crl)
        {
        	show_div('navbox');
            var nav = new navigatorjpspan(tool_bar_handler);
            nav.gettoolbar(crl);
            var nav = new navigatorjpspan(resource_handler);
            nav.getresource(crl);

            display_shopping_cart();
        }

       /**
        *  init the shoppingCart wicht the crl in dB
        *
        * @param $CRL string a crl
        **/
        function init_shopping_cart()
        {
        	if(localcrl != false )
        	{
        		var nav = new navigatorjpspan(resource_db_handler);
            	nav.getresourcedb(localcrl);
            }
            else
            {
            	display_shopping_cart();
            }
        }

       /**
        *  display the other courses of the teacher
        *
        **/
        function display_other_course()
        {
        	var nav = new navigatorjpspan(other_course_handler);
           	nav.getothercourse();
        }

        /**
        *  display the poublic courses of the teacher
        *
        **/
        function display_public_course()
        {
        	var nav = new navigatorjpspan(public_course_handler);
           	nav.getpubliccourses();
        }

	   /**
        *  display the shoppingCart
        *
        **/
        function display_shopping_cart()
        {
            if( shopping_cart.length > 0 )
            {
                clear('shoppingCart');

                print('shoppingCart','\n'+lang_attachements+'\n');

                var line = '<table style=\"border: 0px; width: 100%; font-size: 80%\" >\n';

                for( var i = 0; i < shopping_cart.length; i++)
                {
                    var crl = shopping_cart[i]["crl"];
                    var title = shopping_cart[i]["title"];
                    line += '<tr><td>' + title + '</td>\n';
                    line += '<td>&nbsp;<a href=\"http://claroline.net\" class=\"claroCmd\"  onclick=\"detach(\''+crl+'\');return false;\">';
                    line += '<img src=\"'+img_repository_web+'delete.gif\" border=\"0\" alt=\"'+lang_delete+'\" /></a>&nbsp;\n'+'</td></tr>\n';
                }

                line += '</table>\n';

                print('shoppingCart', line )
            }
            else
            {
                clear('shoppingCart');
            }
        }

        function addHiddenInputToForm( formid, name, value, index )
        {
            var oForm = document.getElementById( formid );
            oForm.innerHTML += '<input type="hidden" name="'+name+'['+index+']" value="'+value+'" />\n';
        }


       /**
        *  management the additon of crl in the shoppingCart
        *
        * @param crl
        * @param title
        **/
        function attach( crl , title )
        {
            if( (in_shopping_cart(crl)) == false )
            {
                addHiddenInputToForm( 'hiddenFields', 'itemList', crl, servAdd );
                addHiddenInputToForm( 'hiddenFields', 'titleList', title, servAdd );

                servAdd++;

            	var item = new Array();
            	item["crl"] = crl;
            	item["title"] = title;

                shopping_cart.push( item );
                serv_add.push( crl );

                if( (in_array( serv_del , crl )) == true )
                {
                	serv_del = array_delete( serv_del , crl );
                }
            }
            else
            {
                alert( lang_already_in_attachement_list.replace("%itemName","["+title+"]") );
            }

            display_shopping_cart();
        }

       /**
        *  management the suppression of crl in the shoppingCart
        *
        * @param crl
        * @param title
        **/
        function detach( crl )
        {
            addHiddenInputToForm( 'hiddenFields', 'servDel', crl, servDel );

            servDel++;

            if( shopping_cart.length > 0 )
            {
                shopping_cart = shopping_cart_delete( crl );

                if( (in_array( serv_add , crl )) == false  )
                {
                	serv_del.push( crl );
                }
                else
              	{
              		serv_add = array_delete( serv_add, crl );
              	}
            }

            display_shopping_cart();
        }

       /**
        *  valide the shoppingCart
        *
        **/
        function linker_confirm()
        {
        	/* if( linklistallreadysubmitted == false )
        	{
            	var nav = new navigatorjpspan(set_shopping_cart_handler);
            	nav.registerattachementlist(serv_add , serv_del );
            	linklistallreadysubmitted = true;
            } */
        }

        /**
        *  reset the shoppingCart
        *
        **/
        function reset_shopping_cart()
        {
            clear('shoppingCart');

            shopping_cart = new Array();
        }

        /**
        *  clear all div
        *
        **/
        function clear_all()
        {
            reset_shopping_cart();

            clear('nav');
            clear('toolBar');
        }


        /**
        *  show a button
        *
        * @param button
        **/
        function show_div(divid)
        {
        	var d = document.getElementById(divid);
        	d.style.display = 'block';
        }

       /**
        *  hide a button
        *
        * @param button
        **/
        function hide_div(divid)
        {
        	var d = document.getElementById(divid);
        	d.style.display = 'none';
        }

        /**
        *  when you click on cancel
        *
        **/
        function close_navigator()
        {
        	hide_div('navbox');
        	clear('nav');
            clear('toolBar');
        }

        var resource_db_handler =
        {
        	getresourcedb:function(result)
        	{
        		for( var i = 0; i < result.length; i++)
                {
                	var item = new Array();
            		item['crl'] = result[i]['crl'];
            		item['title'] = result[i]['title'];

                	shopping_cart.push( item );

                }
                display_shopping_cart();
        	}
        }


        var set_shopping_cart_handler =
        {
        	registerattachementlist:function(result)
        	{
        		if( result == true )
        		{
        			clear_all();
        		}
        		else
        		{
        			alert("session registration failed");
        		}
        	}
        }

        var tool_bar_handler =
        {
            gettoolbar:function(result)
            {
               var parent_crl = result['parent']['crl'];

               clear('toolBar');

               if( parent_crl )
               {
                    print('toolBar','<a href=\"http://claroline.net\" class=\"claroCmd\" onclick=\"display_navigator(\''+parent_crl+'\');return false;\"><img src=\"'+img_repository_web+'parent.gif\" border=\"0\" alt=\"\" />'+lang_up+'</a>');
               }

               else
               {
                   print('toolBar','<img src=\"'+img_repository_web+'parentdisabled.gif\" border=\"0\" alt=\"\" /><span class="claroCmdDisabled">'+lang_up+'</span>');
               }

               print('toolBar','<br />');
            }
        }

        var resource_handler =
        {
            getresource:function(result)
            {
                clear('nav');

                print('nav', '<table style="border: 0px none ; width: 100%;">\n');

                for(i=0; i<result.length; i++)
                {
                    var name = result[i]['name'];
                    var crl = result[i]['crl'];
                    var container = result[i]['container'];
                    var linkable = result[i]['linkable'];
                    var visible = result[i]['visible'];
                    var title = result[i]['title'];

                    var line = '';

                    line += '<tr><td>\n';

                    if( container )
                    {
                    	if( visible == false )
                    	{
                        	line += "<a href=\"http://claroline.net\" class=\"invisible\" onclick=\"display_navigator(\'"+crl+"\');return false;\">"+name+"</a>";
                        }
                        else
                        {
                        	line += "<a href=\"http://claroline.net\" onclick=\"display_navigator(\'"+crl+"\');return false;\">"+name+"</a>";
                        }
                    }
                    else
                    {
                    	if( visible == false )
                    	{
                    		line += '<span class="invisible">'+name+'</span>';
                    	}
                    	else
                    	{
                    		line += name;
                    	}
                    }

                    line += '</td><td>\n';

                    if( linkable == true && !container )
                    {
						line += "&nbsp;&nbsp;<a href=\"http://claroline.net\" class=\"claroCmd\" onclick=\"attach(\'"+crl+"\',\'"+title+"\');return false;\">["+lang_add+"]</a>";
                    }
                    else
                    {
                        line += '&nbsp;';
                    }

                    line += "</td></tr>\n";

                    print('nav',line);
                }
                print('nav','\n</table>\n');

            }
        }

  	   /**
	    *  print a message in a div
	    *
	    * @param div
	    * @param msg
	    **/
        function print(div,msg)
        {
           document.getElementById(div).innerHTML += msg;
        }

	   /**
	    *  clear a div
	    *
	    * @param div
	    **/
        function clear(div)
        {
        	document.getElementById(div).innerHTML = '';
        }

       /**
        *  check if the crl is in shoppingCart
        *
        * @param item a crl
        * @return boolean
        **/
        function in_shopping_cart( item )
    	{
        	for( var i = 0; i < shopping_cart.length; i++)
        	{
        	    if( shopping_cart[i]["crl"] == item )
        	    {
        	        return true;
        	    }
        	}

        	return false;
    	}

	   /**
		*  delete a crl in the shoppingCart
		*
		* @param item a crl
		* @return the shoppingCart without the item
		*/
        function shopping_cart_delete( item )
    	{
        	var temp = new Array();

        	for( var i = 0; i < shopping_cart.length; i++)
        	{
        	    if( shopping_cart[i]["crl"] != item )
        	    {
        		    temp.push( shopping_cart[i] );
        	    }
        	}

        	return temp;
    	}

    	/**
		*
		*
		* @param gap
		*
		*/
    	function delay(gap)
        {
            var then,now;

            then=new Date().getTime();
            now=then;

            while((now-then)<gap)
            {
                now=new Date().getTime();
            }
        }

        /**
		*
		*
		*/
        function prompt_for_external_link()
   		{
    		var url = prompt_for_url();
    		var crl = null;

    		if( url != null )
    		{
 				crl = coursecrl+"/CLEXT___/"+url;
 				crl = html_escape(crl);

 				attach( crl , url );
    		}
   		}

   		/**
		*
		* @param
		* @return
		*/
   		function html_escape(str)
   		{
     		encodedHtml = escape(str);
     		encodedHtml = encodedHtml.replace(/\//g,"%2F");
     		encodedHtml = encodedHtml.replace(/\?/g,"%3F");
     		encodedHtml = encodedHtml.replace(/=/g,"%3D");
      		encodedHtml = encodedHtml.replace(/&/g,"%26");
      		encodedHtml = encodedHtml.replace(/@/g,"%40");
      		return encodedHtml;
      	}

    	/**
		*
		* @param
		* @return
		*/
		function change_button(btn)
		{
			 clear('openCloseAttachment');

		     if ( btn == 'open' )
     		 {
         		print('openCloseAttachment','<a href="#btn" name="btn" onclick="change_button(\'close\');return false;">'+lang_linker_close+'</a>');
         		display_navigator();
     		 }
     		 else if ( btn == 'close' )
     		 {
        		 print('openCloseAttachment','<a href="#btn" name="btn" onclick="change_button(\'open\');return false;\">'+lang_linker_add_new_attachment+'</a>');
        		 close_navigator();
     		 }
     		 else
     		 {
         		alert( 'error: button ' + btn + ' not found' );
     		 }
		}

//</script>
