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

--INSERT INTO llx_project_cost_myobject VALUES (
--	1, 1, 'mydata'
--);
INSERT INTO `llx_c_project_cost_type` (`rowid`, `label`, `capex_ratio`, `taxe_benefit_ratio`, `active`, `ratio_2b_used`) VALUES
(1, 'Frais de chauffage', 0, 0.25, 1, 1),
(2, 'Frais de ménage', 0, 0, 1, 1),
(3, 'Frais de Syndic', 1, 1, 1, 1),
(4, 'Frais d''ascenseur', 0, 1, 1, 2),
(5, 'Travaux', 1, 1, 1, 1);