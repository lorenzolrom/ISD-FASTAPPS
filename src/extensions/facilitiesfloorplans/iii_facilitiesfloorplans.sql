-- ISD-FASTAPPS
-- FACILITIES-FLOORPLANS Extension
-- Database Initialization

CREATE TABLE Facilities_Floorplan(
	id INT(11) NOT NULL AUTO_INCREMENT,
	building INT(11) NOT NULL,
	`floor` VARCHAR(64) NOT NULL,
	imagePath TEXT NOT NULL,
	createUser INT(11) NOT NULL,
	createDate DATE NOT NULL,
	modifyUser INT(11) NOT NULL,
	modifyDate INT(11) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY building_floor (building, `floor`),
	FOREIGN KEY (building) REFERENCES FacilitiesCore_Building(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (modifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

-- Permissions

INSERT INTO Permission VALUES
	('facilities_floorplans');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('facilitiesfloorplans', 'Floorplans', 'facilities/floorplans', 'facilities_floorplans', 'page'),
	('facilitiesfloorplans', 'New Floorplan', 'facilities/floorplans/new', 'facilities_floorplans', 'page'),
	('facilitiesfloorplans', 'View Floorplan', 'facilities/floorplans/view', 'facilities_floorplans', 'page'),
	('facilitiesfloorplans', 'Edit Floorplan', 'facilities/floorplans/edit', 'facilities_floorplans', 'page'),
	('facilitiesfloorplans', 'Delete Floorplan', 'facilities/floorplans/delete', 'facilities_floorplans', 'page');

-- Page Parents
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities') AS x) WHERE url = 'facilities/floorplans';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/floorplans') AS x) WHERE url = 'facilities/floorplans/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/floorplans') AS x) WHERE url = 'facilities/floorplans/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/floorplans') AS x) WHERE url = 'facilities/floorplans/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'facilities/floorplans') AS x) WHERE url = 'facilities/floorplans/delete';