<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
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
 *  \file       dev/projectcostshareholders/shareholder.class.php
 *  \ingroup    project_cost othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2018-09-30 16:06
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

$projectcostshareholderStatusPictoArray=array(0=> 'statut7',1=>'statut3',2=>'statut8',3=>'statut4');
$projectcostshareholderStatusArray=array(0=> 'Draft',1=>'Validated',2=>'Cancelled',3 =>'Payed');
/**
 *	Put here description of your class
 */
class Projectcostshareholder extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */				//!< To return several error codes (or messages)
    public $element='projectcostshareholder';			//!< Id that identify managed objects
    /**
     * @var string Name of table without prefix where object is stored
     */    
    public $table_element='project_cost_share_holder';		//!< Name of table without prefix where object is stored

    public $id;
    // BEGIN OF automatic var creation
    
	public $entity;
	public $soc;
	public $date_creation='';
	public $tms='';
	public $user_creat;
	public $user_modif;
	public $date_start='';
	public $project_cost_share;

    
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
        
		$sql.= 'fk_soc,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_creat,';
		$sql.= 'date_start,';
		$sql.= 'fk_project_cost_share';

        
        $sql.= ") VALUES (";
        
		$sql.=' '.(empty($this->soc)?'NULL':"'".$this->soc."'").',';
		$sql.=' NOW() ,';
		$sql.=' \''.$user->id.'\',';
		$sql.=' '.(empty($this->date_start) || dol_strlen($this->date_start)==0?'NULL':"'".$this->db->idate($this->date_start)."'").',';
		$sql.=' '.(empty($this->project_cost_share)?'NULL':"'".$this->project_cost_share."'").'';

        
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
    function fetch($id,$ref='')
    {
    	global $langs;
        $sql = "SELECT";
        $sql.= " t.rowid,";
        
		$sql.=' t.entity,';
		$sql.=' t.fk_soc,';
		$sql.=' t.date_creation,';
		$sql.=' t.tms,';
		$sql.=' t.fk_user_creat,';
		$sql.=' t.fk_user_modif,';
		$sql.=' t.date_start,';
		$sql.=' t.fk_project_cost_share';

        
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
                
				$this->entity = $obj->entity;
				$this->soc = $obj->fk_soc;
				$this->date_creation = $this->db->jdate($obj->date_creation);
				$this->tms = $this->db->jdate($obj->tms);
				$this->user_creat = $obj->fk_user_creat;
				$this->user_modif = $obj->fk_user_modif;
				$this->date_start = $this->db->jdate($obj->date_start);
				$this->project_cost_share = $obj->fk_project_cost_share;

                
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
            $lien = '<a href="'.dol_buildpath('/project_cost/Projectcostshareholder_card.php',1).'id='.$id.'&action=view"'.$linkclose.'>';
        }else if (!empty($ref)){
            $lien = '<a href="'.dol_buildpath('/project_cost/Projectcostshareholder_card.php',1).'?ref='.$ref.'&action=view"'.$linkclose.'>';
        }else{
            $lien =  "";
        }
        $lienfin=empty($lien)?'':'</a>';

    	$picto='generic';
        $label = '<u>' . $langs->trans("ShareHolder") . '</u>';
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
            global $projectcostshareholderStatusPictoArray,$projectcostshareholderStatusArray;
            return $form->selectarray($htmlname,$projectcostshareholderStatusArray,$this->status);
	}   
    /**
	 *  Retourne le libelle du status (actif, inactif)
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
		global $langs,$projectcostshareholderStatusPictoArray,$projectcostshareholderStatusArray;
		if ($mode == 0)
		{
			$prefix='';
			return $langs->trans($projectcostshareholderStatusArray[$status]);
		}
		if ($mode == 1)
		{
			return $langs->trans($projectcostshareholderStatusArray[$status]);
		}
		if ($mode == 2)
		{
			 return img_picto($projectcostshareholderStatusArray[$status],$projectcostshareholderStatusPictoArray[$status]).' '.$langs->trans($projectcostshareholderStatusArray[$status]);
		}
		if ($mode == 3)
		{
			 return img_picto($projectcostshareholderStatusArray[$status],$projectcostshareholderStatusPictoArray[$status]);
		}
		if ($mode == 4)
		{
			 return img_picto($projectcostshareholderStatusArray[$status],$projectcostshareholderStatusPictoArray[$status]).' '.$langs->trans($projectcostshareholderStatusArray[$status]);
		}
		if ($mode == 5)
		{
			 return $langs->trans($projectcostshareholderStatusArray[$status]).' '.img_picto($projectcostshareholderStatusArray[$status],$projectcostshareholderStatusPictoArray[$status]);
		}
		if ($mode == 6)
		{
			 return $langs->trans($projectcostshareholderStatusArray[$status]).' '.img_picto($projectcostshareholderStatusArray[$status],$projectcostshareholderStatusPictoArray[$status]);
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
        $object=new Projectcostshareholder($this->db);
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
        
		$this->entity='';
		$this->soc='';
		$this->date_creation='';
		$this->tms='';
		$this->user_creat='';
		$this->user_modif='';
		$this->date_start='';
		$this->project_cost_share='';

        
    }
    /**
     *	will clean the parameters
     *	
     *
     *	@return	void
     */       
    function cleanParam(){
        
		if (!empty($this->soc)) $this->soc=trim($this->soc);
		if (!empty($this->date_creation)) $this->date_creation=trim($this->date_creation);
		if (!empty($this->user_creat)) $this->user_creat=trim($this->user_creat);
		if (!empty($this->user_modif)) $this->user_modif=trim($this->user_modif);
		if (!empty($this->date_start)) $this->date_start=trim($this->date_start);
		if (!empty($this->project_cost_share)) $this->project_cost_share=trim($this->project_cost_share);

        
    }
     /**
     *	will create the sql part to update the parameters
     *	
     *
     *	@return	void
     */    
    function setSQLfields($user){
        $sql='';
        
		$sql.=' fk_soc='.(empty($this->soc)!=0 ? 'null':"'".$this->soc."'").',';
		$sql.=' fk_user_modif="'.$user->id.'",';
		$sql.=' date_start='.(dol_strlen($this->date_start)!=0 ? "'".$this->db->idate($this->date_start)."'":'null').',';
		$sql.=' fk_project_cost_share='.(empty($this->project_cost_share)!=0 ? 'null':"'".$this->project_cost_share."'").'';

        
        return $sql;
    }


}
