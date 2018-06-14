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
 *					Initialy built by build_class_from_table on 2018-06-01 19:05
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
$sublist=true;
// Change this following line to use the correct relative path (../, ../../, etc)
if(!$sublist)include 'core/lib/includeMain.lib.php';
// Change this following line to use the correct relative path from htdocs
//include_once(DOL_DOCUMENT_ROOT.'/core/class/formcompany.class.php');
//require_once 'lib/project_cost.lib.php';
require_once 'class/spreadmember.class.php';
require_once 'core/lib/generic.lib.php';
require_once 'core/lib/spreadmember.lib.php';
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
$langs->load("Projectcostspreadmember@project_cost");

// Get parameter
$parent			= GETPOST('id','int');
$parentRef			= GETPOST('ref','int');
$sub_id			= GETPOST('sub_id','int');
$action		= GETPOST('action','alpha');
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
    	$ls_group_id= GETPOST('ls_group_id','int');
	$ls_member_id= GETPOST('ls_member_id','int');

    
}


$page = GETPOST('page','int'); 
if ($page == -1) { $page = 0; }
$limit = GETPOST('limit','int')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;




 // uncomment to avoid resubmision
//if(isset( $_SESSION['Projectcostspreadmember_class'][$tms]))
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
$sub_object=new Projectcostspreadmember($db);
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

		
    switch($action){		
       case 'sub_delete':
        if( ($id>0 || $ref!="")){
         $ret=$form->form_confirm($PHP_SELF.'?id='.$parent.'&sub_id='.$sub_id,$langs->trans('DeleteProjectcostspreadmember'),$langs->trans('ConfirmDelete'),'sub_confirm_delete', '', 0, 1);
         if ($ret == 'html') print '<br />';
         //to have the object to be deleted in the background\
        }
        break;
      case 'sub_add':
        $sub_object->group_id=GETPOST('Groupid');
	$sub_object->member_id=GETPOST('Memberid');
        $result=$sub_object->create($user);
        if ($result > 0)
        {
                // Creation OK
            // remove the tms
               unset($_SESSION['Projectcostspreadmember_'.$tms]);
               setEventMessage('RecordSucessfullyCreated', 'mesgs');
               //ProjectcostspreadmemberReloadPage($backtopage,$result,'');

        }else
        {
                // Creation KO
                if (! empty($sub_object->errors)) setEventMessages(null, $sub_object->errors, 'errors');
                else  setEventMessage('RecordNotSucessfullyCreated', 'errors');
                $action='sub_create';
        }                            
        break;
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
    }                     


/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

if(!$sublist)llxHeader('','Projectcostspreadmember','');
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
    $sql.=' t.group_id,';
    $sql.=' t.member_id,';
    $sql.=' j.ratio';
    
    $sql.= ' FROM '.MAIN_DB_PREFIX.'project_cost_spread_member as t';
    $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'project_cost_spread as j ON t.member_id =j.rowid';
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
        
    	if($ls_group_id) $sqlwhere .= natural_search(array('t.group_id'), $ls_group_id);
	if($ls_member_id) $sqlwhere .= natural_search(array('t.member_id'), $ls_member_id);

     $sql.=' WHERE group_id=\''.$parent.'\' ';
    //list limit
    if(!empty($sqlwhere))
        $sql.=' AND '.substr ($sqlwhere, 5);
    
