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
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
// include conditionnally of the dolibarr version
//if((version_compare(DOL_VERSION, "3.8", "<"))){
  //      dol_include_once('/project_cost/lib/project_cost.lib.php');
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


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




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
    $upload_dir = $conf->project_cost->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'Projectcostline').$ref;
    if(empty($action))$action='viewdoc'; //  the doc handling part send back only the ID without actions
}
if(!empty($ref))
{
    $object->ref=$ref; 
    $object->id=$id; 
    $object->fetch($id);
    $ref=dol_sanitizeFileName($object->ref);
    $upload_dir = $conf->project_cost->dir_output.'/'.get_exdir($object->id,2,0,0,$object,'Projectcostline').$ref;
    
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

         
// Action to remove record
 switch($action){
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
       break;
    case 'delete':
        if( $action=='delete' && ($id>0 || $ref!="")){
         $ret=$form->form_confirm(dol_buildpath('/project_cost/spread_card.php',1).'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostline'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
      
    } 

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectcostline','');
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
         dol_fiche_head($headProject, 'stakeholders', $langs->trans("Project"), 0, 'project');

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


    $sql = 'SELECT';
    $sql.= ' t.rowid,';
    
		$sql.=' t.ref,';
		$sql.=' t.label,';
		$sql.=' t.amount,';
		$sql.=' t.description,';
		$sql.=' t.import_key,';
		$sql.=' t.status,';
		$sql.=' t.fk_project,';
		$sql.=' t.fk_product,';
		$sql.=' t.fk_supplier_invoice,';
		$sql.=' t.c_project_cost_type,';
		$sql.=' t.fk_project_cost_spread';

    
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_line as t';
    $sqlwhere='';
    if(isset($object->entity))
        $sqlwhere.= ' AND t.entity = '.$conf->entity;
    if ($filter && $filter != -1)		// GETPOST('filtre') may be a string
    {
            $filtrearr = explode(',', $filter);
            foreach ($filtrearr as $fil)
            {
                    $filt = explode(':', $fil);
                    $sqlwhere .= ' AND ' . $filt[0] . ' = ' . $filt[1];
            }
    }
    //pass the search criteria
    	if($ls_ref) $sqlwhere .= natural_search('t.ref', $ls_ref);
	if($ls_label) $sqlwhere .= natural_search('t.label', $ls_label);
	if($ls_amount) $sqlwhere .= natural_search(array('t.amount'), $ls_amount);
	if($ls_description) $sqlwhere .= natural_search('t.description', $ls_description);
	if($ls_import_key) $sqlwhere .= natural_search('t.import_key', $ls_import_key);
	if($ls_status) $sqlwhere .= natural_search(array('t.status'), $ls_status);
	if($ls_project) $sqlwhere .= natural_search(array('t.fk_project'), $ls_project);
	if($ls_product) $sqlwhere .= natural_search(array('t.fk_product'), $ls_product);
	if($ls_supplier_invoice) $sqlwhere .= natural_search(array('t.fk_supplier_invoice'), $ls_supplier_invoice);
	if($ls_c_project_cost_type) $sqlwhere .= natural_search(array('t.c_project_cost_type'), $ls_c_project_cost_type);
	if($ls_project_cost_spread) $sqlwhere .= natural_search(array('t.fk_project_cost_spread'), $ls_project_cost_spread);

    
    //list limit
    if(!empty($sqlwhere))
        $sql.=' WHERE '.substr ($sqlwhere, 5);
    
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_line as t';
        if(!empty($sqlwhere))
            $sqlcount.=' WHERE '.substr ($sqlwhere, 5);
	$result = $db->query($sqlcount);
        $nbtotalofrecords = ($result)?$objcount = $db->fetch_object($result)->count:0;
}
    if(!empty($sortfield)){$sql.= $db->order($sortfield,$sortorder);
    }else{ $sortorder = 'ASC';}
    
    if (!empty($limit))
    {
            $sql.= $db->plimit($limit+1, $offset); 
    }
    

    //execute SQL
    dol_syslog($script_file, LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
        $param='';
        if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.urlencode($contextpage);
        if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.urlencode($limit);
        	if (!empty($ls_ref))	$param.='&ls_ref='.urlencode($ls_ref);
	if (!empty($ls_label))	$param.='&ls_label='.urlencode($ls_label);
	if (!empty($ls_amount))	$param.='&ls_amount='.urlencode($ls_amount);
	if (!empty($ls_description))	$param.='&ls_description='.urlencode($ls_description);
	if (!empty($ls_import_key))	$param.='&ls_import_key='.urlencode($ls_import_key);
	if (!empty($ls_status))	$param.='&ls_status='.urlencode($ls_status);
	if (!empty($ls_project))	$param.='&ls_project='.urlencode($ls_project);
	if (!empty($ls_product))	$param.='&ls_product='.urlencode($ls_product);
	if (!empty($ls_supplier_invoice))	$param.='&ls_supplier_invoice='.urlencode($ls_supplier_invoice);
	if (!empty($ls_c_project_cost_type))	$param.='&ls_c_project_cost_type='.urlencode($ls_c_project_cost_type);
	if (!empty($ls_project_cost_spread))	$param.='&ls_project_cost_spread='.urlencode($ls_project_cost_spread);

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $db->num_rows($resql);
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        print_barre_liste($langs->trans("Projectcostline"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);
        print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

        print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
        print '<table class="liste" width="100%">'."\n";
        //TITLE
        print '<tr class="liste_titre">';
        	print_liste_field_titre($langs->trans('Ref'),$PHP_SELF,'t.ref','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Label'),$PHP_SELF,'t.label','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Amount'),$PHP_SELF,'t.amount','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Description'),$PHP_SELF,'t.description','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Status'),$PHP_SELF,'t.status','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Product'),$PHP_SELF,'t.fk_product','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Supplierinvoice'),$PHP_SELF,'t.fk_supplier_invoice','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Cprojectcosttype'),$PHP_SELF,'t.c_project_cost_type','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Projectcostspread'),$PHP_SELF,'t.fk_project_cost_spread','',$param,'',$sortfield,$sortorder);
	print "\n";

        
        print '</tr>';
        //SEARCH FIELDS
        print '<tr class="liste_titre">'; 
        //Search field forref
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_ref" value="'.$ls_ref.'">';
	print '</td>';
