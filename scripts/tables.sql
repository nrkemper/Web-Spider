USE int322_133b40;

DROP TABLE Keywords;
DROP TABLE URLS;

CREATE TABLE URLS(
  URLNo INT(9) NOT NULL auto_increment,
  URL CHAR(50) NOT NULL,
  Author CHAR(50) NOT NULL,
  Name CHAR(50) NOT NULL,
  Description TEXT(300) NOT NULL, 
  StoreDate CHAR(30) NOT NULL,
  CONSTRAINT url_pk PRIMARY KEY(URLNo),
  CONSTRAINT url_url_un UNIQUE(URL)
)ENGINE=InnoDB;

CREATE TABLE Keywords(
  KeywordNo INT(9) NOT NULL auto_increment,
  URLNo INT(9) NOT NULL,
  Keyword CHAR(30) NOT NULL,
  CONSTRAINT keywords_keywordno_urlno_pk PRIMARY KEY(KeywordNo, URLNo),
  CONSTRAINT keywords_keyword_un UNIQUE(KeywordNo, URLNo, Keyword),
  CONSTRAINT keywords_urlno_fk FOREIGN KEY(URLNo) 
  REFERENCES URLS(URLNo)
)ENGINE=InnoDB;
