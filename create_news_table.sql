CREATE TABLE IF NOT EXISTS news (
  project_id integer NOT NULL DEFAULT 0,
  newsid SERIAL PRIMARY KEY,
  subject varchar(100) NOT NULL DEFAULT '',
  body text,
  lastmodified varchar(19) NOT NULL DEFAULT '',
  poster varchar(20) NOT NULL DEFAULT '',
  deleted char(1) NOT NULL DEFAULT 'N'
);
