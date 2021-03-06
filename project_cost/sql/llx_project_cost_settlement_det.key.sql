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


-- BEGIN MODULEBUILDER INDEXES
ALTER TABLE llx_project_cost_settlement_det ADD INDEX idx_project_cost_settlement_det_rowid (rowid);
--ALTER TABLE llx_project_cost_settlement_line ADD INDEX idx_project_cost_settlement_ref (ref);
--ALTER TABLE llx_project_cost_settlement ADD INDEX idx_project_cost_settlement_entity (entity);
--ALTER TABLE llx_project_cost_settlement ADD INDEX idx_project_cost_settlement_status (status);
-- END MODULEBUILDER INDEXES

--ALTER TABLE llx_project_cost_line ADD UNIQUE INDEX uk_project_cost_line_fieldxyz(fieldx, fieldy);

--ALTER TABLE llx_project_cost_line ADD CONSTRAINT llx_project_cost_line_field_id FOREIGN KEY (fk_field) REFERENCES llx_myotherobject(rowid);
ALTER TABLE llx_project_cost_settlement_det ADD CONSTRAINT llx_project_cost_settlement_det_fk_settlement FOREIGN KEY (fk_settlement) REFERENCES llx_project_cost_settlement(rowid) ON DELETE CASCADE;