//Search field forlabel
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_label" value="'.$ls_label.'">';
	print '</td>';
//Search field foramount
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_amount" value="'.$ls_amount.'">';
	print '</td>';
//Search field fordescription
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_description" value="'.$ls_description.'">';
	print '</td>';

//Search field forstatus
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_status" value="'.$ls_status.'">';
	print '</td>';
//Search field forproject

//Search field forproduct
	print '<td class="liste_titre" colspan="1" >';
		$sql_product=array('table'=> 'product','keyfield'=> rowid,'fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
		$html_product=array('name'=>'$ls_product','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicesproduct=null;		
                print select_sellist($sql_product,$html_product, $ls_product,$addChoices_product );
	print '</td>';
//Search field forsupplier_invoice
	print '<td class="liste_titre" colspan="1" >';
        $sql_sup_inv=array('table'=> 'facture_fourn','keyfield'=> 'rowid','fields'=>'ref,libelle', 'join' => '', 'where'=>'','tail'=>'');
        $html_sup_inv=array('name'=>'ls_supplier_invoice','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
        $addChoices_sup_inv=null;
        print select_sellist($sql_sup_inv,$html_sup_inv, $ls_supplier_invoice,$addChoices_sup_inv );
		//print select_generic('facture_fourn','rowid','ls_supplier_invoice','ref','libelle',$ls_supplier_invoice);
	print '</td>';
//Search field forc_project_cost_type
	print '<td class="liste_titre" colspan="1" >';
        $sql_ctype=array('table'=> 'c_project_cost_type','keyfield'=> 'rowid','fields'=>'label', 'join' => '', 'where'=>'','tail'=>'');
        $html_ctype=array('name'=>'ls_c_project_cost_type','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
        $addChoices_ctype=null;
        print select_sellist($sql_ctype,$html_ctype, $ls_c_project_cost_type,$addChoices_ctype );
        print '</td>';
//Search field forproject_cost_spread
	print '<td class="liste_titre" colspan="1" >';
         $sql_spread=array('table'=> 'project_cost_spread','keyfield'=> 'rowid','fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
        $html_spread=array('name'=>'ls_project_cost_spread','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
        $addChoices_spread=null;
        print select_sellist($sql_spread,$html_spread, $ls_supplier_invoice,$addChoices_spread );
		//print select_generic('project_cost_spread','rowid','ls_project_cost_spread','rowid','description',$ls_project_cost_spread);
	print '</td>';

        
        
        print '<td width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';
        print '</tr>'."\n"; 
        $i=0;
        $basedurl=dirname($PHP_SELF).'/line_card.php?action=view&id=';
        while ($i < $num && $i<$limit)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                		print "<tr class=\"oddeven')\"  onclick=\"location.href='";
	print $basedurl.$obj->rowid."'\" >";
		print "<td>".$object->getNomUrl($obj->ref,'',$obj->ref,0)."</td>";
		print "<td>".$obj->label."</td>";
		print "<td>".$obj->amount."</td>";
		print "<td>".$obj->description."</td>";

		print "<td>".$obj->status."</td>";
		print "<td>".print_generic('product','rowid',$obj->fk_product,'ref','label')."</td>";
		print "<td>".print_generic('facture_fourn','rowid',$obj->fk_supplier_invoice,'ref','label')."</td>";
		print "<td>".$obj->c_project_cost_type."</td>";
		print "<td>".print_generic('project_cost_spread','rowid',$obj->fk_project_cost_spread,'rowid','description')."</td>";
		print '<td><a href="line_card.php?action=delete&id='.$obj->rowid.'">'.img_delete().'</a></td>';
		print "</tr>";

                

            }
            $i++;
        }
    }
    else
    {
        $error++;
        dol_print_error($db);
    }

    print '</table>'."\n";
    print '</form>'."\n";
    // new button
    print '<a href="line_card.php?action=create" class="butAction" role="button">'.$langs->trans('New');
    print ' '.$langs->trans('Projectcostline')."</a>\n";

    




// End of page
if(!$sublist)llxFooter();
if(!$sublist)$db->close();
