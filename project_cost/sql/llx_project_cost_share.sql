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


CREATE TABLE llx_project_cost_share(
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(128) NOT NULL, 
	entity integer DEFAULT 1 NOT NULL, 
	label varchar(255) NOT NULL, 
	ratio_1 double(24,8) DEFAULT 1,
	ratio_2 double(24,8) DEFAULT 0, 
	ratio_3 double(24,8) DEFAULT 0,  
	ratio_4 double(24,8) DEFAULT 0, 
	ratio_5 double(24,8) DEFAULT 0, 
	description varchar(1024), 
	date_creation datetime NOT NULL, 
	tms timestamp NOT NULL, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	import_key varchar(14), 
	fk_lot_id integer DEFAULT NULL, 
	isgroup boolean DEFAULT 0,
        date_start datetime DEFAULT NULL,
        date_end datetime DEFAULT NULL,
        fk_project integer NOT NULL --FIXME.
) ENGINE=innodb;