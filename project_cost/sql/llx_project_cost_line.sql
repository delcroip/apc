-- Copyright (C) 2018     Patrick DELCROIX     <pmpdelcroix@gmail.com>
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see http://www.gnu.org/licenses/.


CREATE TABLE llx_project_cost_line(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(128) NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	label varchar(255), 
	amount double(24,8), 
        vat_amount double(24,8),
	description text, 
	date_creation datetime NOT NULL, 
        date_start datetime NOT NULL,
        date_end datetime DEFAULT NULL,
	tms timestamp NOT NULL, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	import_key varchar(14), 
	status integer NOT NULL, 
	fk_project integer NOT NULL, 
	fk_product integer DEFAULT NULL,, 
        product_quatity double(24,8) DEFAULT NULL, 
	fk_supplier_invoice integer DEFAULT NULL,, 
	c_project_cost_type integer not null, 
	fk_project_cost_spread integer not null
	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;