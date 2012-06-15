<?php

App::import('Vendor', 'S3');

/**
 * Amazon S3 Cakephp 2 Component
 *
 * Based on work of Donovan Schönknecht > http://undesigned.org.za/2007/10/22/amazon-s3-php-class
 * Forked from https://github.com/mikedube-/cakephp2-amazon-s3 by Michael Dube
 * 
 * @author Alex Mamonchik alex.mamonchik@gmail.com
 * @link https://github.com/alexmamonchik/cakephp2-amazon-s3
 * @version 0.1-dev
 */
class S3Component extends Component {

	/**
	 * @var object S3 Vendor instance
	 */
	private $S3Vendor;

	/**
	 * @var string Object Name of the latest added object
	 */
	private $objectName;

	/**
	 * @var string Amazon S3 Target Bucket
	 */
	private $bucket = null;

	/**
	 * @var string Base URL of Amazon S3
	 */
	private $baseUrlS3 = 'https://s3.amazonaws.com/';


	public function initialize($controller) {

		$this->S3Vendor = new S3(Configure::read('Amazon.key'), Configure::read('Amazon.secret'));
	}

	
	/**
	 * Check connect to Amazon
	 * @return boolean - true if connected to amazon 
	 */
	public function isConnected() {
		
		return $this->S3Vendor->hasAuth();
	}


	/**
	 * Create new folder (Bucket)
	 * @param string $name - name of Bucket
	 * @param string $access -
	 *		'p'   - private
	 *		'prw' - public read write
	 *		'pr'  - public read
	 *		'ar'  - authenticated read
	 * @return boolean - true if success created
	 */
	public function createFolder($name = '', $access = '') {
		
		$acc = '';
		switch ($access) {
			case 'prw':
				$acc = S3::ACL_PUBLIC_READ_WRITE;
				break;
			case 'pr':
				$acc = S3::ACL_PUBLIC_READ;
				break;
			case 'ar':
				$acc = S3::ACL_AUTHENTICATED_READ;
				break;
			default :
				$acc = S3::ACL_PRIVATE;
		}
		
		return $this->S3Vendor->putBucket($name, $acc);
	}

	
	/**
	 * Delete folder (Bucket)
	 * @todo - make recursive deleting
	 * @param string $name - name of folder for delete
	 * @return type 
	 */
	public function deleteFolder($name = '', $recursive = false) {
		
		return $this->S3Vendor->deleteBucket($name);
	}

	
	public function uploadFile($path_from = '', $to_bucket = '', $unique = false) {
		
		if ($unique) {
			// save with unique name
		}
	}

	public function getFile($filename = '', $save_to = null) {
		
	}

	public function deleteFile($filename = '') {
		
	}

	/*
	 * =====================
	 * Private Methods
	 */

	private function random($length = 20) {

		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = uniqid('', true);
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters - 1))];
		}
		return $string;
	}

}