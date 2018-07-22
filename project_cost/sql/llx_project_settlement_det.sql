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


CREATE TABLE llx_project_settlement_det(
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
        fk_settlement integer NOT NULL,
        fk_project_cost_line integer NOT NULL,
        amount  double(24,8) not null, 
        capex_amount  double(24,8),
        vat_amount  double(24,8),
        taxe_benefit_amount  double(24,8),
        date_creation datetime NOT NULL, 
	date_modification timestamp NOT NULL, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	import_key varchar(14)
) ENGINE=innodb;