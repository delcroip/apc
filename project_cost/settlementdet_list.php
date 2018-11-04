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
 *   	\file       dev/projectsettlementdets/projectsettlementdet_page.php
 *		\ingroup    apc othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-08-23 20:33
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
// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
//require_once 'lib/apc.lib.php';
require_once 'class/settlementdet.class.php';
//require_once 'class/settlement.class.php';
require_once 'core/lib/generic.lib.php';
//require_once 'core/lib/settlement.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
// include conditionnally of the dolibarr version
//if((version_compare(DOL_VERSION, "3.8", "<"))){
        //dol_include_once('/apc/lib/apc.lib.php');
//}
dol_include_once('/core/class/html.formother.class.php');
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("projectsettlementdet@apc");

// Get parameter
// Get parameter
//global $id;
$parent =$id;
//$parent			= GETPOST('id','int');
$parentRef			= GETPOST('ref','int');
$sub_id			= GETPOST('sub_id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
$tms= GETPOST('tms','alpha');
//// Get parameters
$sortfield = GETPOST('sortfield','alpha'); 
$sortorder = GETPOST('sortorder','alpha')?GETPOST('sortorder','alpha'):'ASC';
$removefilter=isset($_POST["removefilter_x"]) || isset($_POST["removefilter"]);
//$applyfilter=isset($_POST["search_x"]) ;//|| isset($_POST["search"]);
if (!$removefilter )		// Both test must be present to be compatible with all browsers
{
    	$ls_settlement= GETPOST('ls_settlement','int');
	$ls_project_cost_line= GETPOST('ls_project_cost_line','int');
	$ls_amount= GETPOST('ls_amount','int');
	$ls_vat_amount= GETPOST('ls_vat_amount','int');
	$ls_import_key= GETPOST('ls_import_key','alpha');
	$ls_c_project_cost_type= GETPOST('ls_c_project_cost_type','alpha');

    
}


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




 // uncomment to avoid resubmision
//if(isset( $_SESSION['projectsettlementdet_class'][$tms]))
//{

 //   $cancel=TRUE;
 //  setEventMessages('Internal error, POST not exptected', null, 'errors');
//}



// Right Management
 /*
if ($user->societe_id > 0 || 
       (!$user->rights->apc->add && ($action=='add' || $action='create')) ||
       (!$user->rights->apc->view && ($action=='list' || $action='view')) ||
       (!$user->rights->apc->delete && ($action=='confirm_delete')) ||
       (!$user->rights->apc->edit && ($action=='edit' || $action='update')))
{
	accessforbidden();
}
*/

// create object and set id or ref if provided as parameter
$object=new Projectsettlementdet($db);
if($sub_id>0)
{
    $object->id=$sub_id; 

}
if(!empty($ref))
{
    $object->ref=$ref; 
    $object->id=$sub_id; 

    
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

         
// Action to remove record
 switch($action){
    case 'sub_confirm_delete':	
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
    case 'sub_delete':
        if( ($sub_id>0 || $ref!="")){
         $ret=$form->form_confirm(dol_buildpath('/project_cost/settlement_card.php',1).'?id='.$parent.'&Projectid='.$projectid.'&action=sub_confirm_delete&sub_id='.$sub_id,$langs->trans('DeleteProjectsettlementdet'),$langs->trans('ConfirmDelete'),'sub_confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
      
    } 

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

//llxHeader('','Projectsettlementdet','');

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
    $sql.=' t.fk_settlement,';
    $sql.=' t.fk_project_cost_line,';
    $sql.=' t.amount,';
    $sql.=' t.vat_amount,';
    $sql.=' t.import_key,';
   $sql.=' c.c_project_cost_type';  
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_settlement_det as t';
    $sql.= ' JOIN '.MAIN_DB_PREFIX.'project_cost_line as c ON fk_project_cost_line=c.rowid ';
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
    	//if($ls_settlement) $sqlwhere .= natural_search(array('t.fk_settlement'), $ls_settlement);
	if($ls_project_cost_line) $sqlwhere .= natural_search(array('t.fk_project_cost_line'), $ls_project_cost_line);
	if($ls_amount) $sqlwhere .= natural_search(array('t.amount'), $ls_amount);
	if($ls_vat_amount) $sqlwhere .= natural_search(array('t.vat_amount'), $ls_vat_amount);
	if($ls_import_key) $sqlwhere .= natural_search('t.import_key', $ls_import_key);
	if($ls_c_project_cost_type) $sqlwhere .= natural_search('t.c_project_cost_type', $ls_c_project_cost_type);

    $sql.=' WHERE fk_settlement=\''.$parent.'\' ';
    //list limit
    if(!empty($sqlwhere))
        $sql.=$sqlwhere;
    
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_settlement_det as t';
        $sqlcount.=' WHERE fk_settlement=\''.$parent.'\' ';
        if(!empty($sqlwhere))
            $sqlcount.=$sqlwhere;
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
        //if (!empty($ls_settlement))	$param.='&ls_settlement='.urlencode($ls_settlement);
	if (!empty($ls_project_cost_line))	$param.='&ls_project_cost_line='.urlencode($ls_project_cost_line);
	if (!empty($ls_amount))	$param.='&ls_amount='.urlencode($ls_amount);
	if (!empty($ls_vat_amount))	$param.='&ls_vat_amount='.urlencode($ls_vat_amount);
	if (!empty($ls_import_key))	$param.='&ls_import_key='.urlencode($ls_import_key);
	if (!empty($ls_c_project_cost_type))	$param.='&ls_c_project_cost_type='.urlencode($ls_c_project_cost_type);

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $db->num_rows($resql);
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        print_barre_liste($langs->trans("Projectsettlementdet"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);
        //print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

        print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
         print '<div class="div-table-responsive">';
        print '<table class="liste " width="100%">'."\n";
        //TITLE
  	print_liste_field_titre($langs->trans('Projectcostline'),$PHP_SELF,'t.fk_project_cost_line','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('AmountHT'),$PHP_SELF,'t.amount','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('VAT'),$PHP_SELF,'t.vat_amount','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Amount'),$PHP_SELF,'t.vat_amount','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Importkey'),$PHP_SELF,'t.import_key','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Cprojectcosttype'),$PHP_SELF,'c.c_project_cost_type','',$param,'',$sortfield,$sortorder);
	print "\n";

        
        print '</tr>';
        //SEARCH FIELDS
        print '<tr class="liste_titre_filter">'; 
 //Search field forproject_cost_line
	print '<td class="liste_titre" colspan="1" >';
		$sql_project_cost_line=array('table'=> 'project_cost_line','keyfield'=> 'rowid','fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
		$html_project_cost_line=array('name'=>'$ls_project_cost_line','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicesproject_cost_line=null;
		print select_sellist($sql_project_cost_line,$html_project_cost_line, $ls_project_cost_line,$addChoices_project_cost_line );
	print '</td>';
//Search field foramountHT
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_amount" value="'.$ls_amount.'">';
	print '</td>';
//Search field forvat_amount
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_vat_amount" value="'.$ls_vat_amount.'">';
	print '</td>';
//Search field for  amount
	print '<td class="liste_titre" colspan="1" >';

	print '</td>';
//Search field forimport_key
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_import_key" value="'.$ls_import_key.'">';
	print '</td>';
//Search field forc_project_cost_type
	print '<td class="liste_titre" colspan="1" >';
        $sql_type=array('table'=> 'c_project_cost_type','keyfield'=> 'rowid','fields'=>'label', 'join' => '', 'where'=>'active=1','tail'=>'');
        $html_type=array('name'=>'ls_c_project_cost_type','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
        $addChoices_type=null;
        print select_sellist($sql_type,$html_type, '',$addChoices_type );
        print '</td>';

        print '<td width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';
        print '</tr>'."\n"; 
        $i=0;
        $basedurl=dirname($PHP_SELF).'/projectsettlementdet_card.php?action=view&Projectid='.$projectid.'&id=';
        while ($i < $num && $i<$limit)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                print "<tr class=\"oddeven\"   >";
		print "<td>".print_sellist($sql_project_cost_line,$obj->fk_project_cost_line,' - ')."</td>";
		print "<td>".price($obj->amount,0,$outputlangs,1,-1,2,$conf->currency)."</td>";
		print "<td>".price($obj->vat_amount,0,$outputlangs,1,-1,2,$conf->currency)."</td>";
		print "<td>".price($obj->vat_amount+$obj->amount,0,$outputlangs,1,-1,2,$conf->currency)."</td>";
		print "<td>".$obj->import_key."</td>";
		print "<td>".print_sellist($sql_type,$obj->c_project_cost_type)."</td>";
		print '<td><a href="settlement_card.php?id='.$parent.'&Projectid='.$projectid.'&action=sub_delete&sub_id='.$obj->rowid.'">'.img_delete().'</a></td>';
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
    print '</div>';
    print '</form>'."\n";
    // new button
   //print '<a href="projectsettlementdet_card.php?action=create" class="butAction" role="button">'.$langs->trans('New');
   // print ' '.$langs->trans('Projectsettlementdet')."</a>\n";

    




// End of page

