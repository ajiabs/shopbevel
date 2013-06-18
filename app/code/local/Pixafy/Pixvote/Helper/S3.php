<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	class Pixafy_Pixvote_Helper_S3 extends Mage_Core_Helper_Abstract 
	{
		const AWS_ACCESS_KEY_PATH = "pixvote/general/aws_access_key";
		const AWS_SECRECT_KEY_PATH = "pixvote/general/aws_secrect_key";
		const AWS_S3_URL_PATH = "pixvote/general/aws_url";
		const AWS_BUCKET_PATH = "pixvote/general/aws_bucket";
		
		public function __construct()
		{
			
		}
		
		function _construct()
		{
			require_once("scripts/amawsons3/S3.php");
		}
		
		function uploadToCloud($s3FilePath,$fileTempName,$acl ="")
		{
			if($acl == "")
			{
				$acl = S3::ACL_PUBLIC_READ;
			}
			$s3 = new S3(Constants::$awsAccessKey, Constants::$awsSecretKey);			
			//Upload file to bucket
			return ($s3->putObjectFile($fileTempName, Constants::$BUCKET, $s3FilePath,$acl)); 
		}
	}
?>
