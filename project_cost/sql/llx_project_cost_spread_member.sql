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

CREATE TABLE IF NOT EXISTS  llx_project_cost_spread_member  (
    rowid integer NOT NULL  AUTO_INCREMENT PRIMARY KEY,
    group_id integer not null,
    member_id integer not null,
    date_creation datetime NOT NULL, 
    tms timestamp NOT NULL, 
    fk_user_creat integer NOT NULL, 
    fk_user_modif integer
)ENGINE=InnoDB;

