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
 *  \file       dev/paymentprojects/paymentproject.class.php
 *  \ingroup    project_cost othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2018-07-21 21:29
 */

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';

$paymentprojectStatusPictoArray=array(0=> 'statut7',1=>'statut3',2=>'statut8',3=>'statut4');
$paymentprojectStatusArray=array(0=> 'Draft',1=>'Validated',2=>'Cancelled',3 =>'Payed');                
                
/**
 *	Put here description of your class
 */
class Paymentproject extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */				//!< To return several error codes (or messages)
    public $element='paymentproject';			//!< Id that identify managed objects
    /**
     * @var string Name of table without prefix where object is stored
     */    
    public $table_element='payment_project';		//!< Name of table without prefix where object is stored

    public $id;
    // BEGIN OF automatic var creation
    
//	public $ref;
	public $entity;
	public $label;
	public $amount;
	public $datep='';
	public $datev='';
	public $project;
	public $soc;
	public $typepayment;
	public $bank;
	public $date_creation='';
	public $date_modification='';
	public $user_creat;
	public $user_modif;
	public $import_key;
        public $num_payment;
    
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
        
//		$sql.= 'ref,';
		$sql.= 'label,';
		$sql.= 'amount,';
		$sql.= 'datep,';
		$sql.= 'datev,';
		$sql.= 'fk_project,';
		$sql.= 'fk_soc,';
		$sql.= 'fk_typepayment,';
		$sql.= 'fk_bank,';
		$sql.= 'date_creation,';
		$sql.= 'fk_user_creat,';
                $sql.= 'num_payment,';
		$sql.= 'import_key';

        
        $sql.= ") VALUES (";
        