// Count total nb of records
$nbtotalofrecords = 0;
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
{
        $sqlcount='SELECT COUNT(*) as count FROM '.MAIN_DB_PREFIX.'project_cost_spread_member as t';
        $sqlcount.=' WHERE group_id=\''.$parent.'\' ';
        if(!empty($sqlwhere))
            $sqlcount.=' AND '.substr ($sqlwhere, 5);
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
        	if (!empty($ls_group_id))	$param.='&ls_group_id='.urlencode($ls_group_id);
	if (!empty($ls_member_id))	$param.='&ls_member_id='.urlencode($ls_member_id);

        
        if ($filter && $filter != -1) $param.='&filtre='.urlencode($filter);
        
        $num = $db->num_rows($resql);
        //print_barre_liste function defined in /core/lib/function.lib.php, possible to add a picto
        print_barre_liste($langs->trans("Projectcostspreadmember"),$page,$PHP_SELF,$param,$sortfield,$sortorder,'',$num,$nbtotalofrecords);
        print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'title_companies', 0, '', '', $limit);

        print '<form method="POST" action="'.$PHP_SELF.'?action=sub_add&id='.$parent.'&Projectid='.$projectid.'">';
       print '<table class="liste" width="100%">'."\n";
        //TITLE
        print '<tr class="liste_titre">';
        //print_liste_field_titre($langs->trans('Groupid'),$PHP_SELF,'t.group_id','',$param,'',$sortfield,$sortorder);
	print "\n";
	print_liste_field_titre($langs->trans('Member'),$PHP_SELF,'t.member_id','',$param,'',$sortfield,$sortorder);
	print "\n";
        print_liste_field_titre($langs->trans('Ratio'),$PHP_SELF,'j.ratio','',$param,'',$sortfield,$sortorder);
	print "\n";

        
        print '</tr>';
        //SEARCH FIELDS
        /*
        print '<tr class="liste_titre">'; 
        //Search field forgroup_id
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_group_id" value="'.$ls_group_id.'">';
	print '</td>';
//Search field formember_id
	print '<td class="liste_titre" colspan="1" >';
	print '<input class="flat" size="16" type="text" name="ls_member_id" value="'.$ls_member_id.'">';
	print '</td>';

        
        
        print '<td width="15px">';
        print '<input type="image" class="liste_titre" name="search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("Search")).'" title="'.dol_escape_htmltag($langs->trans("Search")).'">';
        print '<input type="image" class="liste_titre" name="removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
        print '</td>';
        print '</tr>'."\n"; */
        $i=0;
        // Quick add
        print '<tr><td>';
        $sqlarray=array('table'=>'project_cost_spread','keyfield'=>'rowid','fields'=>'ref,label' );
        $sqlarraymember=$sqlarray;
        $sqlarraymember['where']='(isgroup is NULL or isgroup =0) AND rowid not in (SELECT member_id FROM '.MAIN_DB_PREFIX.'project_cost_spread_member WHERE group_id = '.$parent.' )';

        $htmlarray=array('name'=>'Memberid','separator'=> ' - ');
        print '<input type="hidden" name="tms" value="'.$tms.'">';
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
      
        print '<input type="hidden" value="'.$parent.'" name="Groupid"> ';//.print_sellist($sqlarray, $parent, '' );

        //print '</td><td>';

        print select_sellist($sqlarraymember,$htmlarray,'',array());

        print '</td><td><input class="butAction" type="submit" value="Add">';
               //new speard button
        print '<a href="spread_card.php?action=create&Projectid='.$projectid.'" class="butAction" role="button">'.$langs->trans('New');
        print ' '.$langs->trans('Projectcostspread')."</a>\n</td></tr>";
        
        // list of entries
        while ($i < $num && $i<$limit)
        {
            $obj = $db->fetch_object($resql);
            if ($obj)
            {
                
                // You can use here results
               print "<tr class=\"oddeven\">";
		//print "<td>".print_sellist($sqlarray, $obj->group_id, '' )."</td>";
		print "<td>".print_sellist($sqlarray, $obj->member_id, $htmlarray['separator'] )."</td>";
		print "<td>".$obj->ratio."</td>";
		print '<td><a href="'.$PHP_SELF.'?action=sub_delete&Projectid='.$projectid.'&id='.$parent.'&sub_id='.$obj->rowid.'">'.img_delete().'</a></td>';
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
    print '</from>'."\n";
    // new button

dol_fiche_end();



// End of page
if(!$sublist)llxFooter();
if(!$sublist)$db->close();
