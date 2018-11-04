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
 *   	\file       dev/projectcostshareholders/projectcostshareholder_page.php
 *		\ingroup    project_cost othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2018-09-30 16:06
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

// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
//require_once 'lib/project_cost.lib.php';
require_once 'class/shareholder.class.php';
require_once 'core/lib/generic.lib.php';
//require_once 'core/lib/shareholder.lib.php';
dol_include_once('/core/lib/functions2.lib.php');
//document handling
dol_include_once('/core/lib/files.lib.php');

dol_include_once('/societe/class/societe.class.php');
//dol_include_once('/core/lib/images.lib.php');
dol_include_once('/core/class/html.formfile.class.php');
dol_include_once('/core/class/html.formother.class.php');
$PHP_SELF=$_SERVER['PHP_SELF'];
// Load traductions files requiredby by page
//$langs->load("companies");
$langs->load("projectcostshareholder@project_cost");

// Get parameter
$parent			= GETPOST('id','int');
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
    	$ls_soc= GETPOST('ls_soc','int');
	$ls_date_start_month= GETPOST('ls_date_start_month','int');
	$ls_date_start_year= GETPOST('ls_date_start_year','int');
	//$ls_project_cost_share= GETPOST('ls_project_cost_share','int');

    
}


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




 // uncomment to avoid resubmision
