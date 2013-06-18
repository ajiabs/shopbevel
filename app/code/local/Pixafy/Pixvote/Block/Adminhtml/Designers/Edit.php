<?php
class Pixafy_Pixvote_Block_Adminhtml_Designers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'pixvote';
		$this->_controller = 'adminhtml_designers';
		$designer = Mage::registry("designer_data");
		$this->_addButton('save_and_continue', array(
                  'label' => Mage::helper('pixvote')->__('Save And Continue Edit'),
                  'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                  'class' => 'save',
        ), -100);
		 
		 $this->_formScripts[] = "site_url = '".Mage::getUrl()."'";
		 $this->_formScripts[] = '
			 $j(function(){

					$j("#profile-country").change(function(){
							$j.post(site_url  + "challenges/designers/regions", {country: $j(this).val()}, regionsResult, "json");
					});

				})

				function regionsResult( data )
				{
					$j("#profile-state option").remove();
					for(var i = 0; i < data.totalRecords; i++)
					{
						var item = data.items[i];
						$j("#profile-state").append("<option value=\"" + item.region_id + "\">" + item.name + "</option>")
					}

					var targetClass = "inactive"
					if(data.totalRecords)
					{
						$j("#profile-state").removeClass( targetClass )
					}
					else
					{
						$j("#profile-state").append("<option value=\"\">'. Mage::helper('pixvote')->__("No states found").'</option>")
					}
			}
			 
		';
		
//		$this->_updateButton('save', 'label', Mage::helper('pixvote')->__('Save Item'));
//		$this->_updateButton('delete', 'label', Mage::helper('pixvote')->__('Delete Item'));
//                $this->_addButton('resend', array(
//                    'label'     => Mage::helper('pixvote')->__('Resend Email'),
//                    'onclick'   => 'editForm.submit();',
//                    'class'     => 'save',
//                ), 0);
		$this->_headerText = Mage::helper('pixvote')->__('Edit Form - '.$designer->getName());
	}
}