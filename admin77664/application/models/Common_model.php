<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Common_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// $requestedData['loggedIn_userID'] = $this->session->userdata('UW_ADMIN_ID');
		// $requestedData['loggedIn_Email']  = $this->session->userdata('UW_ADMIN_EMAIL');
		// $requestedData['loggedIn_MOBILE'] = $this->session->userdata('UW_ADMIN_MOBILE');
		// $requestedData['Requested_DATA']  = $_POST?$_POST:$_GET;
		// $this->generatelogs->putLog('ADMIN',json_encode($requestedData)); 
	}

	function milliseconds() {
	    $mt = explode(' ', microtime());
	    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
	}

	function microseconds() {
	    $mt = explode(' ', microtime());
	    return ((int)$mt[1]) * 1000000 + ((int)round($mt[0] * 1000000));
	}

	// This function will return a random
	// string of specified length
	function random_strings($length_of_string)
	{
	 
	    // String of all alphanumeric character
	    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	 
	    // Shuffle the $str_result and returns substring
	    // of specified length
	    return substr(str_shuffle($str_result),
	                       0, $length_of_string);
	}

	/***********************************************************************
	** Function Name returnIntegerEncryptValue
	** Developed By : Dilip Halder
	** Input Parameters 
	** 1. inputInteger = The integer value which need to encrypted
	** 2. returnLength = THe number of digit which need to be return from functon.
	** Function Process :- The function will take integr input and multiply it with current unixtimestamp.
	** The new value will be encrypt using md5 which return 32 bit string, The encrypt string convert to ASCII
	** value and then the desire lenght sub string will be return by function.
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function returnIntegerEncryptValue($inputInteger, $returnLength = 16)
	{
		$returnEncryptInterValue = '';
		$lenghtCounter = 0;
		$currentTimeStamp = $this->microseconds();//$this->milliseconds();//time();
		$vauleToBeEncrypted = $inputInteger * $currentTimeStamp;
		$encryptedString = md5($vauleToBeEncrypted);
		$encryptedStringCharArray = str_split($encryptedString);
		foreach($encryptedStringCharArray as $charValue):
			$asciiValue = ord($charValue);
			$asciiValueLength = strlen($asciiValue);
			$lenghtCounter = $lenghtCounter + $asciiValueLength;
			if($lenghtCounter < $returnLength):	
				$returnEncryptInterValue = $returnEncryptInterValue.$asciiValue;
				$returnEncryptInterValue.' rln '.strlen($returnEncryptInterValue);
			else:
				break;
			endif;
		endforeach;
		$remaingNumberOfDigits = $returnLength - strlen($returnEncryptInterValue);
		if($remaingNumberOfDigits > 0):
			for($remaingDigitsCounter = 0; $remaingDigitsCounter < $remaingNumberOfDigits; $remaingDigitsCounter++):
				$returnEncryptInterValue = $returnEncryptInterValue .rand ( 0 , 9);
			endfor;
		endif;
		return $returnEncryptInterValue;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getNextSequence
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Next Sequence
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getNextSequence($tableName='')
	{
		$this->mongo_db->select(array('seq'));
		$this->mongo_db->where(array('_id'=>$tableName));	
		$result = $this->mongo_db->find_one('uw_counters');
		if($result):  
			$newId				=	$result['seq']+1; 
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->where(array('_id'=>$tableName));
			$this->mongo_db->set(array('seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
			$this->mongo_db->update('uw_counters');
		else:
			$newId				=	100000000000001;
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->insert('uw_counters',array('_id'=>$tableName,'seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
		endif;
		return $encryptValue;//$newId;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name : getNextIdSequence
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Next Id Sequence
	** Date : 29 JULY 2021
	************************************************************************/
	public function getNextIdSequence($sequenceType='',$type='')
	{
		$this->mongo_db->select(array('seq'));
		$this->mongo_db->where(array('_id'=>$sequenceType));	
		$result = $this->mongo_db->find_one('uw_id_sequence');
		if($result):  
			$newId				=	$result['seq']+1; 
			$this->mongo_db->where(array('_id'=>$sequenceType));
			$this->mongo_db->set(array('seq'=>(int)$newId,'encrypted'=>(int)$newId));
			$this->mongo_db->update('uw_id_sequence');
		else:
			$newId				=	1;
			$this->mongo_db->insert('uw_id_sequence',array('_id'=>$sequenceType,'seq'=>(int)$newId,'encrypted'=>(int)$newId));
		endif;  

		if($type=='Sales Person'):  
		$constant 		=	array('users_seq_id'=>'SR');
		endif;

		if($type=='Retailer'):  
		$constant 		=	array('users_seq_id'=>'RT');
		endif;

		if($type=='Users'):  
		$constant 		=	array('users_seq_id'=>'CS');
		endif;

		if($type == 'Freelancer'):
			$constant 		=	array('users_seq_id'=>'FL');
		endif;

		if($type == 'Api User'):
			$constant 		=	array('users_seq_id'=>'AU');
		endif;
		
		$cueNewId 	 	= 	$newId<10?'0000'.$newId:($newId<100?'000'.$newId:($newId<1000?'00'.$newId:($newId<10000?'0'.$newId:$newId)));
		return $constant[$sequenceType].$cueNewId;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : generateSerialNo
	** Developed By  : Dilip Halder
	** Purpose       : This function used for get Next Sequence
	** Date          : 19 November 2024
	************************************************************************/
	public function generateSerialNo($tableName='')
	{
		$this->mongo_db->select(array('seq'));
		$this->mongo_db->where(array('_id'=>$tableName));	
		$result = $this->mongo_db->find_one('uw_counters');
		if($result):  
			$newId				=	$result['seq']+1; 
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->where(array('_id'=>$tableName));
			$this->mongo_db->set(array('seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
			$this->mongo_db->update('uw_counters');
		else:
			$newId				=	100000001;
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->insert('uw_counters',array('_id'=>$tableName,'seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
		endif;
		return $encryptValue;//$newId;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getNextInspectorIdSequence
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Inspector Next Id Sequence
	** Date : 03 AUGUST 2021
	************************************************************************/
	public function getNextInspectorIdSequence()
	{
		$sequenceType		=	'inspector_sequence_id';
		$this->mongo_db->select(array('seq'));
		$this->mongo_db->where(array('_id'=>$sequenceType));	
		$result = $this->mongo_db->find_one('uw_id_sequence');
		if($result):  
			$newId				=	$result['seq']+1; 
			$this->mongo_db->where(array('_id'=>$sequenceType));
			$this->mongo_db->set(array('seq'=>(int)$newId,'encrypted'=>(int)$newId));
			$this->mongo_db->update('uw_id_sequence');
		else:
			$newId				=	1;
			$this->mongo_db->insert('uw_id_sequence',array('_id'=>$sequenceType,'seq'=>(int)$newId,'encrypted'=>(int)$newId));
		endif;  
		$constant 		=	array('inspector_sequence_id'=>'CMPI');
		$cueNewId 	 	= 	$newId<10?'000'.$newId:($newId<100?'00'.$newId:($newId<1000?'0'.$newId:$newId));
		return $constant[$sequenceType].$cueNewId;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : addData
	** Developed By : Dilip Halder
	** Purpose  : This function used for add data
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function addData($tableName='',$param=array())
	{
		$last_insert_id 		=	$this->mongo_db->insert($tableName,$param);
		return $last_insert_id;
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : editData
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for edit data
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	function editData($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->set($param);
		$this->mongo_db->update($tableName);
		return true;
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : manageBalance
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for edit data
	 * * Date          : 21 November 2024
	 * * **********************************************************************/
	function manageBalance($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->inc($param);
		$this->mongo_db->update($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : addManyData
	** Developed By : Afsar Ali
	** Purpose  : This function used for multi add data
	** Date : 11 FEB 2023
	************************************************************************/
	public function addManyData($tableName='',$param=array())
	{
		$last_insert_id 		=	$this->mongo_db->batch_insert($tableName,$param);
		return $last_insert_id;
	}	// END OF FUNCTION

	
	/***********************************************************************
	** Function name : editDataByMultipleCondition
	** Developed By : Dilip Halder
	** Purpose  : This function used for edit data by multiple condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function editDataByMultipleCondition($tableName='',$param=array(),$whereCondition=array())
	{
		$this->mongo_db->where($whereCondition);
		$this->mongo_db->set($param);
		$this->mongo_db->update($tableName);
		return true;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : editMultipleDataByMultipleCondition
	** Developed By : Dilip Halder
	** Purpose  : This function used for edit data by multiple condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function editMultipleDataByMultipleCondition($tableName='',$param=array(),$whereCondition=array())
	{
		$this->mongo_db->where($whereCondition);
		$this->mongo_db->set($param);
		$this->mongo_db->update_all($tableName);
		return true;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : editMultipleDataByMultipleCondition
	** Developed By : Dilip Halder
	** Purpose  : This function used for edit data by multiple condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function editMultipleDataBySingleCondition($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->set($param);
		$this->mongo_db->update_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : deleteData
	** Developed By : Dilip Halder
	** Purpose  : This function used for delete data
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function deleteData($tableName='',$fieldName='',$fieldValue='')
	{
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : deleteParticularData
	** Developed By : Dilip Halder
	** Purpose  : This function used for delete particular data
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function deleteParticularData($tableName='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : deleteByMultipleCondition
	** Developed By : Dilip Halder
	** Purpose  : This function used for delete by multiple condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function deleteByMultipleCondition($tableName='',$whereCondition=array())
	{
		$this->mongo_db->where($whereCondition);
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name: getDataByParticularField
	** Developed By: Dilip Halder
	** Purpose: This function used for get data by encryptId
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getDataByParticularField($tableName='',$fieldName='',$fieldValue='')
	{  
	    
		$this->mongo_db->select('*');
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$result = $this->mongo_db->find_one($tableName);
		
		if($result):
		  //print_r($result);die;
			return $result;
		else:
			return false;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getSingleDataByParticularField
	** Developed By: Dilip Halder
	** Purpose: This function used for get Single Data By Particular Field
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getSingleDataByParticularField($fields=array(),$tableName='',$fieldName='',$fieldValue='')
	{  
		if(empty($fields)): $fields 	=	'*'; endif; 
		$this->mongo_db->select($fields);
		if($fieldName && $fieldValue):
			$this->mongo_db->where(array($fieldName=>$fieldValue));
		endif;
		$result = $this->mongo_db->find_one($tableName);
		if($result):
			return json_decode(json_encode($result),true);
		else:
			return false;
		endif;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name: getDataByQuery
	** Developed By: Dilip Halder
	** Purpose: This function used for get data by query
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getData($action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt='')
	{  
		
		$this->mongo_db->select('*');		
		if(isset($wcon['where']) && $wcon['where'])	$this->mongo_db->where($wcon['where']);	
		if(isset($wcon['where_or']) && $wcon['where_or'])	$this->mongo_db->where_or($wcon['where_or']);	
		if(isset($wcon['where_ne']) && $wcon['where_ne'])	$this->mongo_db->where_ne($wcon['where_ne'][0],$wcon['where_ne'][1]);	
		if(isset($wcon['where_in']) && $wcon['where_in'])	$this->mongo_db->where_in($wcon['where_in'][0],$wcon['where_in'][1]);	
		if(isset($wcon['where_between']) && $wcon['where_between'])	$this->mongo_db->where_between($wcon['where_between'][0],$wcon['where_between'][1],$wcon['where_between'][2]);	
		
		if(isset($wcon['where_gte']) && $wcon['where_gte'])	$this->mongo_db->where_gte($wcon['where_gte'][0][0] ,$wcon['where_gte'][0][1]);	
		if(isset($wcon['where_lte']) && $wcon['where_lte'])	$this->mongo_db->where_lte($wcon['where_lte'][0][0] ,$wcon['where_lte'][0][1]);	
		
		if(isset($wcon['like']) && $wcon['like'])	$this->mongo_db->like($wcon['like'][0],$wcon['like'][1],'i',TRUE,TRUE);
		if($shortField)				$this->mongo_db->order_by($shortField);				
		if($num_page):				$this->mongo_db->limit($num_page);
									$this->mongo_db->offset($cnt);						
		endif; 
		if (isset($wcon['populate']) && $wcon['populate']) {
		    $pipeline = [];
		    if (isset($wcon['where']['ticket'])) {
			    $regexPattern = $wcon['where']['ticket']; // The regex pattern as a string
				$status =  $wcon['where']['status'];
			    // Add $match stage to the pipeline with $regex
			    $pipeline[] = [
			        '$match' => [
			            'ticket' => $regexPattern, // 'i' for case-insensitive
						'status'=>$status,
						'raffle_mode' => [ '$exists' => false ]
			        ]
			    ];
			}elseif(isset($wcon['where']['notification_id'])){
				$regexPattern = $wcon['where']['notification_id'];
				$type = $wcon['where']['is_read'];
				// print_r($wcon);
				$pipeline[] = [
			        '$match' => [
			            'notification_id' => $regexPattern, // 'i' for case-insensitive
						'is_read'=>$type
			        ]
			    ];
			}
		    // Loop through populate conditions
		    foreach ($wcon['populate'] as $populate) {
		        if (isset($populate['field']) && isset($populate['from'])) {
		            $field = $populate['field'];  // Dynamic field (e.g., 'user_id')
		            $from = $populate['from'];    // Dynamic collection (e.g., 'user')

		            // Optional: Local and foreign field (if provided)
		            $localField = isset($populate['localField']) ? $populate['localField'] : $field;
		            $foreignField = isset($populate['foreignField']) ? $populate['foreignField'] : '_id';

		            // Add the $lookup stage to the pipeline
		            $pipeline[] = [
		                '$lookup' => [
		                    'from' => $from,              // The collection to join
		                    'localField' => $localField,   // The local field in the current collection
		                    'foreignField' => $foreignField, // The field in the 'from' collection
		                    'as' => $field               // The alias to store the joined data
		                ],

		            ];
		        }
		    }

		    
		    if (count($pipeline) > 0) {
		        // Print the pipeline for debugging purposes
		      
		        // Set cursor option
		        $options = [ 'batchSize' => 4];

		        // Execute the aggregation query
		        $result = $this->mongo_db->aggregate($tbl_name, $pipeline, $options);
		        
		        return $result ? $result : false;
		    }
		}
		if($action == 'count'):	
			return $this->mongo_db->count($tbl_name);
		elseif($action == 'single'):	
			$result = $this->mongo_db->find_one($tbl_name);
			if($result):
				return $result;
			else:
				return false;
			endif;
		elseif($action == 'multiple'):	
			$result = $this->mongo_db->get($tbl_name);
			if($result):	
				return $result;
			else:		
				return false;
			endif;
		else:
			return false;
		endif;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name: getFieldInArray
	** Developed By: Dilip Halder
	** Purpose: This function used for get data by condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getFieldInArray($field='',$tbl_name='',$wcon='')
	{  
		$returnarray			=	array();
		$this->mongo_db->select(array($field));	
		if(isset($wcon['where']))	$this->mongo_db->where($wcon['where']);	
		if(isset($wcon['where_ne']) && $wcon['where_ne'])	$this->mongo_db->where_ne($wcon['where_ne'][0],$wcon['where_ne'][1]);	
		if(isset($wcon['where_in']) && $wcon['where_in'])	$this->mongo_db->where_in($wcon['where_in'][0],$wcon['where_in'][1]);	
		if(isset($wcon['where_or']) && $wcon['where_or'])	$this->mongo_db->where_or($wcon['where_or']);	
		if(isset($wcon['where_between']) && $wcon['where_between'])	$this->mongo_db->where_between($wcon['where_between'][0],$wcon['where_between'][1],$wcon['where_between'][2]);	
		if(isset($wcon['like']))	$this->mongo_db->like($wcon['like'][0],$wcon['like'][1],'i',TRUE,TRUE);
		$result = $this->mongo_db->get($tbl_name);
		if($result):	
			foreach($result as $info):
				array_push($returnarray,$info[$field]);
			endforeach;
		endif;
		return $returnarray;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name: getLastOrderByFields
	** Developed By: Dilip Halder
	** Purpose: This function used for get Last Order By Fields
	** Date : 06 FEBRUARY 2024
	************************************************************************/ 
	public function getLastOrderByFields($field='',$tbl_name='',$fieldName='',$fieldValue='')
	{  
		$this->mongo_db->select(array($field));	
		if(isset($fieldName) && isset($fieldValue)):
			$this->mongo_db->where(array($fieldName=>$fieldValue));
		endif;
		$this->mongo_db->order_by(array($field=>'DESC'));	
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->find_one($tbl_name);  
		if($result):	
			return $result[$field];
		else:
			return 0;
		endif;
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : setAttributeInUse
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for set Attribute In Use
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	function setAttributeInUse($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$paramarray[$param]	=	'Y';
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->set($paramarray);
		$this->mongo_db->update($tableName);
		return true;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getPaticularFieldByFields
	** Developed By: Dilip Halder
	** Purpose: This function used for get Paticular Field By Fields
	** Date : 06 FEBRUARY 2024
	************************************************************************/ 
	public function getPaticularFieldByFields($field='',$tbl_name='',$fieldName='',$fieldValue='')
	{  
		$this->mongo_db->select(array($field));	
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->find_one($tbl_name);  
		if($result):	
			return $result[$field];
		else:
			return 0;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getParticularFieldByMultipleCondition
	** Developed By: Dilip Halder
	** Purpose: This function used for get Particular Field By Multiple Condition
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getParticularFieldByMultipleCondition($fields=array(),$tableName='',$wcon='')
	{  
		if(empty($fields)): $fields 	=	'*'; endif; 
		$this->mongo_db->select($fields);
		if(isset($wcon['where']))	$this->mongo_db->where($wcon['where']);	
		if(isset($wcon['where_ne']) && $wcon['where_ne'])	$this->mongo_db->where_ne($wcon['where_ne'][0],$wcon['where_ne'][1]);	
		if(isset($wcon['where_in']) && $wcon['where_in'])	$this->mongo_db->where_in($wcon['where_in'][0],$wcon['where_in'][1]);	
		if(isset($wcon['where_or']) && $wcon['where_or'])	$this->mongo_db->where_or($wcon['where_or']);	
		if(isset($wcon['where_between']) && $wcon['where_between'])	$this->mongo_db->where_between($wcon['where_between'][0],$wcon['where_between'][1],$wcon['where_between'][2]);	
		if(isset($wcon['like']))	$this->mongo_db->like($wcon['like'][0],$wcon['like'][1],'i',TRUE,TRUE);
		$result = $this->mongo_db->find_one($tableName);
		if($result):
			return json_decode(json_encode($result),true);
		else:
			return false;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getDataByNewQuery
	** Developed By: Dilip Halder
	** Purpose: This function used for get data by query
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getDataByNewQuery($fields=array(),$action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt='')
	{  
		if(empty($fields)): $fields 	=	'*'; endif; 
		$this->mongo_db->select($fields);	
		if(isset($wcon['where']) && $wcon['where'])	$this->mongo_db->where($wcon['where']);	
		if(isset($wcon['where_ne']) && $wcon['where_ne'])	$this->mongo_db->where_ne($wcon['where_ne'][0],$wcon['where_ne'][1]);	
		if(isset($wcon['where_or']) && $wcon['where_or'])	$this->mongo_db->where_or($wcon['where_or']);	
		if(isset($wcon['where_between']) && $wcon['where_between'])	$this->mongo_db->where_between($wcon['where_between'][0],$wcon['where_between'][1],$wcon['where_between'][2]);	
		if(isset($wcon['like']) && $wcon['like']):	
			$this->mongo_db->like($wcon['like'][0],$wcon['like'][1],'i',TRUE,TRUE);
		endif;
		if(isset($wcon['where_in']) && $wcon['where_in']):	
			foreach($wcon['where_in'] as $whereInData):  
				$this->mongo_db->where_in($whereInData[0],$whereInData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_gte']) && $wcon['where_gte']):	
			foreach($wcon['where_gte'] as $whereGteData):  
				$this->mongo_db->where_gte($whereGteData[0],$whereGteData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_lte']) && $wcon['where_lte']):	
			foreach($wcon['where_lte'] as $whereLteData):  
				$this->mongo_db->where_lte($whereLteData[0],$whereLteData[1]);
			endforeach;
		endif;
		if($shortField)				$this->mongo_db->order_by($shortField);				
		if($num_page):				
			$this->mongo_db->limit($num_page);
			$this->mongo_db->offset($cnt);						
		endif;
		if($action == 'count'):	
			return $this->mongo_db->count($tbl_name);
		elseif($action == 'single'):	
			$result = $this->mongo_db->find_one($tbl_name);
			if($result):
				return json_decode(json_encode($result),true);
			else:
				return false;
			endif;
		elseif($action == 'multiple'):	
			$result = $this->mongo_db->get($tbl_name);
			if($result):	
				return json_decode(json_encode($result),true);
			else:		
				return false;
			endif;
		else:
			return false;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getDataByMultipleAndCondition
	** Developed By: Dilip Halder
	** Purpose: This function used for get data by query
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getDataByMultipleAndCondition($tbl_name='',$query='',$arrayfield=array())
	{  
		$resultData = array();
		$result 		= 		$this->mongo_db->aggregate($tbl_name,$query,array('batchSize'=>4)); 
		foreach($result as $results):
			foreach($results as $key=>$valye):
				if(is_array($results[$key])):
					$results[$key] 	=	$results[$key][0];
				endif;
			endforeach;
			array_push($resultData,$results);
		endforeach;		
		$groupedData 	= json_decode(json_encode($resultData), true);
		return $groupedData;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getDataByGroupBy
	** Developed By: Dilip Halder
	** Purpose: This function used for get Data By Group By
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getDataByGroupBy($tbl_name='',$wcon1='',$wcon2='',$wcon3='',$wcon4='')
	{  
		$resultData = array();
		$Query 		=		array($wcon1,$wcon2,$wcon3,$wcon4);
		$result 	= 		$this->mongo_db->aggregate($tbl_name,$Query,array('batchSize'=>4));
		foreach($result as $result){
			$returnData = $result['result'];
			array_push($resultData,$returnData);
		}			
		$groupedData 	= json_decode(json_encode($resultData[0]), true);
		return $groupedData;
	}	// END OF FUNCTION
	/***********************************************************************
	** Function name: getMultipleDataByParticularField
	** Developed By: Ashish
	** Purpose: This function used for getMultipleDataByParticularField
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	public function getMultipleDataByParticularField($tableName='',$fieldName='',$fieldValue='')
	{  
		$this->mongo_db->select('*');
		if($fieldName && $fieldValue):
			$this->mongo_db->where(array($fieldName=>$fieldValue));
		endif;
		$result = $this->mongo_db->get($tableName);
		if($result):
			return $result;
		else:
			return false;
		endif;
		
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : fetch_common_data_type
	** Developed By : Ashish UMrao
	** Purpose  : This function used for fetch common data type
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function fetch_common_data_type($tableName='',$fieldName='',$fieldValue='')
	{
		$this->mongo_db->select('id,astrologer_id');
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$result = $this->mongo_db->get($tableName);
		if($result):
			return $result;
		else:
			return false;
		endif;
	}
	/***********************************************************************
	** Function name : delete_image_by_image_name
	** Developed By : Ashish UMrao
	** Purpose  : This function used for delete image by image name
	** Date : 06 FEBRUARY 2024
	************************************************************************/
	function delete_image_by_image_name($tableName='',$fieldName='',$fieldValue='')
	{	//echo $tableName.'---'.$fieldName.'---'.$fieldValue; die;
		$this->mongo_db->where(array($fieldName => $fieldValue));
		$this->mongo_db->delete($tableName);
		return true;
	}

	/***********************************************************************
	** Function name: getTitleSlug
	** Developed By: Dilip Halder
	** Purpose: This function used for get Title Slug
	** Date : 06 FEBRUARY 2024
	************************************************************************/ 
	public function getTitleSlug($title='',$tbl_name='')
	{  
		$this->mongo_db->select('count');	
		$this->mongo_db->where(array('title'=>$title));
		$this->mongo_db->where(array('table_name'=>$tbl_name));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->find_one('uw_title_count');
		$data 	= $result['count']?$result['count']:0;
		if($data == 0):	
			$param['title']					=	$title;
			$param['table_name']			=	$tbl_name;
			$param['count']					=	(int)$data+1;
			$alastInsertId					=	$this->addData('uw_title_count',$param);
			$titleSlug 						=	url_title(strtolower($title));
		else:
			$count							=	(int)$data+1;
			$this->mongo_db->where(array('title'=>$title));
			$this->mongo_db->where(array('table_name'=>$tbl_name));
			$this->mongo_db->set(array('count'=>(int)$count));
			$this->mongo_db->update('uw_title_count');
			$titleSlug 						=	url_title(strtolower($title.'-'.$count));
		endif;
		return $titleSlug;//$newId;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: checkDuplicate
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for check duplicate entry
	** Date 			: 05 APRIL 2022
	************************************************************************/ 
	public function checkDuplicate($tbl_name, $whereCon){
		$this->mongo_db->where($whereCon);
		return $this->mongo_db->count($tbl_name, $whereCon);
	} // END OF FUNCTION
	/***********************************************************************
	** Function name 	: checkBulkDuplicate
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for check duplicate entry
	** Date 			: 11 FEB 2023
	************************************************************************/ 
	public function checkBulkDuplicate($tbl_name, $whereCon = array()){
		$this->mongo_db->where_in('coupon_code',$whereCon);
		return $this->mongo_db->count($tbl_name, $whereCon);
	} // END OF FUNCTION
	/***********************************************************************
	** Function name 	: getRechargeStatistics
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for get recharge statistics
	** Date 			: 31 OCT 2022
	************************************************************************/ 
	public function getRechargeStatistics($wcon){
		$this->mongo_db->select('*');
		if(isset($wcon['where']) && $wcon['where']):
			$this->mongo_db->where($wcon['where']);
		endif;
		if(isset($wcon['where_gte']) && $wcon['where_gte']):	
			foreach($wcon['where_gte'] as $whereGteData):  
				$this->mongo_db->where_gte($whereGteData[0],$whereGteData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_lte']) && $wcon['where_lte']):	
			foreach($wcon['where_lte'] as $whereLteData):  
				$this->mongo_db->where_lte($whereLteData[0],$whereLteData[1]);
			endforeach;
		endif;
		
		$result = $this->mongo_db->get('uw_loadBalance');
		$data['admin'] = 0;
		$data['Retailer_credit'] = 0;
		$data['Retailer_debit'] = 0;
		$data['sales_person_credit'] = 0;
		$data['sales_person_debit'] = 0;
		$data['users_credit'] = 0;
		$data['users_debit'] = 0;

		foreach ($result as $key => $value) {
			if($value['created_by'] == 'ADMIN'){
				if($value['record_type'] == 'Credit'){
					$data['admin'] = $data['admin'] + $value['arabian_points'];
					$checkUser = $this->getParticularFieldByMultipleCondition(['users_type'], 'uw_users', ['users_id' => $value['user_id_cred']]);
					if($checkUser['users_type'] == 'Retailer'){
						$data['Retailer_credit'] = $data['Retailer_credit'] + $value['arabian_points'];
					}elseif($checkUser['users_type'] == 'Sales Person'){
						$data['sales_person_credit'] = $data['sales_person_credit'] + $value['arabian_points'];
					}elseif($checkUser['users_type'] == 'Users'){
						$data['users_credit'] = $data['users_credit'] + $value['arabian_points'];
					}
				}
			}elseif($value['created_by'] == 'Retailer'){
				if($value['record_type'] == 'Credit'){
					$data['Retailer_credit'] = $data['Retailer_credit'] + $value['arabian_points'];
				}elseif($value['record_type'] == 'Debit'){
					$data['Retailer_debit'] = $data['Retailer_debit'] + $value['arabian_points'];
				}
			}elseif($value['created_by'] == 'Sales Person'){
				if($value['record_type'] == 'Credit'){
					$data['sales_person_credit'] = $data['sales_person_credit'] + $value['arabian_points'];
				}elseif($value['record_type'] == 'Debit'){
					$data['sales_person_debit'] = $data['sales_person_debit'] + $value['arabian_points'];
				}	
			}elseif($value['created_by'] == 'Users'){
				if($value['record_type'] == 'Credit'){
					$data['users_credit'] = $data['users_credit'] + $value['arabian_points'];
				}elseif($value['record_type'] == 'Debit'){
					$data['users_debit'] = $data['users_debit'] + $value['arabian_points'];
				}
			}
		}
		return $data;
	} // END OF FUNCTION
	/***********************************************************************
	** Function name 	: getRegistrationStatistics
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for get recharge statistics
	** Date 			: 04 NOV 2022
	************************************************************************/ 
	public function getRegistrationStatistics($wcon){
		//echo '<pre>';print_r($wcon);
		$this->mongo_db->select('*');
		if(isset($wcon['where']) && $wcon['where']):
			$this->mongo_db->where($wcon['where']);
		endif;
		if(isset($wcon['where_gte']) && $wcon['where_gte']):	
			foreach($wcon['where_gte'] as $whereGteData):  
				$this->mongo_db->where_gte($whereGteData[0],$whereGteData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_lte']) && $wcon['where_lte']):	
			foreach($wcon['where_lte'] as $whereLteData):  
				$this->mongo_db->where_lte($whereLteData[0],$whereLteData[1]);
			endforeach;
		endif;
		
		$data = $this->mongo_db->count('uw_users');
		return $data;
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: getOrdersStatistics
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for get recharge statistics
	** Date 			: 04 NOV 2022
	************************************************************************/ 
	public function getOrdersStatistics($wcon){
		//echo '<pre>';print_r($wcon);
		$this->mongo_db->select('*');
		if(isset($wcon['where']) && $wcon['where']):
			$this->mongo_db->where($wcon['where']);
		endif;
		if(isset($wcon['where_gte']) && $wcon['where_gte']):	
			foreach($wcon['where_gte'] as $whereGteData):  
				$this->mongo_db->where_gte($whereGteData[0],$whereGteData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_lte']) && $wcon['where_lte']):	
			foreach($wcon['where_lte'] as $whereLteData):  
				$this->mongo_db->where_lte($whereLteData[0],$whereLteData[1]);
			endforeach;
		endif;
		
		$data = $this->mongo_db->count('uw_orders');
		return $data;
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: getOrdersStatistics
	** Developed By 	: AFSAR ALI
	** Purpose 			: This function used for get recharge statistics
	** Date 			: 04 NOV 2022
	************************************************************************/ 
	public function getQuickTicketStatistics($wcon){
		//echo '<pre>';print_r($wcon);
		$this->mongo_db->select('*');
		if(isset($wcon['where']) && $wcon['where']):
			$this->mongo_db->where($wcon['where']);
		endif;
		if(isset($wcon['where_gte']) && $wcon['where_gte']):	
			foreach($wcon['where_gte'] as $whereGteData):  
				$this->mongo_db->where_gte($whereGteData[0],$whereGteData[1]);
			endforeach;
		endif;
		if(isset($wcon['where_lte']) && $wcon['where_lte']):	
			foreach($wcon['where_lte'] as $whereLteData):  
				$this->mongo_db->where_lte($whereLteData[0],$whereLteData[1]);
			endforeach;
		endif;
		
		$data = $this->mongo_db->count('uw_ticket_orders');
		return $data;
	} // END OF FUNCTION

	/***********************************************************************
	** Function name: getInventoryList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Property Data
	** Date : 12 NOV 2022
	************************************************************************/
	public function getInventoryList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'products_id'=>1,
								'qty'=>1,
								'available_qty'=>1,
								'order_request_qty'=>1,
								'collection_point_id'=>1,
								'inventory_id'=>1,
								'creation_date'=>1,
								'status'=>1,

								'product_name'=>'$from_product.title',
								'stock'=>'$from_product.stock',
								'product_seq_id'=>'$from_product.product_seq_id',

								'collection_point_name'=>'$from_collection_point.collection_point_name',
								'users_email'=>'$from_collection_point.users_email',
								'users_mobile'=>'$from_collection_point.users_mobile',

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_emirate_collection_point','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_collection_point')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'products_id','foreignField'=>'products_id','as'=>'from_product')),

												  //array('$lookup'=>array('from'=>'uw_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),

												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			//echo $per_page;die();
			if($page):
				array_push($currentQuery,array('$skip'=>(int)$per_page));
				array_push($currentQuery,array('$limit'=>(int)$page));
			endif; 
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION
	/***********************************************************************
	** Function name 	: updateInventoryStock
	** Developed By 	: Afar Ali
	** Purpose 			: This function used for update Inventory Stock
	** Date 			: 14 NOV 2022
	************************************************************************/
	public function updateInventoryStock($pid='', $qty='')
	{  
		$resultData = array();
		$whereCon = [ 'products_id' => $pid ];
		$this->mongo_db->select('*');
		$this->mongo_db->where($whereCon);
		$resultData = $this->mongo_db->find_one('uw_products');
		$updataData = (int)$resultData['inventory_stock'] - (int)$qty;

		$this->common_model->editData('uw_products', [ 'inventory_stock' =>  $updataData ], 'products_id', $pid );
		return;
	}	// END OF FUNCTION
	/***********************************************************************
	** Function name: getProductRequestList
	** Developed By: Afsar Ali
	** Purpose: This function used for get product request Data
	** Date : 23 NOV 2022
	************************************************************************/
	public function getProductRequestList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'request_id'=>1,
								'collection_point_id'=>1,
								'inventory_id'=>1,
								'product_id'=>1,
								'users_id'=>1,
								'request_qty'=>1,
								'sent_qty'=>1,
								'creation_date'=>1,
								'sent_date'=>1,
								'status'=>1,

								'collection_point_name'=>'$from_collection_point.collection_point_name',
								'users_email'=>'$from_collection_point.users_email',
								'users_mobile'=>'$from_collection_point.users_mobile',

								'product_name'=>'$from_product.title',
								'stock'=>'$from_product.stock',
								'product_seq_id'=>'$from_product.product_seq_id',

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_emirate_collection_point','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_collection_point')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'products_id','foreignField'=>'product_id','as'=>'from_product')),

												  array('$lookup'=>array('from'=>'uw_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),

												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field)); //echo '<pre>';print_r($currentQuery);die;

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			// if($per_page):
			// 	array_push($currentQuery,array('$skip'=>(int)$page));
			// 	array_push($currentQuery,array('$limit'=>(int)$per_page));
			// endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getReferralList
	** Developed By: Afsar Ali
	** Purpose: This function used for get referral point Data
	** Date : 04 JAN 2023
	************************************************************************/
	public function getReferralList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'referral_id'=>1,
								'referral_user_code'=>1,
								'referral_from_id'=>1,
								'referral_to_id'=>1,
								'referral_percent'=>1,
								'referral_cart_amount'=>1,
								'referral_amount'=>1,
								'referral_product_id'=>1,
								'creation_ip'=>1,
								'created_at'=>1,
								'created_by'=>1,
								'status'=>1,

								'product_name'=>'$from_products.title',
								
								'sender_name' => '$from_sender.users_name',
								'sender_mobile' => '$from_sender.users_mobile',
								'sender_email' => '$from_sender.users_email',

								'receiver_name' => '$from_receiver.users_name',
								'receiver_mobile' => '$from_receiver.users_mobile',
								'receiver_email' => '$from_receiver.users_email',


								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;
		

		if(count($whereCondition) != 0):
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_from_id','foreignField'=>'users_id','as'=>'from_sender')),
												  array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_to_id','foreignField'=>'users_id','as'=>'from_receiver')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'referral_product_id','foreignField'=>'products_id','as'=>'from_products')),

												  $selectFields, 
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		else:
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_from_id','foreignField'=>'users_id','as'=>'from_sender')),
												  array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_to_id','foreignField'=>'users_id','as'=>'from_receiver')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'referral_product_id','foreignField'=>'products_id','as'=>'from_products')),

												  $selectFields, 
												  // array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		endif;

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;

			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);

			return $currentData;
		endif;
	}	// END OF FUNCTION



	/***********************************************************************
	** Function name: getReferralList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Sales Data
	** Date : 11 JAN 2023
	************************************************************************/
	public function getSaleslList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'title'=>1,
								'products_id'=>1,
								'stock'=>1,
								'totalStock'=>1,
								'target_stock'=>1,
							  	'category_name'=>1,
							  	'sub_category_name'=>1,
							  	'product_image'=>1,
							  	'clossingSoon'=>1,
							  	'product_seq_id'=>1,
							  	'validuptodate'=>1,
							  	'validuptotime'=>1,
								'actual_product_name'=>'$from_prize.title',
								'actual_product_image'=>'$from_prize.prize_image'
								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		// echo "<pre>";
		// print_r($whereCondition);
		// die();
		

		if(count($whereCondition) != 0):
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_prize','localField'=>'products_id','foreignField'=>'product_id','as'=>'from_prize')),
												  $selectFields, 
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		else:
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_from_id','foreignField'=>'users_id','as'=>'from_sender')),
												  array('$lookup'=>array('from'=>'uw_users','localField'=>'referral_to_id','foreignField'=>'users_id','as'=>'from_receiver')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'referral_product_id','foreignField'=>'products_id','as'=>'from_products')),

												  $selectFields, 
												  // array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		endif;

		// echo "<pre>";
		// print_r($currentQuery);
		// die();

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;

			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);

			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getsignupBonusList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Signup Bonus List
	** Date : 04 JAN 2023
	************************************************************************/
	public function getsignupBonusList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'load_balance_id'=>1,
								'user_id_cred'=>1,
								'arabian_points'=>1,
								'record_type'=>1,
								'arabian_points_from'=>1,
								'creation_ip'=>1,
								'created_at'=>1,
								'created_by'=>1,
								'status'=>1,
								
								'users_name' => '$from_users.users_name',
								'users_mobile' => '$from_users.users_mobile',
								'users_email' => '$from_users.users_email',


								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id_cred','foreignField'=>'users_id','as'=>'from_users')),

												  $selectFields, 
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name: getMembershipCashbackList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Membership Cashback List
	** Date : 04 JAN 2023
	************************************************************************/
	public function getMembershipCashbackList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=array('$project' => array(
								'_id'=>0 ,
								'arabian_points'=>1,
								'created_at' => 1 ,
								'status'=>1,
								'arabian_points_from'=>1,
								'record_type'=>1,
								'users_name'=>'$from_users.users_name',
								'users_type'=>'$from_users.users_type' 
							));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id_cred','foreignField'=>'users_id','as'=>'from_users')),

												  $selectFields, 
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;
		

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getTicketCount
	** Developed By: Dilip Halder
	** Purpose: This function used for get Ticket soldout count List
	** Date : 14 March 2023
	************************************************************************/
	public function getTicketCount($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  

		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 		= array('$project' => array(
								'_id'=>0 ,
								'product_id'=>1,
								'tickets_prefix' => 1,
								'tickets_sequence_start' => 1,
								'tickets_sequence_end'=> 1,
								'tickets_seq_id'=>1,
								'coupon_sold_number'=>'$quickcoupons_totallist.coupon_sold_number',
								'product_title' => '$products.title' ,
								'status'=>1,
								'created_at'=>'$quickcoupons_totallist.created_at'
							));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		if($page):
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_quickcoupons_totallist','localField'=>'tickets_seq_id','foreignField'=>'tickets_seq_id','as'=>'quickcoupons_totallist')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'products')),
												  $selectFields,
												  	array('$match'=>array('$and'=>$whereCondition)),
												  	array('$limit' => (int)$page),
												    array('$sort'=>$short_field));

		else:

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_quickcoupons_totallist','localField'=>'tickets_seq_id','foreignField'=>'tickets_seq_id','as'=>'quickcoupons_totallist')),
										    array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'products')),
										    $selectFields,
											array('$sort'=>$short_field));

		endif;
												  // echo '<pre>';print_r($currentQuery);die;
		
		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: duemanagement
	** Developed By: Dilip Halder
	** Purpose: This function used for get due management Statements
	** Date : 28 April 2023
	************************************************************************/
	public function duemanagement($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  


		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		if($where_condition['where_gte']):

            foreach($where_condition['where_gte'] as $where_key => $where_value):
                // $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
                array_push($filterArray,array($where_value[0]=> array('$gte' => $where_value[1])));
            endforeach;
        endif;

        if($where_condition['where_lte']):

            foreach($where_condition['where_lte'] as $where_key => $where_value):
                // $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
                array_push($filterArray,array($where_value[0]=> array('$lte' => $where_value[1])));
            endforeach;
        endif;

		$selectFields 		= array('$project' => array(
								'_id'=>0 ,
								'due_management_id'=>1,
								'user_id_to'=>1,
								'user_id_deb'=>1,
								'recharge_amt'=>1,
								'cash_collected'=>1,
								'due_amount'=>1,
								'record_type'=>1,
								'advanced_amount'=>1,
								'users_name'=>'$users.users_name',
								'last_name'=>'$users.last_name',
								'country_code'=>'$users.country_code',
								'users_mobile'=>'$users.users_mobile',
								'users_email'=>'$users.users_email',
								'availableArabianPoints'=>'$users.availableArabianPoints',
								'store_name'=>'$users.store_name',

								'sender_users_name'=>'$sender.users_name',
								'sender_last_name'=>'$sender.last_name',
								'sender_country_code'=>'$sender.country_code',
								'sender_users_mobile'=>'$sender.users_mobile',
								'sender_users_email'=>'$sender.users_email',
								'created_by'=>'$sender.users_type',
								'user_type'=>'$users.users_type',
								'created_at'=>'$created_at',
							));

		$whereCondition		=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(
										        array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id_to','foreignField'=>'users_id','as'=>'users')),
										        array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id_deb','foreignField'=>'users_id','as'=>'sender')),

											    $selectFields,
											    array('$unwind' => '$users_name' ),
											    // array('$unwind' => '$last_name' ),
											    // array('$unwind' => '$country_code' ),
											    // array('$unwind' => '$last_name' ),
											    // array('$unwind' => '$users_mobile' ),
											    // array('$unwind' => '$users_email' ),
											    // array('$unwind' => '$sender_users_name' ),
											    // array('$unwind' => '$sender_last_name' ),
											    // array('$unwind' => '$sender_country_code' ),
										        // array('$unwind' => '$sender_users_mobile' ),
										        // array('$unwind' => '$sender_users_email' ),
										        array('$unwind' => '$created_at' ),
											  	array('$match'=>array('$and'=>$whereCondition)),
											  	array('$group' => array('_id' => '$user_id_to' ,
											  		                    'count'=>array('$sum' => 1) ,
											  		                    'users_name' 		=> array('$first'=> '$users_name'),
											  		                    'last_name' 		=> array('$first'=> '$last_name'),
											  		                    'country_code' 		=> array('$first'=> '$country_code'),
											  		                    'users_mobile' 		=> array('$first'=> '$users_mobile'),
											  		                    'users_email' 		=> array('$first'=> '$users_email'),
											  		                    'store_name' 		=> array('$first'=> '$store_name'),
											  		                    'user_id_to' 		=> array('$first'=> '$user_id_to'),
											  		                    'user_id_deb' 		=> array('$first'=> '$user_id_deb'),
											  		                    'sender_users_name' 		=> array('$first'=> '$sender_users_name'),
											  		                    'sender_last_name' 		=> array('$first'=> '$sender_last_name'),
											  		                    'sender_country_code' 		=> array('$first'=> '$sender_country_code'),
											  		                    'sender_users_mobile' 		=> array('$first'=> '$sender_users_mobile'),
											  		                    'sender_users_email' 		=> array('$first'=> '$sender_users_email'),
											  		                    'availableArabianPoints' => array('$first'=> '$availableArabianPoints'),
											  		                    'recharge_amt' 		=> array('$sum'=> '$recharge_amt'),
											  		                    'cash_collected' 	=> array('$sum'=> '$cash_collected'),
											  		                    'due_amount' 		=> array('$sum'=> '$due_amount') ,
											  		                    'advanced_amount' 	=> array('$sum'=> '$advanced_amount'), 
											  		                    'advanced_amount' 	=> array('$sum'=> '$advanced_amount'), 
											  		                    'cash_collected' 	=> array('$sum'=> '$cash_collected'),
											  		                    'created_by' 		=> array('$last'=> '$created_by'),
											  		                    'user_type' 		=> array('$last'=> '$user_type'), 
											  		                    'created_at' 		=> array('$last'=> '$created_at'),
											  		                )),
												array('$sort'=>$short_field)
										); 

										// echo '<pre>';print_r($currentQuery);die;
		
		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name: getAllWalletStatements
	** Developed By: Dilip Halder
	** Purpose: This function used for get Wallet Statements
	** Date : 02 Apirl 2023
	************************************************************************/
	public function getAllWalletStatements($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  

		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 		= array('$project' => array(
								'_id'=>0 ,
								'users_id' => 1,
								'users_name' => 1,
								'last_name' => 1,
								'users_mobile' => 1,
								'users_email' => 1,
								'users_type' => 1,
								'availableArabianPoints' => 1,

								'order_statement' => '$order',
								// 'order_id'=> '$order.order_id',
								// 'payment_from' => '$order.payment_from',
								// 'created_at' => '$order.created_at',
								// 'price' => '$order.price',
								// 'availableArabianPoints' => '$order.availableArabianPoints',
								// 'end_balance' => '$order.end_balance',
								// 'payment_mode' => '$order.payment_mode',
								// 'order_status' =>  '$order.order_status',
								// 'product_is_donate' => '$order.product_is_donate',

								
								// 'product_name' => '$orderdetails.product_name',
								// 'price' => '$orderdetails.price',
								// 'quantity' => '$orderdetails.quantity',

								// 'users_name' => '$users.users_name',
								// 'users_mobile' => '$users.users_mobile',
								// 'users_email' => '$users.users_email',
								// 'arabian_points' => '$loadBalance.arabian_points',
								// // 'record_type' => '$loadBalance.record_type',
								// 'cashback_amount' => '$cashback.cashback',
								// 'cashback_created_at' => '$cashback.created_at',
								// 'referral_amount' => '$referral.referral_amount',
								// 'referral_created_at' => '$referral.created_at',


								// 'product_name' => '$coupons.product_title',
								// 'quantity' => '$coupons.product_qty',
								// 'total_price' => '$coupons.total_price',
								// 'users_name' => '$coupons.order_first_name',
								// 'last_name' => '$coupons.order_last_name',
								// 'users_mobile' => '$coupons.order_users_mobile',
								// 'users_email' => '$coupons.order_users_email',



							
							));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		if($page):
		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_orders_details','localField'=>'order_id','foreignField'=>'order_id','as'=>'order')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'user_id','foreignField'=>'users_id','as'=>'users')),
										    	  array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'users')),
									     		  // array('$lookup'=>array('from'=>'uw_loadBalance','localField'=>'order_id','foreignField'=>'order_id','as'=>'loadBalance')),
									     		  array('$lookup'=>array('from'=>'uw_cashback','localField'=>'order_id','foreignField'=>'order_id','as'=>'cashback')),
									     		  array('$lookup'=>array('from'=>'referral_product','localField'=>'order_id','foreignField'=>'order_id','as'=>'referral')),
												  $selectFields,
												  	array('$match'=>array('$and'=>$whereCondition)),
												  	array('$limit' => (int)$page),
												    array('$sort'=>$short_field));
		else:

		$currentQuery					=	array(
												  array('$lookup'=>array('from'=>'uw_orders','localField'=>'users_id','foreignField'=>'user_id','as'=>'order')),
												  array('$lookup'=>array('from'=>'uw_orders_details','localField'=>'order_id','foreignField'=>'order_id','as'=>'orderdetails')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'user_id','foreignField'=>'users_id','as'=>'users')),
										    	  array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'users')),


									     		  array('$lookup'=>array('from'=>'uw_ticket_coupons','localField'=>'ticket_order_id','foreignField'=>'ticket_order_id','as'=>'coupons')),






									     		  // array('$lookup'=>array('from'=>'uw_loadBalance','localField'=>'order_id','foreignField'=>'order_id','as'=>'loadBalance')),
									     		  array('$lookup'=>array('from'=>'uw_cashback','localField'=>'order_id','foreignField'=>'order_id','as'=>'cashback')),
									     		  array('$lookup'=>array('from'=>'referral_product','localField'=>'order_id','foreignField'=>'order_id','as'=>'referral')),
										    $selectFields,
											array('$sort'=>$short_field));

		endif;
												  // echo '<pre>';print_r($currentQuery);die;
		
		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getNextPOSId
	** Developed By: Dilip Halder
	** Purpose: This function used for get getNextPOSId
	** Date : 09 Fenruary 2024
	************************************************************************/
	public function getNextPOSId($tableName='')
	{
		$this->mongo_db->select(array('seq'));
		$this->mongo_db->where(array('_id'=>$tableName));	
		$result = $this->mongo_db->find_one('uw_counters');

		if($result):  
			$newId				=	$result['seq']+1; 
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->where(array('_id'=>$tableName));
			$this->mongo_db->set(array('seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
			$this->mongo_db->update('uw_counters');
		else:
			$newId				=	1100;
			$encryptValue 		=	$newId;//$this->returnIntegerEncryptValue($newId,16);
			$this->mongo_db->insert('uw_counters',array('_id'=>$tableName,'seq'=>(int)$newId,'encrypted'=>(int)$encryptValue));
		endif;
		return $encryptValue;//$newId;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getOrderDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get OrderDetails..
	** Date 		 : 19 July 2024
	************************************************************************/
	public function getOrderDetails($resultType,$whereCon,$startIndex='',$itemsPerPage='',$tblName = "uw_lotto_orders",$matchBeforeLookup=false)
	{  
		$SelectFields = array(
	  	  // 'pos_number'       => array('$arrayElemAt' => array('$users.pos_number', 0)), 
	      // 'pos_device_id'    => array('$arrayElemAt' => array('$users.pos_device_id', 0)), 

		  'pos_number'       	=> '$pos_number', 
	      'pos_device_id'    	=> '$pos_device_id', 
	      'order_id'         	=> '$order_id',
	      'product_id'     	 	=> '$product_id',
	      'product_name'     	=> '$product_title',
	      'product_qty'      	=> '$product_qty',
	      'users_name'       	=> array('$arrayElemAt' => array('$users.users_name', 0)),
	      'last_name'        	=> array('$arrayElemAt' => array('$users.last_name', 0)),
	      'user_email'       	=> array('$arrayElemAt' => array('$users.users_email', 0)),
	      'store_name'       	=> array('$arrayElemAt' => array('$users.store_name', 0)),
	      'users_type'       	=> array('$arrayElemAt' => array('$users.users_type', 0)),
	      'users_address'      	=> array('$arrayElemAt' => array('$users.address', 0)),
	      'users_area'      	=> array('$arrayElemAt' => array('$users.area', 0)),
	      'user_phone'       	=> '$user_phone',
	      'bindwith_first_name'  => array('$arrayElemAt' => array('$bindwith.users_name', 0)),
	      'bindwith_last_name'   => array('$arrayElemAt' => array('$bindwith.last_name', 0)),
	      'bindwith_email'       => array('$arrayElemAt' => array('$bindwith.users_email', 0)),
	      'bindwith_mobile'      => array('$arrayElemAt' => array('$bindwith.users_mobile', 0)),
	      'bindwith_users_type'  => array('$arrayElemAt' => array('$bindwith.users_type', 0)),
	      'created_at'		       => '$created_at',
	      'total_price' 	 	   => '$total_price',
	      'payment_mode' 	       => '$payment_mode',
	      'order_status'	       => '$order_status',
	      'status' 			       => '$status',
	      'user_id'			       => 1,
	      'ticket'			       => 1,
	      'update_date'	     	   => 1,
	      'selection_values' 	   => 1,
	      'order_code' 			   => 1,
	      'seller_details' 		   => 1,
	      'raffle_mode'			   => 1,
	      'raffle_tickets'		   => 1,
	      'super_ball_mode'	       => 1,
	      'sb_tickect'		   	   => 1,
		  'straight_add_on_amount' => '$straight_add_on_amount',
	      'rumble_add_on_amount'   => '$rumble_add_on_amount',
	      'reverse_add_on_amount'  => '$reverse_add_on_amount',
	      'delivery_address'  	   => '$delivery_address',
	      'delivery_charge'  	   => '$delivery_charge',
	      'pickup_point'  	   	   => '$pickup_point',
	      'admin_bindwith_first_name'   =>  array('$arrayElemAt' => array('$admin_bindwith.users_name', 0)) ,
	      'admin_bindwith_last_name'    =>  array('$arrayElemAt' => array('$admin_bindwith.last_name', 0)) ,
	      'admin_bindwith_users_type'   =>  array('$arrayElemAt' => array('$admin_bindwith.users_type', 0)),
	      'admin_bindwith_users_mobile' =>  array('$arrayElemAt' => array('$admin_bindwith.users_mobile', 0)),
	      'admin_bindwith_users_email'  =>  array('$arrayElemAt' => array('$admin_bindwith.users_email', 0)),
		);
		
		if($whereCon['where']):
			$whereCondition = $whereCon['where'];
		endif;
	 

    	// $lookup       = array( array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id', 'as'=>'users') );

		$lookup = array(
		    array(
		        'from' => 'uw_users',
		        'localField' => 'user_id',
		        'foreignField' => 'users_id',
		        'pipeline' => array(
		            array(
	                    '$project' => array(
	                        'users_name'     => 1,
	                        'last_name'      => 1,
	                        'users_type'     => 1,
	                        'users_mobile'   => 1,
	                        'users_email'    => 1,
	                        'store_name'     => 1,
	                        'bind_person_id' => 1,
	                        'bind_person_name' => 1,
	                        'address' 		   => 1,
	                        'area' 		   	   => 1,
	                    )
	                ),
	            ),
		        'as' => 'users'
		    ),
		    array(
		        'from' => 'uw_users',
	            // 'let' => array('bind_person_id' => array('$toLong' => array('$arrayElemAt' => array('$users.bind_person_id', 0)))),
				'let' => array(
					'bind_person_id' => array(
						'$convert' => array(
							'input' => array('$arrayElemAt' => array('$users.bind_person_id', 0)),
							'to' => 'long',
							'onError' => null,
							'onNull' => null
						)
					)
				),
	            'pipeline' => array(
	                array(
	                    '$match' => array(
	                        '$expr' => array(
	                            '$eq' => array('$users_id', '$$bind_person_id') // Match based on converted bind_person_id
	                        )
	                    )
	                ),
		            array(
	                    '$project' => array(
	                        'users_id' 		=> 1,
	                        'users_name'    => 1,
	                        'users_type'    => 1,
	                        'users_email'   => 1,
	                        'users_mobile'  => 1,
	                    )
	                ),
	            ),
	            'as' => 'bindwith'
		    ),

	     	array(
		        'from' => 'uw_admin',
		        // 'let' => array( 'admin_id_converted' => array( '$toLong' => array(  '$arrayElemAt' => array('$users.bind_person_id', 0) ) ) ),
				'let' => array(
					'admin_id_converted' => array(
						'$convert' => array(
							'input' => array(
								'$arrayElemAt' => array('$users.bind_person_id', 0)
							),
							'to' => 'long',
							'onError' => null,
							'onNull' => null
						)
					)
				),

		        'pipeline' => array(
		            array(
		                '$match' => array(
		                    '$expr' => array(
		                        '$eq' => array('$admin_id', '$$admin_id_converted') // Compare as a number
		                    )
		                )
		            ),
	        	array(
	                    '$project' => array(
	                        'users_id' 		=> '$admin_id',
	                        'users_name'    => '$admin_first_name',
	                        'users_type'    => 'Admin',
	                        'users_email'   => '$admin_email',
	                        'users_mobile'  => '$admin_phone',
	                    )
	                ),
		        ),
		        'as' => 'admin_bindwith'
		    ),


		);

    	$sortBy       = array('created_at' => -1);
		$unwind       = array();
	    $OrderData    = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage,$matchBeforeLookup);
	    return $OrderData;
	    die();
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getWithdrawRequestDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get OrderDetails..
	** Date 		 : 08 January 2025
	************************************************************************/
	public function getWithdrawRequestDetails($resultType,$whereCon,$startIndex='',$itemsPerPage='',$tblName='')
	{  
		$SelectFields = array(
	       'users_name'       	=> array('$arrayElemAt' => array('$users.users_name', 0)),
	      'last_name'        	=> array('$arrayElemAt' => array('$users.last_name', 0)),
	      'user_email'       	=> array('$arrayElemAt' => array('$users.users_email', 0)),
	      'user_mobile'       	=> array('$arrayElemAt' => array('$users.users_mobile', 0)),
	      'store_name'       	=> array('$arrayElemAt' => array('$users.store_name', 0)),
	      'users_type'       	=> array('$arrayElemAt' => array('$users.users_type', 0)),
	      'type'       			=> 1,
	      'amount'       		=> 1,
	      'account_holder_name' => 1,
	      'bank_name'       	=> 1,
	      'account_no'       	=> 1,
	      'swiftBicCode'       	=> 1,
	      'iben'       			=> 1,
	      'orderIds'       		=> 1,
	      'orderData'       	=> 1,
	      'ifsc_code'       	=> 1,
	      'request_id'       	=> 1,
	      'user_id'       		=> 1,
	      'user_oid'       		=> 1,
	      'withdraw_id'       	=> 1,
	      'created_at'       	=> 1,
	      'cripto_id'       	=> 1,
	      'cryto_account_id'    => 1,
	      'status'       		=> 1,
	      'request_id'       	=> 1,
	      'updated_at'       	=> 1,
	      'reason'       		=> 1,
		);
		
		if($whereCon['where']):
			$whereCondition = $whereCon['where'];
		endif;

		$lookup = array(
		    array(
		        'from' => 'uw_users',
		        'localField' => 'user_id',
		        'foreignField' => 'users_id',
		        'pipeline' => array(
		            array(
	                    '$project' => array(
	                        'users_name'     => 1,
	                        'last_name'      => 1,
	                        'users_type'     => 1,
	                        'users_mobile'   => 1,
	                        'users_email'    => 1,
	                    )
	                ),
	            ),
		        'as' => 'users'
		    ),
		);

    	$sortBy       = array('created_at' => -1);
	    $OrderData    = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
	    return $OrderData;
	    die();
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getCashVouchers
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get recharge details.
	** Date 		 : 23 November 2024
	************************************************************************/
	public function getCashVouchers($tblName = "", $whereCon = "")
	{
	    $SelectFields = array(
	        '_id'        		 => '$_id',
	        'coupon_code'        => '$coupon_code',
	        'verification_code'  => '$coupon_code_amount',
	        'amount'  			 => '$amount',
	        'status'             => '$status',
	        'created_date'       => '$created_date',
	        'user_oid'			 => '$user_oid',
	        'users_id'			 => '$users_id',
	        'created_by'         => '$created_by',
	        'seller_id'			 => '$seller_id',
	        'redeemed_date'		 => '$update_date',
	        'created_by_email'   => array('$arrayElemAt' => array('$createdBy.users_email', 0)),
	        'created_by_mobile'  => array('$arrayElemAt' => array('$createdBy.users_mobile', 0)),
	        'created_by_user'    => array('$arrayElemAt' => array('$createdBy.users_type', 0)),
	        'created_availableArabianPoints' => array('$arrayElemAt' => array('$createdBy.availableArabianPoints', 0)),
	        
	        'redeemed_by_user_type'   => array('$arrayElemAt' => array('$RedeemedBy.users_type', 0)),
	        'redeemed_by_pos_number' 	=> array('$arrayElemAt' => array('$RedeemedBy.pos_number', 0)),
	        'redeemed_by_user_mobile' 		=> array('$arrayElemAt' => array('$RedeemedBy.users_mobile', 0)),
	        'redeemed_by_name'   		=> array('$arrayElemAt' => array('$RedeemedBy.users_name', 0)),
	        // 'RedeemedBy' 					 =>  '$RedeemedBy',
	    );

	    if (isset($whereCon['where'])):
	        $whereCondition = $whereCon['where'];
	    endif;
	   
	    $lookup = array(
	    	// createdBy Details
	        array(
	            'from' => 'uw_users',
	            'let' => array(
	                'createdById' => '$created_by',

	            ),
	            'pipeline' => array(
				    array(
				        '$match' => array(
				            '$expr' => array(
				                '$and' => array(
				                    array('$eq' => array('$users_id', '$$createdById')),
				                    array('$ne' => array('$created_user', 'Admin')),
				                ),
				            ),
				        ),
				    ),
				    array(
				        '$project' => array(
				            '_id'    => 1,
				            'users_id'    => 1,
				            'users_name'  => 1,
				            'users_type'  => 1,
				            'users_email' => 1,
				            'users_mobile'=> 1,
				            'availableArabianPoints'=> 1,

				        ),
				    ),
				),
				'as' => 'createdBy',
	        ),

	        // RedeemedBy Details
	        array(
	            'from' => 'uw_users',
	            'let' => array(
	                'sellerID' => '$seller_id',

	            ),
	            'pipeline' => array(
				    array(
				        '$match' => array(
				            '$expr' => array(
				                '$and' => array(
				                    array('$eq' => array('$users_id', '$$sellerID')),
				                ),
				            ),
				        ),
				    ),
				    array(
				        '$project' => array(
				            '_id'    => 1,
				            'users_id'    => 1,
				            // 'users_name'  => 1,
				            'users_type'  => 1,
				            'users_mobile'=> 1,
				            'pos_number'=> 1,

				        ),
				    ),
				),
				'as' => 'RedeemedBy',
	        ),
	    );

	    $sortBy 	= array('sequence_id' => -1);
	    $resultData = $this->common_model->getAggregateData(
	        $tblName,
	        $SelectFields,
	        $whereCondition,
	        $groupBy,
	        $sortBy,
	        $lookup,
	        $unwind,
	        $resultType,
	        $startIndex,
	        $itemsPerPage
	    );

	    return $resultData;
	    die();
	}
	
	/***********************************************************************
	** Function name : getrechargeDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get recharge details.
	** Date 		 : 20 November 2024
	************************************************************************/
	public function getrechargeDetails($tblName = "", $whereCon = "")
	{
	    $SelectFields = array(
	        'rc_id'              => '$rc_id',
	        'coupon_code'        => '$coupon_code',
	        'generate_for'       => '$generate_for',
	        'aed'                => '$aed',
	        'coupon_code_amount' => '$coupon_code_amount',
	        'coupon_code_statys' => '$coupon_code_statys',
	        'status'             => '$status',
	        'created_user'       => '$created_user',
	        'created_by'         => '$created_by',
	        'created_date'       => '$created_date',
	        'expair_date'        => '$expair_date',
	        'redeemed_by'        => '$redeemed_by',
	        'modified_at'      	 => '$modified_at',
	        'batch_id'      	 => '$batch_id',
	        '_id'                => 1,
	        'commission_amount'	 => array('$arrayElemAt' => array('$commission.upoints', 0) ),
	        'commission_remarks' => array('$arrayElemAt' => array('$commission.remarks', 0) ),

	        // 'commission'		 => '$commission',
	        // 'createdByAdmin'	=> '$createdByAdmin',
	        // 'createdBy'			=> '$createdBy'
	        'user_oid'			 => 1,
	        'availableArabianPoints'=> array('$arrayElemAt' => array('$createdBy.availableArabianPoints', 0) ),
	        // 'users_oid'          => array('$arrayElemAt' => array('$createdBy._id', 0) ),
	        'created_by_first_name'   => array('$ifNull' => array(
	        	array('$arrayElemAt' => array('$createdByAdmin.admin_first_name', 0)),
	        	array('$arrayElemAt' => array('$createdBy.users_name', 0))
	        )),
	        'created_by_last_name'   => array('$ifNull' => array(
	        	array('$arrayElemAt' => array('$createdByAdmin.admin_last_name', 0)),
	        	array('$arrayElemAt' => array('$createdBy.last_name', 0))
	        )),

	        'created_by_email'   => array('$ifNull' => array(
	        	array('$arrayElemAt' => array('$createdByAdmin.admin_email', 0)),
	        	array('$arrayElemAt' => array('$createdBy.users_email', 0))
	        )),

	        'created_by_mobile'  => array('$ifNull' => array(
	    	 	array('$arrayElemAt' => array('$createdByAdmin.admin_phone', 0)),
	        	array('$arrayElemAt' => array('$createdBy.users_mobile', 0))
	        )),
	        'created_by_user'  => array('$ifNull' => array(
	    	 	array('$arrayElemAt' => array('$createdByAdmin.created_by', 0)),
	        	array('$arrayElemAt' => array('$createdBy.users_type', 0))
	        )),
	        'created_by_pos_no'  => array('$arrayElemAt' => array('$createdBy.pos_number', 0)),


	        'redeemedBy_by_pos_device_id'  => array('$arrayElemAt' => array('$redeemedBy.pos_device_id', 0)),
	        'redeemedBy_by_pos_number'	   => array('$arrayElemAt' => array('$redeemedBy.pos_number', 0)),
	        'redeemedBy_by_user_mobile'    => array('$arrayElemAt' => array('$redeemedBy.users_mobile', 0)),
	        'redeemedBy_by_user_email'     => array('$arrayElemAt' => array('$redeemedBy.users_email', 0)),
	        'redeemedBy_by_user_type'      => array('$arrayElemAt' => array('$redeemedBy.users_type', 0)),
	        'redeemedBy_by_user_first_name'=> array('$arrayElemAt' => array('$redeemedBy.users_name', 0)),
	        'redeemedBy_by_user_last_name' => array('$arrayElemAt' => array('$redeemedBy.last_name', 0)),
	    );

	    if (isset($whereCon['where'])):
	        $whereCondition = $whereCon['where'];
	    endif;

	    $lookup = array(
	        
	    	// createdBy
	        array(
	            'from' => 'uw_users',
	            'let' => array(
	                'createdById' => '$created_by',

	            ),
	            'pipeline' => array(
				    array(
				        '$match' => array(
				            '$expr' => array(
				                '$and' => array(
				                    array('$eq' => array('$users_id', '$$createdById')),
				                    array('$ne' => array('$created_user', 'Admin')),
				                ),
				            ),
				        ),
				    ),
				    array(
				        '$project' => array(
				            '_id'    => 1,
				            'users_id'    => 1,
				            'users_name'  => 1,
				            'users_type'  => 1,
				            'users_email' => 1,
				            'users_mobile'=> 1,
				            'availableArabianPoints'=> 1,
				            'pos_number' => 1,

				        ),
				    ),
				),
				'as' => 'createdBy',
	        ),
	        // createdByAdmin
	        array(
	            'from' => 'uw_admin',
	            'let' => array(
	                'createdById' => '$created_by',
	            ),
	            'pipeline' => array(
	                array(
	                    '$match' => array(
	                        '$expr' => array(
				                '$and' => array(
				                    array('$eq' => array('$admin_id', '$$createdById')),
				                    array('$eq' => array('$created_user', 'Admin')),
				                ),
				            ),
	                    ),
	                ),
	                array(
	                    '$project' => array(
	                        'admin_id'    => 1,
	                        'admin_first_name' => 1,
	                        'admin_last_name'  => 1,
	                        'admin_email' => 1,
	                        'admin_phone' => 1,
	                        'created_by' => 'Admin',
	                    ),
	                ),
	            ),
	            'as' => 'createdByAdmin',
	        ),
	       
	        // redeemedBy
	        array(
	            'from' => 'uw_users',
	            'let' => array(
	                'redeemedById' => '$redeemed_by',
	            ),
	            'pipeline' => array(
	                array(
	                    '$match' => array(
	                        '$expr' => array(
	                            '$eq' => array('$users_id', '$$redeemedById'),
	                        ),
	                    ),
	                ),
	                array(
	                    '$project' => array(
	                        'pos_number'    => 1,
	                        'pos_device_id' => 1,
	                        'users_email'   => 1,
	                        'users_mobile'  => 1,
	                        'users_type'    => 1,
	                        'users_name'    => 1,
	                        'last_name'     => 1,
	                    ),
	                ),
	            ),
	            'as' => 'redeemedBy',
	        ),

         	// commission
	        array(
	            'from' => 'uw_loadBalance',
	            'let' => array(
	                'rcId' => '$_id',
	            ),
	            'pipeline' => array(
	                array(
	                    '$match' => array(
	                        '$expr' => array(
				                '$and' => array(
				                    array('$eq' => array('$request_oid', '$$rcId')),
				                    array('$in' => array('$narration', array('Recharge Commission', 'K-Points Purchase Commission'))),
				                ),
				            ),
	                    ),
	                ),
	                array(
	                    '$project' => array(
	                        'request_id'  => 1,
	                        'request_oid' => 1,
	                        'record_type' => 1,
	                        'narration'   => 1,
	                        'remarks'     => 1,
	                        'upoints'     => 1,

	                    ),
	                ),
	            ),
	            'as' => 'commission',
	        ),
	    );

	    $sortBy = array('sequence_id' => -1);
	    $OrderData = $this->common_model->getAggregateData(
	        $tblName,
	        $SelectFields,
	        $whereCondition,
	        $groupBy,
	        $sortBy,
	        $lookup,
	        $unwind,
	        $resultType,
	        $startIndex,
	        $itemsPerPage
	    );

	    return $OrderData;
	    die();
	}

	/***********************************************************************
	** Function name : getRechargeData
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get recharge details created by admin.
	** Date 		 : 02 december 2024
	************************************************************************/
	public function getRechargeData($resultType,$whereCon,$startIndex='',$itemsPerPage='',$tblName = "uw_lotto_orders")
	{
	    $SelectFields = array(
        	 '_id'   			=> 0,
            'created_user'      => 1,
            'batch_id'   		=> 1,
			'rc_id'    			=> 1,
            'coupon_code' 		=> 1,
            'batch_count'   	=> 1,
            'first_coupon_code' => 1,
            'last_coupon_code'  => 1,
            'generate_for'  	=> 1,
            'created_date'  	=> 1,

	    );

	    if (isset($whereCon['where'])):
	        $whereCondition = $whereCon['where'];
	    endif;

	    $groupBy   = array(
            '_id' 		    => '$batch_id',
            'batch_id'      => array('$first'   => '$batch_id'),
            'batch_count'	=> array('$sum' => 1 ),
            'first_coupon_code'	=> array('$first' => '$coupon_code'),
            'last_coupon_code'	=> array('$last'  => '$coupon_code'),
            'created_user'	=> array('$last'  => '$created_user'),
            'generate_for'	=> array('$last'  => '$generate_for'),
            'created_date'	=> array('$last'  => '$created_date'),
        );

	    $sortBy    = array('batch_id' => -1);
	    $OrderData = $this->common_model->getAggregateData(
	        $tblName,
	        $SelectFields,
	        $whereCondition,
	        $groupBy,
	        $sortBy,
	        $lookup,
	        $unwind,
	        $resultType,
	        $startIndex,
	        $itemsPerPage
	    );

	    return $OrderData;
	    die();
	}


	/***********************************************************************
	** Function name : OrderStaticReport
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orice static report by condition..
	** Date 		 : 25 May 2024
	************************************************************************/
	public function getAggregateData($tblName='',$SelectFields=array(),$whereCondition='',$groupBy='',$sortBy='',$lookup='',$unwind='',$resultType,$startIndex='',$itemsPerPage='',$matchBeforeLookup=false)
	{
		$query = array();

		if($groupBy):
			$query[] = array('$group' => $groupBy);
		endif;

		if($matchBeforeLookup):
			/*
			 * Optimized order (same output, lighter DB load):
			 * MATCH -> SORT -> SKIP/LIMIT -> LOOKUP -> PROJECT
			 * Filter (and paginate) first so the expensive joins run
			 * only on the rows we actually need instead of the whole collection.
			 */
			if($whereCondition):
				$query[] = array('$match' => $whereCondition);
			endif;

			if($resultType == 'count'):
				$query[] = array('$count' => 'totalCount');
			else:
				if($sortBy):
					$query[] = array('$sort' => $sortBy);
				endif;
				if($itemsPerPage):
					$query[] = array('$skip' => (int)$startIndex);
					$query[] = array('$limit' => (int)$itemsPerPage);
				endif;
				if($lookup && $resultType == ""):
					foreach($lookup as $lookupItem):
						$query[] = array('$lookup' => $lookupItem);
					endforeach;
					if($unwind):
						foreach($unwind as $item):
							$query[] = array('$unwind' => $item);
						endforeach;
					endif;
				endif;
				if($SelectFields):
					$query[] = array('$project' => $SelectFields);
				endif;
			endif;
		else:
			if($sortBy):
				$query[] = array('$sort' => $sortBy);
			endif;

			if($lookup && $resultType == ""):
				foreach($lookup as $lookupItem):
					$query[] = array('$lookup' => $lookupItem);
				endforeach;
				if($unwind):
					foreach($unwind as $item):
						$query[] = array('$unwind' => $item);
					endforeach;
				endif;
			endif;


			if($whereCondition):
				$query[] = array('$match' => $whereCondition);
			endif;

			if($SelectFields):
				$query[] = array('$project' => $SelectFields);
			endif;

			if($resultType == 'count'):
				$query[] = array('$count' => 'totalCount');
			endif;

			if($itemsPerPage):
				$query[] = array('$skip' =>(int)$startIndex);
				$query[] = array('$limit' =>(int)$itemsPerPage);
			endif;
		endif;

		$result  = $this->mongo_db->aggregate($tblName,$query,array('batchSize'=>4));
		if($resultType == 'count'):
			$result = $result[0]['totalCount'];
		else:
			$result = $result;
		endif;

		return $result;
	}   // END OF FUNCTION

	/***********************************************************************
	** Function name : OrderStaticReport
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orice static report by condition..
	** Date 		 : 25 May 2024
	************************************************************************/
	public function getAggregateData2($tblName='',$SelectFields=array(),$whereCondition='',$groupBy='',$sortBy='',$lookup='',$unwind='',$resultType='',$startIndex='',$itemsPerPage='',$matchBeforeSortAndLookup=false)
	{  
		$query = array();

		if($groupBy):
			$query[] = array('$group' => $groupBy);
		endif;

		$whereAfterLookup = $whereCondition;
		if ($matchBeforeSortAndLookup && $whereCondition):
			$query[] = array('$match' => $whereCondition);
			$whereAfterLookup = null;
		endif;

		if($sortBy):
			$query[] = array('$sort' => $sortBy);
		endif;

		if($lookup && ($resultType == "multiple" || $resultType == "single" )):
			foreach($lookup as $lookupItem):
				$query[] = array('$lookup' => $lookupItem);
			endforeach;
			if($unwind):
				foreach($unwind as $item):
					$query[] = array('$unwind' => $item);
				endforeach;
			endif;
		endif;

		if($whereAfterLookup):
			$query[] = array('$match' => $whereAfterLookup);
		endif;

		if($SelectFields):
			$query[] = array('$project' => $SelectFields);
		endif;

		if($resultType == 'count'):
			$query[] = array('$count' => 'totalCount');
		endif;

		if($itemsPerPage):
			$query[] = array('$skip' =>(int)$startIndex);
			$query[] = array('$limit' =>(int)$itemsPerPage);
		endif; 

		$aggOpts = array('batchSize' => 128);
		
		$result  = $this->mongo_db->aggregate($tblName,$query,$aggOpts); 
		 
		if($resultType == 'count'):
			if (empty($result) || !isset($result[0]['totalCount'])) {
				$result = 0;
			} else {
				$result = (int) $result[0]['totalCount'];
			}
		else:
			$result = $result;
		endif;
		return $result;
	}   // END OF FUNCTION

	/***********************************************************************
	** Function name : generateLogs
	** Developed By  : Dilip Halder
	** Purpose       : This function used for generateLogs
	** Date          : 22 FEBRUARY 2025
	************************************************************************/
	public function generateLogs()
	{
		if($this->session->userdata('UW_ADMIN_ID')):     $requestedData['loggedIn_userID'] = $this->session->userdata('UW_ADMIN_ID');     endif;
		if($this->session->userdata('UW_ADMIN_EMAIL')):  $requestedData['loggedIn_Email']  = $this->session->userdata('UW_ADMIN_EMAIL');  endif;
		if($this->session->userdata('UW_ADMIN_MOBILE')): $requestedData['loggedIn_MOBILE'] = $this->session->userdata('UW_ADMIN_MOBILE'); endif;
		$requestedData['access_method']   = $_SERVER['REQUEST_METHOD'];
		$requestedData['requested_data']  = $_POST ? $_POST : $_GET;
		$requestedData['session_data'] 	  = $_SESSION;
		$requestedData['ip_address']      = $this->usersIp();
		$requestedData['timestamp']       = date('Y-m-d H:i:s');
		$requestedData['user_agent']      = $_SERVER['HTTP_USER_AGENT'];
		$requestedData['requested_url']   = $_SERVER['REQUEST_URI'];
		$requestedData['referer']         = $_SERVER['HTTP_REFERER'] ?? 'Direct Access'; // Handle empty referrer
		$this->generatelogs->generateLog(json_encode($requestedData)); 
	}

	/***********************************************************************
	** Function name : usersIp
	** Developed By  : Dilip Halder
	** Purpose       : This function used for usersIp
	** Date          : 24 FEBRUARY 2025
	************************************************************************/
	// public function usersIp()
	// {
	//   	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	// 	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	//     } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	//         // Check for IPs passed from a proxy
	//         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	//     } else {
	//         // Default remote address
	//         $ip = $_SERVER['REMOTE_ADDR'];
	//     }
	    
	//     $apiUrl    		= "http://ip-api.com/json/{$ip}";
    // 	$response  		= file_get_contents($apiUrl);
    // 	$locationData 	= json_decode($response, true);
    // 	return $locationData;
	// }
	public function usersIp()
	{
		$ip = $this->input->ip_address();

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$firstIp = trim($forwardedIps[0]);
			if (filter_var($firstIp, FILTER_VALIDATE_IP)) {
				$ip = $firstIp;
			}
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$clientIp = trim($_SERVER['HTTP_CLIENT_IP']);
			if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
				$ip = $clientIp;
			}
		}

		if (!filter_var($ip, FILTER_VALIDATE_IP)) {
			return array('query' => $ip, 'status' => 'fail', 'message' => 'Invalid IP');
		}

		$apiUrl = 'http://ip-api.com/json/' . $ip;
		$context = stream_context_create(array(
			'http' => array(
				'timeout' => 3,
				'ignore_errors' => true,
			),
		));
		$response = @file_get_contents($apiUrl, false, $context);
		if ($response === false) {
			return array('query' => $ip, 'status' => 'fail', 'message' => 'Geo lookup unavailable');
		}

		$locationData = json_decode($response, true);
		if (!is_array($locationData)) {
			return array('query' => $ip, 'status' => 'fail', 'message' => 'Invalid geo response');
		}

		return $locationData;
	}
	public function saveNotifications($user_id,$title,$message,$order_id=''){
		try{
				
				$param['notification_id']			=	(int)$this->common_model->getNextSequence('uw_notifications');
				$param['notific_title']				= $title;
				$param['notific_message']			= $message;
				$param['creation_ip']			=	currentIp();
				$param['creation_date']			=	(int)$this->timezone->current_time();//currentDateTime();
				$param['created_by']			=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$param['status']				=	'A';
				$alastInsertId					=	$this->common_model->addData('uw_notifications',$param);
				$detailParam['notification_details_id']	=	(int)$this->common_model->getNextSequence('uw_notifications_details');
				$detailParam['users_id']				=	(int)$user_id;
				$detailParam['notification_id']			=	$param['notification_id'];
				$detailParam['notific_title']			= 	$title;
				$detailParam['notific_message']			= 	$message;
				$detailParam['link']					= 	'';
				$detailParam['image']					= 	'';
				$detailParam['is_read']					= 	'N';

				$detailParam['creation_ip']				=	currentIp();
				$detailParam['creation_date']			=	(int)$this->timezone->current_time();//currentDateTime();
				$detailParam['created_by']				=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$detailParam['status']					=	'A';
				$detailParam['push_status']				=    0;
				if($order_id){
					$detailParam['order_id']			=    $order_id;
				}
				$this->common_model->addData('uw_notifications_details',$detailParam);
		}catch(Exception $e){
			
		}
	}


	/***********************************************************************
	** Function name : getRechargeTopupData
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getRechargeTopupData
	** Date          : 29 April 2025
	************************************************************************/
	public function getRechargeTopupData($resultType='',$whereCon='',$startIndex='',$itemsPerPage='',$tblName='')
	{

		$SelectFields = array(
        	 '_id'   			     => 1,
        	 'user_oid' =>1,
            'user_id_cred'           => 1,
            'user_id_deb'   		 => 1,
			'upoints'    			 => 1,
            'sum_arabian_points' 	 => 1,
            'availableArabianPoints' => 1,
            'record_type' 			 => 1,
            'narration'  			 => 1,
            'remarks'  				 => 1,
            'load_balance_id'  		 => 1,
            'created_at'  		 	 => 1,
            'status'  		 		 => 1,
            'created_by'  		     => 1,
            'created_user_id'  		 => 1,
            'users_name'       		 => array('$arrayElemAt' => array('$users.users_name', 0)),
	        'last_name'        		 => array('$arrayElemAt' => array('$users.last_name', 0)),
	        'user_email'       		 => array('$arrayElemAt' => array('$users.users_email', 0)),
	        'user_mobile'       	 => array('$arrayElemAt' => array('$users.users_mobile', 0)),
	        'store_name'       		 => array('$arrayElemAt' => array('$users.store_name', 0)),
	        'users_type'       		 => array('$arrayElemAt' => array('$users.users_type', 0)),
	        'bind_person_name'       => array('$arrayElemAt' => array('$users.bind_person_name', 0)),
	        'pos_number'             => array('$arrayElemAt' => array('$users.pos_number', 0)),
	        'users_mobile'           => array('$arrayElemAt' => array('$users.users_mobile', 0)),
            'seller_first_name'      => array('$arrayElemAt' => array('$AdminBindwith.admin_first_name', 0)),
            'seller_last_name'       => array('$arrayElemAt' => array('$AdminBindwith.admin_last_name', 0)),
            'seller_mobile'          => array('$arrayElemAt' => array('$AdminBindwith.admin_phone', 0)),
            'seller_type'            => array('$arrayElemAt' => array('$AdminBindwith.admin_type', 0)),

            'bindwith_first_name'   => array('$arrayElemAt' => array('$bindwith.users_name', 0)),
            'bindwith_last_name'    => array('$arrayElemAt' => array('$bindwith.last_name', 0)),
            'bindwith_mobile'       => array('$arrayElemAt' => array('$bindwith.users_mobile', 0)),
            'bindwith_users_type'   => array('$arrayElemAt' => array('$bindwith.users_type', 0)),
            'bindwith_pos_number'   => array('$arrayElemAt' => array('$bindwith.pos_number', 0)),
            // 'AdminBindwith'          => '$AdminBindwith',
            // 'bindwith'               => '$bindwith',

	    );

		$lookup  = array( 
			
			array(
	            'from' => 'uw_users',
	            'localField' => 'user_oid',
	            'foreignField' => '_id',
	            'pipeline' => array(
	                array(
	                    '$project' => array(
	                        '_id' => 1,
	                        'users_name' => 1,
	                        'last_name' => 1,
	                        'store_name' => 1,
	                        'pos_number' => 1,
	                        'users_mobile' => 1,
	                        'users_type' => 1,
	                        'bind_person_name' => 1,
	                    ),
	                ),
	            ),
	            'as' => 'users',
            ),

			array(
            'from'         => 'uw_users',
            'localField'   => 'created_user_id',
            'foreignField' => 'users_id',
            'pipeline'     => array(
	                // array(
	                //     '$match' => array(
	                //         'created_by' => array('$ne' => 'ADMIN'),
	                //     ),
	                // ),
	                array(
	                    '$project' => array(
	                        '_id' => 0,
	                        'users_name' => 1,
	                        'last_name' => 1,
	                        'users_mobile' => 1,
	                        'users_type' => 1,
	                        'pos_number' => 1,
	                        
	                    ),
	                ),
	            ),
	            'as' => 'bindwith',
	        ),

			array(
	            'from' => 'uw_admin',
	            'localField' => 'created_user_id',
	            'foreignField' => 'admin_id',
	            'pipeline' => array(
	                // array(
	                //     '$match' => array(
	                //         'created_by' => 'ADMIN',
	                //     ),
	                // ),
	                array(
	                    '$project' => array(
	                        '_id' => 0,
	                        'admin_first_name' => 1,
	                        'admin_last_name' => 1,
	                        'admin_phone' => 1,
	                        'admin_type' => 1,
	                    ),
	                ),
	            ),
	            'as' => 'AdminBindwith',
	        ),

		);

	    if (isset($whereCon['where'])):
	        $whereCondition = $whereCon['where'];
	    endif;

	    $groupBy          = array();
	    $unwind 			 = array();
	    $sortBy    			 = array('_id' => -1);
	    $rechargeTopupData   = $this->getAggregateData(
	        $tblName,
	        $SelectFields,
	        $whereCondition,
	        $groupBy,
	        $sortBy,
	        $lookup,
	        $unwind,
	        $resultType,
	        $startIndex,
	        $itemsPerPage
	    );

	    return $rechargeTopupData;
	    die();
	}

	/***********************************************************************
	** Function name : getReferrelDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getReferrelDetails
	** Date          : 12 June 2025
	************************************************************************/
	public function getReferrelDetails($resultType='', $whereCon='',$shortField='',$itemsPerPage='',$startIndex='')
	{
		try {

			$SelectFields = array(
		      'user_oid'      => 1,
		      'upoints'       => 1,
		      'narration'     => 1,
		      'remarks'       => 1,
		      'created_at'    => 1,
		      'user_id_cred'  => 1,
		      'user_id_deb'   => 1,
		      'referralUser'  => '$referralUser',
		      'referredUser'  => '$referredUser',
			);

			if($whereCon['where']):
				$whereCondition = $whereCon['where'];
			endif;

			$lookup = array(
			    array(
			        'from' => 'uw_users',
			        'localField' => "user_oid",
			        'foreignField' => "_id",
			        'pipeline' => array(
			            array(
		                    '$project' => array(
		                        '_id'          => 0,
		                        'users_name'   => 1,
		                        'last_name'    => 1,
		                        'users_type'   => 1,
		                        'users_mobile' => 1,
		                        'users_email'  => 1,
		                        'pos_number'   => 1,
		                        'bind_person_name'=> 1,
		                         
		                    )
		                ),
		            ),
			        'as' => 'referralUser'
			    ),
			    array(
			        'from' => 'uw_users',
			        'localField' => "request_oid",
			        'foreignField' => "_id",
			        'pipeline' => array(
			            array(
		                    '$project' => array(
		                        '_id'       => 0,
		                        'users_name'       => 1,
		                        'last_name'        => 1,
		                        'users_type'       => 1,
		                        'users_mobile'     => 1,
		                        'users_email'      => 1,
		                        'pos_number'       => 1,
		                        'bind_person_name' => 1,
		                         
		                    )
		                ),
		            ),
			        'as' => 'referredUser'
			    )
			);

			$unwind     = array('$referralUser','$referredUser');
			$tblName    = "uw_loadBalance";
	   	 	$raffleData = $this->getAggregateData($tblName ,$SelectFields ,$whereCondition ,$groupBy ,$shortField ,$lookup ,$unwind ,$resultType,$startIndex,$itemsPerPage);
		    return $raffleData;
		    die();
			
		} catch (Exception $e) {
			echo 'error';
		}
	}
	
	/***********************************************************************
	** Function name : getLoadBalanceUserDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getLoadBalanceUserDetails
	** Date          : 29 October 2025
	************************************************************************/
	// public function fetchresultData($resultType='', $whereCon='',$shortField='',$itemsPerPage='',$startIndex='')
	// {
	// 	$tblName = 'uw_draw_result';
	// 	// Get the total count of documents grouped by result_date
	// 	$groupByCount = array(
	// 	   '_id'           => '$result_date',
	// 	   'count'         => array('$sum' => 1),
	// 	   'creation_first_date' => array('$first' => '$creation_date'),
	// 	   'last_updated_date'   => array('$last'  => '$creation_date'),
	// 	);
		
	// 	// Handle where condition
	// 	$whereConditionCount = array();
	// 	if (!empty($whereCon) && isset($whereCon['where'])):
	// 		$whereConditionCount = $whereCon['where'];
	// 	endif;

	// 	// Handle sort field - use $shortField if provided, otherwise default
	// 	$sortByCount = array('_id' => -1);
	// 	if (!empty($shortField)):
	// 		$sortByCount = $shortField;
	// 	endif;

	// 	// Handle resultType - use provided value or default to 'array'
	// 	$finalResultType = (!empty($resultType)) ? $resultType : 'array';

	// 	$totalResultDateCount = $this->getAggregateData(
	// 		$tblName,
	// 		array(),
	// 		$whereConditionCount,
	// 		$groupByCount,
	// 		$sortByCount,
	// 		[],
	// 		[],
	// 		$finalResultType,
	// 		$startIndex,
	// 		$itemsPerPage
	// 	);

	// 	// Optionally return with or assign to $data or process as needed. 
	// 	// For example: $data['result_date_counts'] = $totalResultDateCount;

	 
	// 	return $totalResultDateCount;
	// }
	/***********************************************************************
	** Function name : getLoadBalanceUserDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getLoadBalanceUserDetails
	** Date          : 29 October 2025
	************************************************************************/
	public function fetchresultData($resultType='',$tblName='', $whereCon='',$shortField='',$itemsPerPage='',$startIndex='')
	{
		// Get the total count of documents grouped by result_date
		$groupByCount = array(
		   '_id'           => '$result_date',
		   'count'         => array('$sum' => 1),
		   'creation_first_date' => array('$first' => '$creation_date'),
		   'last_updated_date'   => array('$last'  => '$creation_date'),
		);
		
		// Handle where condition
		$whereConditionCount = array();
		if (!empty($whereCon) && isset($whereCon['where'])):
			$whereConditionCount = $whereCon['where'];
		endif;

		// Handle sort field - use $shortField if provided, otherwise default
		$sortByCount = array('_id' => -1);
		if (!empty($shortField)):
			$sortByCount = $shortField;
		endif;

		// Handle resultType - use provided value or default to 'array'
		$finalResultType = (!empty($resultType)) ? $resultType : 'array';

		$totalResultDateCount = $this->getAggregateData(
			$tblName,
			array(),
			$whereConditionCount,
			$groupByCount,
			$sortByCount,
			[],
			[],
			$finalResultType,
			$startIndex,
			$itemsPerPage
		);

		// Optionally return with or assign to $data or process as needed. 
		// For example: $data['result_date_counts'] = $totalResultDateCount;

	 
		return $totalResultDateCount;
	}
	
	/***********************************************************************
	** Function name : getHourlyGameOrderData
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getHourlyGameOrderData
	** Date          : 23 April 2026
	************************************************************************/
	public function getHourlyGameOrderData($resultType='', $tblName="", $whereCon='', $shortField='', $itemsPerPage='', $startIndex='', $matchBeforeLookup=false)
	{
	    try {

	        $SelectFields = array(
	            '_id'            => 1,
	            'order_id'       => 1,
	            'users_id'       => 1,
	            'users_oid'      => 1,
	            'products_oid'   => 1,
	            'products_name'  => 1,
	            'start_date'     => 1,
	            'expiry_date'    => 1,
	            'qty'            => 1,
	            'total_price'    => 1,
	            'status'         => 1,
	            'created_at'     => 1,
	            "batch_id"       => 1,
	            "coupon_code"    => 1,
	            "csv_name"       => 1,
	            "is_winner"      => 1,
	            "matching_coupons" => 1,
	            "winner_type"    => 1,
	            "winning_amount" => 1,
				"winning_status" => 1,
				"winner_uploaded_at" => 1,
				"redeemed_at"    => 1,
				"draw_time"      => 1,

	            // USER DATA
				'seller_users_name' 	 => '$users.users_name',
				'seller_users_last_name' => '$users.last_name',
				'seller_full_name' => array('$concat' => array('$users.users_name', ' ', '$users.last_name')),
	            'seller_users_country_code' => '$users.country_code',
	            'seller_users_mobile' => '$users.users_mobile',
	            'seller_users_email'  => '$users.users_email',
	            'seller_users_type'   => '$users.users_type',
	            'seller_store_name'   => '$users.store_name',
				// Keep both keys for backward compatibility across old/new views.
				'seller_pos_number'             => '$users.pos_number',
				'seller_pos_device_id'          => '$users.pos_device_id',
				'seller_users_pos_number'       => '$users.pos_number',
				'seller_users_bind_person_name' => '$users.bind_person_name',
				'users_pos_number'              => '$users.pos_number',
				'bind_person_name'              => '$users.bind_person_name',
				'area'              => '$users.area',

				// SETTLER USER DATA
				'settler_users_name' 	 => '$settler_users.users_name',
				'settler_users_last_name'=> '$settler_users.last_name',
				'settler_full_name'    => array('$concat' => array('$settler_users.users_name', ' ', '$settler_users.last_name')),
				'settler_country_code' => '$settler_users.country_code',
	            'settler_users_mobile' => '$settler_users.users_mobile',
	            'settler_users_email'  => '$settler_users.users_email',
	            'settler_users_type'   => '$settler_users.users_type',
	            'settler_store_name'   => '$settler_users.store_name',
				'settler_pos_number'             => '$settler_users.pos_number',
				'settler_pos_device_id'          => '$settler_users.pos_device_id',
				'settler_users_pos_number'       => '$settler_users.pos_number',
				'settler_users_bind_person_name' => '$settler_users.bind_person_name',

				// BUYER USER DATA
				'buyer_country_code' => 1,
				'buyer_mobile'       => 1,
				'buyer_email'        => 1,
				'sms_type'           => 1,

	            // PRODUCT DATA (NEW)
	            'product_name'   => '$product.title',
	            'product_price'  => '$product.price',
	            'product_status' => '$product.status',
				'tickets'        => '$tickets',
				'updatedAt'      => '$updatedAt',
				'update_date'    => '$update_date'
	        );

	        $whereCondition = array();
	        if (!empty($whereCon['where'])):
	            $whereCondition = $whereCon['where'];
	        endif;
			

	        $lookup = array(
				array(
					'from' => 'uw_users',
					'localField' => "users_oid",
					'foreignField' => "_id",
					'as' => 'users'
				),

				array(
					'from' => 'uw_users',
					'localField' => "settler_users_oid",
					'foreignField' => "_id",
					'as' => 'settler_users'
				),
					
				array(
					'from' => 'uw_hourly_games',
					'localField' => "products_oid",
					'foreignField' => "_id",
					'as' => 'product'
				),

				array(
					'from' => 'uw_hourly_tickets',
					'localField' => "_id",
					'foreignField' => "order_oid",
					'pipeline' => array(
						array(
							'$project' => array(
								'_id' => 0,
								'ticket' => 1,
								'type' => 1,
								'points' => 1,
							)
						)
					),
					'as' => 'tickets'
				),
			);

	        // UNWIND BOTH
	        $unwind = array(
	            array(
	                'path' => '$users',
	                'preserveNullAndEmptyArrays' => true
	            ),
	            array(
	                'path' => '$product',
	                'preserveNullAndEmptyArrays' => true
	            ),
				array(
					'path' => '$settler_users',
					'preserveNullAndEmptyArrays' => true
				),
	        );

	        $tblName = "uw_hourly_orders";
	        $groupBy = '';

	        $hourlyGameData = $this->getAggregateData2( $tblName, $SelectFields, $whereCondition, $groupBy, $shortField, $lookup, $unwind, $resultType, $startIndex, $itemsPerPage, $matchBeforeLookup );
	        return $hourlyGameData;

	    } catch (Exception $e) {
	        echo 'error';
	    }
	}
	/***********************************************************************
	** Function name : getHourlyGameGroupByData
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getHourlyGameGroupByData
	** Date          : 23 April 2026
	************************************************************************/
	public function getHourlyGameGroupByData($whereCon = '', $resultType = '', $page = '', $skip = '')
	{
	    $SelectFields = array(
	       	"batch_id"        => 1,
	        "products_name"   => 1,
	        "draw_date"       => 1,
	        "winning_amount"  => 1,
	        "is_winner"       => 1,
	        "status"          => 1,
			"total_count"	  => 1,
			"redeemed_count"  => 1,
			"active"          => 1,
        	"inactive"        => 1,
        	"deleted"         => 1,
        	"paid"            => 1,
			"unpaid"          => 1,
			"csv_name"        => 1,
			"winner_uploaded_at"=> 1
	    );

	    $whereCondition = [];
	    if (!empty($whereCon) && isset($whereCon['where'])) {
	        $whereCondition = $whereCon['where'];
	    }

		$whereCondition['is_winner'] = "Y";
		
		$groupBy = array(
	        '_id'             => '$batch_id',
	        'batch_id'        => array('$first' => '$batch_id'),
			'csv_name' => array('$first' => '$csv_name'),
			'winner_uploaded_at' => array('$first' => '$winner_uploaded_at'),
			'is_winner' => array('$first' => '$is_winner'),
	        'winning_amount' => array(
	            '$sum' => array('$ifNull' => ['$winning_amount', 0])
	        ),
	        'total_count' => array('$sum' => 1),
	        'active' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'unpaid')),
	                    'then' => 1,
	                    'else' => 0
	                )
	            )
	        ),
	        'inactive' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'Inactive')),
	                    'then' => 1,
	                    'else' => 0
	                )
	            )
	        ),
			'deleted' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'Deleted')),
	                    'then' => 1,
	                    'else' => 0
	                )
	            )
	        ),
			'redeemed_count' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'paid')),
	                    'then' => 1,
	                    'else' => 0
	                )
	            )
	        ),
	        'paid' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'paid')),
	                    'then' => array('$ifNull' => ['$winning_amount', 0]),
	                    'else' => 0
	                )
	            )
	        ),
			'unpaid' => array(
	            '$sum' => array(
	                '$cond' => array(
	                    'if' => array('$eq' => array('$winning_status', 'unpaid')),
	                    'then' => array('$ifNull' => ['$winning_amount', 0]),
	                    'else' => 0
	                )
	            )
	        )
	    );

	    // Sort latest batch first
	    $sortBy  = array('_id' => -1);
	    $tblName = "uw_hourly_orders";

	    $query = array();
	    if (!empty($whereCondition)):
	        $query[] = array('$match' => $whereCondition);
	    endif;
	    $query[] = array('$group' => $groupBy);
	    $query[] = array('$sort' => $sortBy);
	    $query[] = array('$project' => $SelectFields);

	    if ($resultType == 'count'):
	        $query[] = array('$count' => 'totalCount');
	    endif;

	    if ($skip):
	        $query[] = array('$skip' => (int)$page);
	        $query[] = array('$limit' => (int)$skip);
	    endif;

	    $aggOpts = array('batchSize' => 128);
	    $result = $this->mongo_db->aggregate($tblName, $query, $aggOpts);

	    if ($resultType == 'count'):
	        if (empty($result) || !isset($result[0]['totalCount'])):
	            return 0;
	        endif;
	        return (int)$result[0]['totalCount'];
	    endif;

	    return $result;
	}

}	
