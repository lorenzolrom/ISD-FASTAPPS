-- ISD-FASTAPPS
-- ITSM Core Extension
-- Database Initialization


-- INVENTORY

CREATE TABLE ITSM_Commodity(
	id INT(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(32) NOT NULL UNIQUE,
	name VARCHAR(64) NOT NULL,
	commodityType INT(11) NOT NULL,
	assetType INT(11) DEFAULT NULL,
	manufacturer TEXT NOT NULL,
	model TEXT NOT NULL,
	unitCost FLOAT(11,2) NOT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (commodityType) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (assetType) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_Warehouse (
	id INT(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(32) NOT NULL UNIQUE,
	name VARCHAR(64) NOT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	closed TINYINT(1) NOT NULL DEFAULT 0,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_Vendor(
	id INT(11) NOT NULL AUTO_INCREMENT,
	code VARCHAR(32) NOT NULL UNIQUE,
	name TEXT NOT NULL,
	streetAddress TEXT NOT NULL,
	city TEXT NOT NULL,
	state CHAR(2) NOT NULL,
	zipCode CHAR(5) NOT NULL,
	phone VARCHAR(20) NOT NULL,
	fax VARCHAR(20) DEFAULT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_PurchaseOrder(
	id INT(11) NOT NULL AUTO_INCREMENT,
	`number` INT(11) NOT NULL UNIQUE,
	orderDate DATE NOT NULL,
	warehouse INT(11) NOT NULL,
	vendor INT(11) NOT NULL,
	status INT(11) NOT NULL,
	notes TEXT DEFAULT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	sent TINYINT(1) NOT NULL DEFAULT 0,
	sendDate DATE DEFAULT NULL,
	received TINYINT(1) NOT NULL DEFAULT 0,
	receiveDate DATE DEFAULT NULL,
	canceled TINYINT(1) NOT NULL DEFAULT 0,
	cancelDate DATE DEFAULT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (vendor) REFERENCES ITSM_Vendor(id) ON UPDATE CASCADE,
	FOREIGN KEY (warehouse) REFERENCES ITSM_Warehouse(id) ON UPDATE CASCADE,
	FOREIGN KEY (status) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_Asset(
	id INT(11) NOT NULL AUTO_INCREMENT,
	commodity INT(11) NOT NULL,
	warehouse INT(11) DEFAULT NULL,
	assetTag INT(11) NOT NULL UNIQUE,
	parent INT(11) DEFAULT NULL,
	location INT(11) DEFAULT NULL,
	serialNumber VARCHAR(64) DEFAULT NULL,
	manufactureDate VARCHAR(64) DEFAULT NULL,
	purchaseOrder INT(11) DEFAULT NULL,
	notes TEXT DEFAULT NULL,
	createDate DATE NOT NULL,
	discarded TINYINT(1) NOT NULL DEFAULT 0,
	discardDate DATE DEFAULT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	verified TINYINT(1) NOT NULL DEFAULT 0,
	verifyDate DATE DEFAULT NULL,
	verifyUser INT(11) DEFAULT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (location) REFERENCES FacilitiesCore_Location(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (commodity) REFERENCES ITSM_Commodity(id) ON UPDATE CASCADE,
	FOREIGN KEY (warehouse) REFERENCES ITSM_Warehouse(id) ON UPDATE CASCADE,
	FOREIGN KEY (purchaseOrder) REFERENCES ITSM_PurchaseOrder(id) ON UPDATE CASCADE,
	FOREIGN KEY (verifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (parent) REFERENCES ITSM_Asset(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE ITSM_ReturnOrder (
	id INT(11) NOT NULL AUTO_INCREMENT,
	`number` INT(11) NOT NULL UNIQUE,
	`type` INT(11) NOT NULL,
	vendorRMA TEXT DEFAULT NULL,
	orderDate DATE NOT NULL,
	vendor INT(11) NOT NULL,
	status INT(11) NOT NULL,
	notes TEXT DEFAULT NULL,
	warehouse INT(11) NOT NULL,
	sent TINYINT(1) NOT NULL DEFAULT 0,
	sendDate DATE DEFAULT NULL,
	received TINYINT(1) NOT NULL DEFAULT 0,
	receiveDate DATE DEFAULT NULL,
	canceled TINYINT(1) NOT NULL DEFAULT 0,
	cancelDate DATE DEFAULT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	lastModifyDate DATE NOT NULL,
	lastModifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (vendor) REFERENCES ITSM_Vendor(id) ON UPDATE CASCADE,
	FOREIGN KEY (warehouse) REFERENCES ITSM_Warehouse(id) ON UPDATE CASCADE,
	FOREIGN KEY (status) REFERENCES Attribute(id) ON UPDATE CASCADE,
	FOREIGN KEY (lastModifyUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (`type`) REFERENCES Attribute(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_ReturnOrder_Asset(
	id INT(11) NOT NULL AUTO_INCREMENT,
	returnOrder INT(11) NOT NULL,
	asset INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (returnOrder) REFERENCES ITSM_ReturnOrder(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (asset) REFERENCES ITSM_Asset(id) ON UPDATE CASCADE
);

CREATE TABLE ITSM_ReturnOrder_CostItem(
	id INT(11) NOT NULL AUTO_INCREMENT,
	returnOrder INT(11) NOT NULL,
	cost FLOAT(11,2) NOT NULL,
	notes TEXT DEFAULT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (returnOrder) REFERENCES ITSM_ReturnOrder(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ITSM_PurchaseOrder_Commodity(
	id INT(11) NOT NULL AUTO_INCREMENT,
	purchaseOrder INT(11) NOT NULL,
	commodity INT(11) NOT NULL,
	quantity INT(11) NOT NULL,
	unitCost FLOAT (11,2) NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (purchaseOrder) REFERENCES ITSM_PurchaseOrder(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (commodity) REFERENCES ITSM_Commodity(id) ON UPDATE CASCADE	
);

CREATE TABLE ITSM_PurchaseOrder_CostItem(
	id INT(11) NOT NULL AUTO_INCREMENT,
	purchaseOrder INT(11) NOT NULL,
	cost FLOAT(11,2) NOT NULL,
	notes TEXT DEFAULT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (purchaseOrder) REFERENCES ITSM_PurchaseOrder(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE ITSM_Asset_Worksheet(
	asset INT(11) NOT NULL,
	PRIMARY KEY(asset),
	FOREIGN KEY (asset) REFERENCES ITSM_Asset(id) ON UPDATE CASCADE
);

-- HOSTS

CREATE TABLE ITSM_Host (
	id INT(11) NOT NULL AUTO_INCREMENT,
	asset INT(11) NOT NULL,
	ipAddress VARCHAR(39) NOT NULL UNIQUE,
	macAddress CHAR(17) NOT NULL UNIQUE,
	notes TEXT DEFAULT NULL,
	systemName VARCHAR(64) NOT NULL,
	systemCPU VARCHAR(64) DEFAULT NULL,
	systemRAM VARCHAR(64) DEFAULT NULL,
	systemOS VARCHAR(64) DEFAULT NULL,
	systemDomain VARCHAR(64) DEFAULT NULL,
	createDate DATE NOT NULL,
	createUser INT(11) NOT NULL,
	modifyDate DATE NOT NULL,
	modifyUser INT(11) NOT NULL,
	PRIMARY KEY (id),
	FOREIGN KEY (asset) REFERENCES ITSM_Asset(id) ON UPDATE CASCADE,
	FOREIGN KEY (createUser) REFERENCES User(id) ON UPDATE CASCADE,
	FOREIGN KEY (modifyUser) REFERENCES User(id) ON UPDATE CASCADE
);

-- Attributes

INSERT INTO Attribute (extension, `type`, code, name) VALUES
	('itsm', 'coty', 'mate', 'Materials'),
	('itsm', 'coty', 'equi', 'Equipment'),
	('itsm', 'coty', 'asse', 'Asset'),
	('itsm', 'post', 'rdts', 'Ready To Send'),
	('itsm', 'post', 'sent', 'Sent'),
	('itsm', 'post', 'cncl', 'Canceled'),
	('itsm', 'post', 'rcvp', 'Received In Part'),
	('itsm', 'post', 'rcvf', 'Received In Full'),
	('itsm', 'roty', 'warr', 'Warranty'),
	('itsm', 'roty', 'repa', 'Repair'),
	('itsm', 'roty', 'trad', 'Trade-In'),
	('itsm', 'rost', 'rdts', 'Ready To Send'),
	('itsm', 'rost', 'sent', 'Sent'),
	('itsm', 'rost', 'cncl', 'Canceled'),
	('itsm', 'rost', 'rcvd', 'Received');

-- Permissions

INSERT INTO Permission VALUES
	('itsmcore_inventory-use'),
	('itsmcore_inventory-assets'),
	('itsmcore_inventory-assets-write'),
	('itsmcore_inventory-commodities'),
	('itsmcore_inventory-purchaseorders'),
	('itsmcore_inventory-returns'),
	('itsmcore_inventory-settings'),
	('itsmcore_devices-use'),
	('itsmcore_devices-hosts'),
	('itsmcore_devices-hosts-write');

-- Pages

INSERT INTO Page (extension, title, url, permission, type) VALUES
	('itsmcore', 'Inventory', 'inventory', 'itsmcore_inventory-use', 'sect'),
	('itsmcore', 'Assets', 'inventory/assets', 'itsmcore_inventory-assets', 'page'),
	('itsmcore', 'Asset A.P.I.', 'inventory/assets/api', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'View Asset', 'inventory/assets/view', 'itsmcore_inventory-assets', 'page'),
	('itsmcore', 'Edit Asset', 'inventory/assets/edit', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Discard Asset', 'inventory/assets/discard', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Verify Asset', 'inventory/assets/verify', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Link Asset', 'inventory/assets/link', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Asset Location', 'inventory/assets/location', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Asset Warehouse', 'inventory/assets/warehouse', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Asset Worksheet', 'inventory/assets/worksheet', 'itsmcore_inventory-assets-write', 'page'),
	('itsmcore', 'Commodities', 'inventory/commodities', 'itsmcore_inventory-commodities', 'page'),
	('itsmcore', 'New Commodity', 'inventory/commodities/new', 'itsmcore_inventory-commodities', 'page'),
	('itsmcore', 'View Commodity', 'inventory/commodities/view', 'itsmcore_inventory-commodities', 'page'),
	('itsmcore', 'Edit Commodity', 'inventory/commodities/edit', 'itsmcore_inventory-commodities', 'page'),
	('itsmcore', 'Delete Commodity', 'inventory/commodities/delete', 'itsmcore_inventory-commodities', 'page'),
	('itsmcore', 'Purchase Orders', 'inventory/purchaseorders', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'New Purchase Order', 'inventory/purchaseorders/new', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'View Purchase Order', 'inventory/purchaseorders/view', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'Edit Purchase Order', 'inventory/purchaseorders/edit', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'Delete Purchase Order', 'inventory/purchaseorders/delete', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'Purchase Order A.P.I.', 'inventory/purchaseorders/api', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'P.O. Commodities', 'inventory/purchaseorders/commodities', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'P.O. Cost Items', 'inventory/purchaseorders/costitems', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'Process Purchase Order', 'inventory/purchaseorders/process', 'itsmcore_inventory-purchaseorders', 'page'),
	('itsmcore', 'Returns', 'inventory/returns', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'New Return Order', 'inventory/returns/new', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'View Return Order', 'inventory/returns/view', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'Edit Return Order', 'inventory/returns/edit', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'Delete Return Order', 'inventory/returns/delete', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'Return Order A.P.I.', 'inventory/returns/api', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'R.O. Assets', 'inventory/returns/assets', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'R.O. Cost Items', 'inventory/returns/costitems', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'Process Return Order', 'inventory/returns/process', 'itsmcore_inventory-returns', 'page'),
	('itsmcore', 'Settings', 'inventory/settings', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Asset Types', 'inventory/settings/assettypes', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'New Asset Type', 'inventory/settings/assettypes/new', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Edit Asset Type', 'inventory/settings/assettypes/edit', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Delete Asset Type', 'inventory/settings/assettypes/delete', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Warehouses', 'inventory/settings/warehouses', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'New Warehouse', 'inventory/settings/warehouses/new', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'View Warehouse', 'inventory/settings/warehouses/view', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Edit Warehouse', 'inventory/settings/warehouses/edit', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Close Warehouse', 'inventory/settings/warehouses/close', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Vendors', 'inventory/settings/vendors', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'New Vendor', 'inventory/settings/vendors/new', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'View Vendor', 'inventory/settings/vendors/view', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Edit Vendor', 'inventory/settings/vendors/edit', 'itsmcore_inventory-settings', 'page'),
	('itsmcore', 'Devices', 'devices', 'itsmcore_devices-use', 'sect'),
	('itsmcore', 'Hosts', 'devices/hosts', 'itsmcore_devices-hosts', 'page'),
	('itsmcore', 'View Host', 'devices/hosts/view', 'itsmcore_devices-hosts', 'page'),
	('itsmcore', 'New Host', 'devices/hosts/new', 'itsmcore_devices-hosts-write', 'page'),
	('itsmcore', 'Edit Host', 'devices/hosts/edit', 'itsmcore_devices-hosts-write', 'page'),
	('itsmcore', 'Delete Host', 'devices/hosts/delete', 'itsmcore_devices-hosts-write', 'page');

-- Page Parents

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory') AS x) WHERE url = 'inventory/assets';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/discard';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/verify';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/warehouse';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/location';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/worksheet';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/assets') AS x) WHERE url = 'inventory/assets/link';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory') AS x) WHERE url = 'inventory/commodities';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/commodities') AS x) WHERE url = 'inventory/commodities/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/commodities') AS x) WHERE url = 'inventory/commodities/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/commodities') AS x) WHERE url = 'inventory/commodities/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/commodities') AS x) WHERE url = 'inventory/commodities/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory') AS x) WHERE url = 'inventory/purchaseorders';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/delete';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/commodities';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/costitems';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/purchaseorders') AS x) WHERE url = 'inventory/purchaseorders/process';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory') AS x) WHERE url = 'inventory/returns';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/api';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/delete';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/assets';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/costitems';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/returns') AS x) WHERE url = 'inventory/returns/process';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory') AS x) WHERE url = 'inventory/settings';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings') AS x) WHERE url = 'inventory/settings/assettypes';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings') AS x) WHERE url = 'inventory/settings/warehouses';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings') AS x) WHERE url = 'inventory/settings/vendors';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/assettypes') AS x) WHERE url = 'inventory/settings/assettypes/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/assettypes') AS x) WHERE url = 'inventory/settings/assettypes/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/assettypes') AS x) WHERE url = 'inventory/settings/assettypes/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/warehouses') AS x) WHERE url = 'inventory/settings/warehouses/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/warehouses') AS x) WHERE url = 'inventory/settings/warehouses/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/warehouses') AS x) WHERE url = 'inventory/settings/warehouses/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/warehouses') AS x) WHERE url = 'inventory/settings/warehouses/close';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/vendors') AS x) WHERE url = 'inventory/settings/vendors/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/vendors') AS x) WHERE url = 'inventory/settings/vendors/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/vendors') AS x) WHERE url = 'inventory/settings/vendors/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'inventory/settings/vendors') AS x) WHERE url = 'inventory/settings/vendors/delete';

UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'devices') AS x) WHERE url = 'devices/hosts';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'devices/hosts') AS x) WHERE url = 'devices/hosts/view';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'devices/hosts') AS x) WHERE url = 'devices/hosts/new';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'devices/hosts') AS x) WHERE url = 'devices/hosts/edit';
UPDATE Page SET parent = (SELECT id FROM (SELECT id FROM Page WHERE url = 'devices/hosts') AS x) WHERE url = 'devices/hosts/delete';