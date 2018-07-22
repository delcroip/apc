<?php
/* 
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
require_once 'class/line.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/line.lib.php';
//require_once 'core/lib/project_cost.lib.php';
require_once 'core/lib/line.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
// include conditionnally of the dolibarr version
//if((version_compare(DOL_VERSION, "3.8", "<"))){
//dol_include_once('/project_cost/lib/project_cost.lib.php');
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
//}
dol_include_once('/core/class/html.formother.class.php');
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("line@project_cost");

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
	$ls_amount= GETPOST('ls_amount','int');
	$ls_description= GETPOST('ls_description','alpha');
	$ls_import_key= GETPOST('ls_import_key','alpha');
	$ls_status= GETPOST('ls_status','int');
	$ls_project= GETPOST('ls_project','int');
	$ls_product= GETPOST('ls_product','int');
	$ls_supplier_invoice= GETPOST('ls_supplier_invoice','int');
	$ls_c_project_cost_type= GETPOST('ls_c_project_cost_type','int');
	$ls_project_cost_spread= GETPOST('ls_project_cost_spread','int');

    
}
*/






 // uncomment to avoid resubmision
//if(isset( $_SESSION['line_class'][$tms]))
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
$object=new Projectcostline($db);
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

    //$upload_dir = $conf->project_cost->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'Projectcostline').$ref;
    


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
$error=0;
if ($cancel){
        ProjectcostlineReloadPage($backtopage,$projectid,$id,$ref);
}else if (($action == 'add') || ($action == 'update' && ($id>0 || !empty($ref))))
{
    //block resubmit
    if(empty($tms) || (!isset($_SESSION['Projectcostline_'.$tms]))){
            setEventMessage('WrongTimeStamp_requestNotExpected', 'errors');
            $action=($action=='add')?'create':'view';
    }
    //retrive the data
    $object->ref=GETPOST('Ref');
    $object->label=GETPOST('Label');
    $object->amount=GETPOST('Amount');
    $object->vat_amount=GETPOST('Vat_amount');
    $object->description=GETPOST('Description');

    $object->status=GETPOST('Status');
    $object->project=GETPOST('Projectid');
    $object->product=GETPOST('Product');
    $object->supplier_invoice=GETPOST('Supplierinvoice');
    $object->c_project_cost_type=GETPOST('Cprojectcosttype');
    $object->project_cost_spread=GETPOST('Projectcostspread');
    if($object->product<0)$object->product=null;
    if($object->supplier_invoice<0)$object->supplier_invoice=null;
    if($object->project_cost_spread<0)$object->project_cost_spread=null;
    

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
            unset($_SESSION['Projectcostline_'.$tms]);
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
               unset($_SESSION['Projectcostline_'.$tms]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               ProjectcostlineReloadPage($backtopage,$projectid,$result,'');

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
            ProjectcostlineReloadPage($backtopage,$projectid, 0, '');
         break;


          
 }             
//Removing the tms array so the order can't be submitted two times
if(isset( $_SESSION['Projectcostline_'.$tms]))
{
    unset($_SESSION['Projectcostline_'.$tms]);
}
if(($action == 'create') || ($action == 'edit' && ($id>0 || !empty($ref)))){
    $tms=getToken();
    $_SESSION['Projectcostline_'.$tms]=array();
    $_SESSION['Projectcostline_'.$tms]['action']=$action;
            
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectcostline','');
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
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostline'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
    case 'view':
    {
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
        dol_fiche_head($headProject, 'costs', $langs->trans("Project"), 0, 'project');
        // tabs
        if($edit==0 && $new==0){ //show tabs
            $head=ProjectcostlinePrepareHead($object);
            dol_fiche_head($head,'card',$langs->trans('Projectcostline'),0,'project_cost@project_cost');            
        }else{
            print_fiche_titre($langs->trans('Projectcostline'));
        }

	print '<br>';
        if($edit==1){
            if($new==1){
                print '<form method="POST" action="'.$PHP_SELF.'?action=add&Projectid='.$projectid.'">';
            }else{
                print '<form method="POST" action="'.$PHP_SELF.'?action=update&Projectid='.$projectid.'&id='.$id.'">';
            }
                        
            print '<input type="hidden" name="tms" value="'.$tms.'">';
            print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    //        print '<input type="hidden" name="Projectid" value="'.$projectid.'">';
        }else {// show the nav bar
            $basedurl=dol_buildpath("/project_cost/line_list.php", 1).'/line_list.php'.'?Projectid='.$projectid;;
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
// show the field c_project_cost_type

		print '<td class="fieldrequired">'.$langs->trans('Cprojectcosttype').' </td><td>';
		$sql_type=array('table'=> 'c_project_cost_type','keyfield'=> 'rowid','fields'=>'label', 'join' => '', 'where'=>'active=1','tail'=>'');
		$html_type=array('name'=>'Cprojectcosttype','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoices_type=null;
		if($edit==1){
		print select_sellist($sql_type,$html_type, $object->c_project_cost_type,$addChoices_type );
		}else{
		print_sellist($sql_type,$object->c_project_cost_type,'-');		
                }
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field project_cost_spread

		print '<td class="fieldrequired">'.$langs->trans('Projectcostspread').' </td><td>';
		$sql_project_cost_spread=array('table'=> 'project_cost_spread','keyfield'=> rowid,'fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
		$html_project_cost_spread=array('name'=>'Projectcostspread','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicesproject_cost_spread=null;
		if($edit==1){
		print select_sellist($sql_project_cost_spread,$html_project_cost_spread, $object->project_cost_spread,$addChoices_project_cost_spread );
		}else{
		print_sellist($sql_project_cost_spread,$object->project_cost_spread,'-');		}
		print "</td>";
		print "\n</tr>\n";                           
		print "<tr>\n";

// show the field amount

		print '<td>'.$langs->trans('Amount').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->amount.'" name="Amount">';
		}else{
			print $object->amount;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";
// show the field vat_amount

		print '<td>'.$langs->trans('Vat_amount').' </td><td>';
		if($edit==1){
			print '<input type="text" value="'.$object->vat_amount.'" name="Vat_amount">';
		}else{
			print $object->vat_amount;
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
			print $object->getLibStatut(4);
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




// show the field project
/*
		print '<td class="fieldrequired">'.$langs->trans('Project').' </td><td>';
		$sql_project=array('table'=> 'projet','keyfield'=> 'rowid','fields'=>'ref,title', 'join' => '', 'where'=>'','tail'=>'');
		$html_project=array('name'=>'Project','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoices_project=null;
		if($edit==1){
		print select_sellist($sql_project,$html_project, $object->project,$addChoices_project );
		}else{
		print_sellist($sql_project,$object->project,'-');		
                }
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";
*/


// show the field product

		print '<td>'.$langs->trans('Product').' </td><td>';
		$sql_product=array('table'=> 'product','keyfield'=> rowid,'fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
		$html_product=array('name'=>'Product','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicesproduct=null;
		if($edit==1){
		print select_sellist($sql_product,$html_product, $object->product,$addChoices_product );
		}else{
		print_sellist($sql_product,$object->product,'-');		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field supplier_invoice

		print '<td>'.$langs->trans('Supplierinvoice').' </td><td>';
		$sql_supplier_invoice=array('table'=> 'facture_fourn','keyfield'=> rowid,'fields'=>'ref,libelle', 'join' => '', 'where'=>'','tail'=>'');
		$html_supplier_invoice=array('name'=>'Supplierinvoice','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicessupplier_invoice=null;
		if($edit==1){
		print select_sellist($sql_supplier_invoice,$html_supplier_invoice, $object->supplier_invoice,$addChoices_supplier_invoice );
		}else{
		print_sellist($sql_supplier_invoice,$object->supplier_invoice,'-');		}
		print "</td>";
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
                //if($user->rights->Projectcostline->edit)
                //{
                    print '<a href="'.$PHP_SELF.'?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Update').'</a>';
                //}
                
                //if ($user->rights->Projectcostline->delete)
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
        print_fiche_titre($langs->trans('Projectcostline'));
        $head=ProjectcostlinePrepareHead($object);
        dol_fiche_head($head,'info',$langs->trans("Projectcostline"),0,'project_cost@project_cost');            
        print '<table width="100%"><tr><td>';
        dol_print_object_info($object);
        print '</td></tr></table>';
        print '</div>';
        break;

    case 'delete':
        if( ($id>0 || $ref!='')){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostline'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background        
        }
}
dol_fiche_end();

// End of page
llxFooter();
$db->close();