//if(isset( $_SESSION['projectcostshareholder_class'][$tms]))
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
$sub_object=new Projectcostshareholder($db);
if($sub_id>0)
{
    $sub_object->id=$sub_id; 
    $sub_object->fetch($sub_id);
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

         
// Action to remove record
 switch($action){
    case 'sub_confirm_delete':	
       $result=($confirm=='yes')?$sub_object->delete($user):0;
       if ($result > 0)
       {
               // Delete OK
               setEventMessage($langs->trans('RecordDeleted'), 'mesgs');
       }
       else
       {
               // Delete NOK
               if (! empty($sub_object->errors)) setEventMessages(null,$sub_object->errors,'errors');
               else setEventMessage('RecordNotDeleted','errors');
       }
       break;
    case 'sub_delete':
        if(  ($sub_id>0 )){
         $ret=$form->form_confirm($PHP_SELF.'?id='.$parent.'&sub_id='.$sub_id,$langs->trans('DeleteProjectcostsharemember'),$langs->trans('ConfirmDelete'),'sub_confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
              break;
    case 'sub_add': //fixme
        $sub_object->soc=GETPOST('Soc');
        $sub_object->date_start=dol_mktime(0, 0, 0,GETPOST('Datestartmonth'),GETPOST('Datestartday'),GETPOST('Datestartyear'));
        $sub_object->project_cost_share=$id;
        $result=$sub_object->create($user);
        if ($result > 0)
        {
                // Creation OK
            // remove the tms
               unset($_SESSION['Projectcostshareholder'][$tms]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               //ProjectcostshareReloadPage($backtopage,$result,'');

        }else
        {
                // Creation KO
                if (! empty($sub_object->errors)) setEventMessages(null, $sub_object->errors, 'errors');
                else  setEventMessage('RecordNotSucessfullyCreated', 'errors');
                //$action='create';
        }   
    } 

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

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
    $sql.=' t.fk_soc,';
    $sql.=' t.date_start,';
    $sql.=' t.fk_project_cost_share';
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_share_holder as t';
    $sqlwhere='';
    if(isset($sub_object->entity))
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
    	if($ls_soc) $sqlwhere .= natural_search(array('t.fk_soc'), $ls_soc);
	if($ls_date_start_month)$sqlwhere .= ' AND MONTH(t.date_start)="'.$ls_date_start_month."'";
	if($ls_date_start_year)$sqlwhere .= ' AND YEAR(t.date_start)="'.$ls_date_start_year."'";
	//if($ls_project_cost_share) $sqlwhere .= natural_search(array('t.fk_project_cost_share'), $ls_project_cost_share);
     $sql.=' WHERE fk_project_cost_share=\''.$parent.'\' ';
    //list limit
    if(!empty($sqlwhere))
        $sql.=$sqlwhere;
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_share_holder as t';
       $sqlcount.=' WHERE fk_project_cost_share=\''.$parent.'\' ';
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
        	if (!empty($ls_soc))	$param.='&ls_soc='.urlencode($ls_soc);
	if (!empty($ls_date_start_month))	$param.='&ls_date_start_month='.urlencode($ls_date_start_month);
	if (!empty($ls_date_start_year))	$param.='&ls_date_start_year='.urlencode($ls_date_start_year);
	if (!empty($ls_project_cost_share))	$param.='&ls_project_cost_share='.urlencode($ls_project_cost_share);

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $db->num_rows($resql);
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        print_barre_liste($langs->trans("Projectcostshareholder"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);
       // print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

        print '<form method="POST" action="?action=sub_add&id='.$parent.'&Projectid='.$projectid.'">';
         print '<div class="div-table-responsive">';
         print '<table class="liste" width="100%">'."\n";
        //TITLE
        print '<tr class="liste_titre">';
        	print_liste_field_titre($langs->trans('Soc'),$PHP_SELF,'t.fk_soc','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Datestart'),$PHP_SELF,'t.date_start','',$param,'',$sortfield,$sortorder);
	print "\n";
	//print_liste_field_titre($langs->trans('Projectcostshare'),$PHP_SELF,'t.fk_project_cost_share','',$param,'',$sortfield,$sortorder);
	//print "\n";

        
        print '</tr>';
        //SEARCH FIELDS
  /*      print '<tr class="liste_titre">'; 
        //Search field forsoc
	print '<td class="liste_titre" colspan="1" >';
		$sql_soc=array('table'=> 'soc','keyfield'=> 'rowid','fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
		$html_soc=array('name'=>'$ls_soc','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
		$addChoicessoc=null;
		print select_sellist($sql_soc,$html_soc, $ls_soc,$addChoices_soc );
	print '</td>';
//Search field fordate_start
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" type="text" size="1" maxlength="2" name="date_start_month" value="'.$ls_date_start_month.'">';
	$syear = $ls_date_start_year;
	$formother->select_year($syear?$syear:-1,'ls_date_start_year',1, 20, 5);
	print '</td>';
//Search field forproject_cost_share
	//print '<td class="liste_titre" colspan="1" >';
	//	$sql_project_cost_share=array('table'=> 'project_cost_share','keyfield'=> 'rowid','fields'=>'ref,label', 'join' => '', 'where'=>'','tail'=>'');
	//	$html_project_cost_share=array('name'=>'$ls_project_cost_share','class'=>'','otherparam'=>'','ajaxNbChar'=>'','separator'=> '-');
	//	$addChoicesproject_cost_share=null;
	//	print select_sellist($sql_project_cost_share,$html_project_cost_share, $ls_project_cost_share,$addChoices_project_cost_share );
	//print '</td>';

        
        
        print '<td width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';
        print '</tr>'."\n"; */
        $i=0;
        print "<tr><td>";
        print '<input type="hidden" name="tms" value="'.$tms.'">';
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
        print '<input type="hidden" value="'.$parent.'" name="id"> ';
//.print_sellist($sqlarray, $parent, '' );

        //print '</td><td>';
        $filteronlist='';
       print $form->select_company(GETPOST('Soc','int'), 'Soc', $filteronlist, 'SelectThirdParty', 1, 0, array(), 0, 'minwidth300');
        print '</td><td >';
        $form->select_date(-1,'Datestart');
        print '</td><td><input class="butAction" type="submit" value="'.$langs->trans('Add')." ".$langs->trans('Projectcostshareholder').'">';
        print "</td></tr>";
        //$basedurl=dirname($PHP_SELF).'/shareholder_card.php?action=view&subId=';
        $thirdPaty= New Societe($db);
       while ($i < $num && $i<$limit)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                // You can use here results
                print "<tr class=\"oddeven\" >";
		print "<td>". $filteronlist = '';
                $thirdPaty->fetch($obj->fk_soc);
                print $thirdPaty->getNomUrl(1, 'project');
                print "</td><td>".dol_print_date($db->jdate($obj->date_start),'day')."</td>";
			
                print '<td><a href="?action=sub_delete&sub_id='.$obj->rowid.'&id='.$parent.'&Projectid='.$projectid.'">'.img_delete().'</a></td>';
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


    

