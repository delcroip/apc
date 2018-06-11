<?php
/*
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


    
    $devPath='';
    if(strpos($_SERVER['PHP_SELF'], 'dolibarr-min')>0) $devPath="/var/www/html/dolibarr-min";
    else if(strpos($_SERVER['PHP_SELF'], 'dolibarr-6.0.3')>0) $devPath="/var/www/html/dolibarr-6.0.3";
    else if(strpos($_SERVER['PHP_SELF'], 'dolibarr-pgsql')>0) $devPath="/var/www/html/dolibarr-pgsql";
    else $devPath="/var/www/html/dolibarr";
    if (file_exists($devPath."/htdocs/main.inc.php")) $res=@include $devPath."/htdocs/main.inc.php";     // Used on dev env only
    if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only  
