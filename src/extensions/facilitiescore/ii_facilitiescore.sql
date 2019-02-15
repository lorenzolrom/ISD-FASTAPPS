-- ISD-FASTAPPS
-- FACILITIES CORE EXTENSION
-- Database Initialization

CREATE TABLE FacilitiesCore_Building(
	id INT(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(32) NOT NULL UNIQUE,
	name VARCHAR(64) NOT NULL,
	streetAddress TEXT NOT NULL,
	city TEXT NOT NULL,
	state CHAR(2) NOT NULL,
	zipCode CHAR(5) NOT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	picturePath TEXT DEFAULT NULL,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	PRIMARY KEY (id)
);

CREATE TABLE FacilitiesCore_Location(
	id INT(11) NOT NULL AUTO_INCREMENT,
	building INT(11) NOT NULL,
	code VARCHAR(32) NOT NULL,
	name VARCHAR(64),
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	picturePath TEXT DEFAULT NULL,
	UNIQUE KEY building_code (building, code),
	FOREIGN KEY (building) REFERENCES FacilitiesCore_Building(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	PRIMARY KEY (id)
);

-- Permissions

INSERT INTO Permission VALUES ('facilitiescore_facilities-use');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('facilitiescore', 'Facilities', 'facilities', 'facilitiescore_facilities-use', 'sect'),
	('facilitiescore', 'Buildings', 'facilities/buildings', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Building A.P.I.', 'facilities/buildings/api', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'New Building', 'facilities/buildings/new', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Edit Building', 'facilities/buildings/edit', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'View Building', 'facilities/buildings/view', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Delete Building', 'facilities/buildings/delete', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Locations', 'facilities/locations', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'New Location', 'facilities/locations/new', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Edit Location', 'facilities/locations/edit', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'View Location', 'facilities/locations/view', 'facilitiescore_facilities-use', 'page'),
	('facilitiescore', 'Delete Location', 'facilities/locations/delete', 'facilitiescore_facilities-use', 'page');

-- Page parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities') AS x) WHERE url = 'facilities/buildings';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/buildings') AS x) WHERE url = 'facilities/buildings/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/buildings') AS x) WHERE url = 'facilities/buildings/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/buildings') AS x) WHERE url = 'facilities/buildings/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/buildings') AS x) WHERE url = 'facilities/buildings/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/buildings') AS x) WHERE url = 'facilities/buildings/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities') AS x) WHERE url = 'facilities/locations';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/locations') AS x) WHERE url = 'facilities/locations/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/locations') AS x) WHERE url = 'facilities/locations/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/locations') AS x) WHERE url = 'facilities/locations/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/locations') AS x) WHERE url = 'facilities/locations/delete';