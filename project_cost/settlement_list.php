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
 *   	\file       dev/projectsettlements/projectsettlement_page.php
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
require_once 'class/settlement.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/settlement.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
// include conditionnally of the dolibarr version
dol_include_once('/core/class/html.formother.class.php');
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("settlement@project_cost");

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
	$ls_project= GETPOST('ls_project','int');
	$ls_description= GETPOST('ls_description','alpha');
	$ls_date_settlement_month= GETPOST('ls_date_settlement_month','int');
	$ls_date_settlement_year= GETPOST('ls_date_settlement_year','int');
	$ls_import_key= GETPOST('ls_import_key','alpha');
	$ls_status= GETPOST('ls_status','int');

    
}


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




 // uncomment to avoid resubmision
//if(isset( $_SESSION['projectsettlement_class'][$tms]))
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
}
if(!empty($ref))
{
    $object->ref=$ref; 
    $object->id=$id; 
    $object->fetch($id,$ref);
    $ref=dol_sanitizeFileName($object->ref);
    
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
         $ret=$form->form_confirm(dol_buildpath('/project_cost/share_card.php',1).'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectsettlement'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
      
    } 

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectsettlement','');
print "<div> <!-- module body-->";
        $project= new Project($db);
        $project->fetch($projectid);
        $headProject=project_prepare_head($project);
         dol_fiche_head($headProject, 'settlement', $langs->trans("Project"), 0, 'project');
                 print_barre_liste($langs->trans("Projectsettlement"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);
print '</div>';

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
		$sql.=' t.fk_project,';
		$sql.=' t.description,';
		$sql.=' t.date_settlement,';
		$sql.=' t.import_key,';
		$sql.=' t.status';

    
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_settlement as t';
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
	if($ls_date_settlement_month)$sqlwhere .= ' AND MONTH(t.date_settlement)="'.$ls_date_settlement_month."'";
	if($ls_date_settlement_year)$sqlwhere .= ' AND YEAR(t.date_settlement)="'.$ls_date_settlement_year."'";
	if($ls_status) $sqlwhere .= natural_search(array('t.status'), $ls_status);

    
    //list limit
    if(!empty($sqlwhere))
        $sql.=' WHERE fk_project='.$projectid.$sqlwhere;
    
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_settlement as t';
        if(!empty($sqlwhere))
            $sqlcount.=' WHERE fk_project='.$projectid.$sqlwhere;
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
        $param='&Projectid='.$projectid;
        if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.urlencode($contextpage);
        if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.urlencode($limit);
        	if (!empty($ls_ref))	$param.='&ls_ref='.urlencode($ls_ref);
	if (!empty($ls_label))	$param.='&ls_label='.urlencode($ls_label);
	if (!empty($ls_date_settlement_month))	$param.='&ls_date_settlement_month='.urlencode($ls_date_settlement_month);
	if (!empty($ls_date_settlement_year))	$param.='&ls_date_settlement_year='.urlencode($ls_date_settlement_year);
	if (!empty($ls_status))	$param.='&ls_status='.urlencode($ls_status);

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $db->num_rows($resql);
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        //print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

        print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
        print '<div class="div-table-responsive">';
        print '<table class="liste listwithfilterbefore" width="100%">'."\n";
        //TITLE
        print '<tr class="liste_titre">';
        	print_liste_field_titre($langs->trans('Ref'),$PHP_SELF,'t.ref','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Label'),$PHP_SELF,'t.label','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Datesettlement'),$PHP_SELF,'t.date_settlement','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Status'),$PHP_SELF,'t.status','',$param,'',$sortfield,$sortorder);
	print "\n";

        
        print '</tr>';
        //SEARCH FIELDS
        print '<tr class="liste_titre_filter">'; 
        //Search field forref
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_ref" value="'.$ls_ref.'">';
	print '</td>';
//Search field forlabel
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_label" value="'.$ls_label.'">';
	print '</td>';
//Search field fordate_settlement
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" type="text" size="1" maxlength="2" name="date_settlement_month" value="'.$ls_date_settlement_month.'">';
	$syear = $ls_date_settlement_year;
	$formother->select_year($syear?$syear:-1,'ls_date_settlement_year',1, 20, 5);
	print '</td>';
//Search field forstatus
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_status" value="'.$ls_status.'">';
	print '</td>';

        
        
        print '<td width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';
        print '</tr>'."\n"; 
        $i=0;
        $basedurl=dirname($PHP_SELF).'/settlement_card.php?action=view&Projectid='.$projectid.'&id=';
        while ($i < $num && $i<$limit)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                		print "<tr class=\"oddeven')\"  onclick=\"location.href='";
	print $basedurl.$obj->rowid."'\" >";
        $object->project=$projectid;
		print "<td>".$object->getNomUrl($obj->ref,'',$obj->ref,0)."</td>";
		print "<td>".$obj->label."</td>";
		print "<td>".dol_print_date($db->jdate($obj->date_settlement),'day')."</td>";
		print "<td>".$object->LibStatut($obj->status,3)."</td>";
		print '<td><a href="settlement_card.php?action=delete&Projectid='.$projectid.'&id='.$obj->rowid.'">'.img_delete().'</a></td>';
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
    print '<a href="settlement_card.php?action=create&Projectid='.$projectid.'" class="butAction" role="button">'.$langs->trans('New');
    print ' '.$langs->trans('Projectsettlement')."</a>\n";

    




// End of page
llxFooter();
$db->close();
