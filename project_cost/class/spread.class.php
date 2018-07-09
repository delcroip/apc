<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014	   Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2018	   Patrick DELCROIX     <pmpdelcroix@gmail.com>
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
 *  \file       dev/projectcostspreads/projectcostspread.class.php
 *  \ingroup    project_cost othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2018-05-27 19:29
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Projectcostspread extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */				//!< To return several error codes (or messages)
    public $element='projectcostspread';			//!< Id that identify managed objects
    /**
     * @var string Name of table without prefix where object is stored
     */    
    public $table_element='project_cost_spread';		//!< Name of table without prefix where object is stored

    public $id;
    // BEGIN OF automatic var creation
    
	public $ref;
	public $entity;
	public $label;
	public $ratio;
	public $soc;
	public $description;
	public $date_creation='';
	public $tms='';
	public $user_creat;
	public $user_modif;
	public $import_key;
	public $status;
	public $c_sellist;
	public $sellist_selected_id;
	public $isgroup;
        public $project;
	public $date_start='';
	public $date_end='';
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
		$sql.= 'ratio,';
		$sql.= 'fk_soc,';
		$sql.= 'description,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_creat,';
		$sql.= 'status,';
		$sql.= 'c_sellist,';
		$sql.= 'fk_sellist_selected_id,';
		$sql.= 'isgroup,';
                $sql.= 'fk_project,';
		$sql.= 'date_start,';
		$sql.= 'date_end';
        
        $sql.= ") VALUES (";
        
		$sql.=' '.(empty($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql.=' '.(empty($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql.=' '.(empty($this->ratio)?'NULL':"'".$this->ratio."'").',';
		$sql.=' '.(empty($this->soc)?'NULL':"'".$this->soc."'").',';
		$sql.=' '.(empty($this->description)?'NULL':"'".$this->db->escape($this->description)."'").',';
		$sql.=' NOW() ,';
		$sql.=' \''.$user->id.'\',';
		$sql.=' '.(empty($this->status)?'NULL':"'".$this->status."'").',';
		$sql.=' '.(empty($this->c_sellist)?'NULL':"'".$this->c_sellist."'").',';
		$sql.=' '.(empty($this->sellist_selected_id)?'NULL':"'".$this->sellist_selected_id."'").',';
		$sql.=' '.(empty($this->isgroup)?'0':"'".$this->isgroup."'").',';
		$sql.=' '.(empty($this->project)?'0':"'".$this->project."'").',';
                $sql.=' '.(empty($this->date_start) || dol_strlen($this->date_start)==0?'NULL':"'".$this->db->idate($this->date_start)."'").',';
		$sql.=' '.(empty($this->date_end) || dol_strlen($this->date_end)==0?'NULL':"'".$this->db->idate($this->date_end)."'").'';
        
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
        
        $sql.=' t.ref,';
        $sql.=' t.entity,';
        $sql.=' t.label,';
        $sql.=' t.ratio,';
        $sql.=' t.fk_soc,';
        $sql.=' t.description,';
        $sql.=' t.date_creation,';
        $sql.=' t.tms,';
        $sql.=' t.fk_user_creat,';
        $sql.=' t.fk_user_modif,';
        $sql.=' t.import_key,';
        $sql.=' t.status,';
        $sql.=' t.c_sellist,';
        $sql.=' t.fk_sellist_selected_id,';
        $sql.=' t.isgroup,';
        $sql.=' t.fk_project,';
        $sql.=' t.date_start,';
        $sql.=' t.date_end';
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
                $this->ratio = $obj->ratio;
                $this->soc = $obj->fk_soc;
                $this->description = $obj->description;
                $this->date_creation = $this->db->jdate($obj->date_creation);
                $this->tms = $this->db->jdate($obj->tms);
                $this->user_creat = $obj->fk_user_creat;
                $this->user_modif = $obj->fk_user_modif;
                $this->import_key = $obj->import_key;
                $this->status = $obj->status;
                $this->c_sellist = $obj->c_sellist;
                $this->sellist_selected_id = $obj->fk_sellist_selected_id;
                $this->isgroup = $obj->isgroup;
                $this->project = $obj->fk_project;
                $this->date_start = $this->db->jdate($obj->date_start);
                $this->date_end = $this->db->jdate($obj->date_end);
                
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
                $this->fetch($id);
            }if(isset($this->ref)){
                $ref=$this->ref;
            }
        }else
        {
            $this->fetch($id);
        }
        
        $linkclose='';
        if (empty($notooltip))
        {
            if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
            {
                $label=$langs->trans("Showspread");
                $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
            }
            $linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
        }else $linkclose = ($morecss?' class="'.$morecss.'"':'');
        
        if($id){
            $lien = '<a href="'.dol_buildpath('/project_cost/spread_card.php',1).'?id='.$id.'&action=view&Projectid='.$this->project.'"'.$linkclose.'>';
        }else if (!empty($ref)){
            $lien = '<a href="'.dol_buildpath('/project_cost/spread_card.php',1).'?ref='.$ref.'&action=view&Projectid='.$this->project.'"'.$linkclose.'>';
        }else{
            $lien =  "";
        }
        $lienfin=empty($lien)?'':'</a>';

    	$picto='generic';
        $label = '<u>' . $langs->trans("spread") . '</u>';
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
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}

	/**
	 *	Charge les informations d'ordre info dans l'objet commande
	 *
	 *	@param  int		$id       Id of order
	 *	@return	void
	 */
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
        $object=new Projectcostspread($this->db);
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
        $this->ratio='';
        $this->soc='';
        $this->description='';
        $this->date_creation='';
        $this->tms='';
        $this->user_creat='';
        $this->user_modif='';
        $this->import_key='';
        $this->status='';
        $this->c_sellist='';
        $this->sellist_selected_id='';
        $this->isgroup='';
        $this->project='';
        $this->date_start='';
        $this->date_end='';
        
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
        if (!empty($this->ratio)) $this->ratio=trim($this->ratio);
        if (!empty($this->soc)) $this->soc=trim($this->soc);
        if (!empty($this->description)) $this->description=trim($this->description);
        if (!empty($this->date_creation)) $this->date_creation=trim($this->date_creation);
        if (!empty($this->user_creat)) $this->user_creat=trim($this->user_creat);
        if (!empty($this->user_modif)) $this->user_modif=trim($this->user_modif);
        if (!empty($this->import_key)) $this->import_key=trim($this->import_key);
        if (!empty($this->status)) $this->status=trim($this->status);
        if (!empty($this->c_sellist)) $this->c_sellist=trim($this->c_sellist);
        if (!empty($this->sellist_selected_id)) $this->sellist_selected_id=trim($this->sellist_selected_id);
        if (!empty($this->isgroup)) $this->isgroup=trim($this->isgroup);
        if (!empty($this->project)) $this->project=trim($this->project);
        if (!empty($this->date_start)) $this->date_start=trim($this->date_start);
        if (!empty($this->date_end)) $this->date_end=trim($this->date_end);

    }
     /**
     *	will create the sql part to update the parameters
     *	
     *
     *	@return	void
     */    
    function setSQLfields($user){
        $sql='';
        
        $sql.=' ref='.(empty($this->ref) ? 'null':"'".$this->db->escape($this->ref)."'").',';
        $sql.=' label='.(empty($this->label) ? 'null':"'".$this->db->escape($this->label)."'").',';
        $sql.=' ratio='.(empty($this->ratio) ? 'null':"'".$this->ratio."'").',';
        $sql.=' fk_soc='.(empty($this->soc) ? 'null':"'".$this->soc."'").',';
        $sql.=' description='.(empty($this->description) ? 'null':"'".$this->db->escape($this->description)."'").',';
        $sql.=' fk_user_modif='."'".$user->id."',";
        $sql.=' import_key='.(empty($this->import_key) ? 'null':"'".$this->db->escape($this->import_key)."'").',';
        $sql.=' status='.(empty($this->status)? 'null':"'".$this->status."'").',';
        $sql.=' c_sellist='.(empty($this->c_sellist)? 'null':"'".$this->c_sellist."'").',';
        $sql.=' fk_sellist_selected_id='.(empty($this->sellist_selected_id)? 'null':"'".$this->sellist_selected_id."'").',';
        $sql.=' isgroup='.(empty($this->isgroup)? 'null':"'".$this->isgroup."'").',';
        $sql.=' fk_project='.(empty($this->project)? 'null':"'".$this->project."'").',';
        $sql.=' date_start='.(dol_strlen($this->date_start)!=0 ? "'".$this->db->idate($this->date_start)."'":'null').',';
        $sql.=' date_end='.(dol_strlen($this->date_end)!=0 ? "'".$this->db->idate($this->date_end)."'":'null').'';
        
        return $sql;
    }


}
