<?php
/* 
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
 * * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       dev/projectsettlements/settlement_page.php
 *		\ingroup    project_cost othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-07-21 21:29
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
include 'core/lib/includeMain.lib.php';
// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
//require_once 'lib/project_cost.lib.php';
require_once 'class/projectsettlement.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/projectsettlement.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
// include conditionnally of the dolibarr version
//if((version_compare(DOL_VERSION, "3.8", "<"))){
dol_include_once('/project_cost/lib/project_cost.lib.php');
//}
dol_include_once('/core/class/html.formother.class.php');
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("projectsettlement@project_cost");

// Get parameter
$id			= GETPOST('id','int');
$ref                    = GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
$tms= GETPOST('tms','alpha');
$projectid=GETPOST('Projectid','int');
//// Get parameters
/*
$sortfield = GETPOST('sortfield','alpha'); 
$sortorder = GETPOST('sortorder','alpha')?GETPOST('sortorder','alpha'):'ASC';
$removefilter=isset($_POST["removefilter_x"]) || isset($_POST["removefilter"]);
//$applyfilter=isset($_POST["search_x"]) ;//|| isset($_POST["search"]);
if (!$removefilter )		// Both test must be present to be compatible with all browsers
{
    	$ls_ref= GETPOST('ls_ref','alpha');
	$ls_label= GETPOST('ls_label','alpha');
	$ls_project= GETPOST('ls_project','int');
	$ls_description= GETPOST('ls_description','alpha');
	$ls_date_settlement_month= GETPOST('ls_date_settlement_month','int');
	$ls_date_settlement_year= GETPOST('ls_date_settlement_year','int');
	$ls_import_key= GETPOST('ls_import_key','alpha');
	$ls_status= GETPOST('ls_status','int');

    
}
*/






 // uncomment to avoid resubmision
//if(isset( $_SESSION['settlement_class'][$tms]))
//{

 //   $cancel=TRUE;
 //  setEventMessages('Internal error, POST not exptected', null, 'errors');
//}



// Right Management
 /*
if ($user->societe_id > 0 || 
       (!$user->rights->project_cost->add && ($action=='add' || $action='create')) ||
       (!$user->rights->project_cost->view && ($action=='list' || $action='view')) ||
       (!$user->rights->project_cost->delete && ($action=='confirm_delete')) ||
       (!$user->rights->project_cost->edit && ($action=='edit' || $action='update')))
{
	accessforbidden();
}
*/

