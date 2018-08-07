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
 *   	\file       dev/paymentprojects/paymentproject_page.php
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
require_once 'class/paymentproject.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/paymentproject.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/project_cost/lib/project_cost.lib.php');
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
dol_include_once('/core/class/html.formother.class.php');
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("compta");
$langs->load("banks");
$langs->load("bills");
$langs->load("paymentproject@project_cost");

// Get parameter
$id			= GETPOST('id','int');
//$ref                    = GETPOST('ref','alpha');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
$tms= GETPOST('tms','alpha');
$projectid=GETPOST('Projectid','int');
//// Get parameters

 // uncomment to avoid resubmision
//if(isset( $_SESSION['paymentproject_class'][$tms]))
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
$object=new Paymentproject($db);
if($id>0)
{
    $object->id=$id; 
    $object->fetch($id);
    //$ref=dol_sanitizeFileName($object->ref);
    if(empty($action))$action='view'; //  the doc handling part send back only the ID without actions
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
        PaymentprojectReloadPage($backtopage,$projectid,$id,$ref);
}else if (($action == 'add') || ($action == 'update' && ($id>0 || !empty($ref))))
{
    //block resubmit
    if(empty($tms) || (!isset($_SESSION['Paymentproject'][$tms]))){
            setEventMessage('WrongTimeStamp_requestNotExpected', 'errors');
            $action=($action=='add')?'create':'view';
    }
    //retrive the data
    		//$object->ref=GETPOST('Ref');
		$object->label=GETPOST('Label');
		$object->amount=GETPOST('Amount');
		$object->datep=dol_mktime(0, 0, 0,GETPOST('Datepmonth'),GETPOST('Datepday'),GETPOST('Datepyear'));
		$object->datev=dol_mktime(0, 0, 0,GETPOST('Datevmonth'),GETPOST('Datevday'),GETPOST('Datevyear'));
		$object->project=GETPOST('Projectid');
		$object->soc=GETPOST('Soc');
		$object->typepayment=dol_getIdFromCode($db, GETPOST("Typepayment", 'alpha'), 'c_paiement');
		$object->bank=GETPOST('Bank');
                $object->num_payment=GETPOST('Numpayment');
		$object->import_key=GETPOST('Importkey');

 	if (empty($object->datep))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), null, 'errors');
		$error++;
	}
	if (empty($object->project) || $object->project < 0)
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Project")), null, 'errors');
		$error++;
	}
	if (empty($object->typepayment) || $object->typepayment < 0)
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("PaymentMode")), null, 'errors');
		$error++;
	}
	if (empty($object->amount))
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Amount")), null, 'errors');
		$error++;
	}
	if (! empty($conf->banque->enabled) && ! $object->bank > 0)
	{
		setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("BankAccount")), null, 'errors');
		$error++;
	}  
        

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
            unset($_SESSION['Paymentproject'][$tms]);
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
    case 'view':
    case 'viewinfo':
    case 'edit':
        // fetch the object data if possible
        if ($id > 0  )
        {
            $result=$object->fetch($id,'');
            if ($result < 0){ 
                dol_print_error($db);
            }else { // fill the id & ref
                if(isset($object->id))$id = $object->id;

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
               unset($_SESSION['Paymentproject'][$tms]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               
               PaymentprojectReloadPage($backtopage,$projectid,$result,'');

        }else
        {
                // Creation KO
                if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
                else  setEventMessage('RecordNotSucessfullyCreated', 'errors');
                $action='create';
        }                            
        break;
     case 'confirm_delete':
	if ($object->rappro == 0) // FIXME RAPPRO never set
	{
    
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
        }else{
		setEventMessages('Error try do delete a line linked to a conciliated bank transaction', null, 'errors');
        }
            PaymentprojectReloadPage($backtopage,$projectid, 0, '');
         break;


          
 }             
//Removing the tms array so the order can't be submitted two times
if(isset( $_SESSION['Paymentproject'][$tms]))
{
    unset($_SESSION['Paymentproject'][$tms]);
}
if(($action == 'create') || ($action == 'edit' && ($id>0 ))){
    $tms=getToken();
    $_SESSION['Paymentproject'][$tms]=array();
    $_SESSION['Paymentproject'][$tms]['action']=$action;
            
}

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Paymentproject','');
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
        if( $action=='delete' && ($id>0 )){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeletePaymentproject'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
    case 'view':
    {
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
        dol_fiche_head($headProject, 'payment', $langs->trans("Project"), 0, 'project');
        // tabs
        if($edit==0 && $new==0){ //show tabs
            $head=PaymentprojectPrepareHead($object);
            dol_fiche_head($head,'card',$langs->trans('Paymentproject'),0,'project_cost@project_cost');            
        }else{
            print_fiche_titre($langs->trans('Paymentproject'));
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
            print '<input type="hidden" name="Projectid" value="'.$projectid.'">';
        }else {// show the nav bar
            $basedurl=dol_buildpath("/project_cost/payment_list.php", 1).'?Projectid='.$projectid;
            $linkback = '<a href="'.$basedurl.(! empty($socid)?'?socid='.$socid:'').'">'.$langs->trans("BackToList").'</a>';
            if(!isset($object->ref))//save ref if any
                $object->ref=$object->id;
            print $form->showrefnav($object, 'action=view&id', $linkback, 1, 'rowid', 'ref', '');
            //reloqd the ref

        }

	print '<table class="border centpercent">'."\n";

        
		print "<tr>\n";


// show the field label

		print '<td>'.fieldLabel('Label','label',1).' </td><td>';
		if($edit==1){
                        if($object->label=="")$object->label=$langs->trans("ProjectPayment");
			print '<input name="Label" id="label" class="minwidth300" value="'.$object->label.'" name="Label">';
		}else{
			print $object->label;
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field amount

		print '<td>'.fieldLabel('Amount','amount',1).' </td><td>';
		if($edit==1){
			print '<input name="Amount" id="amount" class="minwidth100" value="'.$object->amount.'" name="Amount">';
		}else{
			print price($object->amount,0,$outputlangs,1,-1,-1,$conf->currency);
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field datep

		print '<td class="fieldrequired">'.fieldLabel('DatePayment','datep',1).' </td><td>';
		if($edit==1){
		if($new==1){
			print $form->select_date($object->datep,'Datep');
		}else{
			print $form->select_date($object->datep,'Datep');
		}
		}else{
			print dol_print_date($object->datep,'day');
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field datev

		print '<td>'.fieldLabel('DateValue','datev',1).' </td><td>';
		if($edit==1){
		if($new==1){
			print $form->select_date($object->datev,'Datev');
		}else{
			print $form->select_date($object->datev,'Datev');
		}
		}else{
			print dol_print_date($object->datev,'day');
		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";


// show the field soc

		print '<td class="fieldrequired">'.$langs->trans('Stakeholder').' </td><td>';
		$sql_soc=array('table'=> 'societe','keyfield'=> 'rowid','fields'=>'ref_int,nom', 'join' => '', 'where'=>'','tail'=>'');
		$html_soc=array('name'=>'Soc','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicessoc=null;
		if($edit==1){
		print select_sellist($sql_soc,$html_soc, $object->soc,$addChoices_soc );
		}else{
		print print_sellist($sql_soc,$object->soc,'-');		}
		print "</td>";
		print "\n</tr>\n";
		print "<tr>\n";

// show the field typepayment

		print '<td class="fieldrequired">'.$langs->trans('Typepayment').' </td><td>';
		$sql_typepayment=array('table'=> 'c_paiement','keyfield'=> 'id','fields'=>'libelle', 'join' => '', 'where'=>'','tail'=>'');
		$html_typepayment=array('name'=>'Typepayment','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicestypepayment=null;
		if($edit==1){
		print $form->select_types_paiements($object->typepayment, "Typepayment", '', 2);
		}else{
		print print_sellist($sql_typepayment,$object->typepayment,'-');		}
		print "</td>";
		print "\n</tr>\n";


// show the field bank
	if (! empty($conf->banque->enabled))
	{
		//print '<td>'.fieldLabel('BankAccount','selectaccountid',1).' </td><td>';

		if($edit==1 && $new=1){
		print $form->select_comptes($object->bank,"Bank",0,'',1); // not editable 
		}else{
		if ($object->bank> 0)
		{
			$bankline=new AccountLine($db);
			$bankline->fetch($object->bank);

			print '<tr>';
			print '<td>'.$langs->trans('BankTransactionLine').'</td>';
			print '<td>';
			print $bankline->getNomUrl(1,0,'showall');
			print '</td>';
			print '</tr>';
		}		
                
                
                }

        }


        

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
                //if($user->rights->Paymentproject->edit)
                //{
                    print '<a href="'.$PHP_SELF.'?id='.$id.'&action=edit" class="butAction">'.$langs->trans('Update').'</a>';
                //}
                
                //if ($user->rights->Paymentproject->delete)
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
        print_fiche_titre($langs->trans('Paymentproject'));
        $head=PaymentprojectPrepareHead($object);
        dol_fiche_head($head,'info',$langs->trans("Paymentproject"),0,'project_cost@project_cost');            
        print '<table width="100%"><tr><td>';
        dol_print_object_info($object);
        print '</td></tr></table>';
        print '</div>';
        break;

    case 'delete':
        if( ($id>0 )){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeletePaymentproject'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background        
        }
}
dol_fiche_end();

// End of page
llxFooter();
$db->close();
