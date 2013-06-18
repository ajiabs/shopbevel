<?php
/*
 * Contact importor controller
 * This controller is used to import the email contacts from user accounts
 * using cloudsponge API 
 */

class Contact_Messages_IndexController extends Mage_Core_Controller_Front_Action {
	/*
	 * function to check the user login
	 */
	
	public function logicCheck() {
		$customer = Mage::helper("customer")->getCustomer();
		$custMail = $customer['email'];
		if($custMail == '')
			$this->_redirect('messages/index');
	}
	
	/*
	 * function to generate landing page to show the welcome message
	 */
	
    public function indexAction()  {
 		unset($_SESSION['contexttool']);
        unset($_SESSION['contextmessage']);
		 
        //get current layout state
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'newpage',
            array('template' => 'messages/content.phtml')
        );
 		
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
    
    /*
     * function to show the todo list page
     */
    
    public function todolistAction() {
    	
    	$this->logicCheck();
    	//get current layout state	
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'todolist',
            array('template' => 'messages/todolist.phtml')
        );
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
    
    /*
     * function to show the message compose box
     */
    
    public function composeAction() {
    	
    	$this->logicCheck();
    	$post = $this->getRequest()->getPost();
        $customer = Mage::helper("customer")->getCustomer();
        $custMail = $customer['email'];
        // get user input 
        if ( $post ) {       
        	session_start();
        	$messageBody = $_POST['messageBody'];    
         	$_SESSION['contexttool']['messagebody'] = $messageBody;
         	$_SESSION['contextmessage'] = $messageBody;
            $_SESSION['composemessage'] = $messageBody;
            $myFile = "userfiles/".$custMail;
            $fh = fopen($myFile, 'w') or die("can't open file");
            fwrite($fh, $messageBody);                   
            fclose($fh);
         	if($_SESSION['contexttool']['messagebody'] != '') {                    
	         	$nextUrl =  Mage::getUrl('messages/index/createfanlist');
	         	echo '<script>window.location="'.$nextUrl.'";</script>';
         	}
        }
    	//get current layout state
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'compose',
            array('template' => 'messages/compose.phtml')
        );
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }

    /*
     * function to import the user contacts.
     */
    
     public function createfanlistAction() {
     	$this->logicCheck();
        $this->loadLayout();          
        //get current layout state
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'createfanlist',
            array('template' => 'messages/createfanlist.phtml')
        );
 		
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    /*
     * function to enable/disable checkboxes using ajax
     */
    
    public function changestatusAction(){
        $this->logicCheck();
    	$emailselected = $_POST['mailSelected'];
        $status = $_POST['state'];
        $checkedCount = 0;
        if($status == 'true')
            $val = 1;
        elseif($status == 'false')
            $val = 0;       
        $_SESSION['contexttool']['importmails'][$emailselected]['status'] = $val;        
        foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {   			
            if($impMails['status'] == 1) { 
                    $checkedCount++;
            }
        }
        // return the user count
        echo $checkedCount."~~"." ";        
        exit;
    }
    
    /*
     *  function to select/deselect all check boxes
     */
    
    public function changestatusallAction(){
        $this->logicCheck();
    	$emailselected = $_POST['mailSelected'];
        $arrEmails =  explode(',', $emailselected);
        $checkedCount = 0;              
        $status = $_POST['state'];
        if($status == 'true')
            $val = 1;
        elseif($status == 'false')
            $val = 0;
        foreach($arrEmails as $selectedMail){
        	$_SESSION['contexttool']['importmails'][$selectedMail]['status'] = $val;        
        }
        foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {   			
            if($impMails['status'] == 1) { 
                    $checkedCount++;
            }
        }
        echo $checkedCount."~~"." ";        
        exit;
    }
       
    /*
     * function to receive the emails and store the contents in a session
     */
    
    public function assignusercontactsAction(){        
    	$this->logicCheck();
    	$emailList = $_POST['emaillist'];
    	if($emailList != '') {
    		$emails = explode(',',$emailList);
    		if(sizeof($emails) > 0) {
    			foreach($emails as $email) {
    				if($email != '') {
    					$emailInfo = explode(':',$email);
 	   					$_SESSION['contexttool']['importmails'][$emailInfo[1]] = array('name' => $emailInfo[0],'status' => 0);
    				}
    			}
    		}
    	}
		$checkedCount = 0;
        $contactListCount = array('10','25','50','100');
 		if(!isset($_SESSION['deflistcount'])) {
 			// set the default list count
 			$_SESSION['deflistcount'] = 10;
 		}	
 		$contactNoDefault = $_SESSION['deflistcount'];
 		$htmlCountSel = ' Show <select name=pagelistcounts" id="pagelistcounts" onchange="changeContListcount();">';
 		foreach($contactListCount as $contacts) {
    		$htmlCountSel .= '<option value="'.$contacts.'" '.(($contacts==$contactNoDefault)?'selected="selected"':'').' >'.$contacts.'</option>';
 		}
 		$htmlCountSel .= '</select> Contacts';
 		
 		// pagination implementation
    	$boxEmailList = $htmlCountSel.'<div class="cont-mail-blk"><div class="cont-mail-blk-hd"><div class="cont-mail-blk-hd-col1">Contact</div>
    		<div class="cont-mail-blk-hd-col2">Email</div><div class="cont-mail-blk-hd-col3">Invite</div> </div>';
   		if(sizeof($_SESSION['contexttool']['importmails']) > 0) {
            $i = 1;           
   			foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {
            	if($i > $contactNoDefault) {
   					break;
   				}
            	$checkedMsg = '';
   		 		if($impMails['status'] == 1) { 
                	$checkedMsg = ' checked="checked"';
    			} 
	  			$boxEmailList .= '<div class="cont-mail-blk-rw">
	    		<div class="cont-mail-blk-col1">&nbsp;'.trim($impMails['name']).'</div>
	    		<div class="cont-mail-blk-col2">&nbsp;'.$key.'</div>
	    		<div class="cont-mail-blk-col3"><input class="chkemails" onclick="toggleEmails(this.checked,this.value)" type="checkbox" name="selemails[]" value="'.$key.'" '.$checkedMsg.'></div>
	  			</div>';
	            $i++;
            	if(!is_array($key)){
                	if($pageEmails !='')
                    	$pageEmails = $pageEmails.','.$key;
                    else
                        $pageEmails = $key;
           	}
   		}
                
        foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {   			
        	if($impMails['status'] == 1) { 
        	    $checkedCount++;
            }
        }
        $boxEmailList .= ' <input type="hidden" name="emails" id="emails" value="'.$pageEmails.'">';
                 
        if($i > $contactNoDefault) {
   			$totContacts = sizeof($_SESSION['contexttool']['importmails']);
   			$numPages = ceil($totContacts / $contactNoDefault)+1;
            $paginationLink .= '<div class="pagination_new">';
   			for($i=1;$i<$numPages; $i++){
            	$selclass=($i==1)?"jqloadcontactpage active":"jqloadcontactpage";
   				$paginationLink .= '<a href="#" class="'.$selclass.'" id="'.$i.'">'.$i.'</a>&nbsp;';
   			}
            $paginationLink .= '</div>';
   		}
   	}
   	else $boxEmailList .= '<tr><td colspan="3">No contacts imported</td></tr>';
   	
  		$boxEmailList .= '</div>';
  		$selMessage = 'Total '.$checkedCount.' contacts selected';  		
		$nextStep = '<div class="proceed"><input name="btnContinue" type="button" class="compose_btn" value="Proceed" onclick="checkSelEmails()" /></div>';
  		$boxEmailListOptions = '<div class="choose-blk">
  			 <div class="choose-blk-row">
    		<div class="choose-blk-col1"><input type="checkbox" onclick="toggleChecked(this.checked)"></div>
    		<div class="choose-blk-col2">Choose All </div> </div> <div class="choose-blk-row"><div class="choose-blk-col1">&nbsp;</div>   <div class="choose-blk-col2"><div id="jqEmailSelMessage">'.$selMessage.'</div>
			<input type="hidden" name="emailselcount" id="emailselcount" value="'.$checkedCount.'"></div> </div></div>
			 '.$nextStep.'';
   
   		echo '<div class="jqEmailList"><div class="contact_mail_outerblk_outer"> <div class="contact_mail_outerblk1">'.$boxEmailList.'</div>';
        echo '<br>';
        if(sizeof($_SESSION['contexttool']['importmails']) >0) 
    		echo '<div class="contact_mail_outerblk2">'.$boxEmailListOptions.'</div>';
        echo '  </div></div>'.$paginationLink.'</div>';
    }

    /*
     * function to confirm selected contact list
     */
    
    public function paginateusermailsAction(){
    	$contactListCount = array('10','25','50','100');
  		$this->logicCheck();
   		$curPage  	 = $_POST['page'];
   		$noofitems   = $_POST['noofitems'];
   		$_SESSION['deflistcount'] = $noofitems;
   	
   		$startItem = ($curPage * $noofitems)-$noofitems;
   		$endItem	= $startItem + $noofitems;
 		$htmlCountSel = ' Show <select name=pagelistcounts" id="pagelistcounts" onchange="changeContListcount();">';
 		foreach($contactListCount as $contacts) {
    		$htmlCountSel .= '<option value="'.$contacts.'" '.(($contacts==$noofitems)?'selected="selected"':'').' >'.$contacts.'</option>';
 		}
 		$htmlCountSel .= '</select> Contacts';
    	echo '<script type="text/javascript" charset="utf-8"> jQuery(".jqloadcontactpage").on(\'click\',(function() {
		 var contactPage =  jQuery(this).attr(\'id\');
		 var pageCount =  jQuery(\'#pagelistcounts\').val();
		 jQuery.ajax({
		     url: "'.Mage::getUrl('messages/index/paginateusermails').'",
		     type: "POST",
		     data: {page:contactPage,noofitems:pageCount} ,
		     cache: false,
		     dataType:\'html\',
		     success: function(html) { 
		     	 jQuery(\'#contactemails\').html(html);
		     }
		 });	  
		}));	</script>';
		$checkedCount = 0;
    	$boxEmailList = $htmlCountSel.'<div class="cont-mail-blk"><div class="cont-mail-blk-hd"><div class="cont-mail-blk-hd-col1">Contact</div>
    		<div class="cont-mail-blk-hd-col2">Email</div><div class="cont-mail-blk-hd-col3">Invite</div> </div>';
   		if(sizeof($_SESSION['contexttool']['importmails']) >0) {
   			$i = 1;
	   		foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {
				if($i > $startItem && $i <= $endItem) {
	   			
	   			$checkedMsg = '';
	   		 	if($impMails['status'] == 1) { 
                	$checkedMsg = ' checked="checked"';
	   		 	}
	   		 
	  			$boxEmailList .= '<div class="cont-mail-blk-rw">
	    		<div class="cont-mail-blk-col1">&nbsp;'.trim($impMails['name']).'</div>
	    		<div class="cont-mail-blk-col2">&nbsp;'.$key.'</div>
	    		<div class="cont-mail-blk-col3"><input class="chkemails" onclick="toggleEmails(this.checked,this.value)" type="checkbox" name="selemails[]" value="'.$key.'" '.$checkedMsg.'></div>
	  			</div>';
                $i++;
                if($emails != '')
                	$emails = $emails.','.$key;
                else
                    $emails = $key;
	   		}
	   		else $i++;
   		}
        foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {   			
        	if($impMails['status'] == 1) { 
            	$checkedCount++;
            }
        }
        $boxEmailList .= ' <input type="hidden" name="emails" id="emails" value="'.$emails.'">';
   	 	$totContacts = sizeof($_SESSION['contexttool']['importmails']);
   		$numPages = ceil($totContacts / $noofitems)+1;
        $paginationLink .= '<div class="pagination_new">';
   		for($i=1;$i<$numPages; $i++){
        	$selclass = ($i == $curPage)?"jqloadcontactpage active":"jqloadcontactpage";
   			$paginationLink .= '<a href="#" class="'.$selclass.'" id="'.$i.'">'.$i.'</a>&nbsp;';
   		}
        $paginationLink .= '</div>'; 
   	}
   	else $boxEmailList .= '<tr><td colspan="3">No contacts imported</td></tr>';
  		$boxEmailList .= '</div>';

  		$selMessage = 'Total '.$checkedCount.' contacts selected';
		$nextStep = '<div class="proceed"><input name="btnContinue" type="button" class="compose_btn" value="Proceed" onclick="checkSelEmails()" /></div>';
  		$boxEmailListOptions = '<div class="choose-blk">
  			 <div class="choose-blk-row">
    		<div class="choose-blk-col1"><input type="checkbox" onclick="toggleChecked(this.checked)"></div>
    		<div class="choose-blk-col2">Choose All </div> </div> <div class="choose-blk-row"><div class="choose-blk-col1">&nbsp;</div>   <div class="choose-blk-col2"><div id="jqEmailSelMessage">'.$selMessage.'</div>
			<input type="hidden" name="emailselcount" id="emailselcount" value="'.$checkedCount.'"></div> </div></div>
			 '.$nextStep.'';
   
   		echo '<div class="jqEmailList"><div class="contact_mail_outerblk_outer"> <div class="contact_mail_outerblk1">'.$boxEmailList.'</div>';
   		echo '<br>';
    	echo '<div class="contact_mail_outerblk2">'.$boxEmailListOptions.'</div>  </div></div>'.$paginationLink.'</div>';
    }
    
    /*
     *  function to generate winners todo page
     */
    
    public function winnerstodolistAction() {
    	$this->logicCheck();
    	$selEmails = $_POST['selemails'];

      	$this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'winnerstodolist',
            array('template' => 'messages/winnerstodolist.phtml')
        );
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }  
    
    /*
     * function to show the composed message and send email
     */
    
    public function sendAction() {
    	$this->logicCheck();
    	if(!isset($_SESSION['contexttool']))
    		$this->_redirect('messages/index');
    
    	$customer = Mage::helper("customer")->getCustomer();
     	$post = $this->getRequest()->getPost();
     	// send the email
        if ( $post ) {
            $customer = Mage::helper("customer")->getCustomer();
            $custMail = "userfiles/".$customer['email'];
            unlink($custMail);

			$messageBody = $_POST['textarea'];
        	include ('contactimporter/phpmailer/class.phpmailer.php');
        	$custName = $customer['firstname'].' '.$customer['lastname'];
        	$custMail = $customer['email'];	
        	$adfromemailname = ($custName =='')? 'Admin':$custName;
        	$adfromemail = ($custMail =='')?'admin@shopbevel.com':$custMail;	
        	$mailSubject = 'Please Vote for me in shopbevel';
        	$mailBody = $messageBody;
        	$headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            $headers .= "From: ".$adfromemailname."<".$adfromemail.">" . "\r\n";
            $headers .= "Reply-To: ".$adfromemailname."<".$adfromemail.">" . "\r\n"; 
            $mailMessage = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> <html lang="en"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
            </head> <body> <table width="860" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse; border-spacing:0;"><col width="25"/><col width="810"/><col width="25"/><tr><td colspan="3" width="860">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="860" height="22" border="0" style="display: block;"/></td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td><td width="810" valign="top" align="center"><table width="810" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td width="272"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="272" height="5" border="0" style="display: block;"/></td><td width="280"><h1 style="padding:0;margin:0;"><a href="'.Mage::getBaseUrl().'" style="padding:0;margin:0;text-decoration:none;border:0;">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/logo.gif" width="280" height="62" alt="Shop Bevel" style="font-family: Helvetica, sans-serif; color:#01ccdb; font-size: 20px; display: block; border: none;"/></a></h1></td><td width="109"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="109" height="5" border="0" style="display: block;"/></td><td><table width="149" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td valign="top">
            <a href="http://facebook.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/fb-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top">
            <a href="http://twitter.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/twitter-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top"><a href="http://pinterest.com/ShopBevel/"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/pin-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top"><a href="http://instagram.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/instagram-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td></tr></table></td></tr></table></td><td width="25">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td></tr><tr><td colspan="3" width="860"><table width="860" border="0" cellpadding="0" cellspacing="0" align="left" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td width="143" colspan="4"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="586" height="48" border="0" style="display: block;"/></td><td width="138"><a href="'.Mage::getBaseUrl().'submit-about"></a></td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="136" height="48" border="0" style="display: block;"/></td></tr><tr><td width="143"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-1.jpg" width="143" height="54" border="0" style="display: block;"/></td><td width="118"><a href="'.Mage::getBaseUrl().'shop"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-2.jpg" width="118" height="54" border="0" style="display: block;"/></a>
            </td><td width="127"><a href="'.Mage::getBaseUrl().'vote"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-3.jpg" width="127" height="54" border="0" style="display: block;"/></a></td><td width="198"><a href="'.Mage::getBaseUrl().'about-us"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-4.jpg" width="198" height="54" border="0" style="display: block;"/></a></td><td width="138"><a href="'.Mage::getBaseUrl().'submit-about"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-6.jpg" width="136" height="54" border="0" style="display: block;"/></a></td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-6.jpg" width="136" height="54" border="0" style="display: block;"/></td></tr><tr><td width="586" colspan="4"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="586" height="18" border="0" style="display: block;"/></td><td width="138">&nbsp;</td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="136" height="18" border="0" style="display: block;"/>
            </td></tr></table></td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td><td>'.$mailBody.' </td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/center-callout.gif" width="860" height="100" border="0" style="display: block;"/></td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td><td width="810">&nbsp;</td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="860" height="10" border="0" style="display: block;"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/footer-about.gif" width="860" height="123" border="0" style="display: block;"/></td></tr><tr>
            <td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-blank.jpg" width="25" height="101" border="0" style="display: block;"/></td><td width="810"><table width="810" border="0" cellpadding="0" cellspacing="0" align="left" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-wired.jpg" width="207" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-techcrunch.jpg" width="237" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-forbes.jpg" width="179" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-cbs.jpg" width="187" height="101" border="0" style="display: block;"/></td></tr></table></td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-blank.jpg" width="25" height="101" border="0" style="display: block;"/></td></tr><tr bgcolor="#262626"><td width="25" bgcolor="#262626"></td>
            <td width="810" bgcolor="#262626"><table width="450" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#262626" style="border-collapse: collapse;">
            <tr><td colspan="4" width="450" height="23">&nbsp;</td></tr><tr><td align="center" valign="middle"><a href="'.Mage::getBaseUrl().'" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;">
            <font color="#ffffff">Unsubscribe</font></a></td><td align="center" valign="middle"  style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'privacy" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif;  font-size: 12px; color: #ffffff; text-decoration: none;"><font color="#ffffff">Privacy Policy</font></a></td><td align="center" valign="middle"  style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'about-us" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;">
            <font color="#ffffff">How It Works</font></a></td>
            <td align="center" valign="middle" style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'about-us" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;"><font color="#ffffff">About</font></a></td></tr><tr><td colspan="4" align="center" valign="middle"><p style="font-family: \'Helvetica Neue\', Helvetica, sans-serif;  font-size: 11px; line-height; 20px; color: #cccccc; text-align: center;">122 West 26th Street  Floor 4, New York, NY 10014<br/>ShopBevel Inc. (formerly CrowdJewel) 2013, All Rights Reserved.</p></td></tr><tr><td colspan="4" width="450" height="23">&nbsp;</td></tr></table></td>
            <td width="25" bgcolor="#262626">&nbsp;</td></tr></table></body></html></body> </html> ';
            // send email to selected users
            foreach($_SESSION['contexttool']['importmails'] as $key => $impMails) {
            	if($impMails['status'] == 1) {
            	    $mail  = new PHPMailer();
            		$mail->AddReplyTo($adfromemail,$adfromemailname);
            		$mail->SetFrom($adfromemail, $adfromemailname);
            		$mail->AddAddress($key, $impMails['name']);
            	 	$mail->Subject    = $mailSubject;
            		$mail->AltBody    = 'Please refer in ShopBevel'; 
            		$mail->MsgHTML($mailMessage);
            		$mailsent = $mail->Send();	
            	}
            	unset($_SESSION['contexttool']['importmails'][$key]);
            } 
        	unset($_SESSION['contexttool']);
        	unset($_SESSION['contextmessage']);
        	$nextUrl = Mage::getUrl('messages/index/thankyou');
         	echo '<script>window.location="'.$nextUrl.'";</script>';
        }
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'send',
            array('template' => 'messages/send.phtml')
        );
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
    
    /*
     * function to show the email template preview
     */
    
    public function showmailpreviewAction(){
    	$messageBody = $_POST['emailmsg'];
    	if($messageBody != '') {
    		$_SESSION['contexttool']['messagebody'] = $messageBody;
    	}
    	$messageBody = $_SESSION['contexttool']['messagebody'];
    	$mailMessage = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> <html lang="en"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
            </head> <body> <table width="860" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse; border-spacing:0;"><col width="25"/><col width="810"/><col width="25"/><tr><td colspan="3" width="860">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="860" height="22" border="0" style="display: block;"/></td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td><td width="810" valign="top" align="center"><table width="810" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td width="272"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="272" height="5" border="0" style="display: block;"/></td><td width="280"><h1 style="padding:0;margin:0;"><a href="'.Mage::getBaseUrl().'" style="padding:0;margin:0;text-decoration:none;border:0;">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/logo.gif" width="280" height="62" alt="Shop Bevel" style="font-family: Helvetica, sans-serif; color:#01ccdb; font-size: 20px; display: block; border: none;"/></a></h1></td><td width="109"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="109" height="5" border="0" style="display: block;"/></td><td><table width="149" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td valign="top">
            <a href="http://facebook.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/fb-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top">
            <a href="http://twitter.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/twitter-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top"><a href="http://pinterest.com/ShopBevel/"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/pin-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td><td valign="top"><a href="http://instagram.com/shopbevel"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/instagram-button.gif" width="36" height="62" border="0" style="display: block;"/></a></td></tr></table></td></tr></table></td><td width="25">
            <img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td></tr><tr><td colspan="3" width="860"><table width="860" border="0" cellpadding="0" cellspacing="0" align="left" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td width="143" colspan="4"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="586" height="48" border="0" style="display: block;"/></td><td width="138"><a href="'.Mage::getBaseUrl().'submit-about"></a></td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="136" height="48" border="0" style="display: block;"/></td></tr><tr><td width="143"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-1.jpg" width="143" height="54" border="0" style="display: block;"/></td><td width="118"><a href="'.Mage::getBaseUrl().'shop"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-2.jpg" width="118" height="54" border="0" style="display: block;"/></a>
            </td><td width="127"><a href="'.Mage::getBaseUrl().'vote"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-3.jpg" width="127" height="54" border="0" style="display: block;"/></a></td><td width="198"><a href="'.Mage::getBaseUrl().'about-us"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-4.jpg" width="198" height="54" border="0" style="display: block;"/></a></td><td width="138"><a href="'.Mage::getBaseUrl().'submit-about"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-6.jpg" width="136" height="54" border="0" style="display: block;"/></a></td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/nav-6.jpg" width="136" height="54" border="0" style="display: block;"/></td></tr><tr><td width="586" colspan="4"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="586" height="18" border="0" style="display: block;"/></td><td width="138">&nbsp;</td><td width="136"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="136" height="18" border="0" style="display: block;"/>
            </td></tr></table></td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td><td>'.$messageBody.' </td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/center-callout.gif" width="860" height="100" border="0" style="display: block;"/></td></tr><tr><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td><td width="810">&nbsp;</td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="25" height="5" border="0" style="display: block;"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/blank.gif" width="860" height="10" border="0" style="display: block;"/></td></tr><tr><td colspan="3"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/footer-about.gif" width="860" height="123" border="0" style="display: block;"/></td></tr><tr>
            <td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-blank.jpg" width="25" height="101" border="0" style="display: block;"/></td><td width="810"><table width="810" border="0" cellpadding="0" cellspacing="0" align="left" bgcolor="#ffffff" style="border-collapse: collapse;"><tr><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-wired.jpg" width="207" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-techcrunch.jpg" width="237" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-forbes.jpg" width="179" height="101" border="0" style="display: block;"/></td><td><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-cbs.jpg" width="187" height="101" border="0" style="display: block;"/></td></tr></table></td><td width="25"><img src="'.Mage::getBaseUrl().'skin/frontend/enterprise/bevel_shop/email/images/bottom-logo-blank.jpg" width="25" height="101" border="0" style="display: block;"/></td></tr><tr bgcolor="#262626"><td width="25" bgcolor="#262626"></td>
            <td width="810" bgcolor="#262626"><table width="450" border="0" cellpadding="0" cellspacing="0" align="center" bgcolor="#262626" style="border-collapse: collapse;">
            <tr><td colspan="4" width="450" height="23">&nbsp;</td></tr><tr><td align="center" valign="middle"><a href="'.Mage::getBaseUrl().'" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;">
            <font color="#ffffff">Unsubscribe</font></a></td><td align="center" valign="middle"  style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'privacy" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif;  font-size: 12px; color: #ffffff; text-decoration: none;"><font color="#ffffff">Privacy Policy</font></a></td><td align="center" valign="middle"  style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'about-us" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;">
            <font color="#ffffff">How It Works</font></a></td>
            <td align="center" valign="middle" style="border-left: 1px solid #ffffff; padding-left: 1px;"><a href="'.Mage::getBaseUrl().'about-us" style="font-family: \'Helvetica Neue\', Helvetica, sans-serif; font-size: 12px;  color: #ffffff;text-decoration: none;"><font color="#ffffff">About</font></a></td></tr><tr><td colspan="4" align="center" valign="middle"><p style="font-family: \'Helvetica Neue\', Helvetica, sans-serif;  font-size: 11px; line-height; 20px; color: #cccccc; text-align: center;">122 West 26th Street  Floor 4, New York, NY 10014<br/>ShopBevel Inc. (formerly CrowdJewel) 2013, All Rights Reserved.</p></td></tr><tr><td colspan="4" width="450" height="23">&nbsp;</td></tr></table></td>
            <td width="25" bgcolor="#262626">&nbsp;</td></tr></table></body></html></body> </html> ';
    	echo $mailMessage;
    	exit();
    }
    
    /*
     * function to load the thank you page
     */
    
    public function thankyouAction(){
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'thankyou',
            array('template' => 'messages/thankyou.phtml')
        );
 		
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
}
?>