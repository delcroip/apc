<?php
/* 
 * Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2018     Patrick DELCROIX     <pmpdelcroix@gmail.com>
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
 *   	\file       dev/skeletons/skeleton_page.php
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

require_once 'core/lib/generic.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once 'core/lib/spread.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
// include conditionnally of the dolibarr version
//if((version_compare(DOL_VERSION, "3.8", "<"))){
        //dol_include_once('/project_cost/lib/project_cost.lib.php');
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
$backtopage = GETPOST('backtopage');
$cancel=GETPOST('cancel');
$confirm=GETPOST('confirm');
//$token= GETPOST('token','alpha');
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
	$ls_ratio= GETPOST('ls_ratio','int');
	$ls_soc= GETPOST('ls_soc','int');
        if($ls_soc==-1)$ls_soc='';
	$ls_description= GETPOST('ls_description','alpha');
	$ls_user_creat= GETPOST('ls_user_creat','int');
	if($ls_user_creat==-1)$ls_user_creat='';
	$ls_import_key= GETPOST('ls_import_key','alpha');
	$ls_status= GETPOST('ls_status','int');
	$ls_c_sellist= GETPOST('ls_c_sellist','int');
	$ls_sellist_selected_id= GETPOST('ls_sellist_selected_id','int');
        if($ls_sellist_selected_id==-1)$ls_sellist_selected_id='';
	$ls_isgroup= GETPOST('ls_isgroup','int');

    
}


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




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
$form=new Form($db);
$formother=new FormOther($db);
$fuser=new User($db);
if($id>0)
{
    $object->id=$id; 
    $object->fetch($id);

}else if (empty($projectid)){
    setEventMessage( $langs->trans('noProjectIdPresent').' id:'.$id,'errors');
}
if(!empty($ref))
{
    $object->ref=$ref; 
    $object->id=$id; 
    $object->fetch($id);

}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

// Action to add record
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
        var_dump($action.$id);
        if( $action=='delete' && ($id>0 || $ref!="")){
         $ret=$form->form_confirm($PHP_SELF.'?action=confirm_delete&id='.$id,$langs->trans('DeleteProjectcostspread'),$langs->trans('ConfirmDelete'),'confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
      
    }        


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Projectcostspread','');
print "<div> <!-- module body-->";
    $project= new Project($db);
    $project->fetch($projectid);
    $headProject=project_prepare_head($project);
    dol_fiche_head($headProject, 'stakeholders', $langs->trans("Project"), 0, 'project');
    print_barre_liste($langs->trans("Projectcostspread"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);

    print '</div>'."\n"; // 
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'?Projectid='.$projectid.'">';
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
		$sql.=' t.ratio,';
		$sql.=' t.fk_soc,';
		$sql.=' t.description,';
		$sql.=' t.fk_user_creat,';
		$sql.=' t.import_key,';
		$sql.=' t.status,';
		$sql.=' t.c_sellist,';
		$sql.=' t.fk_sellist_selected_id,';
		$sql.=' t.isgroup';

    
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_spread as t';
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
	if($ls_ratio) $sqlwhere .= natural_search(array('t.ratio'), $ls_ratio);
	if($ls_soc) $sqlwhere .= natural_search(array('t.fk_soc'), $ls_soc);
	if($ls_description) $sqlwhere .= natural_search('t.description', $ls_description);
	if($ls_user_creat) $sqlwhere .= natural_search(array('t.fk_user_creat'), $ls_user_creat);
	if($ls_import_key) $sqlwhere .= natural_search('t.import_key', $ls_import_key);
	if($ls_status) $sqlwhere .= natural_search(array('t.status'), $ls_status);
	if($ls_c_sellist) $sqlwhere .= natural_search(array('t.c_sellist'), $ls_c_sellist);
	if($ls_sellist_selected_id) $sqlwhere .= natural_search(array('t.fk_sellist_selected_id'), $ls_sellist_selected_id);
	if($ls_isgroup) $sqlwhere .=  " AND t.isgroup='1' ";

      $sql.=' WHERE fk_project=\''.$projectid.'\' ';
    //list limit
    if(!empty($sqlwhere))
      $sql.=$sqlwhere;

// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_spread as t';
        if(!empty($sqlwhere))
            $sqlcount.=' WHERE '.substr ($sqlwhere, 5);
	$result = $object->db->query($sqlcount);
        $nbtotalofrecords = ($result)?$objcount = $object->db->fetch_object($result)->count:0;
}
    if(!empty($sortfield)){$sql.= $object->db->order($sortfield,$sortorder);
    }else{ $sortorder = 'ASC';}
    
    if (!empty($limit))
    {
            $sql.= $object->db->plimit($limit+1, $offset); 
    }


    //execute SQL
    dol_syslog($script_file, LOG_DEBUG);
    $resql=$object->db->query($sql);
    if ($resql)
    {
        $param='';
        if (! empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) $param.='&contextpage='.urlencode($contextpage);
        if ($limit > 0 && $limit != $conf->liste_limit) $param.='&limit='.urlencode($limit);
        	if (!empty($ls_ref))	$param.='&ls_ref='.urlencode($ls_ref);
	if (!empty($ls_label))	$param.='&ls_label='.urlencode($ls_label);
	if (!empty($ls_ratio))	$param.='&ls_ratio='.urlencode($ls_ratio);
	if (!empty($ls_soc))	$param.='&ls_soc='.urlencode($ls_soc);
	if (!empty($ls_description))	$param.='&ls_description='.urlencode($ls_description);
	if (!empty($ls_user_creat))	$param.='&ls_user_creat='.urlencode($ls_user_creat);
	if (!empty($ls_import_key))	$param.='&ls_import_key='.urlencode($ls_import_key);
	if (!empty($ls_status))	$param.='&ls_status='.urlencode($ls_status);
	if (!empty($ls_c_sellist))	$param.='&ls_c_sellist='.urlencode($ls_c_sellist);
	if (!empty($ls_sellist_selected_id))	$param.='&ls_sellist_selected_id='.urlencode($ls_sellist_selected_id);
	if (!empty($ls_isgroup))	$param.='&ls_isgroup=1';

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $object->db->num_rows($resql);
          
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        //print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

      
        print '<div class="div-table-responsive">';
        print '<table class="liste tag listwithfilterbefore" width="100%">'."\n";
        //TITLE
        print '<tr class="liste_titre_filter">';
        	print_liste_field_titre($langs->trans('Ref'),$PHP_SELF,'t.ref','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Label'),$PHP_SELF,'t.label','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Ratio'),$PHP_SELF,'t.ratio','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Soc'),$PHP_SELF,'t.fk_soc','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Description'),$PHP_SELF,'t.description','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Status'),$PHP_SELF,'t.status','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Isgroup'),$PHP_SELF,'t.isgroup','',$param,'',$sortfield,$sortorder);
	print "\n";

        
        print '</tr>';
        print "\n";

        //SEARCH FIELDS
        print '<tr class="liste_titre">';
        print "\n";

        //Search field forref
	print '<th class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_ref" value="'.$ls_ref.'">';
	print '</th>';
        print "\n";

//Search field forlabel
	print '<th class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_label" value="'.$ls_label.'">';
	print '</th>';
        print "\n";

//Search field forratio
	print '<th class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_ratio" value="'.$ls_ratio.'">';
	print '</th>';
        print "\n";
//Search field forsoc
	print '<th class="liste_titre" colspan="1" >';
		print select_generic('societe','rowid','ls_soc','nom','',$ls_soc);
	print '</th>';
        print "\n";
//Search field fordescription
	print '<th class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_description" value="'.$ls_description.'">';
	print '</th>';
        print "\n";

//Search field forstatus
	print '<th class="liste_titre" colspan="1" >';
	print '<input  type="checkbox" name="ls_status" value="1" '.(!empty($ls_status)?'checked':'').'>';
	print '</th>';
        print "\n";

//Search field forisgroup
	print '<th class="liste_titre" colspan="1" >';
	print '<input  type="checkbox" name="ls_isgroup" value="1" '.(!empty($ls_isgroup)?'checked':'').'>';
	print '</th>';
        print "\n";

        
        
        print '<th width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</th>';
        print "\n";
        print '</tr>'."\n"; 
        $i=0;
        $basedurl=dol_buildpath('/project_cost/spread_card.php',1).'?action=view&Projectid='.$projectid.'&id=';
        while ($i < $num && $i<$limit)
        {
            $obj = $object->db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                
                print "<tr class=\"oddeven\"  onclick=\"location.href='";
	print $basedurl.$obj->rowid."'\" >";
		print "<td>".$object->getNomUrl($obj->ref,$obj->id,'',0)."</td>";
                print "\n";
		print "<td>".$obj->label."</td>";
                print "\n";
		print "<td>".$obj->ratio."</td>";
                print "\n";
		print "<td>".print_generic('societe','rowid',$obj->fk_soc,'nom','')."</td>";
                print "\n";
		print "<td>".$obj->description."</td>";
                print "\n";
		print "<td>".$object->LibStatut($obj->status,3)."</td>";
                print "\n";
		print "<td>".$object->LibStatut($obj->isgroup,3)."</td>";
                print "\n";
		print '<td><a href="spread_card.php?action=delete&id='.$obj->rowid.'">'.img_delete().'</a></td>';
		print "\n";
                print "</tr>";
                print "\n";
                

            }
            $i++;
        }
    }
    else
    {
        $error++;
        dol_print_error($object->db);
    }

    print '</table>'."\n";
    print '</div>'."\n"; // div-table-responsive
    print '<a href="spread_card.php?action=create" class="butAction" role="button">'.$langs->trans('New');
    print ' '.$langs->trans('Projectcostspread')."</a>\n";
    print '</form>'."\n";
    // new button


    

dol_fiche_end();
//dol_fiche_end();


// End of page
llxFooter();
$object->db->close();
