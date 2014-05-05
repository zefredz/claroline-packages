ICPCRDR Module
==============

In addition of being a fully functionnal Podcast Reader course tool module
for Claroline, this module can be used as teaching material for writing a 
Claroline course tool module.

Most of the thing encountered when writing such a module are covered here 
except :

* course install and uninstall PHP scripts
* platform level install and uninstall scripts
* other types of modules (applet or admin tool)
* group tool module

For more information about the available API, check the Claroline API page at
http://api.claroline.net/

Here are some remarks about the ICPCRDR implementation and about module 
implementation in general

File Structure :
----------------

The file structure of the module is the following ([folder_name] represents a 
folder) :

[conf]
    [def]
        ICPCRDR.def.conf.inc.php :  this file describe the available 
                                    configuration options for the module
[css]                            :  this folder contains the css style sheets 
                                    of the module. Searched by CSSLoader
[img]                            :  contains the icons and images of our 
                                    module, searched by get_icon_url() 
[lang]                           :  the translation files for the module,
                                    searched by get_lang()
    lang_french.lang.php         :  the french translation file in which you 
                                    can find how to write a translation file
[lib]                            :  the libraries of the module, sercahed by
                                    From::module('ICPCRDR')->uses(...)
[mediaelementjs]                 :  this folder is not part of a standard 
                                    module file structure. It contains the 
                                    media player used to display audio and video
[setup]                          :  install and uninstall php and sql scripts
    course_install.sql           :  SQL script executed when installing the
                                    module in a course
    course_uninstall.sql         :  SQL script executed when removing the
                                    module from a course
[templates]                      :  the templates of our module, searched by
                                    the class ModuleTemplate
icon.png*                        :  the icon of the module in the tool and 
                                    module lists
index.php*                       :  the main entry point of our module. NB: The 
                                    entry point is mandatory but its name can 
                                    be something else than index.php or 
                                    entry.php. It only have to match the name
                                    of the entry point defined in the module
                                    manifest.
manifest.xml*                    :  the file that describes the module so it 
                                    can be installed and used in Claroline
proxy.php                        :  a simple HTTP proxy specific to our module

Files marked with * are mandatory in a module.

In addition, a claroline module can have the following folders :

[js]                             :  contains the javascript files of the 
                                    module, searched by JavascriptLoader

Module setup :
--------------

The [setup] folder can contain other files not in the module ICPCRDR module, 
here is the complete structure :

[setup]                          :  install and uninstall php and sql scripts
    course_install.sql           :  SQL script executed when installing the
                                    module in a course
    course_install.php           :  PHP script executed when installing the
                                    module in a course
    course_uninstall.sql         :  SQL script executed when removing the
                                    module from a course
    course_uninstall.php         :  PHP script executed when removing the
                                    module from a course
    install.sql                  :  SQL script executed when installing the
                                    module in a course
    install.php                  :  PHP script executed when installing the
                                    module in a course
    uninstall.sql                :  SQL script executed when removing the
                                    module from a course
    uninstall.php                :  PHP script executed when removing the
                                    module from a course

NB : when installing or uninstalling a module, the scripts execution sequence 
is the following (in both a course or the platform) :

Install:

    1. SQL install script
    2. PHP install script

Uninstall:

    1. PHP uninstall script
    2. SQL uninstall script

This has be been done to make sure the database is still available when 
executing the PHP scripts.

Access to the database :
------------------------

To access to the database, your module must use the Claroline database object
available through Claroline::getDatabse(). The Claroline database layer is 
defined in database/database.lib and is automatically loaded and initialize by 
the kernel. 

The API is described here : http://api.claroline.net/database_8lib_8php.html

NB : if you need to access another database located on another MySQL than the 
one used by Claroline, you can use the MySQL_Database_Connection class.

WARNING : DO NOT FORGET to escape or quote the variable your are using in your
SQL queries with the Database_Connection->escape (for numbers) and 
Database_Connection->quote (for strings) methods to avoid SQL injection issues.

Templates :
-----------

The templates are used to separate the view of our application from the 
controller (index.php) and the data represented by some classes in the lib
folder of our module (In our case PodcastCollection and PodcastParser).

* use ModuleTemplate class to access templates in a module templates folder
    CoreTemplates is for kernel templates in claroline/inc/templates
    PhpTemplate can also be used but requires the absolute complte path to
    the template file
* use get_lang() to get the translation for your strings
* use get_icon_url() to get the url of an icon by just providing its name
* use claro_htmlspecialchars when outputing untrusted strings to avoid XSS
* use $this->variableName to access assigned variables
* use alternative PHP syntax (with no brackets) 
    see http://php.net/manual/en/control-structures.alternative-syntax.php

Layout :
--------

You can use the layout classes from the kernel library display/layout.lib to 
combine two templates. This library can be imported in your module by using

FromKernel::uses('display/layout.lib');

In this module, we are using a two column template with a small column on the 
left for a menu and a large column on the right for the module main pannel. 
This layout class is the LeftMenuLayout class. There is also a RightMenuLayout 
available in the library.

LeftMenuLayout:
--------------------------------------------
|           |                              |
|  LEFT     |          MAIN                |
|  MENU     |          PANNEL              |
|           |                              |
|           |                              |
--------------------------------------------

RightMenuLayout:
--------------------------------------------
|                              |           |
|  MAIN                        |    RIGHT  |
|  PANNEL                      |    MENU   |
|                              |           |
|                              |           |
--------------------------------------------


Each column will contain the rendered output of a template or of a dialog box.

In ICPCRDR we are using the left menu to display the list of podcast :

--------------------------------------------
|           |                              |
|  PODCAST  |          MAIN                |
|  LIST     |          PANNEL              |
|           |                              |
|           |                              |
--------------------------------------------
