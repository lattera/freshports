CREATE TABLE auth_members (
  name varchar(150) DEFAULT '' NOT NULL,
  pass varchar(100) DEFAULT '' NOT NULL,
  PRIMARY KEY pri_key (name, pass)
);
