PRAGMA encoding = 'UTF-8';
PRAGMA page_size = 8192;  -- blob optimisation https://www.sqlite.org/intern-v-extern-blob.html
PRAGMA foreign_keys = ON;
-- The VACUUM command may change the ROWIDs of entries in any tables that do not have an explicit INTEGER PRIMARY KEY

DROP TABLE IF EXISTS anecdotes;
DROP TABLE IF EXISTS occurrences;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS tags_anecdotes;
DROP TABLE IF EXISTS keywords;
DROP TABLE IF EXISTS keywords_anecdotes;

CREATE TABLE anecdotes(
	id				TEXT,		-- xml:id de l'anecdote dans references.xml
	title			TEXT,		-- titre
	short_title		TEXT,
	note			TEXT,		-- note critique dans references.xml
	first_occ		TEXT,
	occ_n			INTEGER
);
CREATE UNIQUE INDEX anecdotes_id on anecdotes(id);

CREATE TABLE occurrences(
	id					TEXT,		-- id de l'occurrence
	anecdote			TEXT,		-- id de l'anecdote
	book				TEXT,		-- id du livre
	comment_before		TEXT,
	content				TEXT,		
	comment_after		TEXT,
	comment_before_txt	TEXT,
	content_txt			TEXT,
	comment_after_txt	TEXT	
);
CREATE UNIQUE INDEX occurrences_id on occurrences(id);
CREATE INDEX occurrences_content_txt on occurrences(content_txt);

CREATE TABLE books(
	id					TEXT,
	path				TEXT,
	title				TEXT,
	author				TEXT,
	lastname			TEXT,
	date				INTEGER,	-- pb pour 18**
	occ_n				INTEGER
);
CREATE UNIQUE INDEX books_id on books(id);

CREATE TABLE keywords(
	id					INTEGER,
	name				TEXT
);
CREATE UNIQUE INDEX keywords_id on keywords(id);

CREATE TABLE keywords_anecdotes(
	keyword			TEXT,
	anecdote		TEXT
);
CREATE INDEX keywords_anecdotes_keyword on keywords_anecdotes(keyword);
CREATE INDEX keywords_anecdotes_anecdote on keywords_anecdotes(anecdote);
