CREATE TABLE __CL_MAIN__SDKLANG_source_list (
 id INTEGER NOT NULL auto_increment,
 name VARCHAR(250) NOT NULL,
 path VARCHAR(250) NOT NULL,
 version VARCHAR(50) NOT NULL,
 type VARCHAR(50) NOT NULL,
 last_update DATETIME NOT NULL default '0000-00-00 00:00:00',
 PRIMARY KEY(id));

CREATE TABLE __CL_MAIN__SDKLANG_translation (
 id INTEGER NOT NULL auto_increment,
 language VARCHAR(250) NOT NULL,
 varName VARCHAR(250) BINARY NOT NULL,
 varContent VARCHAR(250) NOT NULL,
 varFullContent TEXT NOT NULL,
 sourceFile VARCHAR(250) NOT NULL,
 used tinyint(4) default 0,
 INDEX index_language (language,varName),
 INDEX index_content  (language,varContent),
 PRIMARY KEY(id));

CREATE TABLE __CL_MAIN__SDKLANG_to_translate (
 id INTEGER NOT NULL auto_increment,
 varName VARCHAR(250) BINARY NOT NULL,
 langFile VARCHAR(250) NOT NULL,
 sourceFile VARCHAR(250) NOT NULL,
 INDEX index_varName (varName),
 PRIMARY KEY(id));
