<?php
/* 
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

// Change this following line to use the correct relative path (../, ../../, etc)
include 'core/lib/includeMain.lib.php';
include 'class/settlement.class.php';
//require_once 'core/lib/generic.lib.php';
require_once 'core/lib/settlement.lib.php';
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
dol_include_once('/societe/class/societe.class.php');

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

$object= new Projectsettlement($db);
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
// actions

//view

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','ProjectCost - Balance','');

//print_fiche_titre($langs->trans('Balance'));
$project= new Project($db);
$project->fetch($projectid);
$headProject=project_prepare_head($project);
dol_fiche_head($headProject, 'settlement', $langs->trans("Project"), 0, 'project');
 // tabs
$head=ProjectsettlementPrepareHead($object);
dol_fiche_head($head,'balance',$langs->trans('ProjectsettlementBalance'),0,'project_cost@project_cost');            
$resArray=$object->get_balance();
//$resArray[settlement][share_ref][fk_soc][det]
$thirdPaty= New Societe($db);
foreach($resArray as $setid=> $settlement){
    $settlementTitle=true;
    foreach($settlement as $shareref => $lot){
        $lotTitle=true;
        $TotalHT=0;
        $TotalVAT=0;
        if(count($lot)>1){
            print $langs->trans('MultipleOwnerForASingleSettlement');
            exit;
        }
        $socid=array_keys($lot)[0];
         $thirdPaty->fetch($socid);
        $soc=$lot[$socid];
        foreach($soc as $detid => $data){           
            if($settlementTitle) print "</br><h1>".$data["settlement_label"].'</h1></br></br>';$settlementTitle=false;
            if($lotTitle) print '</br>'.$data["share_label"].' - '.$thirdPaty->getNomUrl(1, 'project').'</br>';$lotTitle=false;
            $ratio=$data["ratio_".$data["ratio_2b_used"]]/$data["ratio_total_".$data["ratio_2b_used"]];
            print $data["type_label"].' - '.$data["cost_label"].' - ';
            print $langs->trans("AmountHT").': '.price(round($data["det_amount"]*$ratio,2),0,$outputlangs,1,-1,-1,$conf->currency)."\t";
            print $langs->trans("AmountVAT").': '.price(round($data["det_vat_amount"]*$ratio,2),0,$outputlangs,1,-1,-1,$conf->currency)."\t";
            print $langs->trans("Amount").': '.price(round(($data["det_amount"]+$data["det_vat_amount"])*$ratio,2),0,$outputlangs,1,-1,-1,$conf->currency)."</br>";
            $TotalHT+=$data["det_amount"]*$ratio;
            $TotalVAT+=$data["det_vat_amount"]*$ratio;
        }
        print '<b>Total - ';
        print $langs->trans("AmountHT").': '.price(round($TotalHT,2),0,$outputlangs,1,-1,2,$conf->currency)."\t";
        print $langs->trans("AmountVAT").': '.price(round($TotalVAT,2),0,$outputlangs,1,-1,2,$conf->currency)."\t";
        print $langs->trans("Amount").': '.price(round($TotalHT+$TotalVAT,2),0,$outputlangs,1,-1,2,$conf->currency)."</b></br>";
 
    }
 }
//dol_fiche_end();// balance
dol_fiche_end(); // 
// End of page
llxFooter();
$db->close();