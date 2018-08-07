<?php
/* 
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       dev/spreads/spread_page.php
 *		\ingroup    project_cost othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-05-27 19:29
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
require_once 'class/spread.class.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/spread.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
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
$langs->load("spread@project_cost");

// Get parameter
$id			= GETPOST('id','int');
$ref                    = GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage,alpha');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
$token= GETPOST('token','alpha');
$projectid=GETPOST('Projectid', 'int');
//// Get parameters








 // uncomment to avoid resubmision
//if(isset( $_SESSION['Projectcostspread_class'][$token]))
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
$object=new Projectcostspread($db);
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

//if the action concern the sub then the parent must be in viewmode
if(preg_match('/^sub/',$action) )$action=($id>0)?'view':'create';

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
$error=0;
if ($cancel){
        ProjectcostspreadReloadPage($backtopage,$projectid,$id,$ref);
}else if (($action == 'add') || ($action == 'update' && ($id>0 || !empty($ref))))
{
    //block resubmit
    if(empty($token) || (!isset($_SESSION['Projectcostspread_'.$token]))){
            setEventMessage('WrongTimeStamp_requestNotExpected', 'errors');
            $action=($action=='add')?'create':'view';
    }
    //retrive the data
    $object->ref=GETPOST('Ref');
    $object->label=GETPOST('Label');
    $object->ratio=GETPOST('Ratio');
    $object->soc=GETPOST('Soc');
    $object->description=GETPOST('Description');
    $object->user_creat=GETPOST('Usercreat');
    $object->import_key=GETPOST('Importkey');
    $object->status=GETPOST('Status');
    $object->c_sellist=GETPOST('Csellist');
    $object->sellist_selected_id=GETPOST('Sellistselectedid');
    $object->isgroup=GETPOST('Isgroup');
    $object->project=$projectid;
    $object->date_start=dol_mktime(0, 0, 0,GETPOST('Datestartmonth'),GETPOST('Datestartday'),GETPOST('Datestartyear'));
    $object->date_end=dol_mktime(0, 0, 0,GETPOST('Dateendmonth'),GETPOST('Dateendday'),GETPOST('Dateendyear'));


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
            unset($_SESSION['Projectcostspread_'.$token]);
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
            // remove the token
               unset($_SESSION['Projectcostspread_'.$token]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               ProjectcostspreadReloadPage($backtopage,$projectid,$result,'');

        }else
        {
                // Creation KO
                if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
                else  setEventMessage('RecordNotSucessfullyCreated', 'errors');
                $action='create';
        }                            
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
            ProjectcostspreadReloadPage($backtopage,$projectid, 0, '');
         break;


          
 }             
//Removing the token array so the order can't be submitted two times
if(isset( $_SESSION['Projectcostspread_'.$token]))
{
    unset($_SESSION['Projectcostspread_'.$token]);
}
if(($action == 'create') || ($action == 'edit' && ($id>0 || !empty($ref)))){
    $token=getToken();
    $_SESSION['Projectcostspread_'.$token]=array();
    $_SESSION['Projectcostspread_'.$token]['action']=$action;
            
}
if($object->id && $object->isgroup){
    //include 'spreadmember_list.action.tpl.php';
}
/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectcostspread','');
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
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostspread'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
    case 'view':
    {
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
         dol_fiche_head($headProject, 'stakeholders', $langs->trans("Project"), 0, 'project');
        // tabs
        if($edit==0 && $new==0){ //show tabs
            $head=ProjectcostspreadPrepareHead($object);
            dol_fiche_head($head,'card',$langs->trans('Projectcostspread'),0,'project_cost@project_cost');            
        }else{
            print_fiche_titre($langs->trans('Projectcostspread'));
        }

	print '<br>';
        if($edit==1){
            if($new==1){
                print '<form method="POST" action="'.$PHP_SELF.'?action=add&Projectid='.$projectid.'">';
                
            }else{
                print '<form method="POST" action="'.$PHP_SELF.'?action=update&id='.$id.'&Projectid='.$projectid.'">';
            }
            print '<input type="hidden" name="Projectid" value="'.$projectid.'">';            
            print '<input type="hidden" name="token" value="'.$token.'">';
            print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

        }else {// show the nav bar
            $basedurl=dol_buildpath("/project_cost/spread_list.php", 1).'?Projectid='.$projectid;
            $linkback = '<a href="'.$basedurl.(! empty($socid)?'&socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
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

// show the field ratio

		print '<td>'.$langs->trans('Ratio').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->ratio.'" name="Ratio">';
		}else{
			print $object->ratio;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field soc

		print '<td>'.$langs->trans('Soc').' </td><td>';
		if($edit==1){
		print select_generic('societe','rowid','Soc','nom','',$object->soc);
		}else{
		print print_generic('societe','rowid',$object->soc,'nom','');
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




// show the field status

		print '<td class="fieldrequired">'.$langs->trans('Status').' </td><td>';
		if($edit==1){
                    global $arrayStatus;
			print $form->selectarray('Status',$arrayStatus,$object->status);
                        }else{
			print $object->getLibStatut(2);
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";
if($conf->global->PROJECT_COST_ATTACHED_ITEM){
// show the field c_sellist

		print '<td>'.$langs->trans('Csellist').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->c_sellist.'" name="Csellist">';
		}else{
			print $object->c_sellist;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field sellist_selected_id

		print '<td>'.$langs->trans('Sellistselectedid').' </td><td>';
		if($edit==1){
                    print '<input type="text" value="'.$object->c_sellist.'" name="Sellistselected">';

		}else{
		print $object->sellist_selected_id;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";
}
// show the field isgroup

		print '<td>'.$langs->trans('Isgroup').' </td><td>';
		if($edit==1){
			print '<input type="checkbox" value="1" name="Isgroup" '.($object->isgroup?'checked':'').'>';
		}else{
			print '<input type="checkbox" name="Isgroup" disabled '.($object->isgroup?'checked':'').' >';
		}
		print "</td>";
		print "\n</tr>\n";
// show the field date_start
                print "<tr>\n";
		print '<td>'.$langs->trans('Datestart').' </td><td>';
		if($edit==1){
		if($new==1){
			print $form->select_date(-1,'Datestart');
		}else{
			print $form->select_date($object->date_start,'Datestart');
		}
		}else{
			print dol_print_date($object->date_start,'day');
		}
		print "</td>";
		print "\n</tr>\n";
		

// show the field date_end
                print "<tr>\n";
		print '<td>'.$langs->trans('Dateend').' </td><td>';
		if($edit==1){
		if($new==1){
			print $form->select_date(-1,'Dateend');
		}else{
			print $form->select_date($object->date_end,'Dateend');
		}
		}else{
			print dol_print_date($object->date_end,'day');
		}
		print "</td>";
		print "\n</tr>\n";
		//print "<td></td></tr>\n";

        

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
                //if($user->rights->Projectcostspread->edit)
                //{
                    print '<a href="'.$PHP_SELF.'?id='.$id.'&action=edit&Projectid='.$projectid.'" class="butAction">'.$langs->trans('Update').'</a>';
                //}
                
                //if ($user->rights->Projectcostspread->delete)
                //{
                    print '<a class="butActionDelete" href="'.$PHP_SELF.'?id='.$id.'&action=delete&Projectid='.$projectid.'">'.$langs->trans('Delete').'</a>';
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
        print_fiche_titre($langs->trans('Projectcostspread'));
        $head=ProjectcostspreadPrepareHead($object);
        dol_fiche_head($head,'info',$langs->trans("Projectcostspread"),0,'project_cost@project_cost');            
        print '<table width="100%"><tr><td>';
        dol_print_object_info($object);
        print '</td></tr></table>';
        print '</div>';
        break;

    case 'delete':
        if( ($id>0 || $ref!='')){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostspread'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background        
        }
}
dol_fiche_end();

if($object->id && $object->isgroup){
   // include 'spreadmember_list.view.tpl.php';
    include 'spreadmember_list.php';
}
dol_fiche_end();
// End of page
llxFooter();
$db->close();
