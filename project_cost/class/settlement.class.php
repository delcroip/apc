<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
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
 *  \file       dev/projectsettlements/settlement.class.php
 *  \ingroup    project_cost othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2018-07-21 21:29
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
include_once 'settlementdet.class.php';
$settlmentStatusPictoArray=array(0=> 'statut7',1=>'statut3',2=>'statut8',3=>'statut4');
$settlmentStatusArray=array(0=> 'Draft',1=>'Validated',2=>'Cancelled',3 =>'Payed');
/**
 *	Put here description of your class
 */
class Projectsettlement extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */				//!< To return several error codes (or messages)
    public $element='projectsettlement';			//!< Id that identify managed objects
    /**
     * @var string Name of table without prefix where object is stored
     */    
    public $table_element='project_cost_settlement';		//!< Name of table without prefix where object is stored

    public $id;
    // BEGIN OF automatic var creation
    
	public $ref;
	public $entity;
	public $label;
	public $project;
	public $description;
	public $date_settlement='';
	public $date_creation='';
	public $date_modification='';
	public $user_creat;
	public $user_modif;
	public $import_key;
	public $status;
	public $intermediate;

    
    // END OF automatic var creation


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        $this->cleanParam();

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
        $sql = "INSERT INTO ".MAIN_DB_PREFIX.$this->table_element."(";
        
		$sql.= 'ref,';
		$sql.= 'label,';
		$sql.= 'fk_project,';
		$sql.= 'description,';
		$sql.= 'date_settlement,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_creat,';
		$sql.= 'import_key,';
		$sql.= 'intermediate,';
		$sql.= 'status';

        
        $sql.= ") VALUES (";
        
		$sql.=' '.(empty($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql.=' '.(empty($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql.=' '.(empty($this->project)?'NULL':"'".$this->project."'").',';
		$sql.=' '.(empty($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql.=' '.(empty($this->date_settlement) || dol_strlen($this->date_settlement)==0?'NULL':"'".$this->db->idate($this->date_settlement)."'").',';
		$sql.=' NOW() ,';
		$sql.='\''.$user->id.'\',';
		$sql.=' '.(empty($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'").',';
		$sql.=' '.(empty($this->intermediate)?'NULL':"'".$this->intermediate."'").',';
		$sql.=' '.(empty($this->status)?'0':"'".$this->status."'").'';

        
        $sql.= ")";

        $this->db->begin();

        dol_syslog(__METHOD__, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

        if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);

            if (! $notrigger)
            {
            // Uncomment this and change MYOBJECT to your own tag if you
            // want this action calls a trigger.

            //// Call triggers
            //$result=$this->call_trigger('MYOBJECT_CREATE',$user);
            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
            //// End call triggers
            }
        }

        // Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return $this->id;
        }
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    	Id object
     *  @param	string	$ref	Ref
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id,$ref='', $loadparentdata=1)
    {
    	global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.=' t.ref,';
        $sql.=' t.entity,';
        $sql.=' t.label,';
        $sql.=' t.fk_project,';
        $sql.=' t.description,';
        $sql.=' t.date_settlement,';
        $sql.=' t.date_creation,';
        $sql.=' t.date_modification,';
        $sql.=' t.fk_user_creat,';
        $sql.=' t.fk_user_modif,';
        $sql.=' t.import_key,';
        $sql.=' t.intermediate,';
        $sql.=' t.status';

        
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
        if ($ref) $sql.= " WHERE t.ref = '".$ref."'";
        else $sql.= " WHERE t.rowid = ".$id;
    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
                $this->id    = $obj->rowid;
                
                $this->ref = $obj->ref;
                $this->entity = $obj->entity;
                $this->label = $obj->label;
                $this->project = $obj->fk_project;
                $this->description = $obj->description;
                $this->date_settlement = $this->db->jdate($obj->date_settlement);
                $this->date_creation = $this->db->jdate($obj->date_creation);
                $this->date_modification = $this->db->jdate($obj->date_modification);
                $this->user_creat = $obj->fk_user_creat;
                $this->user_modif = $obj->fk_user_modif;
                $this->import_key = $obj->import_key;
                $this->intermediate = $obj->intermediate;
                $this->status = $obj->status;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user, $notrigger=0)
    {
	$error=0;
        // Clean parameters
        $this->cleanParam(true);
        // Check parameters
        // Put here code to add a control on parameters values
        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET";
        $sql.= $this->setSQLfields($user);
        $sql.= " WHERE rowid=".$this->id;
	$this->db->begin();
	dol_syslog(__METHOD__);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
            if (! $error)
            {
                if (! $notrigger)
                {
            // Uncomment this and change MYOBJECT to your own tag if you
            // want this action calls a trigger.

            //// Call triggers
            //$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
            //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
            //// End call triggers
                 }
            }

        // Commit or rollback
            if ($error)
            {
                foreach($this->errors as $errmsg)
                {
                    dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
                    $this->error.=($this->error?', '.$errmsg:$errmsg);
                }
                $this->db->rollback();
                return -1*$error;
            }
            else
            {
                $this->db->commit();
                return 1;
            }
    }

 
     /**
     *	Return clickable name (with picto eventually)
     *
     *	@param		string			$htmlcontent 		text to show
     *	@param		int			$id                     Object ID
     *	@param		string			$ref                    Object ref
     *	@param		int			$withpicto		0=_No picto, 1=Includes the picto in the linkn, 2=Picto only
     *	@return		string						String with URL
     */
    function getNomUrl($htmlcontent,$id=0,$ref='',$withpicto=0)
    {
	global $conf, $langs;


        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips
    	$result='';
        if(empty($ref) && $id==0){
            if(isset($this->id))  {
                $id=$this->id;
            }else if (isset($this->rowid)){
                $id=$this->rowid;
            }if(isset($this->ref)){
                $ref=$this->ref;
            }
        }
        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("Showshare");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }else $linkclose = ($morecss?' class="'.$morecss.'"':'');
        
        if($id){
            $lien = '<a href="'.dol_buildpath('/project_cost/settlement_card.php',1).'id='.$id.'&Projectid='.$this->project.'&action=view"'.$linkclose.'>';
        }else if (!empty($ref)){
            $lien = '<a href="'.dol_buildpath('/project_cost/settlement_card.php',1).'?ref='.$ref.'&Projectid='.$this->project.'&action=view"'.$linkclose.'>';
        }else{
            $lien =  "";
        }
        $lienfin=empty($lien)?'':'</a>';

    	$picto='generic';
        $label = '<u>' . $langs->trans("share") . '</u>';
        $label.= '<br>';
        if($ref){
            $label.=$langs->trans("Red").': '.$ref;
        }else if($id){
            $label.=$langs->trans("#").': '.$id;
        }
        
        
        
    	if ($withpicto==1){ 
            $result.=($lien.img_object($label,$picto).$htmlcontent.$lienfin);
        }else if ($withpicto==2) {
            $result.=$lien.img_object($label,$picto).$lienfin;
        }else{  
            $result.=$lien.$htmlcontent.$lienfin;
        }
    	return $result;
    }  
            /**
	 *  Retourne select libelle du status (actif, inactif)
	 *
	 *  @param	object 		$form          form object that should be created	
      *  *  @return	string 			       html code to select status
	 */
	function selectLibStatut($form,$htmlname='Status')
	{
            global $settlmentStatusArray;
            return $form->selectarray($htmlname,$settlmentStatusArray,$this->status);
	}    
    /**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs,$settlmentStatusPictoArray,$settlmentStatusArray;

                if($status=="")$status=0;
		if ($mode == 0)
		{
			$prefix='';
			return $langs->trans($settlmentStatusArray[$status]);
		}
		if ($mode == 1)
		{
			return $langs->trans($settlmentStatusArray[$status]);
		}
		if ($mode == 2)
		{
			 return img_picto($settlmentStatusArray[$status],$settlmentStatusPictoArray[$status]).' '.$langs->trans($settlmentStatusArray[$status]);
		}
		if ($mode == 3)
		{
			 return img_picto($settlmentStatusArray[$status],$settlmentStatusPictoArray[$status]);
		}
		if ($mode == 4)
		{
			 return img_picto($settlmentStatusArray[$status],$settlmentStatusPictoArray[$status]).' '.$langs->trans($settlmentStatusArray[$status]);
		}
		if ($mode == 5)
		{
			 return $langs->trans($settlmentStatusArray[$status]).' '.img_picto($settlmentStatusArray[$status],$settlmentStatusPictoArray[$status]);
		}
		if ($mode == 6)
		{
			 return $langs->trans($settlmentStatusArray[$status]).' '.img_picto($settlmentStatusArray[$status],$settlmentStatusPictoArray[$status]);
		}
	}

    /**
     *  Delete object in database
     *
    *	@param  User	$user        User that deletes
    *   @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return	int					 <0 if KO, >0 if OK
     */
    function delete($user, $notrigger=0)
    {
        global $conf, $langs;
        $error=0;
        $this->db->begin();
        if (! $error)
        {
            if (! $notrigger)
            {
        // Uncomment this and change MYOBJECT to your own tag if you
        // want this action calls a trigger.
        //// Call triggers
        //$result=$this->call_trigger('MYOBJECT_DELETE',$user);
        //if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
        //// End call triggers
            }
        }
        if (! $error)
        {
        $sql = "DELETE FROM ".MAIN_DB_PREFIX.$this->table_element;
        $sql.= " WHERE rowid=".$this->id;

        dol_syslog(__METHOD__);
        $resql = $this->db->query($sql);
        if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        else if ($this->db->affected_rows($resql)==0){$error++;$this->errors[]="Item no found in database"; }
        
        }

// Commit or rollback
        if ($error)
        {
            foreach($this->errors as $errmsg)
            {
                dol_syslog(__METHOD__." ".$errmsg, LOG_ERR);
                $this->error.=($this->error?', '.$errmsg:$errmsg);
            }
            $this->db->rollback();
            return -1*$error;
        }
        else
        {
            $this->db->commit();
            return 1;
        }
    }



    /**
     *	Load an object from its id and create a new one in database
     *
     *	@param	int		$fromid     Id of object to clone
     * 	@return	int					New id of clone
     */
    function createFromClone($fromid)
    {
        global $user,$langs;
        $error=0;
        $object=new Projectsettlement($this->db);
        $this->db->begin();
        // Load source object
        $object->fetch($fromid);
        $object->id=0;
        $object->statut=0;
        // Clear fields
        // ...
        // Create clone
        $result=$object->create($user);

        // Other options
        if ($result < 0)
        {
            $this->error=$object->error;
            $error++;
        }
        if (! $error)
        {
        }
        // End
        if (! $error)
        {
            $this->db->commit();
            return $object->id;
        }
        else
        {
            $this->db->rollback();
            return -1;
        }
    }

    /**
     *	Initialise object with example values
     *	Id must be 0 if object instance is a specimen
     *
     *	@return	void
     */
    function initAsSpecimen()
    {
        $this->id=0;
        
		$this->ref='';
		$this->entity='';
		$this->label='';
		$this->project='';
		$this->description='';
		$this->date_settlement='';
		$this->date_creation='';
		$this->date_modification='';
		$this->user_creat='';
		$this->user_modif='';
		$this->import_key='';
		$this->intermediate='';
		$this->status='';

        
    }
    /**
     *	will clean the parameters
     *	
     *
     *	@return	void
     */       
    function cleanParam(){
        
		if (!empty($this->ref)) $this->ref=trim($this->ref);
		if (!empty($this->label)) $this->label=trim($this->label);
		if (!empty($this->project)) $this->project=trim($this->project);
		if (!empty($this->description)) $this->description=trim($this->description);
		if (!empty($this->date_settlement)) $this->date_settlement=trim($this->date_settlement);
		if (!empty($this->date_creation)) $this->date_creation=trim($this->date_creation);
		if (!empty($this->date_modification)) $this->date_modification=trim($this->date_modification);
		if (!empty($this->user_creat)) $this->user_creat=trim($this->user_creat);
		if (!empty($this->user_modif)) $this->user_modif=trim($this->user_modif);
		if (!empty($this->import_key)) $this->import_key=trim($this->import_key);
		if (!empty($this->intermediate)) $this->intermediate=trim($this->intermediate);
		if (!empty($this->status)) $this->status=trim($this->status);

        
    }
     /**
     *	will create the sql part to update the parameters
     *	
     *
     *	@return	void
     */    
    function setSQLfields($user){
        $sql='';
        $sql.=' ref='.(empty($this->ref)!=0 ? 'null':"'".$this->db->escape($this->ref)."'").',';
        $sql.=' label='.(empty($this->label)!=0 ? 'null':"'".$this->db->escape($this->label)."'").',';
        $sql.=' fk_project='.(empty($this->project)!=0 ? 'null':"'".$this->project."'").',';
        $sql.=' description='.(empty($this->description)!=0 ? 'null':"'".$this->db->escape($this->description)."'").',';
        $sql.=' date_settlement='.(dol_strlen($this->date_settlement)!=0 ? "'".$this->db->idate($this->date_settlement)."'":'null').',';
        $sql.=' date_modification=NOW() ,';
        $sql.=' fk_user_modif="'.$user->id.'",';
        $sql.=' import_key='.(empty($this->import_key)!=0 ? 'null':"'".$this->db->escape($this->import_key)."'").',';
        $sql.=' intermediate='.(empty($this->intermediate)!=0 ? 'null':"'".$this->intermediate."'").',';
        $sql.=' status='.(empty($this->status)!=0 ? '0':"'".$this->status."'").'';

        return $sql;            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
                $this->id    = $obj->rowid;
                
                $this->ref = $obj->ref;
                $this->entity = $obj->entity;
                $this->label = $obj->label;
                $this->project = $obj->fk_project;
                $this->description = $obj->description;
                $this->date_settlement = $this->db->jdate($obj->date_settlement);
                $this->date_creation = $this->db->jdate($obj->date_creation);
                $this->date_modification = $this->db->jdate($obj->date_modification);
                $this->user_creat = $obj->fk_user_creat;
                $this->user_modif = $obj->fk_user_modif;
                $this->import_key = $obj->import_key;
                $this->intermediate = $obj->intermediate;
                $this->status = $obj->status;

                
            }
    }
    /*Funciton to generate the settlement details
     * 
     */
    function generateSettlementDet($userin){
        global $user;
        if(!is_object($userin))$userin=user;
        // Select the cost that are not yet covered by a Settlement for the project, / managmeent on the settlememt 
        $sql="SELECT MAX(pcl.amount) as amount,MAX(pcl.vat_amount) as vat_amount, SUM(psd.amount) as sent_amount,SUM(psd.vat_amount) as sent_vat, MAX(pcl.date_start) as date_start, MAX(pcl.date_end)  as date_stop, ";
        $sql.= "pcl.rowid,MAX(pcl.status) as status ,MAX(pcl.fk_project) as fk_project,"; // GROUP and WHERE
        $sql.= " MAX(pcl.c_project_cost_type) as c_project_cost_type";//join from cost type
        $sql.= " FROM ".MAIN_DB_PREFIX."project_cost_line as pcl";
        $sql.= " LEFT JOIN  ".MAIN_DB_PREFIX."project_cost_settlement_det as psd";
        $sql.= " ON  psd.fk_project_cost_line = pcl.rowid";
        $sql.= " LEFT JOIN  ".MAIN_DB_PREFIX."project_cost_settlement as ps";
        $sql.= " ON  psd.fk_settlement = ps.rowid";
        $sql.= " WHERE  pcl.fk_project='".$this->project."' AND pcl.status='1'";
        //$sql.= ($this->intermediate>0)?("OR ps.intermediate='".$this->intermediate."')"):')'; // take only the settlement null 
        $sql.= " AND  pcl.date_start<'".$this->db->idate($this->date_settlement)."'";
        $sql.= " GROUP BY pcl.rowid   "; 
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $i=0;
            $num = $this->db->num_rows($resql);
            while ($i < $num )
            {
                $obj = $this->db->fetch_object($resql);
                if ($obj)
                {
                    $date_start=$this->db->jdate($obj->date_start);
                    $date_stop=$this->db->jdate($obj->date_stop);
                    $cost_ended=($date_stop<$this->date_settlement);
                    $prorata=$cost_ended?1:($this->date_settlement-$date_start)/($date_stop-$date_start);                  
                    $det=new Projectsettlementdet($this->db);
                    $det->settlement=$this->id;
                    $det->project_cost_line=$obj->rowid;
                    $det->amount=$obj->amount*$prorata - $obj->sent_amount;
                    //$det->c_project_cost_type=$obj->c_project_cost_type; // FIXME cost type should be enough
                    $det->vat_amount=$obj->vat_amount*$prorata - $obj->sent_vat;
                    //if there is no amount then clear the record
                    if(round($det->amount,3)<>0 || round($det->vat_amount,3)<>0){
                        $det->create($userin);
                    }
                   
                    $i++;
                    
                }
            }
            return 1;
        }
        return -1;
    }
    
    function get_balance($withPartial=true){
        $resArray=array();
        //$lastfullsettlement=$this->get_lastsettlement();
        $sql="SELECT psd.rowid as det,psd.amount as det_amount, psd.vat_amount as det_vat_amount, ";
        $sql.=" ps.rowid as settlement, ps.ref as settlement_ref,ps.label as settlement_label, date_settlement,";
        $sql.=" pcl.label as cost_label,pcl.date_start as cost_start,pcl.date_end  as cost_end,";
        $sql.=" cpct.capex_ratio,cpct.taxe_benefit_ratio, cpct.label as type_label, ratio_2b_used,";
        $sql.=" pcs.date_start as share_start,pcs.date_end as share_end,pcsh.fk_soc,pcsh.date_start as shareholder_start, pcs.ref as share_ref,pcs.label as share_label,";
        $sql.=" pcs.ratio_1,pcs.ratio_2,pcs.ratio_3,pcs.ratio_4,pcs.ratio_5,";
        $sql.=" total.ratio_total_1,total.ratio_total_2,total.ratio_total_3,total.ratio_total_4,total.ratio_total_5,";
        $sql.=" psm.group_id as share_group";
        $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement_det as psd ";
        // join settlement
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_settlement as ps ON  psd.fk_settlement=ps.rowid";
        //join cost
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_line  as pcl ON psd.fk_project_cost_line=pcl.rowid";
        //join cost type
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."c_project_cost_type as cpct ON pcl.c_project_cost_type=cpct.rowid";
        //join share member ( used only when share group are used)
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_share_member as psm ON  psm.group_id= pcl.fk_project_cost_share";
        // join share  
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_share as pcs ON CASE";
        // if no share is speficied then all the project share are used (active at date settlement and part of the project) 
        $sql.=" WHEN  (pcl.fk_project_cost_share = '0' OR pcl.fk_project_cost_share is NULL)  AND pcs.isgroup < '1' AND (pcs.date_start is NULL OR pcs.date_start <= ps.date_settlement) AND (pcs.date_end IS NULL or pcs.date_end >= ps.date_settlement) THEN '1'";
        // if there is a specific share linked
        $sql.=" WHEN  pcl.fk_project_cost_share > '0' AND  pcs.isgroup < '1' AND pcl.fk_project_cost_share= pcs.rowid  THEN '1'";
        // if there is a share group linked
        $sql.=" WHEN  pcl.fk_project_cost_share > '0'  AND  pcs.isgroup = '1' AND psm.member_id= pcs.rowid THEN '1'";
        $sql.=" ELSE '0' END ='1'";
        // join owner
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_share_holder as pcsh ON pcsh.rowid =(SELECT rowid from ".MAIN_DB_PREFIX."project_cost_share_holder as pcsh2 WHERE pcs.rowid= pcsh2.fk_project_cost_share ORDER BY date_start DESC LIMIT 1)";
        // total ratio for the spoeficic line
        $sql.=" LEFT OUTER JOIN (SELECT psd2.rowid as psd_id, SUM(ratio_1) as ratio_total_1,SUM(ratio_2) as ratio_total_2,SUM(ratio_3) as ratio_total_3,SUM(ratio_4) as ratio_total_4,SUM(ratio_5) as ratio_total_5";
        $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement_det as psd2 ";
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_settlement as ps2 ON  psd2.fk_settlement=ps2.rowid";
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_line  as pcl2 ON psd2.fk_project_cost_line=pcl2.rowid";
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_share_member as psm2 ON  psm2.group_id= pcl2.fk_project_cost_share";
        $sql.=" LEFT JOIN ".MAIN_DB_PREFIX."project_cost_share as pcs2 ON CASE";
        // if no share is speficied then all the project share are used (active at date settlement and part of the project) 
        $sql.=" WHEN  (pcl2.fk_project_cost_share = '0' OR  pcl2.fk_project_cost_share is NULL)  AND pcs2.isgroup < '1' AND (pcs2.date_start is NULL OR pcs2.date_start <= ps2.date_settlement) AND (pcs2.date_end IS NULL or pcs2.date_end >= ps2.date_settlement) THEN '1'";
        // if there is a specific share linked
        $sql.=" WHEN  pcl2.fk_project_cost_share > '0' AND  pcs2.isgroup < '1' AND pcl2.fk_project_cost_share= pcs2.rowid  THEN '1'";
        // if there is a share group linked
        $sql.=" WHEN  pcl2.fk_project_cost_share > '0'  AND  pcs2.isgroup = '1' AND psm2.member_id= pcs2.rowid THEN '1'";
        $sql.=" ELSE '0' END ='1'";
        $sql.=" WHERE psd2.fk_settlement ={$this->id}";
        $sql.=" GROUP BY psd2.rowid) as total";
        $sql.=" ON total.psd_id=psd.rowid";   
        $sql.=" WHERE psd.fk_settlement={$this->id} AND  pcs.fk_project ={$this->project}";

        $sql.=" ORDER BY  settlement,share_ref, fk_soc,cpct.label";        
        
        $resql=$this->db->query($sql);
        if ($resql)
        {
            
            $num = $this->db->num_rows($resql);
            for($i=0;$i<$num;$i++){
                
                $obj = $this->db->fetch_object($resql);
                $resArray[$obj->settlement][$obj->share_ref][$obj->fk_soc][$obj->det]=array(
                'det_amount' =>  $obj->det_amount,
                'det_vat_amount' =>  $obj->det_vat_amount,
              //  'settlement_ref' =>  $obj->settlement_ref,
                'settlement_label' =>  $obj->settlement_label,
                'date_settlement' =>  $obj->date_settlement,
                'cost_label' =>  $obj->cost_label,
                'cost_start' =>  $obj->cost_start,
                'cost_end' =>  $obj->cost_end,
                'capex_ratio' =>  $obj->capex_ratio,
                'taxe_benefit_ratio' =>  $obj->taxe_benefit_ratio,
                'type_label' =>  $obj->type_label,
                'ratio_2b_used' =>  $obj->ratio_2b_used,
                'share_start' =>  $obj->share_start,
                'share_end' =>  $obj->share_end,
                'share_label' =>  $obj->share_label,
                'share_group' =>  $obj->share_group,
                'ratio_1' =>  $obj->ratio_1,
                'ratio_2' =>  $obj->ratio_2,
                'ratio_3' =>  $obj->ratio_3,
                'ratio_4' =>  $obj->ratio_4,
                'ratio_5' =>  $obj->ratio_5,
                'ratio_total_1' =>  $obj->ratio_total_1,
                'ratio_total_2' =>  $obj->ratio_total_2,
                'ratio_total_3' =>  $obj->ratio_total_3,
                'ratio_total_4' =>  $obj->ratio_total_4,
                'ratio_total_5' =>  $obj->ratio_total_5);
                
                
            }
            $settlementArray=($withPartial)?$this->get_partial_settlement(): NULL;

            if($settlementArray && is_array($settlementArray) && count($settlementArray)>0){
                foreach ($settlementArray as $subSettlement){
                    $subArray=$subSettlement->get_balance(false);
                    if(is_array($subArray))$resArray=array_merge($resArray,$subArray );
                }
            }
          
        }else
        {
            $error++;
            dol_print_error($db);
        }
        return $resArray;
    }
    function get_partial_settlement(){
        $resArray=array();

        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.=' t.ref,';
        $sql.=' t.entity,';
        $sql.=' t.label,';
        $sql.=' t.fk_project,';
        $sql.=' t.description,';
        $sql.=' t.date_settlement,';
        $sql.=' t.date_creation,';
        $sql.=' t.date_modification,';
        $sql.=' t.fk_user_creat,';
        $sql.=' t.fk_user_modif,';
        $sql.=' t.import_key,';
        $sql.=' t.intermediate,';
        $sql.=' t.status';
        $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement as t ";
        $sql.=" WHERE  date_settlement BETWEEN  (SELECT MAX(date_settlement) as date ";
            $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement ";
            $sql.=" WHERE date_settlement < '".$this->db->idate($this->date_settlement)."'";
            $sql.=" AND (intermediate IS NULL OR intermediate=0 )";
            $sql.=" AND fk_project='".$this->project."') ";
        $sql.=" AND '".$this->db->idate($this->date_settlement)."' AND intermediate ='1'";
        $sql.=" AND fk_project='".$this->project."' ORDER BY date_settlement ";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            
            for ($i=0;$i<$num;$i++)
            {
                $resArray[$i]=new Projectsettlement($this->db);
                $obj = $this->db->fetch_object($resql);
                $resArray[$i]->id    = $obj->rowid;
                $resArray[$i]->ref = $obj->ref;
                $resArray[$i]->entity = $obj->entity;
                $resArray[$i]->label = $obj->label;
                $resArray[$i]->project = $obj->fk_project;
                $resArray[$i]->description = $obj->description;
                $resArray[$i]->date_settlement = $this->db->jdate($obj->date_settlement);
                $resArray[$i]->date_creation = $this->db->jdate($obj->date_creation);
                $resArray[$i]->date_modification = $this->db->jdate($obj->date_modification);
                $resArray[$i]->user_creat = $obj->fk_user_creat;
                $resArray[$i]->user_modif = $obj->fk_user_modif;
                $resArray[$i]->import_key = $obj->import_key;
                $resArray[$i]->intermediate = $obj->intermediate;
                $resArray[$i]->status = $obj->status;
                
            }
            return $resArray;

        }else
        {
            $error++;
            dol_print_error($db);
            return null;
        }
    } 
 /*   function get_last_settlement(){
        $resArray=array();

        $sql = "SELECT";
        $sql.= " t.rowid,";
        $sql.=' t.ref,';
        $sql.=' t.entity,';
        $sql.=' t.label,';
        $sql.=' t.fk_project,';
        $sql.=' t.description,';
        $sql.=' t.date_settlement,';
        $sql.=' t.date_creation,';
        $sql.=' t.date_modification,';
        $sql.=' t.fk_user_creat,';
        $sql.=' t.fk_user_modif,';
        $sql.=' t.import_key,';
        $sql.=' t.intermediate,';
        $sql.=' t.status';
        $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement as t ";
        $sql.=" WHERE  date_settlement BETWEEN  (SELECT MAX(date_settlement) as date ";
            $sql.=" FROM ".MAIN_DB_PREFIX."project_cost_settlement ";
            $sql.=" WHERE date_settlement < '".$this->db->idate($this->date_settlement)."'";
            $sql.=" AND (intermediate IS NULL OR intermediate=0 )";
            $sql.=" AND fk_project='".$this->project."') ";
        $sql.=" AND '".$this->db->idate($this->date_settlement)."' AND intermediate ='1'";
        $sql.=" AND fk_project='".$this->project."' ORDER BY date_settlement ";

        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            
            for ($i=0;$i<$num;$i++)
            {
                $resArray[$i]=new Projectsettlement($this->db);
                $obj = $this->db->fetch_object($resql);
                $resArray[$i]->id    = $obj->rowid;
                $resArray[$i]->ref = $obj->ref;
                $resArray[$i]->entity = $obj->entity;
                $resArray[$i]->label = $obj->label;
                $resArray[$i]->project = $obj->fk_project;
                $resArray[$i]->description = $obj->description;
                $resArray[$i]->date_settlement = $this->db->jdate($obj->date_settlement);
                $resArray[$i]->date_creation = $this->db->jdate($obj->date_creation);
                $resArray[$i]->date_modification = $this->db->jdate($obj->date_modification);
                $resArray[$i]->user_creat = $obj->fk_user_creat;
                $resArray[$i]->user_modif = $obj->fk_user_modif;
                $resArray[$i]->import_key = $obj->import_key;
                $resArray[$i]->intermediate = $obj->intermediate;
                $resArray[$i]->status = $obj->status;
                
            }
            return $resArray;

        }else
        {
            $error++;
            dol_print_error($db);
            return null;
        }
    }*/

}