// create object and set id or ref if provided as parameter
$object=new Projectsettlement($db);
if($id>0)
{
    $object->id=$id; 
    $object->fetch($id);
    $ref=dol_sanitizeFileName($object->ref);
    if(empty($action))$action='view'; //  the doc handling part send back only the ID without actions
    if($projectid<1){
        $projectid=$object->project;
    }
}else if(!empty($ref))
{
    $object->ref=$ref; 
    $object->id=$id; 
    $object->fetch($id);
    $ref=dol_sanitizeFileName($object->ref);
        if($projectid<1){
        $projectid=$object->project;
    }

}else if (empty($projectid)){
    setEventMessage( $langs->trans('noProjectIdPresent').' id:'.$id,'errors');
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
$error=0;
if ($cancel){
        ProjectsettlementReloadPage($backtopage,$projectid,$id,$ref);
}else if (($action == 'add') || ($action == 'update' && ($id>0 || !empty($ref))))
{
    //block resubmit
    if(empty($tms) || (!isset($_SESSION['projectsettlement'][$tms]))){
            setEventMessage('WrongTimeStamp_requestNotExpected', 'errors');
            $action=($action=='add')?'create':'view';
    }
    //retrive the data
    		$object->ref=GETPOST('Ref');
		$object->label=GETPOST('Label');
		$object->project=GETPOST('Projectid');
		$object->description=GETPOST('Description');
		$object->date_settlement=dol_mktime(0, 0, 0,GETPOST('Datesettlementmonth'),GETPOST('Datesettlementday'),GETPOST('Datesettlementyear'));
		$object->import_key=GETPOST('Importkey');
		$object->status=GETPOST('Status');

    

// test here if the post data is valide
 /*
 if($object->prop1==0 || $object->prop2==0) 
 {
     if ($id>0 || $ref!='')
        $action='create';
     else
        $action='edit';
 }
  */
        
 }else if ($id==0 && $ref=='' && $action!='create') 
 {
     $action='create';
 }
 
 
  switch($action){		
    case 'update':
        $result=$object->update($user);
        if ($result > 0)
        {
            // Creation OK
            unset($_SESSION['projectsettlement'][$tms]);
            setEventMessage('RecordUpdated','mesgs');

        }
        else
        {
                // Creation KO
            if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
            else setEventMessage('RecordNotUpdated', 'errors');

        }
        $action='view';
    case 'delete':
        if(isset($_GET['urlfile'])) $action='deletefile';
    case 'view':
    case 'viewinfo':
    case 'edit':
        // fetch the object data if possible
        if ($id > 0 || !empty($ref) )
        {
            $result=$object->fetch($id,$ref);
            if ($result < 0){ 
                dol_print_error($db);
            }else { // fill the id & ref
                if(isset($object->id))$id = $object->id;
                if(isset($object->rowid))$id = $object->rowid;
                if(isset($object->ref))$ref = $object->ref;
            }

        }else
        {
            setEventMessage( $langs->trans('noIdPresent').' id:'.$id,'errors');
            $action='create';
        }
        break;
    case 'add':
        $result=$object->create($user);
        if ($result > 0)
        {
                // Creation OK
            // remove the tms
               unset($_SESSION['projectsettlement'][$tms]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               ProjectsettlementReloadPage($backtopage,$projectid,$result,'');

        }else
        {
                // Creation KO
                if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
                else  setEventMessage('RecordNotSucessfullyCreated', 'errors');
                $action='create';
        }                            
        break;
     case 'generate_det':
          $object->generateSettlementDet($user);
          ProjectsettlementReloadPage($backtopage,$projectid,$object->id,'');

          break;
     case 'confirm_delete':

            $result=($confirm=='yes')?$object->delete($user):0;
            if ($result > 0)
            {
                // Delete OK
                setEventMessage($langs->trans('RecordDeleted'), 'mesgs');
            }
            else
            {
                // Delete NOK
                if (! empty($object->errors)) setEventMessages(null,$object->errors,'errors');
                else setEventMessage('RecordNotDeleted','errors');
            }
            ProjectsettlementReloadPage($backtopage,$projectid, 0, '');
         break;


          
 }             
//Removing the tms array so the order can't be submitted two times
if(isset( $_SESSION['projectsettlement'][$tms]))
{
    unset($_SESSION['projectsettlement'][$tms]);
}
if(($action == 'create') || ($action == 'edit' && ($id>0 || !empty($ref)))){
    $tms=getToken();
    $_SESSION['projectsettlement'][$tms]=array();
    $_SESSION['projectsettlement'][$tms]['action']=$action;
            
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectsettlement','');
print "<div> <!-- module body-->";
$form=new Form($db);
$formother=new FormOther($db);
$fuser=new User($db);
// Put here content of your page

// Example : Adding jquery code
/*print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_needroot();
	});
});
</script>';*/

$edit=$new=0;
switch ($action) {
    case 'create':
        $new=1;
    case 'edit':
        $edit=1;
   case 'delete';
        if( $action=='delete' && ($id>0 || $ref!="")){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectsettlement'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
    case 'view':
    {
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
        dol_fiche_head($headProject, 'settlement', $langs->trans("Project"), 0, 'project');
        // tabs
        if($edit==0 && $new==0){ //show tabs
            $head=ProjectsettlementPrepareHead($object);
            dol_fiche_head($head,'card',$langs->trans('Projectsettlement'),0,'project_cost@project_cost');            
        }else{
            print_fiche_titre($langs->trans('Projectsettlement'));
        }

	print '<br>';
        if($edit==1){
            if($new==1){
                print '<form method="POST" action="'.$PHP_SELF.'?action=add&Projectid='.$projectid.'">';
            }else{
                print '<form method="POST" action="'.$PHP_SELF.'?action=update&id='.$id.'&Projectid='.$projectid.'">';
            }
                        
            print '<input type="hidden" name="tms" value="'.$tms.'">';
            print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

        }else {// show the nav bar
            $basedurl=dol_buildpath("/project_cost/settlement_list.php", 1).'?Projectid='.$projectid;
            $linkback = '<a href="'.$basedurl.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
            if(!isset($object->ref))//save ref if any
                $object->ref=$object->id;
            print $form->showrefnav($object, 'action=view&id', $linkback, 1, 'rowid', 'ref', '');
            //reloqd the ref

        }

	print '<table class="border centpercent">'."\n";

        
		print "<tr>\n";

// show the field ref

		print '<td class="fieldrequired">'.$langs->trans('Ref').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->ref.'" name="Ref">';
		}else{
			print $object->ref;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field label

		print '<td>'.$langs->trans('Label').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->label.'" name="Label">';
		}else{
			print $object->label;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";



// show the field description

		print '<td>'.$langs->trans('Description').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->description.'" name="Description">';
		}else{
			print $object->description;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field date_settlement

		print '<td class="fieldrequired">'.$langs->trans('Datesettlement').' </td><td>';
		if($edit==1){
		if($new==1){
			print $form->select_date(-1,'Datesettlement');
		}else{
			print $form->select_date($object->date_settlement,'Datesettlement');
		}
		}else{
			print dol_print_date($object->date_settlement,'day');
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";


// show the field status

		print '<td class="fieldrequired">'.$langs->trans('Status').' </td><td>';
		if($edit==1){
			print $object->selectLibStatut($form, 'Status');
		}else{
			print $object->getLibStatut(2);
		}
		print "</td>";
		print "\n</tr>\n";
		print "<td></td></tr>\n";

        

	print '</table>'."\n";
	print '<br>';
	print '<div class="center">';
        if($edit==1){
        if($new==1){
                print '<input type="submit" class="butAction" name="add" value="'.$langs->trans('Add').'">';
            }else{
                print '<input type="submit" name="update" value="'.$langs->trans('Update').'" class="butAction">';
            }
            print ' &nbsp; <input type="submit" class="butActionDelete" name="cancel" value="'.$langs->trans('Cancel').'"></div>';
            print '</form>';
        }else{
            $parameters=array();
            $reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
            if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

            if (empty($reshook))
            {
                print '<div class="tabsAction">';

                // Boutons d'actions
                //if($user->rights->Projectsettlement->edit)
                //{
                    print '<a href="'.$PHP_SELF.'?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Update').'</a>';
                    
                    print '<a href="'.$PHP_SELF.'?id='.$id.'&action=generate_det" class="butAction">'.$langs->trans('GenerateDet').'</a>';
                //}
                
                //if ($user->rights->Projectsettlement->delete)
                //{
                    print '<a class="butActionDelete" href="'.$PHP_SELF.'?id='.$id.'&action=delete">'.$langs->trans('Delete').'</a>';
                //}
                //else
                //{
                //    print '<a class="butActionRefused" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$langs->trans('Delete').'</a>';
                //}
                    
                print '</div>';
            }
        }
        break;
    }
        case 'viewinfo':
        print_fiche_titre($langs->trans('Projectsettlement'));
        $head=ProjectsettlementPrepareHead($object);
        dol_fiche_head($head,'info',$langs->trans("Projectsettlement"),0,'project_cost@project_cost');            
        print '<table width="100%"><tr><td>';
        dol_print_object_info($object);
        print '</td></tr></table>';
        print '</div>';
        break;

    case 'delete':
        if( ($id>0 || $ref!='')){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectsettlement'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background        
        }
}
dol_fiche_end();

// End of page
llxFooter();
$db->close();
