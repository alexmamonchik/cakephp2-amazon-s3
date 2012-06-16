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
	private $prefix = '';


	public function initialize($controller) {

		$this->S3Vendor = new S3(Configure::read('Amazon.key'), Configure::read('Amazon.secret'));
		$this->prefix = Configure::read('Amazon.prefix');
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
		
		return $this->S3Vendor->putBucket($this->prefix.$name, $this->getPermissions($access));
	}

	
	/**
	 * Delete folder (Bucket)
	 * @param string $name - name of folder for delete
	 * @param boolean $recursive - will delete bucket with all files
	 * @return type 
	 */
	public function deleteFolder($name = '', $recursive = false) {
		
		if ($recursive) {
			$files = $this->getFileList($name);
			foreach ($files as $file) {
				$this->deleteFile($name, $file);
			}
		}
		
		return $this->S3Vendor->deleteBucket($this->prefix.$name);
	}
	
	
	/**
	 * Get list of files in bucket
	 * @param string $bucket - name of bucket
	 * @return array - list of files in $bucket
	 */
	public function getFileList($bucket = '') {
		
		$bucket_info = $this->S3Vendor->getBucket($this->prefix.$bucket);

		$files = array_keys($bucket_info);
		
		return $files;
	}

	
	/**
	 * Upload file to Amazon s3
	 * @param string $path_from - full path to file on local computer
	 * @param string $to_bucket - name of bucket. Will be created If not exist
	 * @param string $access - as in 'createFolder'
	 * @param boolead $unique - save with original name or with unique random 20 length name
	 * @return string | boolean - will return link to file if success upload and FALSE if else
	 */
	public function uploadFile($path_from = '', $to_bucket = '', $access = 'pr', $unique = false) {
		
		$isSuccess = false;
		$filename = basename($path_from);

		if ($unique) {
			$filename = $this->random();
		}
		
		if (!$this->isExistFolder($to_bucket)) {
			$this->createFolder($this->prefix.$bucket, 'pr');
		}
		
		if ($this->S3Vendor->putObjectFile($path_from, $this->prefix.$to_bucket, $filename, $this->getPermissions($access))) {
			$isSuccess = $this->prefix.$to_bucket.'/'.$filename;
		}
		
		return $isSuccess;
	}

	public function getFile($bucket ='', $filename = '', $save_to = null) {
		
		
	}

	
	/**
	 * Delete file from bucket
	 * @param string $bucket - name of bucket
	 * @param string $filename - name of file
	 * @return boolean
	 */
	public function deleteFile($bucket ='', $filename = '') {
		
		return $this->S3Vendor->deleteObject($this->prefix.$bucket, $filename);
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

	
	private function getPermissions($access) {
		
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
		
		return $acc;
	}
	
	
	/**
	 * Check exist bucket in Amazon S3
	 * @param string $bucket - nam of bucket
	 * @return boolean - true if $bucket already exist 
	 */
	private function isExistFolder($bucket = '') {
		
		$isExist = true;
		
		if ($this->S3Vendor->getBucket($this->prefix.$bucket) === false) {
			$isExist = false;
		}
		
		return $isExist;
	}
}