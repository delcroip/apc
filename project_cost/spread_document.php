<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018     Patrick DELCROIX     <pmpdelcroix@gmail.com>
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

/**
 *  \file       htdocs/modulebuilder/template/Projectcostspread_document.php
 *  \ingroup    project_cost
 *  \brief      Tab for documents linked to Projectcostspread
 */

if($_SERVER['SCRIPT_FILENAME'])include 'core/lib/includeMain.lib.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once 'class/spread.class.php';
require_once 'core/lib/spread.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
// Load traductions files requiredby by page
$langs->loadLangs(array("spread@project_cost","companies","other"));


$action=GETPOST('action','aZ09');
$confirm=GETPOST('confirm');
$id=(GETPOST('socid','int') ? GETPOST('socid','int') : GETPOST('id','int'));
$ref = GETPOST('ref', 'alpha');
$projectid=GETPOST('Projectid','int');
// Security check - Protection if external user
//if ($user->societe_id > 0) access_forbidden();
//if ($user->societe_id > 0) $socid = $user->societe_id;
//$result = restrictedArea($user, 'project_cost', $id);

$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;

// Initialize technical objects
$object=new Projectcostspread($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction=$conf->project_cost->dir_output . '/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('Projectcostspreaddocument'));     // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
$extralabels = $extrafields->fetch_name_optionals_label('Projectcostspread');

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php';  // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals

if($id<1)ProjectcostspreadReloadPage ($backtopage, $projectid, $id, $ref);
else if($projectid<1){
    $object->fetch($id);
    $projectid=$object->project;
}
//if ($id > 0 || ! empty($ref)) $upload_dir = $conf->sellyoursaas->multidir_output[$object->entity] . "/packages/" . dol_sanitizeFileName($object->id);
if ($id > 0 || ! empty($ref)) $upload_dir = $conf->sellyoursaas->multidir_output[$object->entity] . "/packages/" . dol_sanitizeFileName($object->ref);



/*
 * Actions
 */

include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';


/*
 * View
 */

$form = new Form($db);

$title=$langs->trans("Projectcostspread").' - '.$langs->trans("Files");
$help_url='';
//$help_url='EN:Module_Third_Parties|FR:Module_Tiers|ES:Empresas';
llxHeader('', $title, $help_url);
    $project= new Project($db);
    $project->fetch($projectid);
    $headProject=project_prepare_head($project);
    dol_fiche_head($headProject, 'stakeholders', $langs->trans("Project"), 0, 'project');

if ($object->id)
{
	/*
	 * Show tabs
	 */
	if (! empty($conf->notification->enabled)) $langs->load("mails");
	$head = ProjectcostspreadPrepareHead($object);

	dol_fiche_head($head, 'document', $langs->trans("Projectcostspread"), -1, 'spread@project_cost');


	// Construit liste des fichiers
	$filearray=dol_dir_list($upload_dir,"files",0,'','(\.meta|_preview.*\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
	$totalsize=0;
	foreach($filearray as $key => $file)
	{
		$totalsize+=$file['size'];
	}

	// Object card
	// ------------------------------------------------------------
	$linkback = '<a href="' .dol_buildpath('/project_cost/spread_list.php',1) . '?restore_lastsearch_values=1' . (! empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

	dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

    print '<div class="fichecenter">';

    print '<div class="underbanner clearboth"></div>';
	print '<table class="border centpercent">';

	// Number of files
	print '<tr><td class="titlefield">'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.count($filearray).'</td></tr>';

	// Total size
	print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

	print '</table>';

	print '</div>';

	dol_fiche_end();

	$modulepart = 'project_cost';
	//$permission = $user->rights->project_cost->create;
	$permission = 1;
	//$permtoedit = $user->rights->project_cost->create;
	$permtoedit = 1;
	$param = '&id=' . $object->id;

	//$relativepathwithnofile='Projectcostspread/' . dol_sanitizeFileName($object->id).'/';
	$relativepathwithnofile='Projectcostspread/' . dol_sanitizeFileName($object->ref).'/';

	include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
}
else
{
	accessforbidden('',0,0);
}


llxFooter();
$db->close();
