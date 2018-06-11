-- Advanced Project Cost
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
-- along with this program.  If not, see <http://www.gnu.org/licenses/>.

CREATE TABLE IF NOT EXISTS  llx_c_project_cost_type  (
   rowid integer NOT NULL  AUTO_INCREMENT PRIMARY KEY,
   label varchar(200) NOT NULL ,
   capex_ratio float DEFAULT 1,
   taxe_benefit_ratio float DEFAULT 1, 
   active integer NULL
)ENGINE=InnoDB;