//		$sql.=' '.(empty($this->ref)?'NULL':"'".$this->db->escape($this->ref)."'").',';
		$sql.=' '.(empty($this->label)?'NULL':"'".$this->db->escape($this->label)."'").',';
		$sql.=' '.(empty($this->amount)?'NULL':"'".$this->amount."'").',';
		$sql.=' '.(empty($this->datep) || dol_strlen($this->datep)==0?'NULL':"'".$this->db->idate($this->datep)."'").',';
		$sql.=' '.(empty($this->datev) || dol_strlen($this->datev)==0?'NULL':"'".$this->db->idate($this->datev)."'").',';
		$sql.=' '.(empty($this->project)?'NULL':"'".$this->project."'").',';
		$sql.=' '.(empty($this->soc)?'NULL':"'".$this->soc."'").',';
		$sql.=' '.(empty($this->typepayment)?'NULL':"'".$this->typepayment."'").',';
		$sql.=' '.(empty($this->bank)?'NULL':"'".$this->bank."'").',';
		$sql.=' NOW() ,';
		$sql.='\''.$user->id.'\',';
		$sql.=' '.(empty($this->num_payment)?'NULL':"'".$this->db->escape($this->num_payment)."'").',';                
		$sql.=' '.(empty($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'").'';

        
        $sql.= ")";

        $this->db->begin();

        dol_syslog(__METHOD__, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

        if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.$this->table_element);
                if ($this->id > 0)
                {
                        if (! empty($conf->banque->enabled) && ! empty($this->amount))
                        {
                                // Insert into llx_bank
                                require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
                                $acc = new Account($this->db);
                                $result=$acc->fetch($this->bank);
                                if ($result <= 0) dol_print_error($this->db);

                                // Insert payment into llx_bank
                                // Add link 'payment_salary' in bank_url between payment and bank transaction
                                $bank_line_id = $acc->addline( // FIXME shows as salary payment
                                        $this->datep,
                                        $this->typepayment,
                                        $this->label,
                                        $this->amount,
                                        $this->num_payment,
                                        '',
                                        $user
                                );

                                // Update fk_bank into llx_paiement.
                                // So we know the payment which has generate the banking ecriture
                                if ($bank_line_id > 0)
                                {
                                        $this->update_fk_bank($bank_line_id); //fixme
                                }
                                else
                                {
                                        $this->error=$acc->error;
                                        $error++;
                                }

                                if (! $error)
                                {
                                        // Add link 'payment_salary' in bank_url between payment and bank transaction
                                        $url=dol_buildpath('/project_cost/payment_card.php',1).'?action=view&id="';

                                        $result=$acc->add_url_line($bank_line_id, $this->id, $url, "(ProjectPayment)", "payment_salary");
                                        if ($result <= 0)
                                        {
                                                $this->error=$acc->error;
                                                $error++;
                                        }
                                }

                                $fproject=new Project($this->db);
                                $fproject->fetch($this->project);

                                // Add link 'user' in bank_url between operation and bank transaction
                                $result=$acc->add_url_line(
                                        $bank_line_id,
                                        $this->project,
                                        DOL_URL_ROOT.'/projet/card.php?id=',
                                        $fproject->title,
                                        // $langs->trans("SalaryPayment").' '.$fuser->getFullName($langs).' '.dol_print_date($this->datesp,'dayrfc').' '.dol_print_date($this->dateep,'dayrfc'),
                                        'project'
                                );

                                if ($result <= 0)
                                {
                                        $this->error=$acc->error;
                                        $error++;
                                }
                        }

            //// Call triggers
            $result=$this->call_trigger('PROJECT_PAYMENT_CREATE',$user);
            if ($result < 0) { $error++; }
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
	 *  Update link between payment salary and line generate into llx_bank
	 *
	 *  @param	int		$id_bank    Id bank account
	 *	@return	int					<0 if KO, >0 if OK
	 */
	function update_fk_bank($id_bank)
	{
		$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' SET fk_bank = '.$id_bank;
		$sql.= ' WHERE rowid = '.$this->id;
		$result = $this->db->query($sql);
		if ($result)
		{
			return 1;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
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
        
//		$sql.=' t.ref,';
		$sql.=' t.entity,';
		$sql.=' t.label,';
		$sql.=' t.amount,';
		$sql.=' t.datep,';
		$sql.=' t.datev,';
		$sql.=' t.fk_project,';
		$sql.=' t.fk_soc,';
		$sql.=' t.fk_typepayment,';
		$sql.=' t.fk_bank,';
		$sql.=' t.date_creation,';
		$sql.=' t.date_modification,';
		$sql.=' t.fk_user_creat,';
		$sql.=' t.fk_user_modif,';
		$sql.=' t.num_payment,';
		$sql.=' t.import_key';

        
        $sql.= " FROM ".MAIN_DB_PREFIX.$this->table_element." as t";
//        if ($ref) $sql.= " WHERE t.ref = '".$ref."'";
       // else 
            $sql.= " WHERE t.rowid = ".$id;
    	dol_syslog(get_class($this)."::fetch");
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
                $this->id    = $obj->rowid;
                
//				$this->ref = $obj->ref;
				$this->entity = $obj->entity;
				$this->label = $obj->label;
				$this->amount = $obj->amount;
				$this->datep = $this->db->jdate($obj->datep);
				$this->datev = $this->db->jdate($obj->datev);
				$this->project = $obj->fk_project;
				$this->soc = $obj->fk_soc;
				$this->typepayment = $obj->fk_typepayment;
				$this->bank = $obj->fk_bank;
				$this->date_creation = $this->db->jdate($obj->date_creation);
				$this->date_modification = $this->db->jdate($obj->date_modification);
				$this->user_creat = $obj->fk_user_creat;
				$this->user_modif = $obj->fk_user_modif;
				$this->num_payment = $obj->num_payment;                                
				$this->import_key = $obj->import_key;

                
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
            $result=$this->call_trigger('PROJECT_PAYMENT_MODIFY',$user);
            if ($result < 0) { $error++; }
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
  //          }if(isset($this->ref)){
  //              $ref=$this->ref;
            }
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
            $lien = '<a href="'.dol_buildpath('/project_cost/payment_card.php',1).'id='.$id.'&action=view"'.$linkclose.'>';
   //     }else if (!empty($ref)){
   //         $lien = '<a href="'.dol_buildpath('/project_cost/payment_card.php',1).'?ref='.$ref.'&action=view"'.$linkclose.'>';
        }else{
            $lien =  "";
        }
        $lienfin=empty($lien)?'':'</a>';

    	$picto='generic';
        $label = '<u>' . $langs->trans("paymentproject") . '</u>';
        $label.= '<br>';
     //   if($ref){
      //      $label.=$langs->trans("Red").': '.$ref;
        //}else if($id){
            $label.=$langs->trans("#").': '.$id;
       // }
        
        
        
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
            global $paymentprojectStatusArray;
            return $form->selectarray($htmlname,$paymentprojectStatusArray,$this->status);
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
		global $langs, $paymentprojectStatusArray,$paymentprojectStatusPictoArray;
                if($status=="")$status=0;

		if ($mode == 0)
		{
			$prefix='';
			return $langs->trans($paymentprojectStatusArray[$status]);
		}
		if ($mode == 1)
		{
			return $langs->trans($paymentprojectStatusArray[$status]);
		}
		if ($mode == 2)
		{
			 return img_picto($paymentprojectStatusArray[$status],$paymentprojectStatusPictoArray[$status]).' '.$langs->trans($paymentprojectStatusArray[$status]);
		}
		if ($mode == 3)
		{
			 return img_picto($paymentprojectStatusArray[$status],$paymentprojectStatusPictoArray[$status]);
		}
		if ($mode == 4)
		{
			 return img_picto($paymentprojectStatusArray[$status],$paymentprojectStatusPictoArray[$status]).' '.$langs->trans($paymentprojectStatusArray[$status]);
		}
		if ($mode == 5)
		{
			 return $langs->trans($paymentprojectStatusArray[$status]).' '.img_picto($paymentprojectStatusArray[$status],$paymentprojectStatusPictoArray[$status]);
		}
		if ($mode == 6)
		{
			 return $langs->trans($paymentprojectStatusArray[$status]).' '.img_picto($paymentprojectStatusArray[$status],$paymentprojectStatusPictoArray[$status]);
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
            if ($object->bank)
            {
                $accountline=new AccountLine($db);
                $result=$accountline->fetch($object->bank);
                if ($error > 0) $error=$accountline->delete($user);	// $result may be 0 if not found (when bank entry was deleted manually and fk_bank point to nothing)
                if ($error < 0) {
                    $object->error=$accountline->error;
                     $this->db->rollback();
                    setEventMessages($object->error, $object->errors, 'errors');
                }else{
                     $this->db->commit();
                }
            }else
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
        $object=new Paymentproject($this->db);
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
        
	//	$this->ref='';
		$this->entity='';
		$this->label='';
		$this->amount='';
		$this->datep='';
		$this->datev='';
		$this->project='';
		$this->soc='';
		$this->typepayment='';
		$this->bank='';
		$this->date_creation='';
		$this->date_modification='';
		$this->user_creat='';
		$this->user_modif='';
		$this->import_key='';
                $this->num_payment='';

        
    }
    /**
     *	will clean the parameters
     *	
     *
     *	@return	void
     */       
    function cleanParam(){
        
	//	if (!empty($this->ref)) $this->ref=trim($this->ref);
		if (!empty($this->label)) $this->label=trim($this->label);
		if (!empty($this->amount)) $this->amount=trim($this->amount);
		if (!empty($this->datep)) $this->datep=trim($this->datep);
		if (!empty($this->datev)) $this->datev=trim($this->datev);
		if (!empty($this->project)) $this->project=trim($this->project);
		if (!empty($this->soc)) $this->soc=trim($this->soc);
		if (!empty($this->typepayment)) $this->typepayment=trim($this->typepayment);
		if (!empty($this->bank)) $this->bank=trim($this->bank);
		if (!empty($this->date_creation)) $this->date_creation=trim($this->date_creation);
		if (!empty($this->date_modification)) $this->date_modification=trim($this->date_modification);
		if (!empty($this->user_creat)) $this->user_creat=trim($this->user_creat);
		if (!empty($this->user_modif)) $this->user_modif=trim($this->user_modif);
                if (!empty($this->num_payment)) $this->num_payment=trim($this->num_payment);
		if (!empty($this->import_key)) $this->import_key=trim($this->import_key);

        
    }
     /**
     *	will create the sql part to update the parameters
     *	
     *
     *	@return	void
     */    
    function setSQLfields($user){
        $sql='';
        
	//	$sql.=' ref='.(empty($this->ref)!=0 ? 'null':"'".$this->db->escape($this->ref)."'").',';
		$sql.=' label='.(empty($this->label)!=0 ? 'null':"'".$this->db->escape($this->label)."'").',';
		$sql.=' amount='.(empty($this->amount)!=0 ? 'null':"'".$this->amount."'").',';
		$sql.=' datep='.(dol_strlen($this->datep)!=0 ? "'".$this->db->idate($this->datep)."'":'null').',';
		$sql.=' datev='.(dol_strlen($this->datev)!=0 ? "'".$this->db->idate($this->datev)."'":'null').',';
		$sql.=' fk_project='.(empty($this->project)!=0 ? 'null':"'".$this->project."'").',';
		$sql.=' fk_soc='.(empty($this->soc)!=0 ? 'null':"'".$this->soc."'").',';
		$sql.=' fk_typepayment='.(empty($this->typepayment)!=0 ? 'null':"'".$this->typepayment."'").',';
		$sql.=' fk_bank='.(empty($this->bank)!=0 ? 'null':"'".$this->bank."'").',';
		$sql.=' date_modification=NOW() ,';
		$sql.=' fk_user_modif="'.$user->id.'",';
		$sql.=' num_payment='.(empty($this->num_payment)!=0 ? 'null':"'".$this->db->escape($this->num_payment)."'").',';
		$sql.=' import_key='.(empty($this->import_key)!=0 ? 'null':"'".$this->db->escape($this->import_key)."'").'';

        
        return $sql;
    }
    /**
     *      Add record into bank for payment with links between this bank record and invoices of payment.
     *      All payment properties must have been set first like after a call to create().
     *
     *      @param	User	$user               Object of user making payment
     *      @param  string	$mode               'payment_project'
     *      @param  string	$label              Label to use in bank record
     *      @param  int		$accountid          Id of bank account to do link with
     *      @param  string	$emetteur_nom       Name of transmitter
     *      @param  string	$emetteur_banque    Name of bank
     *      @return int                 		<0 if KO, >0 if OK
     */
    function addPaymentToBank($user,$mode,$label,$accountid,$emetteur_nom,$emetteur_banque)
    {
        global $conf;

        $error=0;

        if (! empty($conf->banque->enabled))
        {
            require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

            $acc = new Account($this->db);
            $acc->fetch($accountid);

            $total=$this->total;
            if ($mode == 'payment_project') $amount=$total;

            // Insert payment into llx_bank
            $bank_line_id = $acc->addline(
                $this->datepaid,
                $this->paymenttype,  // Payment mode id or code ("CHQ or VIR for example")
                $label,
                $amount,
                $this->num_payment,
                '',
                $user,
                $emetteur_nom,
                $emetteur_banque
            );

            // Update fk_bank in llx_paiement.
            // On connait ainsi le paiement qui a genere l'ecriture bancaire
            if ($bank_line_id > 0)
            {
                $result=$this->update_fk_bank($bank_line_id);
                if ($result <= 0)
                {
                    $error++;
                    dol_print_error($this->db);
                }

                // Add link 'payment', 'payment_supplier', 'payment_project' in bank_url between payment and bank transaction
                $url='';
                if ($mode == 'payment_project') $url=dol_buildpath('/project_cost/payment_card.php',1).'?id='.$this->id.'&action=view&Projectid='.$this->project;
                if ($url)
                {
                    $result=$acc->add_url_line($bank_line_id, $this->id, $url, '(paiement project)', $mode);
                    if ($result <= 0)
                    {
                        $error++;
                        dol_print_error($this->db);
                    }
                }
            }
            else
            {
                $this->error=$acc->error;
                $error++;
            }
        }

        if (! $error)
        {
            return 1;
        }
        else
        {
            return -1;
        }
    }





}
