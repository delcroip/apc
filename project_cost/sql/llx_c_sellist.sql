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

CREATE TABLE IF NOT EXISTS  llx_c_sellist  (
   rowid integer NOT NULL  AUTO_INCREMENT PRIMARY KEY,
   ref varchar(50) not null,
   label varchar(200) NOT NULL ,
   sql_table varchar(50) DEFAULT 'user',
   sql_refid varchar(50) DEFAULT 'rowid',
   sql_fields varchar(100) DEFAULT 'label', 
   sql_join varchar(100) DEFAULT NULL,
   sql_where varchar(100) DEFAULT NULL,
   active integer NULL
)ENGINE=InnoDB;

