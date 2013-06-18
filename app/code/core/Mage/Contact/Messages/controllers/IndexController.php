<?php
class Contact_Messages_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //Get current layout state
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
    
    
    public function postAction()    {
        $this->loadLayout();          
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'newpage',
            array('template' => 'messages/post.phtml')
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
    	$post = $this->getRequest()->getPost();
        if ( $post ) {
        
         	$messageBody = $post['textarea'];
         	$_SESSION['contexttool']['messagebody'] = $messageBody;
         	$this->_redirect('messages/index/createfanlist');
        }
    	//Get current layout state
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
    
    
    
     public function createfanlistAction() {
        $this->loadLayout();          
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'createfanlist',
            array('template' => 'messages/createfanlist.phtml')
        );
 		
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
    }
    
    
    /*
     * function to receive the emails and store the emails in a session
     */
    public function assignusercontactsAction(){
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

    	$boxEmailList = '<table width="681" border="1"><tr><td width="216"><strong>Contact</strong></td>
    	<td width="340"><strong>Email</strong></td><td width="103"><strong>Invite</strong></td> </tr>';
   
   		foreach($_SESSION['contexttool']['importmails'] as $key=>$impMails) {
  			$boxEmailList .= '<tr>
    		<td>'.$impMails['name'].'</td>
    		<td>'.$key.'</td>
    		<td><input class="chkemails" onclick="toggleEmails(this.checked)" type="checkbox" name="selemails[]" value="'.$key.'"></td>
  			</tr>';
   		}
  		$boxEmailList .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>';

 		$nextStep = '<input name="btnContinue" type="button" value="Proceed" onclick="checkSelEmails()" />';
		
  		$boxEmailListOptions = '<table width="164" border="1">
  			<tr><td width="26">&nbsp;</td>    <td width="122">&nbsp;</td>  </tr>  <tr>
    		<td><input type="checkbox" onclick="toggleChecked(this.checked)"></td>
    		<td>Choose All </td>  </tr>  <tr> 		<td></td>
    		<td></td>  </tr>  <tr>    <td>&nbsp;</td>    <td><div id="jqEmailSelMessage"></div>
			<input type="hidden" name="emailselcount" id="emailselcount"></td>  </tr></table>
			'.$nextStep.'';
   
   		echo '<div class="jqEmailList"><table width="971" border="1">   <tr>
    		<td width="677" height="291">'.$boxEmailList.'</td>
    		<td width="278">'.$boxEmailListOptions.'</td>   </tr></table></div>';
 
        
    }
    
    public function winnerstodolistAction() {
    	// unset($_SESSION['contexttool']);
    	 
    	$selEmails = $_POST['selemails'];
    	 
    	foreach($_SESSION['contexttool']['importmails'] as $key=>$impMails) {
    		 
    		if (in_array($key, $selEmails)) {
   		 		 $_SESSION['contexttool']['importmails'][$key]['status'] = 1;
			}
    	}
    	//echo "<pre>";
    	//print_r($_SESSION['contexttool']['importmails']);
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
     * shows the message send page
     */
    public function sendAction() {
    
     	$post = $this->getRequest()->getPost();
        if ( $post ) {
			$messageBody = $_POST['textarea'];
        	include ('contactimporter/phpmailer/class.phpmailer.php');
        	$adfromemailname = 'Admin';
        	$adfromemail = 'admin@shopbevel.com';
        	$mailSubject = 'Please Vote for me in shopbevel';
        	$mailBody = $messageBody;
        	 
        	
        	$headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
            $headers .= "From: ".$adfromemailname."<".$adfromemail.">" . "\r\n";
            $headers .= "Reply-To: ".$adfromemailname."<".$adfromemail.">" . "\r\n";
            
            $mail  = new PHPMailer();
            $mail->AddReplyTo($adfromemail,$adfromemailname);
            $mail->SetFrom($adfromemail, $adfromemailname);
            
            foreach($_SESSION['contexttool']['importmails'] as $key=>$impMails) {
            	if($impMails['status'] == 1)
            		$mail->AddAddress($key, $impMails['name']);
            }
            $mail->Subject    = $mailSubject;
            $mail->AltBody    = 'Please refer in ShopBevel'; // optional, comment out and test
            $mail->MsgHTML($mailBody);
            $mailsent = $mail->Send();
        	//echo "<pre>";
        	//print_r($mailBody);
        	unset($_SESSION['contexttool']);
        	$this->_redirect('messages/index/thankyou');
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
    		$_SESSION['contexttool']['messagebody']= $messageBody;
    	}
    	$messageBody = $_SESSION['contexttool']['messagebody'];
    	echo $messageBody;
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