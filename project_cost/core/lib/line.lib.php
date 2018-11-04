<?php
/* 
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) Patrick Delcroix <pmpdelcroix@gmail.com>
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
 *   	\file       dev/lines/line_page.php
 *		\ingroup    project_cost othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-06-11 21:10
 */
$arrayStatus=array(0 => $langs->trans('Disabled'),1 => $langs->trans('Enabled'));
function ProjectcostlineReloadPage($backtopage,$projectid,$id,$ref){
        if (!empty($backtopage)){
            header("Location: ".$backtopage);            
        }else if (!empty($ref) ){
            header("Location: ".dol_buildpath("/project_cost/line_card.php", 1).'?action=view&Projectid='.$projectid.'&ref='.$ref);
        }else if ($id>0)
        {
           header("Location: ".dol_buildpath("/project_cost/line_card.php", 1).'?action=view&Projectid='.$projectid.'&id='.$id);
        }else{
           header("Location: ".dol_buildpath("/project_cost/line_list.php", 1).'?Projectid='.$projectid);

        }
    exit();
}
/**
 * Prepare array of tabs for Projectcostline
 *
 * @param	Projectcostline	$object		Projectcostline
 * @return 	array					Array of tabs
 */
function ProjectcostlinePrepareHead($object)
{
	global $db, $langs, $conf;

	$langs->load("project_cost@project_cost");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/project_cost/line_card.php", 1).'?id='.$object->id.'&Projectid='.$object->project;
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	if (isset($object->fields['note_public']) || isset($object->fields['note_private']))
	{
		$nbNote = 0;
		if (!empty($object->note_private)) $nbNote++;
		if (!empty($object->note_public)) $nbNote++;
		$head[$h][0] = dol_buildpath('/project_cost/line_note.php', 1).'?id='.$object->id.'&Projectid='.$object->project;
		$head[$h][1] = $langs->trans('Notes');
		if ($nbNote > 0) $head[$h][1].= ' <span class="badge">'.$nbNote.'</span>';
		$head[$h][2] = 'note';
		$h++;
	}

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
	$upload_dir = $conf->project_cost->dir_output . "/Projectcostline/" . dol_sanitizeFileName($object->ref);
	$nbFiles = count(dol_dir_list($upload_dir,'files',0,'','(\.meta|_preview.*\.png)$'));
	$nbLinks=Link::count($db, $object->element, $object->id);
	$head[$h][0] = dol_buildpath("/project_cost/line_document.php", 1).'?id='.$object->id.'&Projectid='.$object->project;
	$head[$h][1] = $langs->trans('Documents');
	if (($nbFiles+$nbLinks) > 0) $head[$h][1].= ' <span class="badge">'.($nbFiles+$nbLinks).'</span>';
	$head[$h][2] = 'document';
	$h++;

	//$head[$h][0] = dol_buildpath("/project_cost/line_agenda.php", 1).'?id='.$object->id.'&Projectid='.$object->project;
	//$head[$h][1] = $langs->trans("Events");
	//$head[$h][2] = 'agenda';
	//$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@project_cost:/project_cost/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@project_cost:/project_cost/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'Projectcostline@project_cost');

	return $head;
}
