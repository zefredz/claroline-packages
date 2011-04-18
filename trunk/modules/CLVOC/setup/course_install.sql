CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_definitions` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  definition TEXT NOT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_dictionaries` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_texts` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL DEFAULT '',
  content TEXT NULL DEFAULT '',
  wordList TEXT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_text_dictionaries` (
  td_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  dictionaryId INT(11) NOT NULL,
  textId INT(11) NOT NULL,
  PRIMARY KEY(td_id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_words` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_word_definitions` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  dictionaryId INT(11) NOT NULL,
  definitionId INT(11) NOT NULL,
  wordId INT(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_dictionary_tree` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  parentId INT(11) NOT NULL DEFAULT 0,
  itemId INT(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_tags` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `__CL_COURSE__glossary_tags_entries` (
  id INT(11) NOT NULL AUTO_INCREMENT,
  entryId INT(11) NOT NULL,
  tagID INT(11) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=MyISAM;