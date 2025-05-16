<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Common_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}

	function milliseconds() {
	    $mt = explode(' ', microtime());
	    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
	}

	function microseconds() {
	    $mt = explode(' ', microtime());
	    return ((int)$mt[1]) * 1000000 + ((int)round($mt[0] * 1000000));
	}

	/***********************************************************************
	** Function Name returnIntegerEncryptValue
	** Developed By : Manoj Kumar
	** Input Parameters 
	** 1. inputInteger = The integer value which need to encrypted
	** 2. returnLength = THe number of digit which need to be return from functon.
	** Function Process :- The function will take integr input and multiply it with current unixtimestamp.
	** The new value will be encrypt using md5 which return 32 bit string, The encrypt string convert to ASCII
	** value and then the desire lenght sub string will be return by function.
	** Date : 14 JUNE 2021
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
	** Developed By : Manoj Kumar
	** Purpose  : This function used for get Next Sequence
	** Date : 14 JUNE 2021
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
	** Function name : generateSerialNo
	** Developed By  : Manoj Kumar
	** Purpose       : This function used for get Next Sequence
	** Date          : 15 November 2024
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
	** Function name : getNextIdSequence
	** Developed By : Manoj Kumar
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
		
		if($type=='doctor'):  
		$constant 		=	array('doctor_seq_id'=>'DOC-');
		endif;

		if($type=='hospital'):  
		$constant 		=	array('hospital_seq_id'=>'HID-');
		endif;

		if($type=='product'):  
		$constant 		=	array('product_seq_id'=>'PID-');
		endif;

		if($type=='health'):  
		$constant 		=	array('health_seq_id'=>'HID-');
		endif;
		
		$cueNewId 	 	= 	$newId<10?'0000'.$newId:($newId<100?'000'.$newId:($newId<1000?'00'.$newId:($newId<10000?'0'.$newId:$newId)));
		return $constant[$sequenceType].$cueNewId;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name : getNextInspectorIdSequence
	** Developed By : Manoj Kumar
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
	** Developed By : Manoj Kumar
	** Purpose  : This function used for add data
	** Date : 14 JUNE 2021
	************************************************************************/
	public function addData($tableName='',$param=array())
	{
		$last_insert_id 		=	$this->mongo_db->insert($tableName,$param);
		return $last_insert_id;
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : editData
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for edit data
	 * * Date : 14 JUNE 2021
	 * * **********************************************************************/
	function editData($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$result =	$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->set($param);
		$this->mongo_db->update($tableName);
		
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : editDataByMultipleCondition
	** Developed By : Manoj Kumar
	** Purpose  : This function used for edit data by multiple condition
	** Date : 14 JUNE 2021
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
	** Developed By  : Manoj Kumar
	** Purpose  	 : This function used for edit data by multiple condition
	** Date 		 : 14 JUNE 2021
	** Updated By    : Dilip Halder
	** Updated Date  : 16 October 2024
	************************************************************************/
	function editMultipleDataByMultipleCondition($tableName='',$param=array(),$whereCondition=array())
	{
		$this->mongo_db->where($whereCondition);
		$this->mongo_db->set($param);
		$result = $this->mongo_db->update_all($tableName);
		return $result->getModifiedCount();
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : editMultipleDataByMultipleCondition
	** Developed By : Manoj Kumar
	** Purpose  : This function used for edit data by multiple condition
	** Date : 14 JUNE 2021
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
	** Developed By : Manoj Kumar
	** Purpose  : This function used for delete data
	** Date : 14 JUNE 2021
	************************************************************************/
	function deleteData($tableName='',$fieldName='',$fieldValue='')
	{
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : deleteParticularData
	** Developed By : Manoj Kumar
	** Purpose  : This function used for delete particular data
	** Date : 14 JUNE 2021
	************************************************************************/
	function deleteParticularData($tableName='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name : deleteByMultipleCondition
	** Developed By : Manoj Kumar
	** Purpose  : This function used for delete by multiple condition
	** Date : 14 JUNE 2021
	************************************************************************/
	function deleteByMultipleCondition($tableName='',$whereCondition=array())
	{
		$this->mongo_db->where($whereCondition);
		$this->mongo_db->delete_all($tableName);
		return true;
	}	// END OF FUNCTION
	
	/***********************************************************************
	** Function name: getDataByParticularField
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by encryptId
	** Date : 14 JUNE 2021
	************************************************************************/
	public function getDataByParticularField($tableName='',$fieldName='',$fieldValue='')
	{  
		$this->mongo_db->select('*');
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$result = $this->mongo_db->find_one($tableName);
		if($result):
			return $result;
		else:
			return false;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getSingleDataByParticularField
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Single Data By Particular Field
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by query
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by condition
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Last Order By Fields
	** Date : 14 JUNE 2021
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
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for set Attribute In Use
	 * * Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Paticular Field By Fields
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Particular Field By Multiple Condition
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by query
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by query
	** Date : 14 JUNE 2021
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
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Data By Group By
	** Date : 14 JUNE 2021
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
	** Date : 14 JUNE 2021
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
	** Date : 14 JUNE 2021
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
	** Date : 14 JUNE 2021
	************************************************************************/
	function delete_image_by_image_name($tableName='',$fieldName='',$fieldValue='')
	{	//echo $tableName.'---'.$fieldName.'---'.$fieldValue; die;
		$this->mongo_db->where(array($fieldName => $fieldValue));
		$this->mongo_db->delete($tableName);
		return true;
	}

	/***********************************************************************
	** Function name: getTitleSlug
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Title Slug
	** Date : 14 JUNE 2021
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
	** Function name: getInventoryList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Property Data
	** Date : 16 NOV 2022
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
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION
	/***********************************************************************
	** Function name: getOrderList
	** Developed By: Afsar Ali
	** Purpose: This function used for get order Data
	** Date : 16 NOV 2022
	************************************************************************/
	public function getOrderList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
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
								'sequence_id'=>1,
								'order_id'=>1,
								'user_id'=>1,
								'user_type'=>1,
								'user_email'=>1,
								'user_phone'=>1,
								'product_is_donate'=>1,
								//'shipping_method'=>1,
								//'emirate_id'=>1,
								//'emirate_name'=>1,
								'collection_point_id'=>1,
								'collection_point_name'=>1,
								//'shipping_address'=>1,
								//'shipping_charge'=>1,
								//'inclusice_of_vat'=>1,
								'subtotal'=>1,
								'vat_amount'=>1,
								'total_price'=>1,
								'payment_mode'=>1,
								'payment_from'=>1,
								'order_status'=>1,
								'creation_ip'=>1,
								'created_at'=>1,
								'collection_status'=>1,
								'expairy_data'=>1,

								'product_id'=>'$from_order_details.product_id',
								'product_name'=>'$from_orders_details.product_name',
								'quantity'=>'$from_orders_details.quantity',

								// 'collection_point_name'=>'$from_collection_point.collection_point_name',
								// 'users_email'=>'$from_collection_point.users_email',
								// 'users_mobile'=>'$from_collection_point.users_mobile',

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_orders','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_orders')),
												  array('$lookup'=>array('from'=>'uw_orders_details','localField'=>'order_details_id','foreignField'=>'sequence_id','as'=>'from_orders_details')),

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
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	
	/***********************************************************************
	** Function name: getAvailableTickets
	** Developed By: Dilip Halder
	** Purpose: This function used for get Available Tickets
	** Date : 10 August 2023
	************************************************************************/

	public function getAvailableTickets($CA='')
	{		
		 	//Get current Ticket order sequence from admin panel.
			$tblName = 'uw_tickets_sequence';
			$whereCon2['where']		 			= 	array('product_id' => (int)$CA['product_id'] , 'status' => 'A');	
			$shortField 						= 	array('tickets_seq_id'=>'ASC');
			$TicketSequence 				= 	$this->common_model->getData('multiple',$tblName,$whereCon2,$shortField,'0','0');

			$tblName = 'uw_products';
			$whereCon2['where']		 			= 	array('products_id' => (int)$CA['product_id'] , 'status' => 'A');	
			$productDetails 					= 	$this->common_model->getData('single',$tblName,$whereCon2,$shortField,'0','0');

			if($productDetails):
				// getting number of tickets genereating wirth this order.
				if($CA['is_donated'] == 'N'):
					$tickets = $CA['quantity']*$productDetails['sponsored_coupon'];
				else:
					$tickets = 2*$CA['quantity']*$productDetails['sponsored_coupon'];
				endif;
			else:

			 	if($CA['payment_from'] == 'App'):
			 		echo outPut(0,lang('SUCCESS_CODE'),lang('CAMPAIGN_CLOSED'). " for " .$CA['name'],$result);die();
			 	else:
			 		$this->session->set_flashdata('error', lang('CAMPAIGN_CLOSED'));
        			redirect('user-cart');
			 	endif;

			endif;

			foreach ($TicketSequence as $key => $CurrentTicketSequence):
				//Getting Tickets generating sequence from each available slots ..
				$ticketID[] 				= $CurrentTicketSequence['tickets_seq_id'];
				$series[] 					= $CurrentTicketSequence['tickets_prefix'];
				$tickets_sequence_start[] 	= $CurrentTicketSequence['tickets_sequence_start'];
				$tickets_sequence_end[] 	= $CurrentTicketSequence['tickets_sequence_end'];
				$tickets_sold_count[] 		= $CurrentTicketSequence['tickets_sold_count'];
				$total_tickets[] 			= $CurrentTicketSequence['tickets_sequence_end'] - $CurrentTicketSequence['tickets_sequence_start'] ;
			endforeach;

			// available slots count.
			for($i=0; $i < $tickets ; $i++):
				if(empty($NextSolt)):
					$soltNo    = 0;
				endif;

				A: //ticket slot changing.

				if($NextSolt && empty($tickets_sequence_start[$soltNo])):

					if(count($coupons) >= $tickets):
						$coupons = array_slice($coupons, 0,$tickets); 
						return $coupons;
					endif;

					if($CA['payment_from'] == 'App'):
			 			echo outPut(0,lang('SUCCESS_CODE'),lang('TICKET_NOT_AVAILABLE'). " for " .$CA['name'],$result);die();
				 	else:
				 		$this->session->set_flashdata('error', lang('TICKET_NOT_AVAILABLE'). " for " .$CA['name']);
	        			redirect('user-cart');
				 	endif;

				endif;

				// Coupon sequence start..
				$sequence = $tickets_sequence_start[$soltNo]+$i;
				$singleCoupon = $series[$soltNo].$sequence;

				// Online coupon checking...
				$whereCondition['where'] = array('coupon_code' => $singleCoupon , 'product_id' => (int)$CA['product_id']);
				$CouponCount			 = $this->geneal_model->getData2('count','uw_coupons',$whereCondition);

				// Quick coupon checking...
				$whereCondition1['where']        = array( 'coupon_code' => array('$in' => [$singleCoupon]) ,'product_id'=> (int)$CA['product_id']   );
				$QuickSoldoutTickets			 = $this->geneal_model->getData2('count','uw_ticket_coupons',$whereCondition1);

				if($CouponCount == 1 || $QuickSoldoutTickets == 1):

					 $tickets_sequence_start[$soltNo]++;
					 // echo 'Soldout Tickets = '.$singleCoupon .'<br>';
					 goto A;
				elseif($tickets_sequence_end[$soltNo] == $sequence):
			 		 
			 		 $coupons[] 			= $singleCoupon;
					 $tickets_sequence_start++;
				  	 $NextSolt ='next slot';
					 $i =0;
					 $soltNo++;
					 goto A;

				endif;
			 		
			 		$coupons[] 			= $singleCoupon;
			endfor;

			$coupons = array_slice($coupons, 0, $tickets);

			return $coupons;
			
	}

	/***********************************************************************
	** Function name: getOrderList
	** Developed By: Dilip Halder
	** Purpose: This function used for get order Data
	** Date : 14 July 2023
	************************************************************************/
	public function addCoupons($couponData='')
	{
		
		$ticket_count = count($couponData['tickets']);
 		
		//Get current Ticket order sequence from admin panel.
		$tblName = 'uw_tickets_sequence';
		$whereCon2['where']		 			= 	array('product_id' => (int)$couponData['product_id'] , 'status' => 'A');	
		$shortField 						= 	array('tickets_seq_id'=>'ASC');
		$TicketSequence 				= 	$this->common_model->getData('multiple',$tblName,$whereCon2,$shortField,'0','0');
		
		// echo "<pre>";print_r($couponData);die();

		if($TicketSequence):

			foreach($TicketSequence  as $key => $CurrentTicketSequence ):
				
				$range = range($CurrentTicketSequence['tickets_sequence_start'], $CurrentTicketSequence['tickets_sequence_end']); 
				foreach($range as $number):
				    
				    $ticketNumber = $CurrentTicketSequence['tickets_prefix'] .$number;

				    //Adding soldout number for current ticket sequence.
					if(in_array($ticketNumber, $couponData['tickets'])):

						$couponArray[] 		= $ticketNumber;
						$Soldoutcouponcount =  count($couponArray);
						
						$couponTicketSequence["tickets_sold_count"] 	 =	(int)$CurrentTicketSequence['tickets_sold_count']+ $Soldoutcouponcount;
						$this->geneal_model->editData('uw_tickets_sequence',$couponTicketSequence,'tickets_seq_id',(int)$CurrentTicketSequence['tickets_seq_id']);
					endif;
				endforeach;

				// removing soldout number to asssign soldout quantity for next slot. 
				unset($couponArray);
				
				// Addeding status inactive for complete soldout solts.
				$ticket  	= $CurrentTicketSequence['tickets_prefix'].$CurrentTicketSequence['tickets_sequence_end'];
				$ticketID   =  $CurrentTicketSequence['tickets_seq_id'];

				$slotEndingTickect =  in_array( $ticket , $couponData['tickets']);
				if($slotEndingTickect):
					$updateTickectSequence['status']		=	 "I";
					$whereTickectSequence =	array('tickets_seq_id' => (int)$ticketID);
					$this->common_model->editDataByMultipleCondition('uw_tickets_sequence',$updateTickectSequence,$whereTickectSequence);
				endif;
				//end

			endforeach;

			$lowerBound = (int)$CurrentTicketSequence['tickets_sequence_start'];
			$upperBound = (int)$CurrentTicketSequence['tickets_sequence_end'];
			$percentage = 90;

			/* Finds the number near to 90% from start to end. */
		  	$rangeStart 		= (int)$CurrentTicketSequence['tickets_sequence_start'];
		  	$rangeEnd 			= (int)$CurrentTicketSequence['tickets_sequence_end'];
		  	$percentage 		= 0.90;
		  	$result 			= ($rangeEnd - $rangeStart) * $percentage;
		  
		    $tickectnumber 		= $CurrentTicketSequence['tickets_prefix'].round($result);
	    	
		    // echo $tickectnumber;die();

		    $SoldoutSMStickect 	=  in_array($tickectnumber, $couponData['tickets']);

		    if($SoldoutSMStickect):



			    for ($i=1; $i<=2 ; $i++):
					// send sms form here.
					if($i == 1):
						$country_code='+971';
						$mobileNumber='+971501292591';
						$this->sendTickectFullSMS($mobileNumber,$country_code ,$couponData['product_name']);
					endif;

					if($i == 2):
						$country_code='+971';
						$mobileNumber='+97154332166';
						$this->sendTickectFullSMS($mobileNumber,$country_code ,$couponData['product_name']);
					endif;

					$country_code='+91';
					$mobileNumber='+918700144841';
					$this->sendTickectFullSMS($mobileNumber,$country_code ,$couponData['product_name']);
					die();
			    endfor;
		    endif;

		    for ($i=0; $i < $ticket_count; $i++):

				//Coupon storing work.
				$coupon_code = $couponData['tickets'][$i];

				$couponParam['coupon_id']		= 	(int)$this->geneal_model->getNextSequence('uw_coupons');
				$couponParam['order_id']		= 	$couponData['order_id'];;
				$couponParam['users_id']		= 	(int)$couponData['userID'];
				$couponParam['users_email']		= 	$couponData['emailID'];
				$couponParam['product_id']		= 	$couponData['product_id'];
				$couponParam['product_name']	= 	$couponData['product_name'];
				$couponParam['is_donated'] 		=	$couponData['is_donated'];
				$couponParam['coupon_status'] 	=	'Live';
				$couponParam['coupon_code'] 	= 	$coupon_code;
				$couponParam['coupon_type'] 	= 	$couponData['is_donated'] == 'Y' ? "Donated": 'Simple';
				$couponParam['created_at']		=	date('Y-m-d H:i');

				$this->geneal_model->addData('uw_coupons',$couponParam);
			endfor;

		endif;

	}


	/***********************************************************************
	** Function name 	: sendTickectFullSMS
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send send tickect sms to admin.'
	** Date 			: 17-07-2023
	************************************************************************/
	function sendTickectFullSMS($mobileNumber='',$country_code='',$product_title='') {  
		
		$enableSMS = $this->common_model->getData('single','uw_enablesms');

		// Finding country code and sending sms using sms country.
        if($enableSMS['smscountry'] == "enable"):

        	$SMSCOUNTRY = explode(',', $enableSMS['sms_country_available_country']);

			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY as $key => $item):
				$SMSCOUNTRY[$key] = trim($item);
			endforeach;

            // checing country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY) ):
				if($mobileNumber):
					$message		=	$product_title . " Campaign 90% Soldout. Please add more tickets";
					$senderid		=	"AD-DLZARBA";
					$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;

        // Finding country code and sending sms using digitizebird.
        if($enableSMS['digitizebird'] == "enable"):

        	$SMSCOUNTRY1 = explode(',', $enableSMS['digitizebird_available_country']);
			
			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY1 as $key => $item1):
				$SMSCOUNTRY1[$key] = trim($item1);
			endforeach;

            // checing country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY1) ):
				if($mobileNumber):
					$message		=	"Your DealzArabia password has been reset successfully";
					$senderid		=	"DLZRBIA";
					$returnMessage	=	$this->sendMessageDigitizebirdFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;
	} //END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : sendMessageFunction 
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function use for send Message Function
	 * * Date : 08 APRIL 2021
	 * * **********************************************************************/
	public function sendMessageFunction($phone='',$message='',$sender_id='') {
		try {
			if(!empty($phone) && !empty($message) && !empty($sender_id)):
				//Please Enter Your Details
				$user='pltech'; //your username
				$password=SMSCOUNTRYPASSWORD; //your password
				$mobilenumbers=substr($phone,1); //enter Mobile numbers comma seperated
				$message = $message; //enter Your Message
				$senderid=$sender_id; //Your senderid
				
				$messagetype="N"; //Type Of Your Message
				$DReports="Y"; //Delivery Reports
				$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
				$message = urlencode($message);
				$ch = curl_init();
				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt ($ch, CURLOPT_POSTFIELDS,
				"User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
				$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
				// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
				$curlresponse = curl_exec($ch); // execute
				if(curl_errno($ch))
				return "ERROR";
				//echo 'curl error : '. curl_error($ch);
				if (empty($ret)) {
					return "some kind of an error happened";
				// some kind of an error happened
				//die(curl_error($ch));
				curl_close($ch); // close cURL handler
				} else {
				$info = curl_getinfo($ch);
				curl_close($ch); // close cURL handler
				return $curlresponse; //echo "Message Sent Succesfully" ;
				}
			endif;
		} catch (\Throwable $th) {
			return "FAIL";
		}
		
	}

	public function sendMessageDigitizebirdFunction($phone='',$message='',$senderid='')
	{
		try {
			if(!empty($phone) && !empty($message) && !empty($senderid)):
				
				$ApiKey 		= 'ybG+HgfvR2YzK/LOlwwBXU7YRhKu+LK5Vi6Mfg5N5AI=';
				$ClientId 		= 'a2d20910-34bc-4e0d-a3bf-904667459a01';
				$CompanyId 		= '7';
				$message = urlencode($message);
				$url = "https://user.digitizebirdsms.com/api/v2/SendSMS?SenderId=$senderid&Is_Unicode=false&Is_Flash=true&Message=$message&MobileNumbers=$phone&ApiKey=$ApiKey&ClientId=$ClientId&CompanyId=$CompanyId";
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array( 'accept: text/plain' ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				return $response; 
			endif;
		} catch (\Throwable $th) {
			return "FAIL";
		}
	}

	/***********************************************************************
	** Function name : addorder
	** Developed By : Dilip Halder
	** Purpose  : This function used Addd new Order details..
	** Date : 28 July 2023
	************************************************************************/
	public function addorder($POST='')
	{	

			$UserID 			 	=   $POST['users_id'];
			$UserTYPE 			 	=	$POST['user_type'];
			$UserEMAIL 			 	=	$POST['user_email'];
			$UserPHONE 			 	=	$POST['user_phone'];
			$product_is_donate 		=	$POST['product_is_donate'];
			$shipping_method 		=	$POST['shipping_method'];
			$emirate_id 			=	$POST['emirate_id'];
			$emirate_name 			=	$POST['emirate_name'];
			$area_id 				=	$POST['area_id'];
			$area_name 				=	$POST['area_name'];
			$collection_point_id 	=	$POST['collection_point_id'];
			$collection_point_name 	=	$POST['collection_point_name'];
			$shipping_charge 		=	$POST['shipping_charge'];
			$inclusice_of_vat 		=	$POST['inclusice_of_vat'];
			$subtotal 				=	$POST['subtotal'];
			$vat_amount 			=	$POST['vat_amount'];
			$total_price 			=	$POST['inclusice_of_vat'];
			$payment_method 		=	$POST['payment_mode'];
			$device_type 			=	$POST['device_type'];
			$app_version 			=	$POST['app_version'];
			
			//Checking user detail in user collection.
			$user_wcon['where']		=	array('users_id' => (int)$UserID );
			$userData 				=	$this->geneal_model->getData2('single', 'uw_users', $user_wcon);

			
			if($payment_method == 'Arabian Points'):
				if($inclusice_of_vat > $userData['availableArabianPoints']):
					 echo outPut(0,lang('SUCCESS_CODE'),lang('LOW_BALANCE'),$result); die();
				endif;
			endif;


			if($userData):
				if($userData['status'] == 'A'):
							$wcon['where']		=	array('user_id' => (int)$UserID );
							$cartItems          =	$this->geneal_model->getData2('multiple', 'uw_cartItems', $wcon);
							
							if($cartItems == 0):
			                    echo outPut(0,lang('SUCCESS_CODE'),lang('CART_EMPTY'),$result);die();
			                endif;

							foreach($cartItems as $CA):
								$finalPrice += $CA['qty'] * $CA['price'];
								//check Stock
								$stockCheck = $this->geneal_model->getStock($CA['id'], $CA['qty']);
								if($CA['is_donated'] == 'N'):
									$data['product_is_in_donate'] 		 =  'N';
								endif;

								// Ticket generation work......
								$ticketCheck['product_id']  = $CA['id'];
								$ticketCheck['name']  		= $CA['name'];
								$ticketCheck['quantity'] 	= $CA['qty'];
								$ticketCheck['is_donated'] 	= $CA['is_donated'];
								$ticketCheck['payment_from']= 'App';

								// Checking soldout tickets and available tickets.
								$tickets 	= $this->common_model->getAvailableTickets($ticketCheck);

							endforeach;
								
							$data['finalprice'] = $finalPrice;

							if($data['product_is_in_donate'] == 'N'):
								$data['shipping']   = 0;//SHIPPING_CHARGE;
							else:
								$data['shipping']   = 0;
							endif;
							 

							$productCount   =	$this->geneal_model->getData2('count', 'uw_cartItems', $wcon);
					
							if($this->input->post('product_is_donate') == 'N'):
								$collection_points  				=	explode('_____',$this->input->post('collection_points'));
							endif;

						    $tblName1 					=	'uw_emirate_collection_point';
							$where1['where'] 			=	array( 'status' => 'A','collection_point_id' => (int)$collection_points[2]);
							$order1 					=	['collection_point_id' => 'ASC'];
							$data1						=	$this->geneal_model->getData2('single',$tblName1,$where1,$order1);

							// Generating order param
							$ORparam["sequence_id"]		    	=	(int)$this->geneal_model->getNextSequence('uw_orders');
							$ORparam["order_id"]		        =	$this->geneal_model->getNextOrderId();
							$ORparam["user_id"] 				=	(int)$UserID;
							$ORparam["user_type"] 				=	$UserTYPE;
							$ORparam["user_email"] 				=	$UserEMAIL ? $UserEMAIL : '';
							$ORparam["user_phone"] 				=	$UserPHONE;

							if($product_is_donate):
								$ORparam["product_is_donate"] 		=	$product_is_donate;
							else:
								$ORparam["product_is_donate"] 		= '';
							endif;


							if($shipping_method):
								$ORparam["shipping_method"] 		=	$shipping_method;
							else:
								$ORparam["shipping_method"] 		= '';
							endif;

							if($emirate_id):
								$ORparam["emirate_id"] 		=	$emirate_id;
							else:
								$ORparam["emirate_id"] 		= '';
							endif;

							if($emirate_name):
								$ORparam["emirate_name"] 		=	$emirate_name;
							else:
								$ORparam["emirate_name"] 		= '';
							endif;

							if($area_id):
								$ORparam["area_id"] 		=	$area_id;
							else:
								$ORparam["area_id"] 		= '';
							endif;

							if($area_name):
								$ORparam["area_name"] 		=	$area_name;
							else:
								$ORparam["area_name"] 		= '';
							endif;

							if($collection_point_id):
								$ORparam["collection_point_id"] 		=	$collection_point_id;
							else:
								$ORparam["collection_point_id"] 		= '';
							endif;

							if($collection_point_name):
								$ORparam["collection_point_name"] 		=	$collection_point_name;
							else:
								$ORparam["collection_point_name"] 		= '';
							endif;
							$ORparam["shipping_address"]		=	'';
							$ORparam["shipping_charge"] 		=	(float)$shipping_charge ? (float)$shipping_charge :(float)$this->input->post('shipping_charge');
							$ORparam["inclusice_of_vat"] 		=	(float)$inclusice_of_vat ? (float)$inclusice_of_vat : (float)$this->input->post('inclusice_of_vat') ;
							$ORparam["subtotal"] 				=	(float)$subtotal ? (float)$subtotal : (float)$this->input->post('subtotal') ;
							$ORparam["vat_amount"] 				=	(float)$vat_amount ? (float)$vat_amount : (float)$this->input->post('vat_amount');
							$ORparam["total_price"] 			=	(float)$total_price ? (float)$total_price : (float)$ORparam["inclusice_of_vat"];
							$ORparam["payment_mode"] 			=	$payment_method;
							$ORparam["payment_from"] 			=	'App';
							$ORparam["order_status"] 			=	"Initialize";
							$ORparam["device_type"] 			=	$device_type;
						    $ORparam["app_version"] 			=	$app_version;
						 	$ORparam["availableArabianPoints"] 	=	(float)$userData["availableArabianPoints"];
					        $ORparam["end_balance"] 			=	(float)$userData["availableArabianPoints"] - (float)$ORparam["inclusice_of_vat"] ;
							$ORparam["creation_ip"] 			=	$this->input->ip_address();
							$ORparam["created_at"] 				=	date('Y-m-d H:i');

							// Adding details in Order Collection..
							$orderInsertID 						=	$this->geneal_model->addData('uw_orders', $ORparam);

							//These field for dealzareabia.ea site.
							$ORparam["product_count"] 			=	(int)$productCount;
							$ORparam["finaltotal"] 				=	(float)$finalPrice;

							foreach($cartItems as $CA):	
								//Manage Inventory
								if($ORparam["product_is_donate"] == 'N'):
									$where['where']		=	array(
																'products_id'			=>	(int)$CA['id'],
																'collection_point_id' 	=>	(int)$ORparam["collection_point_id"]
															);

									$INVcheck  =	$this->geneal_model->getData2('single','uw_inventory',$where);
									if($INVcheck <> ''):
										$orqty = $INVcheck['order_request_qty'] + (int)$CA['qty'];
										$INVUpdate['order_request_qty']		=	$orqty;
										$this->geneal_model->editDataByMultipleCondition('uw_inventory',$INVUpdate,$where['where']);
										else:
											$INVparam['products_id']				= 	(int)$CA['id'];
											$INVparam['qty']						=	(int)0;
											$INVparam['available_qty']				=	(int)0;
											$INVparam['order_request_qty']			=	(int)$CA['qty'];
											$INVparam['collection_point_id']		=	(int)$ORparam["collection_point_id"];

											$INVparam['inventory_id']				=	(int)$this->common_model->getNextSequence('uw_inventory');
											
											$INVparam['creation_ip']				=	currentIp();
											$INVparam['creation_date']				=	(int)$this->timezone->utc_time();//currentDateTime();
											$INVparam['status']						=	'A';
											$this->geneal_model->addData('uw_inventory', $INVparam);
										endif;
									endif;
									
									//END
									$ORDparam["order_details_id"] 	=	(int)$this->geneal_model->getNextSequence('uw_orders_details');
									$ORDparam["order_sequence_id"]	=	(int)$ORparam["sequence_id"];
									$ORDparam["order_id"]			=	$ORparam["order_id"];
									$ORDparam["user_id"]			=	(int)$CA['user_id'];
									$ORDparam["product_id"] 		=	(int)$CA['id'];
									$ORDparam["product_name"] 		=	$CA['name'];
									$ORDparam["quantity"] 		    =	(int)$CA['qty'];
									if($CA['color']):
										$ORDparam["color"] 		    =	$CA['color'];
									endif;
									if($CA['size']):
										$ORDparam["size"] 		    =	$CA['size'];
									endif;
									$ORDparam["price"] 		        =	(float)$CA['price'];
									$ORDparam["tax"] 		        =	(float)0;
									$ORDparam["subtotal"] 		    =	(float)$CA['subtotal'];
									$ORDparam["is_donated"] 		=	$CA['is_donated'];
									$ORDparam["other"] 		        =	array(
																				'image' 		=>	$CA['other']->image,
																				'description' 	=>	$CA['other']->description,
																				'aed'			=>	$CA['other']->aed
																			);
									$ORDparam["current_ip"] 		=	$CA['current_ip'];
									$ORDparam["rowid"] 				=	$CA['rowid'];
									$ORDparam["curprodrowid"] 		=	$CA['curprodrowid'];

									$this->geneal_model->addData('uw_orders_details', $ORDparam);

							endforeach;
										
									$ORparam['userData'] = $userData;
									$serializedArray = urlencode(serialize($ORparam));
							return $serializedArray;

				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);die();
				endif;

			else:
				 echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result); die();
			endif;

		
	} // END OF FUNCTION


	public function addquickorders($POST='',$sellerDetails='')
	{	
			$USRERMOBILE = ltrim($this->input->post('users_mobile'), '0');


			// Adding order details.
		    $ORparam["sequence_id"]                 =   (int)$this->geneal_model->getNextSequence('uw_ticket_orders');
	        $ORparam["ticket_order_id"]             =   $this->geneal_model->getNextQuickBuyOrderId();
	        $ORparam["user_id"]                     =   (int)$this->input->get('users_id');
	        $ORparam["user_type"]                   =   $sellerDetails['users_type']; //$this->input->post('user_type');
	        $ORparam["user_email"]                  =   $sellerDetails['users_email'];  //$this->input->post('user_email');
	        $ORparam["user_phone"]                  =   $sellerDetails['users_mobile']; //$this->input->post('user_phone');
	        $ORparam["product_id"]                  =   (int)$this->input->post('product_id');
	        $ORparam["product_title"]               =   $this->input->post('product_title');
	        $ORparam["product_qty"]                 =   $this->input->post('product_qty');
	        $ORparam["product_color"]               =   $this->input->post('product_color');
	        $ORparam["product_size"]                =   $this->input->post('product_size');
	        $ORparam["prize_title"]                 =   $this->input->post('prize_title');
	        $ORparam["vat_amount"]                  =   (float)$this->input->post('vat_amount');
	        $ORparam["subtotal"]                    =   (float)$this->input->post('subtotal');
	        $ORparam["total_price"]                 =   (float)$this->input->post('total_price');
	        $ORparam["availableArabianPoints"]      =   (float)$sellerDetails["availableArabianPoints"];
	        $ORparam["end_balance"]                 =   (float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
	        $ORparam["payment_mode"]                =   'Arabian Points';
	        $ORparam["payment_from"]                =   'Quick';
	        $ORparam["product_is_donate"]           =   $this->input->post('product_is_donate'); //$this->input->post('product_is_donate');
	        $ORparam["order_status"]                =   "Success";
	        $ORparam["device_type"]                 =   $this->input->post('device_type');
	        $ORparam["app_version"]                 =   $this->input->post('app_version');
	        $ORparam["order_first_name"]            =   $this->input->post('first_name');
	        $ORparam["order_last_name"]             =   $this->input->post('last_name');
	        $ORparam["order_users_country_code"]    =   $this->input->post('country_code')?$this->input->post('country_code'):"+971";
	        $ORparam["order_users_mobile"]          =   $USRERMOBILE;
	        $ORparam["order_users_email"]           =   $this->input->post('users_email');
	        $ORparam["SMS"]                         =   $this->input->post('SMS');
	        $ORparam["creation_ip"]                 =   $this->input->ip_address();
	        $ORparam["created_at"]                  =   date('Y-m-d H:i');
	        
	        //Saving order details for Ticket
	        $orderInsertID                          =   $this->geneal_model->addData('uw_ticket_orders', $ORparam);

	        
	        //

	        $tblName                    =   'uw_products';
	        $where['where']             =   array( 'products_id'=> (int)$ORparam['product_id'] ,'status' => 'A');
	        $Sortdata                   =   array('category_id'=> 'DESC');
	        $productDetails             =   $this->geneal_model->getData2('single', $tblName, $where, $Sortdata);

	        // Stock update section..
	        $stock                      = (int)$productDetails['stock'] - (int)$ORparam['product_qty']  ; // updated stock.
	        $updateParams               =   array( 'stock' => (int)$stock );    
	        $updatedstatus              = $this->geneal_model->editData('uw_products',$updateParams,'products_id',(int)$ORparam['product_id']);

	        
	        $updateParams               =   array( 'order_status' => 'Success','created_at' => $ORparam['created_at']); 
	        $updatedstatus 				= $this->geneal_model->editData('uw_ticket_orders',$updateParams,'users_id',(int)$ORparam['user_id']);

	        // Deduct the purchesed points and get available arabian points of user.
        	$currentBal                     =   $this->geneal_model->debitPointsByAPI($ORparam['total_price'],$ORparam["user_id"]); 


        	 // checking available tickets..
			$ticketCheck['product_id']  	= $ORparam['product_id'];
			$ticketCheck['quantity'] 		= $ORparam['product_qty'];
			$ticketCheck['is_donated'] 		= $ORparam['product_is_donate'];
			$ticketCheck['name'] 		   	= $ORparam['product_title'];
			$ticketCheck['payment_from'] 	= 'Quick';

			// Checking soldout tickets and available tickets.
			$couponList 	= $this->common_model->getAvailableTickets($ticketCheck);

        	// Adding coupon ..
        	$couponData['coupon_id']        =   (int)$this->geneal_model->getNextSequence('uw_coupons');
            $couponData['users_id']         =   (int)$ORparam["user_id"];
            $couponData['users_email']      =   $ORparam["user_email"];

            $couponData['order_first_name']     =   $ORparam["order_first_name"];
            $couponData['order_last_name']      =   $ORparam["order_last_name"];
            $couponData['order_users_country_code'] =   $ORparam["order_users_country_code"]?$ORparam["order_users_country_code"]:"+971";
            $couponData['order_users_mobile']   =   $ORparam["order_users_mobile"];
            $couponData['order_users_email']    =   $ORparam["order_users_email"];

            $couponData['ticket_order_id']      =   $ORparam["ticket_order_id"];
            $couponData['product_id']           =   (int)$ORparam['product_id'];
            $couponData['product_title']        =   $ORparam['product_title'];
            $couponData['product_qty']          =   $ORparam['product_qty'];
            $couponData['total_price']          =   $ORparam['total_price'];
            $couponData["product_qty"]          =   $ORparam['product_qty'];
            $couponData["product_color"]        =   $ORparam['product_color'];
            $couponData["prize_title"]          =   $ORparam['prize_title'];
            $couponData["device_type"]          =   $ORparam['device_type'];
            $couponData["app_version"]          =   $ORparam['app_version'];
            $couponData['is_donated']           =   $ORparam["product_is_donate"];
            $couponData['coupon_status']        =   'Live';
            $couponData['coupon_code']          =   $couponList;
            $couponData['draw_date']            =   array($productDetails['draw_date']);
            $couponData['draw_time']            =   array($productDetails['draw_time']);
            if($ORparam["product_is_donate"] == 'Y'):
            	$couponData['coupon_type']          =   'Donated';
            endif;
            $couponData['created_at']           =   date('Y-m-d H:i');

            $this->geneal_model->addData('uw_ticket_coupons',$couponData);

            $couponParam['tickets'] = $couponList;
            $couponParam['product_id']	= (int)$ORparam['product_id'];
            $this->updateCouponsAvailability($couponParam);

            $results = $couponData;

            if($couponData['order_users_email']):
                $this->emailsendgrid_model->sendQuickMailToUser($couponData);
            endif;

            if($ORparam['SMS'] == "Y"):
                $this->sms_model->sendQuickTicketDetails($couponData['ticket_order_id'],$couponData['order_users_mobile'],$couponList,$couponData['product_id'],$couponData['order_users_country_code'],$couponData['is_donated']);
            endif;
            
            return $couponData;


	}


	/***********************************************************************
	** Function name : addorder
	** Developed By : Dilip Halder
	** Purpose  : This function used Addd new Order details..
	** Date : 28 July 2023
	************************************************************************/
	public function updateCouponsAvailability($couponData='')
	{
		$ticket_count = count($couponData['tickets']);

		for ($i=0; $i < $ticket_count; $i++):

			$tblName = 'uw_quickcoupons_totallist';
			$whereCon2['where']		 			= 	array( 'product_id' => (int)$couponData['product_id'] );	
			$totalsoldCoupons 					= 	$this->common_model->getData('single',$tblName,$whereCon2,$shortField);

			//Get current Ticket order sequence from admin panel.
			$tblName = 'uw_tickets_sequence';
			$whereCon2['where']		 			= 	array('product_id' => (int)$couponData['product_id'] , 'status' => 'A');	
			$shortField 						= 	array('tickets_seq_id'=>'ASC');
			$CurrentTicketSequence 				= 	$this->common_model->getData('single',$tblName,$whereCon2,$shortField,'0','0');

			//Adding soldout number for current ticket sequence.
			$couponTicketSequence["tickets_sold_count"] 	 =	(int)$CurrentTicketSequence['tickets_sold_count']+ 1;
			$this->geneal_model->editData('uw_tickets_sequence',$couponTicketSequence,'tickets_seq_id',(int)$CurrentTicketSequence['tickets_seq_id']);

			if(empty($totalsoldCoupons)):

				//Storing total ticket count in uw_quickcoupons_totallist
				$quickcoupons1["id"]				=	(int)$this->geneal_model->getNextSequence('uw_quickcoupons_totallist');
				$quickcoupons1["product_id"] 		=	(int)$couponData['product_id'];
			    $quickcoupons1["tickets_seq_id"] 	=	(int)$CurrentTicketSequence['tickets_seq_id'];
			    $quickcoupons1["tickets_sold_count"]=	(int)$i+1;
			    $quickcoupons1["creation_ip"] 		=	$this->input->ip_address();
			    $quickcoupons1["created_at"] 		=	date('Y-m-d H:i');
			    //Saving quick coupons number  
			    $this->geneal_model->addData('uw_quickcoupons_totallist', $quickcoupons1);
			else:

				$quickcoupons1["tickets_sold_count"]	=	(int)$totalsoldCoupons['tickets_sold_count']+1;
			    $quickcoupons1["creation_ip"] 			=	$this->input->ip_address();
			    $quickcoupons1["updated_at"] 			=	date('Y-m-d H:i');

				$this->geneal_model->editData('uw_quickcoupons_totallist',$quickcoupons1,'tickets_seq_id',(int)$CurrentTicketSequence['tickets_seq_id']);

			endif;

			 
			 
		endfor;
	}

	
	/***********************************************************************
	** Function name : CheckAvailableTickets
	** Developed By : Dilip Halder
	** Purpose  : This function used to validate product.
	** Date : 20 October 2023
	************************************************************************/
	public function CheckAvailableTickets($productID, $productQty,$productIsDonated,$plateform,$CouponGenerate='',$USER=''){

		// Checking product current status.
		$tblName 				=  'uw_products';
		$whereCon2['where']		= 	array('products_id' => (int)$productID );	
		$productData 			= 	$this->common_model->getData('single',$tblName,$whereCon2);
		// echo "<pre>"; print_r( $productData );die();

		// Product Validation start 
		if($plateform === 'web'):
			// Web Error List
			if(empty($productData) ):
			  $this->session->set_flashdata('error', lang('TICKET_NOT_AVAILABLE'). " for " .$productData['title'] );
	    	  redirect('user-cart');
			  die();
			elseif($productData['stock'] == 0):
			   $this->session->set_flashdata('error', lang('OUTOFSTOCK'). " - " .$productData['title'] );
	    	   redirect('user-cart');
			   die();
			elseif($productData['stock'] < (int)$productQty ):
			  $this->session->set_flashdata('error', lang('PRO_QTY'). " for " .$productData['title'] );
	    	  redirect('user-cart');
			  die();
			endif;
		elseif($plateform === 'mobile-web'):
			// Web Error List
			if(empty($productData) ):
			  $this->session->set_flashdata('error', lang('TICKET_NOT_AVAILABLE'). " for " .$productData['title'] );
	    	  redirect('/');
			  die();
			elseif($productData['stock'] == 0):
			   $this->session->set_flashdata('error', lang('OUTOFSTOCK'). " - " .$productData['title'] );
	    	   redirect('/');
			   die();
			elseif($productData['stock'] < (int)$productQty ):
			  $this->session->set_flashdata('error', lang('PRO_QTY'). " for " .$productData['title'] );
	    	  redirect('/');
			  die();
			endif;

		elseif($plateform === 'app'):
			// App Error List
			if(empty($productData) ):
			  echo outPut(0,lang('SUCCESS_CODE'),lang('TICKET_NOT_AVAILABLE'),$result);die();
			elseif($productData['status'] == 'I' || $productData['status'] == 'D' ):
			  echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_AVAILABLE'). " - " .$productData['title'],$result);die();
			elseif($productData['stock'] == 0):
			  echo outPut(0,lang('SUCCESS_CODE'),lang('OUTOFSTOCK'). " - " .$productData['title'],$result);die();
			elseif($productData['stock'] < (int)$productQty ):
			  echo outPut(0,lang('SUCCESS_CODE'),lang('PRO_QTY'). " for " .$productData['title'],$result);die();
			endif;
		endif;
		// END


		if(!empty($productData)):

			// Checking product current status.
			$tblName 				=  'uw_tickets_sequence';
			$whereCon2['where']		= 	array('product_id' => (int)$productID , "status" => "A" );	
			// Tickets details
			$ticketDetails 			= 	$this->common_model->getData('multiple',$tblName,$whereCon2);

			if($productData['sponsored_coupon']):
				$sponsored_coupon = $productData['sponsored_coupon'];
			else:
				$sponsored_coupon = 1;
			endif;

			if($productIsDonated == 'N' ):
				$soldOutQty = $productQty*$sponsored_coupon ;
			elseif($productIsDonated == 'Y'):
			 	$soldOutQty = $productQty*$sponsored_coupon*2 ;
			endif;

			if($this->input->post('isVoucher') == 'Y'):
				$soldOutQty = 2*$productQty*$sponsored_coupon;
			endif;

			if(!empty($ticketDetails)):
				//slots available
				$ticketSlotCount		= 	$this->common_model->getData('count',$tblName,$whereCon2);
				foreach ($ticketDetails as $key => $items):
					$TotalTicketCount   += $items['total_ticket_count'];
				endforeach;
			endif;
			
			$slot= 0;

			


			$tickets_prefix 		= $ticketDetails[$slot]['tickets_prefix'];
			// $totalTickts 			= $ticketDetails[$slot]['total_ticket_count'];
			$totalTickts 			= $TotalTicketCount;
			$randomTicketlength 	= $ticketDetails[$slot]['coupon_length'];
			$soldoutTickts 			= $ticketDetails[$slot]['coupon_sold_number']?$ticketDetails[$slot]['coupon_sold_number']:0;
			$soldOutQty             = $soldOutQty;

			$AvailableTicketCount  = $totalTickts-$soldoutTickts;

			if($plateform === 'web'):
				// WEB Error List.
				if($soldOutQty > $AvailableTicketCount):
					$this->session->set_flashdata('error', lang('TICKET_NOT_AVAILABLE'). " for " .$productData['title']);
			    	redirect('user-cart');
					die();
				endif;
			elseif($plateform === 'mobile-web'):
				// WEB Error List.
				if($soldOutQty > $AvailableTicketCount):
					$this->session->set_flashdata('error', lang('TICKET_NOT_AVAILABLE'). " for " .$productData['title']);
			    	redirect('/');
					die();
				endif;
			elseif($plateform === 'app'):
			endif;

			//USER Verification
			if($USER['USERID']):
				// $whereCon  		= array('users_id'  =>(int)$USER['USERID'] ,'status'=> 'A');
				$tbl_name  		= 'uw_users';
				$whereCon  		= array('users_id'  =>(int)$USER['USERID']);
				$this->mongo_db->select('*');
				$this->mongo_db->where($whereCon);	
				$SellerDetails = $this->mongo_db->find_one($tbl_name);
				
				if($plateform === 'web'):
				// WEB Error List.
					if(empty($SellerDetails)):
						$this->session->set_flashdata('error', lang('ERROR')); redirect('user-cart'); die();
		  			elseif($SellerDetails['status'] == "I" || $SellerDetails['status'] == "D" ):
		  				$this->session->set_flashdata('error', lang('ACCOUNT_BLOCKED')); redirect('user-cart'); die();
					elseif($SellerDetails['availableArabianPoints']< $USER['total_price']):
		  				$this->session->set_flashdata('error', lang('LOW_BALANCE')); redirect('user-cart'); die();
					endif;
				elseif($plateform === 'mobile-web'):
					// WEB Error List.
					if(empty($SellerDetails)):
						$this->session->set_flashdata('error', lang('ERROR')); redirect('/'); die();
		  			elseif($SellerDetails['status'] == "I" || $SellerDetails['status'] == "D" ):
		  				$this->session->set_flashdata('error', lang('ACCOUNT_BLOCKED')); redirect('/'); die();
					elseif($SellerDetails['availableArabianPoints']< $USER['total_price']):
		  				$this->session->set_flashdata('error', lang('LOW_BALANCE')); redirect('/'); die();
					endif;
				elseif($plateform === 'app'):
					// WEB Error List.
					if(empty($SellerDetails)):
						echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);die();
		  			elseif($SellerDetails['status'] == "I" || $SellerDetails['status'] == "D" ):
		  				echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);die();
					elseif($SellerDetails['availableArabianPoints']< $USER['total_price']):
		  				echo outPut(0,lang('SUCCESS_CODE'),lang('LOW_BALANCE'),$result);die();
					endif;
				endif;
			endif;


			if($CouponGenerate):
				 
					$tblName 						=	"uw_coupons";
					$whereExistinCoupons['where']	=	array('product_id' => (int)$productID );
					$NormalCouponData 		=	$this->common_model->getData('multiple',$tblName, $whereExistinCoupons);


					$tblNameQ 						=	"uw_ticket_coupons";
					$whereQExistinCoupons['where']	=	array('product_id' => (int)$productID );
					$QuickCouponData 				=	$this->common_model->getData('multiple',$tblNameQ, $whereQExistinCoupons);

					$currentExistingCouponData1 = array_column($QuickCouponData, 'coupon_code');

					$QuickExistingCoupons =array();
					foreach ($currentExistingCouponData1 as $key => $item) {
						for ($i=0; $i < count($item); $i++) { 
							array_push($QuickExistingCoupons ,$item[$i]);
						}
					}
					
					$NormalCouponData = array_column($NormalCouponData, 'coupon_code');

					$currentExistingCouponData = array_merge($NormalCouponData,$QuickExistingCoupons );

				    if($ticketSlotCount):
				    	for($i=0; $i < $ticketSlotCount; $i++):
							$available_tickets_in_slots = $ticketDetails[$i]['total_ticket_count'] -$ticketDetails[$i]['coupon_sold_number'];
							$tickets_prefix1 = $ticketDetails[$i]['tickets_prefix'];
							$SoltticketCount[] =  $available_ticketsSslots +=$available_tickets_in_slots;
						endfor;
				    endif;
			      	

					// Generate and display unique coupon codes
					$uniqueCoupons = array();
					while($soldOutQty > 0) {
					    
					    $coupon = $this->generateCouponCode($tickets_prefix,$randomTicketlength,$currentExistingCouponData);
					    $uniqueCoupons[] = $coupon;
					    $soldOutQty--;
					    
					    //Assinging next tickets prefix..
					    $GeneratedCouponCount = count($uniqueCoupons);
					    foreach ($SoltticketCount as $key => $item) {
				  		  	if($GeneratedCouponCount == $item):
				  		  		 $tickets_prefix = $ticketDetails[$key+1]['tickets_prefix'];
				  		  	endif;
					    }

					}

					return $uniqueCoupons;

			endif;


		endif;
	}

	/***********************************************************************
	** Function name : generateCouponCode
	** Developed By : Dilip Halder
	** Purpose  : This function used to generate coupons only.
	** Date : 20 October 2023
	************************************************************************/
	function generateCouponCode($tickets_prefix,$randomTicketlength,$currentExistingCouponData) {
	    $characters = '0123456789';
	    $couponCode = '';
		   
	    // Generate a unique coupon code until it's not a duplicate
	    do {
	        $couponCode = '';
	        for ($i = 0; $i < $randomTicketlength- strlen($tickets_prefix); $i++) {
	            $couponCode .= $characters[rand(0, strlen($characters) - 1)];
	        }

	        $couponCode = $tickets_prefix.$couponCode;

	    } while ($this->isCouponCodeDuplicate($couponCode ,$currentExistingCouponData ));
	    
	    return $couponCode;
	}

	// Function to check if a coupon code is a duplicate
	function isCouponCodeDuplicate($code,$currentExistingCouponData) {
	    // Replace this with your database or data structure logic to check for duplicates // $existingCodes = array('ABC123', 'XYZ789'); // Simulated existing codes
	    $existingCodes = $currentExistingCouponData; // Simulated existing codes
	    return in_array($code, $existingCodes);
	}

	/***********************************************************************
	** Function name : reduceAvailableTickets
	** Developed By : Dilip Halder
	** Purpose  : This function used to reduce soldout coupons availibility .
	** Date : 20 October 2023
	************************************************************************/
	public function reduceAvailableTickets($productID,$couponList)
	{
		// Checking product current status.
		if($couponList):
			foreach ($couponList as $key => $item) :
					$shortField 			=  array('tickets_seq_id' => 1);
					$tblName 				=  'uw_tickets_sequence';
					$whereCon2['where']		= 	array('product_id' => (int)$productID , "status" => "A" );	
					$ticketDetails 			= 	$this->common_model->getData('single',$tblName,$whereCon2,$shortField);
					 
					$coupon_sold_number 					= 	(int)$ticketDetails['coupon_sold_number'] +1;
					$ticketUpdate['coupon_sold_number']		=	(int)$coupon_sold_number;

					if($coupon_sold_number  == $ticketDetails['total_ticket_count']):
						$ticketUpdate['status']	=	'I';
					else:
						$ticketUpdate['status']	=	'A';
					endif;
					
					$ticketwhere['where']		=	array('product_id' => (int)$productID ,'tickets_seq_id' => (int)$ticketDetails['tickets_seq_id'] );
					$this->common_model->editDataByMultipleCondition('uw_tickets_sequence',$ticketUpdate,$ticketwhere['where']);

					// 90% soldout sms for amdin.
					$soldoutPercenteage =  round($ticketDetails['total_ticket_count']*90/100);

					if($soldoutPercenteage == $coupon_sold_number):
						//-------------------------------------------------------------------------------------------------------//
		                    $tblName 				=  'uw_products';
							$whereCon2['where']		= 	array('products_id' => (int)$productID , "status" => "A" );	
							$productDetails 			= 	$this->common_model->getData('single',$tblName,$whereCon2,$shortField);
						//-------------------------------------------------------------------------------------------------------//
					 	// send sms form here.
	                    $country_code='+971';
	                    $mobileNumber='+971501292591';
	                    $this->common_model->sendTickectFullSMS($mobileNumber,$country_code ,$productDetails['title']);

	                    $country_code='+971';
	                    $mobileNumber='+97154332166';
	                    $this->common_model->sendTickectFullSMS($mobileNumber,$country_code ,$productDetails['title']);

					endif;
			endforeach;

		endif;
	}

	/***********************************************************************
	** Function name : commissionList
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get commission Amount .
	** Date 		 : 13 February 2024
	** Updated By    : Dilip halder
	** Date 		 : 25 May 2024
	************************************************************************/
	public function commissionList($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'narration'=> array('$in' => ['Commission','Commission Reverted'] ) );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		$whereCon['narration']       = array('$in' => ['Commission','Commission Reverted'] );
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		// Order Id wise filter..
		if(!empty($DateFilter['where_in']['order_id'])):
		 $whereCon['order_id']       = array( '$in' => $DateFilter['where_in']['order_id']);
		endif;

		$walletwhereCon['where']     = $whereCon;
		$Commitionlist  		     = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);
		$totalCommission = 0; 
		foreach ($Commitionlist as $key => $item):
				$commition_amount = $item['upoints'];
				if($item['record_type']  == "Credit" ):
				  $totalCommission = $totalCommission + $commition_amount;
				elseif($item['record_type']  == "Debit" ):
				  $totalCommission = $totalCommission  -$commition_amount ;
				endif;
		endforeach;

		return $totalCommission;
	}

	/***********************************************************************
	** Function name : cancelOraderList
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get cancelOraderList Amount .
	** Date 		 : 22 February 2024
	** Updated By    : Dilip halder
	** Date 		 : 25 May 2024
	************************************************************************/
	public function cancelOraderList($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'narration'=> array('$in' => ['Commission','Commission Reverted'] ) );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		// $whereCon['narration']       = array( '$in' => array('Order','Order Cancalled') );
		$whereCon['narration']       = 'Order Cancalled';
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		// Order Id wise filter..
		if(!empty($DateFilter['where_in']['order_id'])):
		 $whereCon['order_id']       = array( '$in' => $DateFilter['where_in']['order_id']);
		endif;

		$walletwhereCon['where']     = $whereCon;
		$totalSales  		         = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);

		$totalCancelAmount = 0; 
		foreach($totalSales as $key => $item):
				$commition_amount = $item['upoints'];
			  	$totalCancelAmount = $totalCancelAmount + $commition_amount;
		endforeach;
		return $totalCancelAmount;
	}

	/***********************************************************************
	** Function name : CancellationPIDs
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get CancellationPIDs.
	** Date 		 : 17 Jul 2024
	** Updated By    :  
	** Date 		 :  
	************************************************************************/
	public function CancellationPIDs($user_oid='',$DateFilter='')
	{

		$tblName 	  	   		= 'uw_lotto_orders';
		$shortField        		= array('product_id'=> -1);
		$walletwhereCon    		= $DateFilter;
		$CancellationPIDsArray  = $this->common_model->getFieldInArray('product_id',$tblName, $walletwhereCon);
		return $CancellationPIDsArray;
	}

	/***********************************************************************
	** Function name : RedeemedPrizeList
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get RedeemedPrizeList.
	** Date 		 : 22 February 2024
	** Updated By    : Dilip halder
	** Date 		 : 25 May 2024
	************************************************************************/
	public function RedeemedPrizeList($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'narration'=> array('$in' => ['Commission','Commission Reverted'] ) );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		// $whereCon['narration']       = array( '$in' => array('Order','Order Cancalled') );
		$whereCon['narration']       = 'Redeem Prize';
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;
		$walletwhereCon['where']     = $whereCon;
		// $redeemedProduct              = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);
		$redeemedProduct              = $this->common_model->getFieldInArray('product_id',$tblName, $walletwhereCon);
		$redeemedProduct = array_unique($redeemedProduct);
		return $redeemedProduct;
		 
	}

	/***********************************************************************
	** Function name : totalSales
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get cancelOraderList Amount .
	** Date 		 : 22 February 2024
	** Updated By    : Dilip halder
	** Date 		 : 25 May 2024
	************************************************************************/
	public function totalSales($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'narration'=> array('$in' => ['Commission','Commission Reverted'] ) );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		// $whereCon['narration']       = array( '$in' => array('Order','Order Cancalled') );
		// $whereCon['narration']       = 'Order';
		$whereCon['narration']       = array( '$in' => array('Order','Order Cancalled') );

		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		// Order Id wise filter..
		if(!empty($DateFilter['where_in']['order_id'])):
		 $whereCon['order_id']       = array( '$in' => $DateFilter['where_in']['order_id']);
		endif;

		$walletwhereCon['where']     = $whereCon;

		$totalSales  		         = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);
		 

		$totalCommissionAmount = 0; 
		foreach ($totalSales as $key => $item):
				$commition_amount = $item['upoints'];
				if($item['record_type']  == "Credit" ):
				  $totalCommissionAmount = $totalCommissionAmount - $commition_amount;
				elseif($item['record_type']  == "Debit" ):
				  $totalCommissionAmount = $totalCommissionAmount + $commition_amount ;
				endif;
		endforeach;


		return $totalCommissionAmount;
	}

	/***********************************************************************
	** Function name : totalCustomerPaid
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get cancelOraderList Amount .
	** Date 		 : 22 February 2024
	** Updated By    : Dilip halder
	** Date 		 : 25 May 2024
	************************************************************************/
	public function totalCustomerPaid($user_oid='',$DateFilter='',$idd='')
	{
		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		$whereCon['narration']       = 'Redeem Prize';
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		if(!empty($DateFilter['where_in']['product_id'])):
         $productList =  $DateFilter['where_in']['product_id'];
		 // $whereCon['product_id'] = array( '$in' => array_map('intval',$productList));
		 $walletwhereCon['where_in'] = array( '0' => 'product_id' , '1'=> array_map('intval',$productList));
		endif;

		if(!empty($DateFilter['product_id'])):
			$whereCon['product_id']  = (int)$DateFilter['product_id'];
		endif;

		$walletwhereCon['where']     = $whereCon;

		$totalCustomer 		         = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);

		$totalCustomerPaid = 0; 
		foreach($totalCustomer as $key => $item):
				$commition_amount = $item['upoints'];
			  	$totalCustomerPaid = $totalCustomerPaid + $commition_amount;
		endforeach;
		return $totalCustomerPaid;
	}

	/***********************************************************************
	** Function name : totalProductDetails
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get cancelOraderList Amount .
	** Date 		 : 22 February 2024
	************************************************************************/
	public function totalProductDetails($user_oid='',$DateFilter='',$usersId='')
	{
		
		// $whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);


		$tblName 			= 'uw_users';
		$shortField         = array('');
		$whereCon['where']  = array('_id' => new MongoDB\BSON\ObjectId($user_oid) );
		$USERDATA 			= $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		
		$usersId = $usersId?$usersId:$USERDATA['users_id'];


		$whereConOrder['user_id'] = (int)$usersId;
		if($DateFilter['created_at']):
			$whereConOrder['created_at'] = $DateFilter['created_at'];
		endif;

		if($DateFilter['product_title']):
			$whereConOrder['product_title'] = $DateFilter['product_title'];
		endif;

		$tblName 	  	   		 = 'uw_lotto_orders';
		$shortField        		 = array('load_balance_id'=> -1 );
		$walletwhereCon['where'] = $whereConOrder;
		$orderData  		     = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);

		return $orderData;
	}

	/***********************************************************************
	** Function name : getsummeryReport
	** Developed By  : Dilip Halder
	** Purpose       : This function used for get data by query
	** Date          : 12 February 2024
	************************************************************************/
	public function getsummeryReport($action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt=''){
		$filterArray 				   =	array();

		if($wcon['where']):
			foreach($wcon['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		if($wcon['where_gte']):
	        foreach($wcon['where_gte'] as $where_key => $where_value):
	            array_push($filterArray,array($where_value[0]=> array('$gte' => $where_value[1])));
	        endforeach;
	    endif;

	    if($wcon['where_lte']):
	        foreach($wcon['where_lte'] as $where_key => $where_value):
	            array_push($filterArray,array($where_value[0]=> array('$lte' => $where_value[1])));
	        endforeach;
	    endif;

		$selectFields 					=  	array( '$project' => array(
													'_id'=>0,
													'order_id'=>1,
													'user_id'=>1,
													'product_id'=>1,
													'user_type'=>1,
													'user_email'=>1,
													'user_phone'=>1,
													'product_title'=>1,
											      	"product_qty"=> array( '$toInt' => '$product_qty'),
													'total_price'=>1,
													'prize_title'=>1,
													'status'=>1,
													'product_image'=>'$product.product_image',
													'product_price'=>'$product.straight_add_on_amount',
													'end_balance' =>1,
													'payment_mode' =>1,
													'created_at' =>1,
													'raffle_mode' =>1,
													
										));

		$whereCondition					=	array();



		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'product')),
												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
											  	  array('$unwind' => '$product_image' ),
											  	  array(
										  	  		'$group' => array(
							  	  						'_id' => '$product_title' ,
				  		                    			'price'   => array('$first' => '$product_price'),
				  		                    			'sales_count'=>array('$sum' =>  '$product_qty'),
				  		                    			'sales'=>array('$sum' => '$total_price'),
							  	  						'product_image' => array('$first' => '$product_image'),
							  	  						'product_id' => array('$first' => '$product_id')
			                 		  				)),
							  	  					array('$sort'=>$shortField),
												);

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
	}

	/***********************************************************************
	** Function name : commissionList
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get commission Amount .
	** Date 		 : 13 February 2024
	************************************************************************/
	public function commissionListold($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_loadBalance';
		$shortField        			= array('load_balance_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'narration'=> array('$in' => ['Commission','Commission Reverted'] ) );
		
		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		$whereCon['narration']       = array('$in' => ['Commission','Commission Reverted'] );
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		$walletwhereCon['where']     = $whereCon;
		$Commitionlist  		     = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);

		$totalCommission = 0; 
		foreach ($Commitionlist as $key => $item):
				$commition_amount = $item['upoints'];
				if($item['record_type']  == "Credit" ):
				  $totalCommission = $totalCommission + $commition_amount;
				elseif($item['record_type']  == "Debit" ):
				  $totalCommission = $totalCommission  -$commition_amount ;
				endif;
		endforeach;

		return $totalCommission;
	}

	/***********************************************************************
	** Function name : cancelOraderList
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to get cancelOraderList Amount .
	** Date 		 : 13 February 2024
	************************************************************************/
	public function cancelOraderListold($user_oid='',$DateFilter='')
	{

		$tblName 	  	   			= 'uw_lotto_orders';
		$shortField        			= array('sequence_id'=> -1 );
		// $walletwhereCon['where']    = array('user_oid' => new MongoDB\BSON\ObjectId($user_oid) ,'status'=> 'CL');

		$whereCon['user_oid']        = new MongoDB\BSON\ObjectId($user_oid);
		$whereCon['status']        	 = 'CL';
		if($DateFilter['created_at']):
			$whereCon['created_at']  = $DateFilter['created_at'];
		endif;

		$walletwhereCon['where']     = $whereCon;
		$CancelOraderList  			 = $this->common_model->getData('multiple',$tblName, $walletwhereCon, $shortField);

		$TotalcancelOrder = 0; 
		foreach ($CancelOraderList as $key => $item):
			  	$TotalcancelOrder = $TotalcancelOrder + $item['total_price'];
		endforeach;
		return $TotalcancelOrder;
	}

	/***********************************************************************
	** Function name : UserAuthCheck
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to check User Auth.
	** Date 		 : 23 February 2024
	************************************************************************/
	public function UserAuthCheck($REQUEST_TYPE='')
	{
		if($REQUEST_TYPE):
			header('Access-Control-Allow-Origin: *');
	        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
	        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	        $APIKEY  		= $_SERVER['HTTP_APIKEY'];
		    $APIDATE        = $_SERVER['HTTP_APIDATE'];
		    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
			if($REQUEST_METHOD == $REQUEST_TYPE):
				$tableName          = 'uw_users';
				$wcon['users_type'] = 'Api User';
				$wcon['status']	 	= 'A';
				$wcon['api_key']	= $APIKEY;
				$wcon['created_at']	= $APIDATE;
				if($this->input->get('users_id')  || $this->input->post('users_id')):
				 $wcon['users_id']	= $this->input->get('users_id')?(int)$this->input->get('users_id'):(int)$this->input->post('users_id');
				endif;
				$whereCon['where']  = $wcon;
				$shortField 	    = '';
				$USerData 		    = $this->common_model->getData('single',$tableName, $whereCon,$shortField);

				// fields retriving from users data..
				$country_code = $USerData['country_code'];
				if(!empty($country_code)):
					// conversion
					$tableName1         = 'uw_conversions';
					$Fieldslist 		= array('country_code','conversion_rate','time_zone','country');
					$whereCon['where']  = array('country_code' => $country_code ,'status' => 'A');
					$conversion         = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tableName1, $whereCon);
					 
				endif;
				// $responce          = ($USerData == 1)? true : false;
				if($USerData):
					$responce['country'] 	= $conversion['country'];
					$responce['conversion'] = $conversion['conversion_rate'];
					$responce['time_zone']  = $conversion['time_zone'];
				else:
					$responce = false;
				endif;
				return $responce;
			else:
				return false;
			endif;
		else:
			return false;
		endif;
	}

	/***********************************************************************
	** Function name : checkBalance
	** Developed By  : Dilip Halder
	** Purpose       : This function used to check user's balance.
	** Date 		 : 08 July 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function checkBalance($accesstype="",$users_id='',$total_price="")
	{  
		$error = 'N';
		$this->mongo_db->select(array('availableArabianPoints','users_id'));
		$this->mongo_db->where(array('users_id'=> (int)$users_id));	
		$userData = $this->mongo_db->find_one('uw_users');

		if( $total_price > $userData['availableArabianPoints'] ):
			$error = 'Y';
			if($accesstype == 'web' && $error  == 'Y'):
		    	$this->session->set_flashdata('alert_error',lang('LOW_BALANCE'));
		    	redirect('/wallets');die();
		 	else:
		 		echo outPut(0,lang('SUCCESS_CODE'),lang('LOW_BALANCE'),$result); die();
		 	endif;
		endif;

	}

	/***********************************************************************
	** Function name : order_initialize
	** Developed By  : Dilip Halder
	** Purpose       : This function used to initialize orders.
	** Date 		 : 08 July 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function order_initialize($user_id='', $products_id='', $quantity='', $straight_add_on_amount='', $rumble_add_on_amount='', $reverse_add_on_amount='',$selection_values='', $subtotal='', $total_price='', $draw_date='',$draw_time='',$lotto_type='', $country_code='', $users_mobile='',$users_email='', $device_type='', $app_name='', $app_version='', $ticket='', $payment_mode='',$prize_title,$pickup_point='',$delivery_address='',$delivery_charge='',$first_name="" ,$last_name='',$users_lat='',$users_long='',$users_address='',$pos_number='',$raffle_mode='') {

		$param["sequence_id"]		     = (int)$this->geneal_model->getNextSequence('uw_initilize_orders');
		$param["user_id"]		     	 = (int)$user_id;
		$param['pos_number'] 			 = $pos_number;
		$param["order_id"]		     	 = $this->geneal_model->getNextUWINOrderId();
		$param['products_id']			 = (int)$products_id;
		$param['quantity'] 			     = (int)$quantity;
		$param['straight_add_on_amount'] = (int)$straight_add_on_amount;
		$param['rumble_add_on_amount']   = (int)$rumble_add_on_amount;
		$param['reverse_add_on_amount']  = (int)$reverse_add_on_amount;
		$param['selection_values']  	 = $selection_values;
		$param['vat_amount']  			 = (float)$vat_amount;
		$param['subtotal']  			 = (float)$subtotal;
		$param['total_price']  			 = (float)$total_price;
		$param['prize_title']  			 = $prize_title;
		$param['draw_date']  			 = $draw_date;
		$param['draw_time']  			 = $draw_time;
		$param['lotto_type']  			 = (int)$lotto_type;
		$param['country_code']  	     = $country_code;
		$param['users_mobile']  	     = (int)$users_mobile;
		$param['users_email']  	         = $users_email;
		$param['device_type']  	     	 = $device_type;
		$param['app_name']  	     	 = $app_name;
		$param['app_version']  	     	 = $app_version;
		$param['ticket']  	     	 	 = $ticket;
		$param['payment_mode']  	     = $payment_mode;
		$param["pickup_point"] 			 = $pickup_point;	 
		$param["delivery_address"] 		 = $delivery_address;	 
		$param["delivery_charge"] 		 = (float)$delivery_charge;
		$param["order_first_name"] 			= $first_name;
     	$param["order_last_name"] 			= $last_name;
     	$param["order_users_country_code"] 	= $country_code;
     	$param["order_users_mobile"] 		= (int)$users_mobile;
     	$param["order_users_email"] 		= $users_email;
     	$param['latitude']					= $users_lat;
		$param['longitude']					= $users_long;
		$param['address']					= $users_address;
		$param['raffle_mode']				= $raffle_mode;
		$param['created_at']  	     	    = date('Y-m-d H:i');
	    $orderInsertID 					    = $this->geneal_model->addData('uw_initilize_orders', $param);
	    return $orderInsertID;
	}

	/***********************************************************************
	** Function name : paymentCapture
	** Developed By  : Dilip Halder
	** Purpose       : This function used to initialize orders.
	** Date 		 : 09 July 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	// public function paymentCapture($accesstype='',$users_id='',$order_id='',$payment_mode='',$transaction_id='',$order_status='',$raffle_mode='') {
	// 	$tblName  	     	= 'uw_lotto_orders';
	// 	$whereCon['where'] 	= array('order_id' => $order_id);
	// 	$CheckOrders	    = $this->getData('single',$tblName,$whereCon);
	// 	$error = 'N';
	// 	if($CheckOrders):
	// 		$error = 'Y';
	// 		if($accesstype == 'web' && $error  == 'Y'):
	// 			$this->session->set_flashdata('alert_error',lang('ALREADY_ORDER_PLACED'));
	// 	    	redirect('/');die();
	// 		else:
	// 			echo outPut(0,lang('FORBIDDEN_CODE'),lang('ALREADY_ORDER_PLACED'),$result);die();
	// 		endif;
	// 	endif;

	// 	// Orders Details
	// 	$tblName  	     	= 'uw_initilize_orders';
	// 	$whereCon['where'] 	= array('order_id' => $order_id);
	// 	$OrderDetails	        = $this->getData('single',$tblName,$whereCon);
	// 	// echo "<pre>";print_r($OrderDetails);die();

	// 	// Products Details
	// 	$PRODUCTSearchFiled = array('title','draw_id','draw_date','draw_time','products_id','straight_add_on_amount','rumble_add_on_amount','reverse_add_on_amount','reffle_prefix','reffle_length'); 
	// 	$this->mongo_db->select($PRODUCTSearchFiled);
	// 	$this->mongo_db->where('products_id' ,(int)$OrderDetails['products_id']);
	// 	$ProductData   = $this->mongo_db->find_one('uw_products');
	// 	// echo "<pre>";print_r($ProductData);die();

	// 	/* ---------------------------------------------------- */
		
	// 	//User Details 
	// 	$USERSearchFiled    = array('users_type','users_id','availableArabianPoints','country_code','users_mobile','users_email','referred_by','referrel_amount','redeemed_referrel_amount'); 
	// 	$this->mongo_db->select($USERSearchFiled);
	// 	$this->mongo_db->where('users_id' ,(int)$OrderDetails['user_id']);
	// 	$UserData  = $this->mongo_db->find_one('uw_users');
	//  	$user_oid = $UserData['_id']->{'$id'};

	// 	/* ---------------------------------------------------- */

	// 	// Order Placing
	// 	/* ---------------------------------------------------- */
	// 	if($order_status == "Success"):
	// 		$status = "A";
	// 	else:
	// 		$status = "A";
	// 	endif;

	// 		$availableArabianPoints = (float)$UserData['availableArabianPoints'];
	// 	if($OrderDetails['payment_mode'] == "UPoints"):
	// 		$end_balance = (float)$availableArabianPoints - $OrderDetails['total_price'];

	// 		// Deduct the purchesed points and get available arabian points of user.
    //     	$currentBal  = $this->geneal_model->debitPointsByAPI((float)$OrderDetails['total_price'],(int)$UserData['users_id']); 
    //     	if($accesstype == 'web'):
	// 			$this->session->set_userdata('availableArabianPoints',$currentBal);
	// 		endif;
	// 	else:
	// 		$end_balance = (float)$availableArabianPoints;
	// 	endif;

	// 	// new function...
	// 	if($OrderDetails['raffle_mode'] == "Y"):
	// 		$quantity 	    = $OrderDetails['quantity'];
	// 		$reffle_prefix  = $ProductData['reffle_prefix'];
	// 		$reffle_length  = $ProductData['reffle_length'];
	// 		$raffle_tickets = $this->generateRaffle($quantity,$reffle_prefix ,$reffle_length);
	// 	endif;

	// 	$param["sequence_id"]		     	= (int)$this->geneal_model->getNextSequence('uw_lotto_orders');
	// 	$param["order_id"]		     	 	= $OrderDetails['order_id'];
	// 	$param["transaction_id"]		    = $transaction_id;
	// 	$param["user_id"]		     	 	= (int)$UserData['users_id'];
	// 	$param["user_type"]		     	 	= $UserData['users_type'];
	// 	$param["user_email"]		     	= $UserData['users_email'];
	// 	$param["user_phone"]		     	= $UserData['users_mobile'];
	// 	$param["pos_number"]		     	= $OrderDetails['pos_number'];
	// 	$param["store_name"]		     	= $UserData['store_name'];
	// 	$param["product_id"]		     	= $ProductData['products_id'];
	// 	$param["product_title"]		     	= $ProductData['title'];
	// 	$param["product_qty"]		     	= $OrderDetails['quantity'];
	// 	$param["prize_title"]		     	= $OrderDetails['prize_title'];
	// 	$param["straight_add_on_amount"] 	= (float)$OrderDetails['straight_add_on_amount'];
	// 	$param["rumble_add_on_amount"]   	= (float)$OrderDetails['rumble_add_on_amount'];
	// 	$param["reverse_add_on_amount"]  	= (float)$OrderDetails['reverse_add_on_amount'];
	// 	$param["selection_values"]  		= $OrderDetails['selection_values'];
	// 	$param["vat_amount"]		     	= (float)$OrderDetails['vat_amount'];
	// 	$param["subtotal"]  			 	= (float)$OrderDetails['subtotal'];
	// 	$param["total_price"]  			 	= (float)$OrderDetails['total_price'];
	// 	$param["availableArabianPoints"] 	= (float)$availableArabianPoints;
	// 	$param["end_balance"] 			 	= (float)$end_balance;
	// 	$param["payment_mode"] 			 	= $OrderDetails['payment_mode'];
	// 	$param["pickup_point"] 			    = $OrderDetails['pickup_point'];	 
	// 	$param["delivery_address"] 		    = $OrderDetails['delivery_address'];	 
	// 	$param["delivery_charge"] 		    = (float)$OrderDetails['delivery_charge'];	 
	// 	$param["draw_id"]		    		= (int)$ProductData['draw_id'];
	// 	$param["draw_date"]		    		= $ProductData['draw_date'];
	// 	$param["draw_time"]		    		= $ProductData['draw_time'];
	// 	$param["payment_from"] 			 	= 'Lotto';
	// 	$param["product_is_donate"] 	 	= $OrderDetails['product_is_donate'];
	// 	$param["order_status"] 	 		 	= $order_status;
	// 	$param["device_type"] 	 		 	= $OrderDetails['device_type'];
	// 	$param["app_version"] 	 		 	= $OrderDetails['app_version'];
	// 	$param["ticket"] 	 		 	 	= $OrderDetails['ticket'];
	// 	$param["status"] 	 		 	 	= $status;
	// 	$param["order_first_name"] 	 	 	= $OrderDetails['order_first_name'];
	// 	$param["order_last_name"] 	 	 	= $OrderDetails['order_last_name'];
	// 	$param["order_users_country_code"]  = $OrderDetails['order_users_country_code'];
	// 	$param["order_users_mobile"] 	 	= $OrderDetails['order_users_mobile'];
	// 	$param["order_users_email"] 	 	= $OrderDetails['order_users_email'];
	// 	$param["SMS"] 	 					= $OrderDetails['SMS'];
	// 	$param['latitude']					= $OrderDetails['latitude'];
	// 	$param['longitude']					= $OrderDetails['longitude'];
	// 	$param['address']					= $OrderDetails['address'];
	// 	if(!empty($raffle_tickets)):
	// 		$param['raffle_mode']			= $OrderDetails['raffle_mode'];
	// 		$param['raffle_tickets']		= $raffle_tickets;
	// 	endif;
	// 	$param["creation_ip"] 	 			= currentIp();
	// 	$param["created_at"] 	 			= $OrderDetails['created_at'];
	//     $orderInsertID 					 = $this->geneal_model->addData('uw_lotto_orders', $param);

	//     // Added syntex for first redeemed_referrel_amount.  
	//  	if(!empty($UserData['referred_by']) && empty($UserData['redeemed_referrel_amount'])):

	//  		$USERSearchFiled = array('users_type','users_id','availableArabianPoints','country_code','users_mobile','users_email','referred_by','referrel_amount','redeemed_referrel_amount','commission_percentage'); 
	//  		$ReferredBy  	 = (string)$UserData['referred_by'];
	// 		$this->mongo_db->select($USERSearchFiled);
	// 		$this->mongo_db->where('_id' ,new MongoDB\BSON\ObjectId($ReferredBy));
	// 		$SellerData  	 = $this->mongo_db->find_one('uw_users');

	// 		if(!empty($SellerData)):
	// 			$commition_amount = $OrderDetails['total_price']*$SellerData['commission_percentage']/100;
	// 			$CommissionParam  = array('totalArabianPoints'=> +$commition_amount ,'availableArabianPoints'=> +$commition_amount,'redeemed_referrel_amount' => +$commition_amount); 
	// 			$commissionData   = $this->manageBalance("uw_users",$CommissionParam ,"_id" ,new MongoDB\BSON\ObjectId($ReferredBy));

	// 			$UserCommissionParam = array('redeemed_referrel_amount' => $commition_amount); 
	// 			$commissionData      = $this->manageBalance("uw_users",$UserCommissionParam ,"_id" ,new MongoDB\BSON\ObjectId($user_oid));

	//  		 	// First Commission amount  releasing for btb user..
	// 		    $FirstpurchaseParam["load_balance_id"]		 = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 			$FirstpurchaseParam["order_oid"] 			 = new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 			$FirstpurchaseParam["user_oid"] 			 = new MongoDB\BSON\ObjectId($SellerData['_id']->{'$id'});
	// 			$FirstpurchaseParam["user_id_deb"]			 = (int)0;
	// 			$FirstpurchaseParam["order_id"] 			 = $orderInsertID['order_id'];
	// 			$FirstpurchaseParam["user_id_cred"] 		 = (int)$SellerData['users_id'];
	// 			$FirstpurchaseParam["upoints"] 				 = (float)$commition_amount;
	// 			$FirstpurchaseParam["availableArabianPoints"]= (float)$SellerData['availableArabianPoints'];
	// 			$FirstpurchaseParam["end_balance"] 			 = (float)$SellerData['availableArabianPoints'] + $commition_amount;
	// 		    $FirstpurchaseParam["record_type"] 			 = 'Credit';
	// 		    $FirstpurchaseParam["narration"]			 = 'Referrel Commission';
	// 		    $FirstpurchaseParam["remarks"]				 = 'Ticket ID : '.$orderInsertID['order_id'];
	// 		    $FirstpurchaseParam["creation_ip"] 	 		 = currentIp();
	// 		    $FirstpurchaseParam["created_at"] 			 = date('Y-m-d H:i');
	// 		    $FirstpurchaseParam["created_by"] 			 = (int)$UserData['users_id'];
    // 			$FirstpurchaseParam["status"] 				 =	"A";
	// 	    	$this->geneal_model->addData('uw_loadBalance', $FirstpurchaseParam);
	// 		endif;
 	//  	endif;

	//     // Order capturing in order uw_loadbalance table..
	//     $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 	$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 	$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 	$fromuserparam["user_id_deb"]			 =	(int)$UserData['users_id'];
	// 	$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
	// 	$fromuserparam["user_id_cred"] 			 =	(int)0;
	// 	$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
	// 	$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
	// 	$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
	//     $fromuserparam["record_type"] 			 =	'Debit';
	//     $fromuserparam["narration"]				 =	'Order';
	//     $fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
	//     $fromuserparam["creation_ip"] 	 		 =  currentIp();
	//     $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
	//     $fromuserparam["created_by"] 			 =	(int)$UserData['users_id'];
	//     $fromuserparam["status"] 				 =	"A";
    // 	$this->geneal_model->addData('uw_loadBalance', $fromuserparam);
	// 	/* Order capturing code start here.  End */
	    
	//     return $orderInsertID;
	// }



	public function paymentCapture($accesstype='',$users_id='',$order_id='',$payment_mode='',$transaction_id='',$order_status='',$raffle_mode='') {
		$tblName  	     	= 'uw_lotto_orders';
		$whereCon['where'] 	= array('order_id' => $order_id);
		$CheckOrders	    = $this->getData('single',$tblName,$whereCon);
		$error = 'N';
		if($CheckOrders):
			$error = 'Y';
			if($accesstype == 'web' && $error  == 'Y'):
				$this->session->set_flashdata('alert_error',lang('ALREADY_ORDER_PLACED'));
		    	redirect('/');die();
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('ALREADY_ORDER_PLACED'),$result);die();
			endif;
		endif;
 
		// Orders Details
		$tblName  	     	= 'uw_initilize_orders';
		$whereCon['where'] 	= array('order_id' => $order_id);
		$OrderDetails	    = $this->getData('single',$tblName,$whereCon);
		// echo "<pre>";print_r($OrderDetails);die();

		// Products Details
		$PRODUCTSearchFiled = array('title','draw_id','draw_date','draw_time','products_id','straight_add_on_amount','rumble_add_on_amount','reverse_add_on_amount','reffle_prefix','reffle_length'); 
		$this->mongo_db->select($PRODUCTSearchFiled);
		$this->mongo_db->where('products_id' ,(int)$OrderDetails['products_id']);
		$ProductData   = $this->mongo_db->find_one('uw_products');
		// echo "<pre>";print_r($ProductData);die();

		/* ---------------------------------------------------- */
		
		//User Details 
		$USERSearchFiled    = array('users_type','users_id','availableArabianPoints','country_code','users_mobile','users_email','referred_by','referrel_amount','redeemed_referrel_amount','winningBalance'); 
		$this->mongo_db->select($USERSearchFiled);
		$this->mongo_db->where('users_id' ,(int)$OrderDetails['user_id']);
		$UserData  = $this->mongo_db->find_one('uw_users');
	 	$user_oid = $UserData['_id']->{'$id'};

		/* ---------------------------------------------------- */

		// Order Placing
		/* ---------------------------------------------------- */
		if($order_status == "Success"):
			$status = "A";
		else:
			$status = "A";
		endif;

			$availableArabianPoints = (float)$UserData['availableArabianPoints'];
		if($OrderDetails['payment_mode'] == "UPoints"):
			$end_balance = (float)$availableArabianPoints - $OrderDetails['total_price'];

			// Deduct the purchesed points and get available arabian points of user.
        	$currentBal  = $this->geneal_model->debitPointsByAPI((float)$OrderDetails['total_price'],(int)$UserData['users_id']); 
        	if($accesstype == 'web'):
				$this->session->set_userdata('availableArabianPoints',$currentBal);
			endif;
		else:
			$end_balance = (float)$availableArabianPoints;
		endif;

		// new function...
		if($OrderDetails['raffle_mode'] == "Y"):
			$quantity 	    = $OrderDetails['quantity'];
			$reffle_prefix  = $ProductData['reffle_prefix'];
			$reffle_length  = $ProductData['reffle_length'];
			$raffle_tickets = $this->generateRaffle($quantity,$reffle_prefix ,$reffle_length);
		endif;
		
		$this->load->library('mongodb_client');
		$session = $this->mongodb_client->client->startSession();
		$session->startTransaction();
		try {
			
			$param["sequence_id"]		     	= (int)$this->geneal_model->getNextSequence('uw_lotto_orders');
			$param["order_id"]		     	 	= $OrderDetails['order_id'];
			$param["transaction_id"]		    = $transaction_id;
			$param["pos_number"]		     	= $OrderDetails['pos_number'];
			$param["user_id"]		     	 	= (int)$UserData['users_id'];
			$param["user_type"]		     	 	= $UserData['users_type'];
			$param["user_email"]		     	= $UserData['users_email'];
			$param["user_phone"]		     	= $UserData['users_mobile'];
			$param["store_name"]		     	= $UserData['store_name'];
			$param["product_id"]		     	= $ProductData['products_id'];
			$param["product_title"]		     	= $ProductData['title'];
			$param["product_qty"]		     	= $OrderDetails['quantity'];
			$param["prize_title"]		     	= $OrderDetails['prize_title'];
			$param["straight_add_on_amount"] 	= (float)$OrderDetails['straight_add_on_amount'];
			$param["rumble_add_on_amount"]   	= (float)$OrderDetails['rumble_add_on_amount'];
			$param["reverse_add_on_amount"]  	= (float)$OrderDetails['reverse_add_on_amount'];
			$param["selection_values"]  		= $OrderDetails['selection_values'];
			$param["vat_amount"]		     	= (float)$OrderDetails['vat_amount'];
			$param["subtotal"]  			 	= (float)$OrderDetails['subtotal'];
			$param["total_price"]  			 	= (float)$OrderDetails['total_price'];
			$param["availableArabianPoints"] 	= (float)$availableArabianPoints;
			$param["end_balance"] 			 	= (float)$end_balance;
			$param["payment_mode"] 			 	= $OrderDetails['payment_mode'];
			$param["pickup_point"] 			    = $OrderDetails['pickup_point'];	 
			$param["delivery_address"] 		    = $OrderDetails['delivery_address'];	 
			$param["delivery_charge"] 		    = (float)$OrderDetails['delivery_charge'];	 
			$param["draw_id"]		    		= (int)$ProductData['draw_id'];
			$param["draw_date"]		    		= $ProductData['draw_date'];
			$param["draw_time"]		    		= $ProductData['draw_time'];
			$param["payment_from"] 			 	= 'Lotto';
			$param["product_is_donate"] 	 	= $OrderDetails['product_is_donate'];
			$param["order_status"] 	 		 	= $order_status;
			$param["device_type"] 	 		 	= $OrderDetails['device_type'];
			$param["app_version"] 	 		 	= $OrderDetails['app_version'];
			$param["ticket"] 	 		 	 	= $OrderDetails['ticket'];
			$param["status"] 	 		 	 	= $status;
			$param["order_first_name"] 	 	 	= $OrderDetails['order_first_name'];
			$param["order_last_name"] 	 	 	= $OrderDetails['order_last_name'];
			$param["order_users_country_code"]  = $OrderDetails['order_users_country_code'];
			$param["order_users_mobile"] 	 	= $OrderDetails['order_users_mobile'];
			$param["order_users_email"] 	 	= $OrderDetails['order_users_email'];
			$param["SMS"] 	 					= $OrderDetails['SMS'];
			$param['latitude']					= $OrderDetails['latitude'];
			$param['longitude']					= $OrderDetails['longitude'];
			$param['address']					= $OrderDetails['address'];
			if(!empty($raffle_tickets)):
				$param['raffle_mode']			= $OrderDetails['raffle_mode'];
				$param['raffle_tickets']		= $raffle_tickets;
			endif;
			$param["creation_ip"] 	 			= currentIp();
			$param["created_at"] 	 			= $OrderDetails['created_at'];
			// $orderInsertID 					 = $this->geneal_model->addData('uw_lotto_orders', $param);
			$orderInsertID = $this->mongodb_client->insertDocument('uw_lotto_orders', $param, $session);
			$o_id  = (string) $orderInsertID['_id'];


			// if(!empty($UserData['referred_by']) && empty($UserData['redeemed_referrel_amount'])):

			// Added syntex for first raferrel commission for btb users..  
			$tblName  	     	= 'uw_lotto_orders';
			$whereCon['where'] 	= array('user_id' => (int)$UserData['users_id']);
			$firstPurchase	    = $this->getData('count',$tblName,$whereCon);
			if(!empty($UserData['referred_by'])  && $firstPurchase == 0 ):
 
				$USERSearchFiled = array('users_type','users_id','availableArabianPoints','country_code','users_mobile','users_email','referred_by','referrel_amount','redeemed_referrel_amount','commission_percentage'); 
				$ReferredBy  	 = (string)$UserData['referred_by'];
				$this->mongo_db->select($USERSearchFiled);
				$this->mongo_db->where('_id' ,new MongoDB\BSON\ObjectId($ReferredBy));
				$SellerData  	 = $this->mongo_db->find_one('uw_users');

				if(!empty($SellerData)):
					$seller_oid 	      = $SellerData['_id']->{'$id'};
					$commition_amount = $OrderDetails['total_price']*$SellerData['commission_percentage']/100;
					$CommissionParam  = array('totalArabianPoints'=> +$commition_amount ,'availableArabianPoints'=> +$commition_amount,'redeemed_referrel_amount' => +$commition_amount); 
					$commissionData   = $this->manageBalance("uw_users",$CommissionParam ,"_id" ,new MongoDB\BSON\ObjectId($ReferredBy));

					$UserCommissionParam = array('redeemed_referrel_amount' => $commition_amount); 
					$commissionData      = $this->manageBalance("uw_users",$UserCommissionParam ,"_id" ,new MongoDB\BSON\ObjectId($seller_oid));

					// First Commission amount  releasing for btb user..
					$FirstpurchaseParam["load_balance_id"]		 = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
					$FirstpurchaseParam["order_oid"] 			 = new MongoDB\BSON\ObjectId($o_id);
					$FirstpurchaseParam["user_oid"] 			 = new MongoDB\BSON\ObjectId($seller_oid);
					$FirstpurchaseParam["user_id_deb"]			 = (int)0;
					$FirstpurchaseParam["order_id"] 			 = $orderInsertID['order_id'];
					$FirstpurchaseParam["user_id_cred"] 		 = (int)$SellerData['users_id'];
					$FirstpurchaseParam["upoints"] 				 = (float)$commition_amount;
					$FirstpurchaseParam["availableArabianPoints"]= (float)$SellerData['availableArabianPoints'];
					$FirstpurchaseParam["end_balance"] 			 = (float)$SellerData['availableArabianPoints'] + $commition_amount;
					$FirstpurchaseParam["record_type"] 			 = 'Credit';
					$FirstpurchaseParam["narration"]			 = 'Referrel Commission';
					$FirstpurchaseParam["remarks"]				 = 'Ticket ID : '.$orderInsertID['order_id'];
					$FirstpurchaseParam["creation_ip"] 	 		 = currentIp();
					$FirstpurchaseParam["created_at"] 			 = date('Y-m-d H:i');
					$FirstpurchaseParam["created_by"] 			 = (int)$UserData['users_id'];
					$FirstpurchaseParam["status"] 				 =	"A";
					// $FirstpurchaseinsertResult = $this->geneal_model->addData('uw_loadBalance', $FirstpurchaseParam);
					$FirstpurchaseinsertResult = $this->mongodb_client->insertDocument('uw_loadBalance', $FirstpurchaseParam, $session);
				endif;
			endif;

			// Order capturing in order uw_loadbalance table..
			$fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
			$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($o_id);
			$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
			$fromuserparam["user_id_deb"]			 =	(int)$UserData['users_id'];
			$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
			$fromuserparam["user_id_cred"] 			 =	(int)0;
			$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
			$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
			$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
			$fromuserparam["record_type"] 			 =	'Debit';
			$fromuserparam["narration"]				 =	'Order';
			$fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
			$fromuserparam["creation_ip"] 	 		 =  currentIp();
			$fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
			$fromuserparam["created_by"] 			 =	(int)$UserData['users_id'];
			$fromuserparam["status"] 				 =	"A";
			// $fromuserinsertResult = $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
			$fromuserinsertResult = $this->mongodb_client->insertDocument('uw_loadBalance', $fromuserparam, $session);
			if($fromuserinsertResult || $FirstpurchaseinsertResult ){
				$session->commitTransaction();
			   // $session->abortTransaction();
				// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
				return $orderInsertID;
		   }else{
			   $session->abortTransaction();
			   echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: Unable to capture payment',[]);
			   die();
		   }
			
		} catch (Exception $e) {
			$session->abortTransaction();
			echo $e;
			echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: ',$e);
			die();
		}
		
		/* Order capturing code start here.  End */
	    
	   
	}

	/***********************************************************************
	** Function name : userValidate
	** Developed By  : Dilip Halder
	** Purpose       : This function used to user Validation 
	** Date 		 : 11 June 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function userValidate($users_id,$requestFrom)
	{  	
		$FieldList = array('users_id','status');
		$this->mongo_db->select($FieldList);
		$this->mongo_db->where(array('users_id'=>(int)$users_id));
		$UserData = $this->mongo_db->find_one('uw_users');

		if(empty($UserData)):
			if($requestFrom == "app"):
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('INVALID_USER'),$result);die();
			elseif($requestFrom == 'web'):
				$this->session->set_flashdata('alert_error',lang('INVALID_USER'));
		    	redirect('/');die();
			endif;

		elseif($UserData['status'] == 'I'):
			if($requestFrom == "app"):
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('ACCOUNT_INACTIVE'),$result);die();
			elseif($requestFrom == 'web'):
				$this->session->set_flashdata('alert_error',lang('ACCOUNT_INACTIVE'));
		    	redirect('/');die();
			endif;
		elseif($UserData['status'] == 'D' || $UserData['status'] == 'B' ):
			if($requestFrom == "app"):
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('ACCOUNT_BLOCKED'),$result);die();
			elseif($requestFrom == 'web'):
				$this->session->set_flashdata('alert_error',lang('ACCOUNT_BLOCKED'));
		    	redirect('/');die();
			endif;

		endif;
	}

	/***********************************************************************
	** Function name : orderDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orderDetails 
	** Date 		 : 11 June 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function orderDetails($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='')
	{  	
		if($searchBy && $searchValue ):
			if( is_numeric($searchValue) ):
				$whereCon[$searchBy]  = (int)$searchValue;
			else:
				$whereCon[$searchBy]  = $searchValue;
			endif;
		// else:
			// $whereCon['order_status'] = 'Success';
			// $whereCon['status']  		= 'A';
		endif;
			$whereCon['user_id']  	=  (int)$user_id;

		$SelectFields = array(
           "order_id"	=> 1,
           "product_id" => 1,
           "draw_id" => 1,
           "product_title" => 1,
           "product_qty" => 1,
           "prize_title" => 1,
           "total_price"=> 1,
           "user_id"    => 1,
           "payment_mode" =>1,
           "order_status" => 1,
           "ticket" => 1,
           "status"=> 1,
           "selection_values" => 1,
           "created_at" => 1,
           'draw_date_time' => array( '$concat' => array('$drawData.draw_date', ' ', '$drawData.draw_time') ),
           'app_image'	=> '$productData.app_image',
           'product_image' => '$productData.product_image',
           'text_field_1'  => '$productData.text_field_1',
           'text_field_2'  => '$productData.text_field_2',
           'text_field_3'  => '$productData.text_field_3',
           'straight_game_name'  => '$productData.straight_game_name',
           'rumble_game_name'    => '$productData.rumble_game_name',
           'reverse_game_name'   => '$productData.reverse_game_name',
           'draw' => 1,
           'winner_type' => 1,
           'delivery_charge' => 1,
           'reffle_prefix' 	 => '$productData.reffle_prefix',
           'reffle_length' 	 => '$productData.reffle_length',
           'raffle_mode'  	 => 1,
           'raffle_tickets'  => 1,
           'lotto_type' => '$productData.lotto_type',
           'lotto_range_end' 		=> '$productData.lotto_range_end',
           'straight_add_on_amount' => '$productData.straight_add_on_amount',
           'rumble_add_on_amount'   => '$productData.rumble_add_on_amount',
           'reverse_add_on_amount'  => '$productData.reverse_add_on_amount',
           'straight_settings_default_check'  => '$productData.straight_settings_default_check',
           'rumble_settings_default_check'    => '$productData.rumble_settings_default_check',
           'reverse_settings_default_check'   => '$productData.reverse_settings_default_check',
        );

        if($whereCon):
          $whereCondition  =  $whereCon;
        endif;

        if($date['from'] && $date['to']):
        	$whereCondition['created_at']  =  array('$gte' => $date['from'] , '$lte' => $date['to']);
        elseif($date['from']):
			$whereCondition['created_at']['$gte']  =  $date['from'];
		elseif($date['to']):
			$whereCondition['created_at']['$lte']  =  $date['to'];
		endif;

        $sortBy       = array('sequence_id' => -1);
        $tblName      = "uw_lotto_orders";
        $lookup       = array( 
    					array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'productData') ,
    					array('from'=>'uw_products_draw_records','localField'=>'draw_id','foreignField'=>'draw_id','as'=>'drawData'),
    					array('from'=>'uw_uwin_winner','localField'=>'order_id','foreignField'=>'order_id','as'=>'draw'),
					  );
        $unwind 	  = array('$productData','$drawData'); 
 		$result       = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
 		return $result;
	}

	/***********************************************************************
	** Function name : transactionDetails
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orderDetails 
	** Date 		 : 12 June 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function transactionDetails($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='',$uoid='')
	{  	
		if($searchBy && $searchValue ):
			if( is_numeric($searchValue) ):
				$whereCon[$searchBy]  = (int)$searchValue;
			else:
				$whereCon[trim($searchBy)]  = trim($searchValue);
			endif;
	
		endif;
		$whereCon  =  array(
							'$or' => array(
									array('user_id_cred' => (int)$user_id),
									array('user_id_deb' => (int)$user_id)
								),
							'user_oid' => new MongoDB\BSON\ObjectId($uoid)
						);

		$SelectFields = array(
           "load_balance_id" => 1,
           "user_id_cred"	 => 1,
           "user_id_deb"	 => 1,
           "order_id"	     => 1,
           "upoints"		 => 1,
           "availableArabianPoints"	=> 1,
           "end_balance"			=> 1,
           "record_type"			=> 1,
           "narration"				=> 1,
           "remarks"				=> 1,
           "created_at"				=> 1,
        );

        if($whereCon):
          $whereCondition  =  $whereCon;
        endif;

        if($date['from'] && $date['to']):
        	$whereCondition['created_at']  =  array('$gte' => $date['from'] , '$lte' => $date['to']);
        elseif($date['from']):
			$whereCondition['created_at']['$gte']  =  $date['from'];
		elseif($date['to']):
			$whereCondition['created_at']['$lte']  =  $date['to'];
		endif;

        $sortBy       = array('load_balance_id' => -1);
        $tblName      = "uw_loadBalance";
 		$result       = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
 		
 		return $result;
	}

	/***********************************************************************
	** Function name : winningHistory
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orderDetails 
	** Date 		 : 13 June 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function winningHistory($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='')
	{  	

    	// ---------------------------------------------------------//
		$Where1['where']['user_id']    =   (int)$user_id;
    	
    	if($date['from']):
			$Where1['where']['created_at']['$gte']  =  $date['from'];
		endif;

		if($date['to']):
			$Where1['where']['created_at']['$lte']  =  $date['to'];
		endif;

		if($searchBy && $searchValue):
			if(is_numeric($searchValue)):
			   $Where1['where']['$or']   = array( array( $searchBy => (float)$searchValue ), array($searchBy =>(int)$searchValue) ) ;
			else:
			   $Where1['where'][$searchBy]   = $searchValue;
			endif;
		endif;



    	$TBLNAME 		 			   = "uw_lotto_orders";
		$ORDER_ID	    = 'order_id';
		$ORDER_ID_ARRAY = $this->getFieldInArray($ORDER_ID,$TBLNAME,$Where1);
		// echo "<pre>";
		// print_r($ORDER_ID_ARRAY);
		// die();
		
	 	// echo "<pre>";print_r($ORDER_ID_ARRAY);die();
        // ---------------------------------------------------------//
		 
		$SelectFields = array(
			"order_id"       		=> 1,
			"winner_status"  		=> '$status',
			"winning_ticket" 		=> '$code',
			"winning_amount" 		=> '$amount',
			"winning_date"   		=> '$created_at',
			"straight_amount"   	=> '$orderData.straight_add_on_amount',
			"rumble_amount"    	 	=> '$orderData.rumble_add_on_amount',
			"reverse_amount"    	=> '$orderData.reverse_add_on_amount',
			"prize_title"    		=> '$orderData.prize_title',
			"order_status"    		=> '$orderData.order_status',
			"device_type"    		=> '$orderData.device_type',
			"app_version"    		=> '$orderData.app_version',
			"ticket"    			=> '$orderData.ticket',
			"cancelleatiion_status" => '$orderData.status',
			"payment_mode" 			=> '$orderData.payment_mode',
			"user_id" 				=> '$orderData.user_id',
			'products_id'			=> '$orderData.product_id',
			'products_image'		=> '$productData.product_image',
			'product_title'			=> '$productData.title',
			'product_app_image'	    => '$productData.app_image',
			'product_lotto_type'	=> '$productData.lotto_type',
        );

		if($ORDER_ID_ARRAY):
            $whereCondition    =  array('order_id' => array('$in'=> $ORDER_ID_ARRAY));
			$lookup     = array( 
	    					array('from'=>'uw_lotto_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'orderData') ,
	    					array('from'=>'uw_products','localField'=>'orderData.product_id','foreignField'=>'products_id','as'=>'productData') ,
						  );
	        $unwind 	= array('$orderData','$productData'); 
	        // $unwind 	= array('$orderData'); 

	        $tblName    = "uw_uwin_winner";
	        $sortBy     = array('voucher_id' => -1);
	 		$result     = $this->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
        endif;
       
 		return $result;
	}

	/***********************************************************************
	** Function name : winningGallery
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get winningGallery 
	** Date 		 : 14 June 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function winningGallery($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='')
	{  	

		if($searchBy && $searchValue ):
			if($searchBy == 'user_id' || $searchBy == 'product_id' || $searchBy == 'winning_ticket'):
				$whereCon['orderData.'.$searchBy]  = is_numeric($searchValue)?(int)$searchValue:$searchValue;
			elseif($searchBy == 'first_name' || $searchBy == 'last_name'):
				$whereCon['userData.'.$searchBy]  = is_numeric($searchValue)?(int)$searchValue:$searchValue;
			else:
				if( is_numeric($searchValue) ):
					$whereCon[$searchBy]  = (int)$searchValue;
				else:
					$whereCon[trim($searchBy)]  = trim($searchValue);
				endif;
			endif;
		endif;
		
		$whereCon['status']  = 'A';

		$SelectFields = array(
           "section_id" 	 => 1,
           "status"	 		 => 1,
           "winner_image"	 => 1,
           "order_id"	     => 1,
           "order_id"	     => 1,
           "winning_date"	 => '$winner.created_at',
           "winning_ticket"	 => '$winner.code',
           "users_id"	 	 => '$winner.code',
           "winning_amount"	 => array('$toDouble' => '$winner.amount'),
           "winner"	     => '$winner.code',
           'draw_date'	 => '$orderData.draw_date',
           'draw_time'	 => '$orderData.draw_time',
           "product_id"	 => '$orderData.product_id',
           "first_name"	 => '$userData.users_name',
           "last_name"	 => '$userData.last_name',
        );

        if($whereCon):
          $whereCondition  =  $whereCon;
        endif;

        if($date['from']):
			$whereCondition['winner.created_at']['$gte']  =  $date['from'];
		endif;

		if($date['to']):
			$whereCondition['winner.created_at']['$lte']  =  $date['to'];
		endif;

		$lookup     = array( 
    					array('from'=>'uw_uwin_winner','localField'=>'order_id','foreignField'=>'order_id','as'=>'winner') ,
    					array('from'=>'uw_lotto_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'orderData') ,
    					array('from'=>'uw_users','localField'=>'orderData.user_id','foreignField'=>'users_id','as'=>'userData') ,
				  	);
        $unwind 	= array('$userData','$winner','$orderData'); 


        $sortBy     = array('created_at' => -1);
        $tblName    = "uw_winners_gallery";
 		$result     = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
 		return $result;
	}

	public function cartValidation()
	{
		$ticket_range = $this->session->userdata('ticket_range');
		$ticket_mode  = $this->session->userdata('ticket_mode');
		$products_id  = $this->session->userdata('products_id');
		$users_id     = $this->session->userdata('users_id');

		if($ticket_range == '' || $ticket_mode == '' || $products_id == ''  ):
			redirect('/');
		elseif($users_id == ""):
			redirect('/login');
		endif;
	}

	public function cartDestory()
	{
		$cartArray = array('ticket_range', 'products_id','ticket_mode');
		$this->session->unset_userdata($cartArray);
	}

	// This function will return a random
	// string of specified length
	function random_strings($length_of_string)
	{
	    // String of all alphanumeric character
	    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	    // Shuffle the $str_result and returns substring
	    // of specified length
	    return substr(str_shuffle($str_result),0, $length_of_string);
	}

	/***********************************************************************
	** Function name : productSettings
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to control productSettings
	** Date 		 : 14 August 2024
	************************************************************************/
	public function productSettings($campaignData='')
	{
		$tblName  		 = 'uw_settings';
		$where['where']  =  array('status' => 'A');
		$global_setting	 =	$this->common_model->getData('single',$tblName,$where);

		
		$data['straight_settings']  = $campaignData['straight_settings'] ? $campaignData['straight_settings'] : $global_setting['straight_settings'];
		$data['rumble_settings']    = $campaignData['rumble_settings']   ? $campaignData['rumble_settings']   : $global_setting['rumble_settings'];
		$data['reverse_settings']   = $campaignData['reverse_settings']  ? $campaignData['reverse_settings']  : $global_setting['reverse_settings'];

		$data['straight_settings_default_check'] = $campaignData['straight_settings_default_check']  ? $campaignData['straight_settings_default_check']  : $global_setting['straight_settings_default_check'];
		$data['rumble_settings_default_check'] 	 = $campaignData['rumble_settings_default_check']    ? $campaignData['rumble_settings_default_check']    : $global_setting['rumble_settings_default_check'];
		$data['reverse_settings_default_check']  = $campaignData['reverse_settings_default_check']   ? $campaignData['reverse_settings_default_check']   : $global_setting['reverse_settings_default_check'];
		$data['game_rule_image']   = $campaignData['game_rule_image']   ? $campaignData['game_rule_image']   : $global_setting['game_rule_image'];
		$data['game_description']  = $campaignData['game_description']   ? $campaignData['game_description']   : $global_setting['game_description'];
		return $data;

	}

	/***********************************************************************
	** Function name : redeemRechargeCoupon
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used for redeem coupons..
	** Date 		 : 24 August 2024
	************************************************************************/
	public function redeemRechargeCoupon($userId = '', $coupon='',$plateform='')
	{	
		$Fieldslist           = array('users_id', 'status','is_verify','totalArabianPoints','availableArabianPoints');
		$tblName              = 'uw_users';
		$whereCon['where'] 	  = array('users_id' => (int)$userId);
		$userDetails          = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $whereCon);

				
	 	$tblName     = 'uw_coupon_code_only';
	  	$whereCon1['where']   = array('coupon_code' => is_numeric($coupon)? (int)$coupon : $coupon );
	  	$Fieldslist  = array('coupon_code', 'coupon_code_amount','coupon_code_statys','created_for_user_id','expair_date');
	 	$CouponData  = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $whereCon1);

	  	$currentDateDate = strtotime(date('Y-m-d'));
	  	$expairyDate     = strtotime($CouponData['expair_date']);
	 	// echo "<pre>";print_r($expairyDate);die();

	  	//Coupon code validation...
	  	if(empty($CouponData)):
	  		if($plateform == 'web'):
	 		 	$this->session->set_flashdata('alert_error', lang('INVALID_COUPON'));
	    	  	redirect('/wallets');
			  	die();
		 	else:
				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$result);die();
		 	endif;

	  	elseif($currentDateDate > $expairyDate || $CouponData['coupon_code_statys'] != 'Active' || !empty($CouponData['created_for_user_id']) && (int)$CouponData['created_for_user_id'] != (int)$userId ):
			if($plateform == 'web'):
	 		 	$this->session->set_flashdata('alert_error', lang('RECHARGE_CODE_EXPIRED'));
	    	  	redirect('/wallets');
			  	die();
		 	else:
				echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_CODE_EXPIRED'),$result);die();
		 	endif;
 	 	elseif($currentDateDate <= $expairyDate ):
	  	
		  	$coupon_code   			 = $CouponData['coupon_code']; 
		  	$coupon_amount 			 = $CouponData['coupon_code_amount']; 
		  	$totalArabianPoints      = $userDetails['totalArabianPoints']; 
		  	$availableArabianPoints  = $userDetails['availableArabianPoints']; 

		  	// Adding User data.. 
			$Userparam['totalArabianPoints']     = 	(float)$totalArabianPoints + $coupon_amount;
			$Userparam['availableArabianPoints'] = 	(float)$availableArabianPoints + $coupon_amount;
			$this->geneal_model->editData('uw_users',$Userparam,'users_id',(int)$userId);

			// Updating coupon data
			$CouponParam['coupon_code_statys']  = 'Redeemed'; 	 
			$CouponParam['redeemed_by']  		= (int)$userId; 	 
			$CouponParam['redeemed_date']  		= date('Y-m-d H:i'); 
			$couponCode 						= is_numeric($coupon)? (int)$coupon : $coupon;
			$this->geneal_model->editData('uw_coupon_code_only',$CouponParam,'coupon_code', $couponCode);


			$recharge_oid    = $CouponCodeData['_id']->{'$id'};
			$user_oid 	 = $userDetails['_id']['$id'];
		    // Commission capturing in order uw_loadbalance table..
		    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
		    $commissionParam["recharge_oid"] 			 =	new MongoDB\BSON\ObjectId($recharge_oid);
			$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
			$commissionParam["user_id_cred"] 			 =	(int)$userId;
			$commissionParam["user_id_deb"]			 	 =	(int)0;
			$commissionParam["upoints"] 				 =	(float)$coupon_amount;
			$commissionParam["availableArabianPoints"] 	 =	(float)$availableArabianPoints;
			$commissionParam["end_balance"] 			 =	(float)$availableArabianPoints+$coupon_amount;
		    $commissionParam["record_type"] 			 =	'Credit';
		    $commissionParam["narration"]				 =	'Recharge Coupon';
		    $commissionParam["remarks"]				 	 =	'Recharge Coupon Code : '.$coupon_code;
		    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
		    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
		    $commissionParam["created_by"] 			 	 =	(int)$userId;
		    $commissionParam["status"] 				 	 =	"A";
	    	$this->geneal_model->addData('uw_loadBalance', $commissionParam);
	    	// Credit the purchesed points and get available arabian points of user.

	    	if($plateform == 'web'):
	 		 	$this->session->set_flashdata('alert_success', lang('RECHARGE_SUCCESSFULLY'));
	    	  	redirect('/wallets');
			  	die();
		 	else:
				echo outPut(1,lang('SUCCESS_CODE'),lang('RECHARGE_SUCCESSFULLY'),$result);
		 	endif;
		endif;
	}

	/***********************************************************************
	** Function name : winningBalance
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show winningBalance.
	** Date 		 : 14 August 2024
	** Updated By	 : Dilip Halder
	** Updated Date	 : 17 October 2024
	************************************************************************/
	public function winningBalance($USERID='')
	{	
		/* Winning amount getting code Start */
		$tblName    		     = 'uw_lotto_orders';
		$Fieldslist 			 = 'order_id';
		$whereCondition['where'] = array( 'user_id' => (int)$USERID ,'order_status' =>  'Success' , 'status' => 'A' );
		$OrderIDs   			 = $this->common_model->getFieldInArray($Fieldslist,$tblName,$whereCondition);

		$tblName       	 		  = 'uw_uwin_winner';
		$whereCondition2['where'] =  array('order_id' =>  array('$in' => $OrderIDs)  ,'redeem_status'=> array('$ne' => "paid") ,'status' => (int)1 , 'soft_delete' => (int)0 );
		$winningHistory 		  = $this->common_model->getDataByNewQuery($Fieldslist,'multiple',$tblName,$whereCondition2);
		/* Winning amount getting code End */

		if($winningHistory):
			$totalAmount = 0;
			/* User Details code Start */
	 		$tableName	  		 = "uw_users";
		    $Fields 	 		 = array('_id','users_id' ,'availableArabianPoints','totalwinningBalance','winningBalance','totalArabianPoints');
		    $userDetails  		 = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);
		    
		    $totalArabianPoints  	= $userDetails['totalArabianPoints'];
		    $availableArabianPoints = $userDetails['availableArabianPoints'];
		    $totalwinningBalance 	= $userDetails['totalwinningBalance'];
		    $winningBalance 	 	= $userDetails['winningBalance'];
			/* User Details code End */
			
			foreach($winningHistory as $item):
				$viewAfterDateTime = date('Y-m-d H:i:s' , strtotime($item['created_at']. ' +30 Minutes'));
				$currentDate       = date('Y-m-d H:i:s');
				if($viewAfterDateTime <= $currentDate &&  empty($item['redeem_status']) && $item['redeem_status'] != 'paid'):

					$totalAmount += $item['amount'];
					$tickect_id   = $item['order_id'];
					
					$updateParams['redeem_status'] 	= 'paid';
					$updateParams['redeem_by_mode'] = 'UPoints';
					$updateParams["modified_at"]    = date('Y-m-d H:i');
					$updateParams['seller_id'] 		= (int)$USERID;
					$updateParams['created_ip'] 	= $this->input->ip_address();;
					$WInnner_whereCon    			= array('order_id' => $tickect_id , 'status' => (int)'1');
					$updatedstatus 					= $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
				endif;
			endforeach;

			// if($updatedstatus):
				$loadBalanceParam['load_balance_id'] =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                $loadBalanceParam['user_oid']        =   new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                $loadBalanceParam['user_id_deb']     =   (int)0;
                $loadBalanceParam['user_id_cred']    =   (int)$USERID;
                $loadBalanceParam['record_type']     =   'Credit';
                $loadBalanceParam['narration']       =   'Winning Amount';
                $loadBalanceParam['remarks']         =   'Prize amount ('.$totalAmount.') added to Winning balance';
             	$loadBalanceParam["availableArabianPoints"] =   (float)$availableArabianPoints;
				$loadBalanceParam["end_balance"] 		    =   (float)$availableArabianPoints + $totalAmount;
                $loadBalanceParam['upoints']         =   (float)$totalAmount;
                $loadBalanceParam['creation_ip']     =   $this->input->ip_address();;
                $loadBalanceParam['created_at']      =   date('Y-m-d H:i');
                $loadBalanceParam['created_by']      =   (int)$id;
                $loadBalanceParam['status']          =   'A';
                $this->geneal_model->addData('uw_loadBalance', $loadBalanceParam);

                //Crediting winning Amount
		        $param['totalArabianPoints']    	= (float)$totalArabianPoints + $totalAmount;
		        $param['availableArabianPoints']    = (float)$availableArabianPoints + $totalAmount;
		        $param['totalwinningBalance']       = (float)$totalwinningBalance + $totalAmount;
		        $param['winningBalance']            = (float)$winningBalance + $totalAmount;
		        $param['update_date']               = date('Y-m-d h:m');
		        $this->common_model->editData('uw_users',$param, 'users_id',(int)$USERID);
			// endif;
		endif;

        $tableName	 = "uw_users";
	    $Fields 	 = array('_id','users_id' ,'availableArabianPoints','totalwinningBalance','winningBalance');
	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);
        $userDetails['winningBalance'];
        $userDetails['availableArabianPoints'];
        return $userDetails;
	}

	/***********************************************************************
	** Function name : redeemwinningAMount
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show redeemwinningAMount.
	** Date 		 : 26 August 2024
	************************************************************************/
	public function redeemwinningAMount($USERID='',$amount='',$plateform="")
	{
		
		$tableName	 = "uw_users";
	    $Fields 	 = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','status');
	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

	    $winning_amount 			= $amount;
	    $availableWinningBalance	= $userDetails['winningBalance'];
	    $availableArabianPoints		= $userDetails['availableArabianPoints'];
	    $totalArabianPoints			= $userDetails['totalArabianPoints'];
	    $user_OId 	 				= $userDetails['_id']['$id'];

		if( (!empty($userDetails)  && $userDetails['status'] == 'A') && ( $availableWinningBalance >= $winning_amount) ):

			$availableArabianPoints  = $availableArabianPoints + $winning_amount;
			$availableWinningBalance = $availableWinningBalance - $winning_amount;
		 	 
            $uparam['totalArabianPoints']   	= (float)$totalArabianPoints + $winning_amount;
            $uparam['availableArabianPoints']   = (float)$availableArabianPoints;
            $uparam['winningBalance']   		= (float)$availableWinningBalance;
            $uparam['update_date']      		= date('Y-m-d h:m');
            $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$USERID);

            /* Load Balance Table -- after redeeming */
			$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
			$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($user_OId);
			$Redeemparam["user_id_deb"]              =   (int)0;
			$Redeemparam["user_id_cred"]             =   (int)$USERID;
			$Redeemparam["upoints"]       		     =   (float)$winning_amount;
			$Redeemparam["record_type"]              =   'Credit';
			$Redeemparam["narration"]  			     =   'Redeem Prize';
			$Redeemparam["remarks"]  			     =   "Redeemed Prize ".$winning_amount." AED in Upoints";
			$Redeemparam["availableArabianPoints"] 	 =   (float)$userDetails['availableArabianPoints'];
			$Redeemparam["end_balance"] 		 	 =   (float)$availableArabianPoints;
			$Redeemparam["creation_ip"]         	 =   currentIp();
			$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
			$Redeemparam["created_by"]         	  	 =   (int)$USERID;
			$Redeemparam["status"]               	 =   "A";
			$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
		    $this->geneal_model->addRedeem_Cash_Amount_TO_Seller($USERID,(float)$winning_amount);

		    if($plateform == 'web'):
				$this->session->set_flashdata('alert_success', lang('REDEEMED_WINNING_AMOUNT'));
	    	  	redirect('/wallets');
			elseif($plateform == 'app'):

				$tableName	 = "uw_users";
			    $Fields 	 = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','status','totalwinningBalance');
			    $userData 	 = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);
                
                $result 	 = array (
                    'totalArabianPoints'     => $userData['totalArabianPoints'],
                    'availableArabianPoints' => $userData['availableArabianPoints'],
                    'winningBalance'         => $userData['winningBalance'],
                    'totalWinningBalance'    => $userData['totalwinningBalance'],
                );
			 	echo outPut(1,lang('SUCCESS_CODE'),lang('REDEEMED_WINNING_AMOUNT'),$result);
			endif;

		elseif($userDetails['status'] === 'I' || $userDetails['status'] === 'D' || $userDetails['status'] === 'B' ):
			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('ACCOUNT_INACIVE'));
			  	redirect('/wallets');
			elseif($plateform == 'app'):
				 echo outPut(0,lang('FORBIDDEN_CODE'),lang('ACCOUNT_INACIVE'),$result);
			endif;
		else:

			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('LOW_WINNING_BALANCE'));
			  	redirect('/wallets');
			elseif($plateform == 'app'):
				 echo outPut(0,lang('FORBIDDEN_CODE'),lang('LOW_WINNING_BALANCE'),$result);
			endif;

		endif;
		

	  	die();

	}

	/***********************************************************************
	** Function name : withdrawWinningBalance
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show withdrawWinningBalance.
	** Date 		 : 26 August 2024
	************************************************************************/
	public function withdrawWinningBalance($USERID='',$POSTDATA='',$plateform="")
	{ 	
        $tableName   = "uw_users";
        $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','status');
        $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

		$wallet_type 				= $POSTDATA['type'];
        $amount             		= (float)$POSTDATA['amount'];
        $cripto_id             		= (int)$POSTDATA['cripto_id'];

        $account_holder_name        = $POSTDATA['account_holder_name'];
        $bank_name        			= $POSTDATA['bank_name'];
        $account_no        			= $POSTDATA['account_no'];
        $ifsc_code        			= $POSTDATA['ifsc_code'];

        $availableWinningBalance    = $userDetails['winningBalance'];
        $availableArabianPoints     = $userDetails['availableArabianPoints'];
        $totalArabianPoints         = $userDetails['totalArabianPoints'];
        $user_OId                   = $userDetails['_id']['$id'];
        $user_OId                   = $userDetails['_id']['$id'];

		if($wallet_type == 'Bank'): 
	 		$prams['type']                  =   $wallet_type;
            $prams['amount']                =   (float)$amount;
            $prams['account_holder_name']   =   $account_holder_name;
            $prams['bank_name']             =   $bank_name;
            $prams['account_no']            =   base64_encode($account_no);
            $prams['ifsc_code']             =   $ifsc_code;

	 	elseif($wallet_type === 'Cripto'):
            $prams['type']                  =   $wallet_type;
            $prams['amount']                =   (float)$amount;
            $prams['cripto_id']             =   $cripto_id;
        else:
            $prams['type']                  =   $wallet_type;
            $prams['amount']                =   (float)$amount;
            $prams['full_name']             =   $this->input->post('full_name');
            $prams['phone']                 =   $this->input->post('phone');
            $prams['country']               =   base64_encode($this->input->post('country'));
            $prams['city']                  =   $this->input->post('city');
        endif;
     	$prams['request_id']            	=   (int)$this->geneal_model->getNextSequence('uw_withdraw_requests');
        $prams['user_id']               	=   (int)$USERID;
        $prams['user_oid']              	=   new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
	 	$prams['withdraw_id']           	=    (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	 	$prams['creation_ip']           	=   $this->input->ip_address();
        $prams['created_at']            	=   date('Y-m-d H:i');
        $prams['created_by']           	 	=   (int)$USERID;
        $prams['status']                	=   'P';
        $result = $this->geneal_model->addData('uw_withdraw_requests', $prams);

        if($result):
            $availableArabianPoints 			 = (float)$userDetails['availableArabianPoints'] - (float)$amount;
            $winningBalance 		 			 = (float)$userDetails['winningBalance'] - (float)$amount;
            $uparam['availableArabianPoints']    = $availableArabianPoints;
            $uparam['winningBalance']      		 = $winningBalance;
            $uparam['update_date']          	 = date('Y-m-d h:m');
            $isInsert 							 = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$USERID);
            
            if($isInsert):
	            $debitRecord['load_balance_id'] =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	            $debitRecord['user_oid']        =   new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
	            $debitRecord['request_id']      =   $result['request_id'];
	            $debitRecord['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
	            $debitRecord['user_id_deb']     =   (int)$USERID;
	            $debitRecord['user_id_cred']    =   (int)0;
	            $debitRecord["availableArabianPoints"] 	 =   (float)$userDetails['availableArabianPoints'];
				$debitRecord["end_balance"] 		 	 =   (float)$userDetails['availableArabianPoints']-$amount;
	            $debitRecord['record_type']     =   'Debit';
	            $debitRecord['narration']       =   "Withdraw Request ( ".$wallet_type ." )";
	            $debitRecord['remarks']         =   'Winning amount '.$amount.' aed request sent for review.';
	            $debitRecord['upoints']         =   (float)$amount;
	            $debitRecord['winningBalance']  =   (float)$winningBalance;
	            $debitRecord['creation_ip']     =   $this->input->ip_address();;
	            $debitRecord['created_at']      =   date('Y-m-d H:i');
	            $debitRecord['created_by']      =   (int)$USERID;
	            $debitRecord['status']          =   'A';
	            $this->geneal_model->addData('uw_loadBalance', $debitRecord);

            endif;

        endif;

        if($plateform == 'web'):
			$this->session->set_flashdata('alert_success', lang('REQUESTED_SENT_TO_ADMIN'));
    	  	redirect('/wallets');
		  	die();
		elseif($plateform == 'app'):
			echo outPut(1,lang('SUCCESS_CODE'),lang('REQUESTED_SENT_TO_ADMIN'),$result);die();
		endif;
	}

	/***********************************************************************
	** Function name : generateWinnerVouvcher
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show generateWinnerVouvcher.
	** Date 		 : 26 August 2024
	************************************************************************/
	public function generateWinnerVouvcher($USERID='',$amount='',$plateform="")
	{
	
	   	$tableName	 = "uw_users";
	    $Fields 	 = array('_id','users_id' ,'availableArabianPoints','totalwinningBalance','winningBalance','status');
	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);
	    // echo "<pre>"; print_r($userDetails); die();

	    $winning_amount 			= $amount;
	    $availableWinningBalance	= $userDetails['winningBalance'];
	    $availableArabianPoints		= $userDetails['availableArabianPoints'];
	    $user_OId 	 				= $userDetails['_id']['$id'];

		if( ( !empty($userDetails) && $userDetails['status'] == 'A' ) && ( $availableArabianPoints >= $winning_amount ) && ( $availableWinningBalance >= $winning_amount && $winning_amount >= 100 ) ):

			$availableArabianPoints  			= $availableArabianPoints  - $winning_amount;
			$availableWinningBalance 			= $availableWinningBalance - $winning_amount;
            $uparam['availableArabianPoints']  	= (float)$availableArabianPoints;
            $uparam['winningBalance']   		= (float)$availableWinningBalance;
            $uparam['update_date']      		= date('Y-m-d h:m');
            $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$USERID);

			$coupon_code_length = 12; // Length of the coupon code
            $code = generateRandomString($coupon_code_length, "n");
            $isDuplicate = true; // Flag to track duplicates
            
            while ($isDuplicate) {
                $whereCon['where'] = array('coupon_code' => (int)$code);
                $DuplicateCoupn = $this->common_model->getData('single', 'uw_cash_vouchers', $whereCon);

                if (empty($DuplicateCoupn)) {
                    $isDuplicate = false; // No duplicate found
                } else {
                    // Regenerate the code
                    $code = generateRandomString($coupon_code_length, "n");
                }
            }

			$param['voucher_id']		= (int)$this->common_model->getNextSequence('uw_cash_vouchers');
			$param['coupon_code'] 		= (int)$code;
			$param['verification_code'] = (int)rand(1111,9999);
			$param['amount'] 			= (float)$winning_amount;
            $param['users_id']      	= (int)$USERID;
            $param['user_oid']      	= new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
            $param['status']        	= 'A';
            $param['creation_ip']       = $this->input->ip_address();;
            $param['created_at']        = date('Y-m-d H:i');
            $param['created_date']      = (int)$this->timezone->utc_time();
            $param['created_by']        = (int)$USERID;
            $result = $this->geneal_model->addData('uw_cash_vouchers', $param);

            $loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
            $loadBalance['user_oid']        =  new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
            $loadBalance['request_id']      =  $param['voucher_id'];
            $loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
            $loadBalance['user_id_deb']     =  (int)$USERID;
            $loadBalance['user_id_cred']    =  (int)0;
            $loadBalance["availableArabianPoints"] =   (float)$userDetails['availableArabianPoints'];
			$loadBalance["end_balance"] 		   =   (float)$userDetails['availableArabianPoints'] - $winning_amount;
            $loadBalance['record_type']     = 'Debit';
            $loadBalance['narration']       = 'Cash Voucher';
            $loadBalance['remarks']         = 'Winning amount '.$winning_amount.' AED moved on cash voucher ('.$code.').';
            $loadBalance['upoints']         = (float)$winning_amount;
            $loadBalance['winningBalance']  = (float)$availableWinningBalance - $winning_amount;
            $loadBalance['creation_ip']     = $this->input->ip_address();;
            $loadBalance['created_at']      = date('Y-m-d H:i');
            $loadBalance['created_by']      = (int)$USERID;
            $loadBalance['status']          = 'A';
            $this->geneal_model->addData('uw_loadBalance', $loadBalance);

            if($plateform == 'web'):
				$this->session->set_flashdata('alert_success', lang('VOUCHER_CREATED_SUCCESSFULLY'));
			  	redirect('/wallets');
			elseif($plateform == 'app'):
				 echo outPut(1,lang('SUCCESS_CODE'),lang('VOUCHER_CREATED_SUCCESSFULLY'),$result);
			endif;

		elseif($userDetails['status'] === 'I' || $userDetails['status'] === 'B' || $userDetails['status'] === 'D' ):

			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('ACCOUNT_INACIVE'));
			  	redirect('/wallets');
			elseif($plateform == 'app'):
				 echo outPut(0,lang('FORBIDDEN_CODE'),lang('ACCOUNT_INACIVE'),$result);die();
			endif;

	 	elseif( $availableWinningBalance < $winning_amount ):
        	
        	$error_msg = str_replace('###AMOUNT###', $winning_amount ,  lang('LOW_AVAILABLE_WINNING_BALANCE'));
        	if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', $error_msg);
			  	redirect('/wallets');
			elseif($plateform == 'app'):
				 echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg ,$result);die();
			endif;

		elseif( $availableArabianPoints < $winning_amount ):
            $error_msg = str_replace('###AMOUNT###', $winning_amount , lang('LOW_AVAILABLE_BALANCE_TO_REDEEM'));
            echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg,$result);die(); 
             
		else:
			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('MIN_WITHDRAW_AMOUNT_ERROR'));
			  	redirect('/wallets');
			elseif($plateform == 'app'):
            	 echo outPut(0,lang('SUCCESS_CODE'),lang('MIN_WITHDRAW_AMOUNT_ERROR'),$result);die();
			endif;
		endif;
	}

	/***********************************************************************
	** Function name : transactionHistory
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show transactionHistory.
	** Date 		 : 26 August 2024
	************************************************************************/
	public function transactionHistory($USERID='',$searchData='')
	{	

		//getting usersID
		$tblName     = 'uw_users';
		$userID      = $this->common_model->getSingleDataByParticularField(array('_id'),$tblName, 'users_id', (int)$USERID);
		$uoid        = $userID['_id']['$id'];

		$resultType  = 'count';
		$totalcount  = $this->transactionDetails($USERID,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date,$uoid);

		$itemsPerPage = $searchData['itemsPerPage']?$searchData['itemsPerPage']:1 ;
		$page = $searchData['pageno']?$searchData['pageno'] : 1;
		// Sample long array with data
		$longArray = $totalcount;
		// Calculate total number of pages
		$totalPages = ceil($longArray / $itemsPerPage);
		$totalpage= array();
		// Pagination links
		for ($i = 1; $i <= $totalPages; $i++) {
		    if ($i == $page) {
		         $current_page = $i;
		         $totalpage[] = $i;
		    } else {
		         $totalpage[] = $i;
		    }
		}

		$startIndex    = ($page - 1) * $itemsPerPage;
 		$resultType    = '';
 		$transactionHistory  = $this->transactionDetails($USERID,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date,$uoid);
	    
	    if(!empty($transactionHistory)):
	    	$totalRows 			= $itemsPerPage*$totalPages;
			$baseUrl   			= base_url('wallets');
	    	$result['current_page'] = $current_page;
			$result['transactionHistory'] = $transactionHistory;
		    $result['pagination']   = Pagination($baseUrl,$page,$totalPages ,$itemsPerPage,$totalRows);
        	return $result;
	    endif;
	}	

	/***********************************************************************
	** Function name : vocherHistory
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used to show vocherHistory.
	** Date 		 : 26 August 2024
	************************************************************************/
	public function vocherHistory($USERID='',$searchData='')
	{	
		$whereCon['where'] = array('users_id'=> (int)$USERID);
		$tblName  		   = 'uw_cash_vouchers';
		$totalcount 	   = $this->common_model->getData('count',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex,$date);

		$itemsPerPage = $searchData['itemsPerPage']?$searchData['itemsPerPage']:1 ;
		$page = $searchData['pageno']?$searchData['pageno'] : 1;
		// Sample long array with data
		$longArray = $totalcount;
		// Calculate total number of pages
		$totalPages = ceil($longArray / $itemsPerPage);
		$totalpage  = array();
		// Pagination links
		for ($i = 1; $i <= $totalPages; $i++) {
		    if ($i == $page) {
		         $current_page = $i;
		         $totalpage[] = $i;
		    } else {
		         $totalpage[] = $i;
		    }
		}


		$startIndex    = ($page - 1) * $itemsPerPage;
 		$resultType    = '';
		$CashVouchers  = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex,$date);
		 if(!empty($CashVouchers)):
	    	$totalRows 			= $itemsPerPage*$totalPages;
			$baseUrl   			= base_url('wallets');
	    	$result['current_page'] = $current_page;
			$result['CashVouchers'] = $CashVouchers;
		    $result['pagination']   = Pagination($baseUrl,$page,$totalPages ,$itemsPerPage,$totalRows);
        	return $result;
	    endif;
		// echo "<pre>";print_r($baseUrl);die();
	}

	/***********************************************************************
	** Function name : getCashVouchers
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get getCashVouchers 
	** Date 		 : 29 August 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function getCashVouchers($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='')
	{  	
		if($searchBy && $searchValue ):
			if( is_numeric($searchValue) ):
				$whereCon[$searchBy]  = (int)$searchValue;
			else:
				$whereCon[$searchBy]  = $searchValue;
			endif;
		// else:
			// $whereCon['order_status'] = 'Success';
			// $whereCon['status']  		= 'A';
		endif;
		$whereCon['$or'] = array( array('users_id'=> (int)$user_id)  ,array('seller_id'=> (int)$user_id) ); 

		$SelectFields = array(
		  'voucher_id'  => 1,
		  'coupon_code' => 1,
		  'amount' 		=> 1,
		  'users_id' 	=> 1,
		  'status' 		=> 1,
		  'created_at'  => 1,
		  'verification_code' => 1,
		  'update_date' => 1,
		  'seller_id' => 1,
        );

        if($whereCon):
          $whereCondition  =  $whereCon;
        endif;

        if($date['from'] && $date['to']):
        	$whereCondition['update_date']  =  array('$gte' => $date['from'] , '$lte' => $date['to']);
        elseif($date['from']):
			$whereCondition['update_date']['$gte']  =  $date['from'];
		elseif($date['to']):
			$whereCondition['update_date']['$lte']  =  $date['to'];
		endif;

        $sortBy       = array('update_date' => -1);
        $tblName      = "uw_cash_vouchers";
        $result       = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
 		return $result;
	}


	/***********************************************************************
	** Function name : getSellerCashVouchers
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get getSellerCashVouchers 
	** Date 		 : 04 November 2024
	** Updated By    :  
	** Updated Date  :  
	************************************************************************/
	public function getSellerCashVouchers($user_id,$searchBy='',$searchValue="",$itemsPerPage,$startIndex,$resultType,$date='')
	{  	
		if($searchBy && $searchValue ):
			if( is_numeric($searchValue) ):
				$whereCon[$searchBy]  = (int)$searchValue;
			else:
				$whereCon[$searchBy]  = $searchValue;
			endif;
		// else:
			// $whereCon['order_status'] = 'Success';
			// $whereCon['status']  		= 'A';
		endif;

		if($searchBy != "redeemed_by"):
			$whereCon['seller_id']  	    =  (int)$user_id;
		endif;

		$SelectFields = array(
		  'voucher_id'  => 1,
		  'coupon_code' => 1,
		  'amount' 		=> 1,
		  'users_id' 	=> 1,
		  'status' 		=> 1,
		  'created_at'  => 1,
		  'verification_code' => 1,
		  'update_date' => 1,
		  'seller_id' => 1,
        );

        if($whereCon):
          $whereCondition  =  $whereCon;
        endif;

        if($date['from'] && $date['to']):
        	$whereCondition['created_at']  =  array('$gte' => $date['from'] , '$lte' => $date['to']);
        elseif($date['from']):
			$whereCondition['created_at']['$gte']  =  $date['from'];
		elseif($date['to']):
			$whereCondition['created_at']['$lte']  =  $date['to'];
		endif;


        $sortBy       = array('created_at' => -1);
        $tblName      = "uw_cash_vouchers";
        $result       = $this->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
 		return $result;
	}

	/***********************************************************************
	** Function name : redeemCouponVoucher
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get redeemCouponVoucher 
	** Date 		 : 30 August 2024
	************************************************************************/
	public function redeemCouponVoucher($userId='',$coupon='',$plateform='')
	{
	 
		$Fieldslist           = array('users_id', 'status','is_verify','totalArabianPoints','availableArabianPoints','referrel_amount','unlocking_referrel_amount');
		$tblName              = 'uw_users';
		$whereCon['where'] 	  = array('users_id' => (int)$userId);
		$userDetails          = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $whereCon);

		if($userDetails['status'] == 'I' || $userDetails['status'] == 'B' || $userDetails['status'] == 'D' || $userDetails['is_verify'] == 'N' ):
			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('ACCOUNT_NOT_VERIFY'));
	    	  	redirect('/wallets');
			  	die();
			elseif($plateform == 'app'):
				echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_NOT_VERIFY'),$result);die();
			endif;
		elseif(!empty($userDetails) &&  $userDetails['status'] == 'A' ):
			
		  $tblName     = 'uw_coupon_code_only';
		  $whereCon1['where']   = array('coupon_code' => is_numeric($coupon)? (int)$coupon : $coupon );
		  $Fieldslist  = array('coupon_code', 'coupon_code_amount','coupon_code_statys','created_for_user_id','expair_date');
		  $CouponData  = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $whereCon1);
		  // echo "<pre>";print_r($CouponData);die();

		  $currentDateDate = strtotime(date('Y-m-d'));
		  $expairyDate     = strtotime($CouponData['expair_date']);

		  //Coupon code validation...
		  if(empty($CouponData)):
		  	if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('INVALID_COUPON'));
	    	  	redirect('/wallets');
			  	die();
			elseif($plateform == 'app'):
				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$result);die();
			endif;



		  elseif($currentDateDate > $expairyDate || $CouponData['coupon_code_statys'] != 'Active' || !empty($CouponData['created_for_user_id']) && (int)$CouponData['created_for_user_id'] != (int)$userId ):

			if($plateform == 'web'):
				$this->session->set_flashdata('alert_error', lang('RECHARGE_CODE_EXPIRED'));
	    	  	redirect('/wallets');die();
			elseif($plateform == 'app'):
				echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_CODE_EXPIRED'),$result);die();
			endif;

		  elseif($currentDateDate <= $expairyDate ):
		  	$coupon_code   			 = $CouponData['coupon_code']; 
		  	$coupon_amount 			 = $CouponData['coupon_code_amount']; 
		  	$totalArabianPoints      = $userDetails['totalArabianPoints']; 
		  	$availableArabianPoints  = $userDetails['availableArabianPoints']; 

		  	// Adding User data.. 
			$Userparam['totalArabianPoints']     = 	(float)$totalArabianPoints + $coupon_amount;
			$Userparam['availableArabianPoints'] = 	(float)$availableArabianPoints + $coupon_amount;
			$this->geneal_model->editData('uw_users',$Userparam,'users_id',(int)$userId);

			// Updating coupon data
			$CouponParam['coupon_code_statys']  = 'Redeemed'; 	 
			$CouponParam['status']  			= 'C'; 	 
			$CouponParam['redeemed_by']  		= (int)$userId; 	 
			$CouponParam['modified_at']  		= date('Y-m-d H:i'); 
			$couponCode 						= is_numeric($coupon)? (int)$coupon : $coupon;
			// $couponCode 						= $coupon;
			$this->geneal_model->editData('uw_coupon_code_only',$CouponParam,'coupon_code', $couponCode);

			if(!empty($userDetails['referrel_amount']) && $userDetails['referrel_amount'] > 0):
	            $this->referralAmountRelease($userId,$coupon_amount);
	        endif;

			$recharge_oid    = $CouponCodeData['_id']->{'$id'};
			$user_oid 	 = $userDetails['_id']['$id'];
		    // Commission capturing in order uw_loadbalance table..
		    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
		    $commissionParam["recharge_oid"] 			 =	new MongoDB\BSON\ObjectId($recharge_oid);
			$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
			$commissionParam["user_id_cred"] 			 =	(int)$userId;
			$commissionParam["user_id_deb"]			 	 =	(int)0;
			$commissionParam["upoints"] 				 =	(float)$coupon_amount;
			$commissionParam["availableArabianPoints"] 	 =	(float)$availableArabianPoints;
			$commissionParam["end_balance"] 			 =	(float)$availableArabianPoints+$coupon_amount;
		    $commissionParam["record_type"] 			 =	'Credit';
		    $commissionParam["narration"]				 =	'Recharge Coupon';
		    $commissionParam["remarks"]				 	 =	'Recharge Coupon Code : '.$coupon_code;
		    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
		    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
		    $commissionParam["created_by"] 			 	 =	(int)$userId;
		    $commissionParam["status"] 				 	 =	"A";
	    	$this->geneal_model->addData('uw_loadBalance', $commissionParam);
	    	// Credit the purchesed points and get available arabian points of user.
			if($plateform == 'web'):
	    	  	$success_msg = str_replace('###AMOUNT###', $coupon_amount ,  lang('RECHARGE_SUCCESSFULLY'));
				$this->session->set_flashdata('alert_success', $success_msg );
	    	  	redirect('/wallets'); die();

			elseif($plateform == 'app'):
				$result = [];
	    	  	$success_msg = str_replace('###AMOUNT###', $coupon_amount ,  lang('RECHARGE_SUCCESSFULLY'));
				echo outPut(1,lang('SUCCESS_CODE'),$success_msg ,$result);die();
			endif;

		  endif;
		else:
			echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
		endif;
	}

	
	/***********************************************************************
	** Function name : topUpAmount
	** Developed By  : Dilip Halder
	** Purpose       : This function used to topUpAmount 
	** Date 		 : 30 August 2024
	** Updated By    : Dilip Halder
	** updated Date  : 12-09-2024
	************************************************************************/
	public function topUpAmount($userId='',$amount='',$plateform='')
	{

		$device_type = $plateform =='web'?'web':'app';

	 	$request_id = (int)$this->geneal_model->getNextSequence('uw_topup');
	 	$Param["request_id"]  = (int)$request_id;
	 	$Param["users_id"]	  =	(int)$userId;
	 	$Param["amount"]	  =	(int)$amount;
	    $Param["creation_ip"] =	$this->input->ip_address();
	    $Param["created_at"]  =	date('Y-m-d H:i:s');
	    $Param["status"] 	  =	"P";
	    $Param["device_type"] =	$device_type;
    	$this->geneal_model->addData('uw_topup', $Param);

		$encData     = array('amount' =>(int)$amount, 'user'=>(int)$userId,'request_id' => (int)$request_id,'device_type' => $device_type );
		$RequestData = rtrim(base64_encode(json_encode($encData)),"=");
		if($plateform == 'app'):
			$result['payment_url'] = 'https://buy2day.global/payment/pay-online/'.$RequestData;
			return $result;
			die();
		else:
	    	redirect('https://buy2day.global/payment/pay-online/'.$RequestData);
	    	// redirect('http://localhost/payment/pay-online/'.$RequestData);
		endif;
	}

	/***********************************************************************
	** Function name : pageContent
	** Developed By  : Dilip Halder
	** Purpose       : This function used to pageContent 
	** Date 		 : 04 October 2024
	************************************************************************/
	public function pageContent($searchData ='')
	{
		
		$pageno  	  = $searchData['pageno'];
		$created_date = $searchData['created_date'];
		$itemsPerPage = $searchData['itemsPerPage'];
		$startIndex   = $searchData['startIndex'] -1;
		
    	$shortField 	= array('content_id' => -1);
    	$selectFields 	= array('image');

	 	$tblName    								   = 'uw_contents';
    	$whereCon['where']['status']    			   = 'A';
    	$whereCon['where']['added_for'] 			   = array('$all' => array('Result Page'));
    	$whereCon['where']['added_for_result_page']    = array('$all' => array('Website'));
    	
    	if($created_date):
    		$whereCon['where']['created_date'] = $created_date;
    	endif;

    	$totalcount = $this->common_model->getData('count',$tblName,$whereCon);
		// Current page number (received from URL query parameter, e.g., ?page=2)
		$page = isset($pageno) ? (int)$pageno : 1;

		// Calculate total number of pages
		$totalPages = ceil($totalcount / $itemsPerPage);
		$totalpage= array();
		// Pagination links
		for ($i = 1; $i <= $totalPages; $i++) {
		    if ($i == $page) {
		         $current_page = $i;
		         $totalpage[] = $i;
		    } else {
		         $totalpage[] = $i;
		    }
		}
 		
 		$startIndex  = ($page - 1) * $itemsPerPage;
 		$resultType  = '';
 		$pagecontent  = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex,$selectFields);;
		$totalpage 				    = count($totalpage);
		$result['page_content'] 	= $pagecontent?$pagecontent:array();
		$result['current_page']     = $current_page;
		$result['total_page'] 	    =   $totalpage;

    	return $result;
	}

	/***********************************************************************
	** Function name : getCashSummery
	** Developed By  : Dilip Halder
	** Purpose       : This function used to getCashSummery 
	** Date 		 : 13 November 2024
	************************************************************************/
	public function getCashSummery($UserData ='')
	{
	    $tblName = 'uw_loadBalance';
		$whereCondition['user_oid']    = new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});

		if($UserData['from']):
			$whereCondition['created_at']['$gte']    =  $UserData['from'];
		endif;
		if($UserData['to']):
			$whereCondition['created_at']['$lte']    =  $UserData['to'];
		endif;
		 
		if($this->input->post('search_by') && $this->input->post('search_value')):
			$whereCondition[$this->input->post('search_by')] = is_numeric($this->input->post('search_value'))?(int)$this->input->post('search_value') : $this->input->post('search_value');
		endif;

		// Define the aggregation pipeline
		$pipeline = [
		    // Match stage to filter documents based on the condition
		    ['$match' => $whereCondition],
		    // Group stage to calculate counts and sums for each coupon status
		    ['$group' => [
		        '_id' => '$users_id',
	           'total_upoint_transfer' => array(
				    '$sum' => array(
				        '$cond' => array(
				            array(
				                '$and' => array(
				                    array('$eq' => array('$narration', 'Recharge')),
				                    array('$eq' => array('$record_type', 'Debit')),
				                    array('$eq' => array('$status', 'A')),
				                ),
				            ),
				            '$upoints',
				            0
				        )
				    )
				),
		       'total_cancelled_upoint_transfer' => array(
				    '$sum' => array(
				        '$cond' => array(
				            array(
				                '$and' => array(
				                    array('$eq' => array('$narration', 'Recharge')),
				                    array('$eq' => array('$record_type', 'Debit')),
				                    array('$eq' => array('$status', 'R')),
				                ),
				            ),
				            '$upoints',
				            0
				        )
				    )
				),
	        	
	           'total_upoints_sold' => array(
				    '$sum' => array(
				        '$cond' => array(
				            array(
				                '$and' => array(
				                    array('$eq' => array('$narration', 'Recharge Coupon')),
				                    array('$eq' => array('$record_type', 'Debit')),
				                    array('$eq' => array('$status', 'A')),
				                )
				            ),
				            '$upoints',
				            0
				        )
				    )
				),

	           'total_cancelled_upoints_sold' => array(
				    '$sum' => array(
				        '$cond' => array(
				            array(
				                '$and' => array(
				                    array('$eq' => array('$narration', 'Recharge Coupon')),
				                    array('$eq' => array('$record_type', 'Debit')),
				                    array('$eq' => array('$status', 'R')),
				                )
				            ),
				            '$upoints',
				            0
				        )
				    )
				),

	           'total_upoints_redeemed' => array(
				    '$sum' => array(
				        '$cond' => array(
				            array(
				                '$and' => array(
				                    array('$eq' => array('$narration', 'Cash Prize Redeem')),
				                    array('$eq' => array('$record_type', 'Credit')),
				                    array('$eq' => array('$status', 'A')),

				                )
				            ),
				            '$upoints',
				            0
				        )
				    )
				),
		       'redeemed_referrel_amount' => array(
				    '$sum' => array(
				        '$cond' => array(
	                     	array(
				                '$and' => array(
				                    array('$eq' => array('$status', 'A')),
				                    array('$in' => array('$narration', array('Referrel Commission')))
				                ),
				            ),
				            '$upoints',
				            0
				        )
				    )
				),
		       'total_commisson' => array(
				    '$sum' => array(
				        '$cond' => array(
	                     	array(
				                '$and' => array(
				                    array('$eq' => array('$status', 'A')),
				                    array('$in' => array('$narration', array('Recharge Commission','Referrel Commission')))

				                ),
				            ),
				            '$upoints',
				            0
				        )
				    )
				),
		       'total_cancelled_commisson' => array(
				    '$sum' => array(
				        '$cond' => array(
	                     	array(
				                '$and' => array(
				                    array('$eq' => array('$status', 'R')),
				                    array('$in' => array('$narration', array('Recharge Commission')))

				                ),
				            ),
				            '$upoints',
				            0
				        )
				    )
				),

		    ]],


		    ['$addFields' => [
		        'total_due' => [
	            	

	            	'$subtract' => [ [ '$add' => ['$total_upoint_transfer', '$total_upoints_sold'] ] ,  [ '$add' => ['$total_commisson', '$total_upoints_redeemed'] ] ]
		        ]
		    ]],

		    
		    // Project stage to select specific fields
		     array('$project' =>  array(		        
		    	'_id'         				 		   => 0,
		    	'total_upoint_transfer'      		   => 1,
		    	'total_cancelled_upoint_transfer'      => 1,
		    	'total_upoints_sold'      		   	   => 1,
		    	'total_cancelled_upoints_sold'         => 1,
		    	'total_upoints_redeemed' 			   => 1, // referral commission..
		    	'redeemed_referrel_amount' 			   => 1, // referral commission..
		    	'total_commisson'      				   => 1,
		    	'total_cancelled_commisson'      	   => 1,
		    	'total_due'      	   				   => 1,
		    ))


		];


		// Execute the aggregation
		$resultData = $this->mongo_db->aggregate($tblName, $pipeline, ['batchSize' => 10]);
		// $resultData['0']['redeemed_referrel_amount'] = (int)$UserData['redeemed_referrel_amount'];
	 	
		return $resultData;
	}
	
	/***********************************************************************
	** Function name : getRaffleSummery
	** Developed By  : Dilip Halder
	** Purpose       : This function used for getRaffleSummery 
	** Date 		 : 24 January 2025
	************************************************************************/
	public function getRaffleSummary($whereCondition = array(),$product_title = '')
	{
	    $tblName = 'uw_loadBalance';
	    // Define the aggregation pipeline
	    $pipeline = [
	        // Match stage to filter documents based on the condition
	        ['$match' => $whereCondition],
	        // Lookup stage to join with the uw_products collection and fetch only product_id
	        [
	            '$lookup' => [
	                'from' => 'uw_products',
	                'localField' => 'product_oid',
	                'foreignField' => '_id',
	                'pipeline' => [
	                    [
	                        '$project' => [
	                            '_id' 		               => 0,  
	                            'products_id' 			   => 1,  
	                            'title' 	  			   => 1 ,
	                            'straight_add_on_amount'   => 1 ,
	                            'product_image'   		   => 1 ,
	                            'enable_raffle_ticket'     => 1 ,
	                        ]
	                    ]
	                ],
	                'as' => 'product'
	            ]
	        ],
	        ['$unwind' => '$product'],
         	// Project stage to select specific fields
	        [
	            '$project' => [
	                '_id'	 		=> 0,
	                'upoints' 		=> 1,
	                'load_balance_id'=> 1,
	                'order_oid' 	=> 1,
	                'user_oid' 		=> 1,
	                'product_oid' 	=> 1,
	                'product_qty' 	=> 1,
	                'user_id_deb' 	=> 1,
	                'narration'     => 1,
	                'created_at'    => 1,
	                'record_type'   => 1,
	                'status' 		=> 1,
	                'product_id'    => '$product.products_id',
	                'product_title' => '$product.title',
	                'price' 		=> '$product.straight_add_on_amount',
	                'product_image' => '$product.product_image',
	                'enable_raffle_ticket' => '$product.enable_raffle_ticket',

	            ]
	        ],
	        [
            	'$match' => [
            		"product_title" => isset($product_title) ? trim($product_title) : ['$exists' => true],
            		"enable_raffle_ticket" => "Enable"
            	]
            ],
            ['$addFields' => [
                'total_due' => '$total_raffle_prize_redeemed'
            ]],
	        ['$group' => [
                '_id'  	     		 => '$product_title',

               'product_id'       	 => array('$first'  => '$product_id'),
               'product_title'       => array('$first'  => '$product_title'),
               'product_image'       => array('$first' => '$product_image'),
               'sales_count' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$narration', 'Raffle Order')),
                                    array('$eq' => array('$record_type', 'Debit')),
                                    array('$eq' => array('$status', 'A')),
                                ),
                            ),
                            '$product_qty',
                            0
                        )
                    )
                ),
                'price' => array('$first' => '$price'),
                'sales' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$narration', 'Raffle Order')),
                                    array('$eq' => array('$record_type', 'Debit')),
                                    array('$eq' => array('$status', 'A')),
                                ),
                            ),
                            '$upoints',
                            0
                        )
                    )
                ),
                'commissionAmount' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$status', 'A')),
                                    array('$eq' => array('$narration', 'Raffle Commission')),
                                ),
                            ),
                            '$upoints',
                            0
                        )
                    )
                ),

                'totalcancelOrderAmount' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$narration', 'Raffle Order')),
                                    array('$eq' => array('$record_type', 'Debit')),
                                    array('$eq' => array('$status', 'CL')),
                                ),
                            ),
                            '$upoints',
                            0
                        )
                    )
                ),
                'commissionCanceledAmount' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$status', 'CL')),
                                    array('$eq' => array('$narration', 'Raffle Commission')),
                                ),
                            ),
                            '$upoints',
                            0
                        )
                    )
                ),
                'totalCustomerPaid' => array(
                    '$sum' => array(
                        '$cond' => array(
                            array(
                                '$and' => array(
                                    array('$eq' => array('$narration', 'Raffle Cash Prize Redeem')),
                                    array('$eq' => array('$record_type', 'Debit')),
                                    array('$eq' => array('$status', 'A')),
                                )
                            ),
                            '$upoints',
                            0
                        )
                    )
                ),
            ]],
            ['$addFields' => [
                'total_due' => '$total_raffle_prize_redeemed'
            ]],
            ['$sort' => ['product_id'=> -1]],
	    ];


	    // echo "<pre>";
	    // print_r($pipeline);
	    // die();

	    // Execute the aggregation
	    $resultData = $this->mongo_db->aggregate($tblName, $pipeline, ['batchSize' => 10]);

	    return $resultData;
	}

	/***********************************************************************
	** Function name : getRaffleCampaignReport
	** Developed By  : Dilip Halder
	** Purpose       : This function used for get data by query
	** Date          : 12 February 2024
	************************************************************************/
	public function getRaffleCampaignReport($action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt=''){
		$filterArray 	 =	array();

		if($wcon['where']):
			foreach($wcon['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		if($wcon['where_gte']):
	        foreach($wcon['where_gte'] as $where_key => $where_value):
	            array_push($filterArray,array($where_value[0]=> array('$gte' => $where_value[1])));
	        endforeach;
	    endif;

	    if($wcon['where_lte']):
	        foreach($wcon['where_lte'] as $where_key => $where_value):
	            array_push($filterArray,array($where_value[0]=> array('$lte' => $where_value[1])));
	        endforeach;
	    endif;

		$selectFields    = array( '$project' => array(
							'_id'     =>0,
							'order_id'=>1,
							'user_id'=>1,
							'product_id'=>1,
							'user_type'=>1,
							'user_email'=>1,
							'user_phone'=>1,
							'product_title'=>1,
					      	"product_qty"=> array( '$toInt' => '$product_qty'),
							'total_price'=>1,
							'prize_title'=>1,
							'status'=>1,
							'product_image'=>'$product.product_image',
							'product_price'=>'$product.straight_add_on_amount',
							'end_balance' =>1,
							'payment_mode' =>1,
							'created_at' =>1,
							'raffle_mode' => 1,
							'user_oid' => 1,
					));

		$whereCondition =	array();



		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'product')),
												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
											  	  array('$unwind' => '$product_image' ),
											  	  array(
										  	  		'$group' => array(
							  	  						'_id' 			=> '$product_title' ,
				  		                    			'price'   		=> array('$first' => '$product_price'),
				  		                    			'sales_count'	=> array('$sum' =>  '$product_qty'),
				  		                    			'sales'         => array('$sum' => '$total_price'),
							  	  						'product_image' => array('$first' => '$product_image'),
							  	  						'product_id'    => array('$first' => '$product_id'),
							  	  						// 'orderlist'     => array('$push'  => '$order_id'),
			                 		  				)),
							  	  					array('$sort'=>$shortField),
												);

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
	}

	/***********************************************************************
	** Function name : getAggregateData
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get Aggregate Data
	** Date 		 : 27 May 2024
	** Updated By    : Dilip Halder
	** Updated Date  : 30 May 2024
	************************************************************************/
	public function getAggregateData($tblName='',$SelectFields='',$whereCondition='',$groupBy='',$sortBy='',$lookup='',$unwind='',$resultType="",$page='',$per_page='')
	{  
		$query = array();
		

		if($groupBy):
			$query[] = array('$group' => $groupBy);
		endif;

		if($sortBy):
			$query[] = array('$sort' => $sortBy);
		endif;

		if($lookup):
			foreach($lookup as $lookupItem):
				$query[] = array('$lookup' => $lookupItem);
			endforeach;
		endif;

		if($unwind):
			foreach($unwind as $item):
				$query[] = array('$unwind' => $item);
			endforeach;
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

		if($per_page):
			$query[] = array('$skip' =>(int)$page);
			$query[] = array('$limit' =>(int)$per_page);
		endif; 
		$result  = $this->mongo_db->aggregate($tblName,$query,array('batchSize'=>4)); 

		if($resultType == 'count'):
			$result = $result[0]['totalCount'];
		else:
			$result = $result;
		endif;
		return $result;
	}   // END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : manageBalance
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used credit balance..
	 * * Date          : 23 DECEMBER 2024
	 * * **********************************************************************/
	function manageBalance($tableName='',$param='',$fieldName='',$fieldValue='')
	{ 
		$this->mongo_db->where(array($fieldName=>$fieldValue));
		$this->mongo_db->inc($param);
		$this->mongo_db->update($tableName);
		return true;
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : getRechargeHistory
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used getRechargeHistory..
	 * * Date          : 24 DECEMBER 2024
	 * * **********************************************************************/
	public function getRechargeHistory($tblName="", $whereCondition="", $sortBy='',$resultType='',$itemsPerPage='',$startIndex='')
	{

		$SelectFields = array(
			'status'       => 1,
			'upoints'      => 1,
			'record_type'  => 1,
			'narration'    => 1,
			'created_at'   => 1,
		 	'users_name'   => array('$arrayElemAt' => array('$users.users_name', 0)),
			'users_email'  => array('$arrayElemAt' => array('$users.users_email',0)),
			'country_code' => array('$arrayElemAt' => array('$users.country_code',0)),
			'users_mobile' => array('$arrayElemAt' => array('$users.users_mobile',0)),
		);

		$lookup  = array(
		   array(
		   	'from' => 'uw_users',
            'let' => ['user_oid' => '$user_id_cred'],
            'pipeline' => [
                ['$match' => ['$expr' => ['$eq' => ['$users_id', '$$user_oid']]]],
                ['$project' => [
	                'users_name' => 1, 
		   			'last_name' => 1, 
			   		'users_email' => 1, 
			   		'country_code' => 1, 
			   		'users_mobile' => 1, 
                ]]
            ],
            'as' => 'users'
		   )
	  	);
		$result  = $this->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$startIndex,$itemsPerPage);
		return $result;

	}

	/* * *********************************************************************
	 * * Function name : referralAmountRelease
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to unlocking referralAmount..
	 * * Date          : 24 DECEMBER 2024
	 * * **********************************************************************/
	public function referralAmountRelease($USERID="",$TopupAmount="")
	{
	 	$tableName   = "uw_users";
        $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','referrel_amount');
        $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);
        
        $user_oid		  = $userDetails['_id']['$id'];
        $unlockingAmount  = $userDetails['totalArabianPoints'];
        if($unlockingAmount >= $userDetails['referrel_amount']):
    		$param['totalArabianPoints'] 	 = +$userDetails['referrel_amount'];
    		$param['availableArabianPoints'] = +$userDetails['referrel_amount'];
    		$param['referrel_amount'] 		 = -$userDetails['referrel_amount'];
	    	
	    	$this->mongo_db->where(array('users_id'=>(int)$USERID));
			$this->mongo_db->inc($param);
			$this->mongo_db->update($tableName);

			$commisionBalance1['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
	        $commisionBalance1['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
	        $commisionBalance1['user_id_deb']     =  (int)0;
	        $commisionBalance1['user_id_cred']    =  (int)$userDetails['users_id'];
	        $commisionBalance1['upoints']         = (float)$userDetails['referrel_amount'];
	        $commisionBalance1['record_type']     = 'Credit';
	        // $commisionBalance1['narration']       = 'Referral Amount';
	        // $commisionBalance1['remarks']         = "Referral amount has been added";
	        $commisionBalance1['narration']       = 'Signup Point';
	        $commisionBalance1['remarks']         = "Signup bonus has been added";
	        $commisionBalance1['creation_ip']     = $this->input->ip_address();;
	        $commisionBalance1['created_at']      = date('Y-m-d H:i');
	        $commisionBalance1['created_by']      = (int)$userDetails['users_id'];
	        $commisionBalance1['status']          = 'A';
	        $commisionBalance1["availableArabianPoints"]  =	(float)$userDetails["availableArabianPoints"];
			$commisionBalance1["end_balance"] 			  =	(float)$userDetails["availableArabianPoints"]+$userDetails['referrel_amount'];
	    	$this->common_model->addData('uw_loadBalance', $commisionBalance1);
        endif;
	}

	/***********************************************************************
	** Function name : getNextPOSId
	** Developed By  : Dilip Halder
	** Purpose	     : This function used for get getNextPOSId
	** Date          : 27 December 2024
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
	 * Function name : generateRaffle
	 * Developed By  : Dilip Halder
	 * Purpose       : This function generates unique raffle codes.
	 * Date          : 12 January 2025
	 ************************************************************************/
	public function generateRaffle($quantity = '', $raffle_prefix = '', $raffle_length = 8)
	{
	    // Validate inputs
	    if (!is_numeric($quantity) || $quantity <= 0) {
	        return "Invalid quantity provided.";
	    }
	    if (!is_numeric($raffle_length) || $raffle_length <= 0) {
	        return "Invalid raffle length provided.";
	    }

	    // Initialize an array to store generated raffles
	    $raffles = [];
	    $tblName = 'uw_lotto_orders';

	    for ($i = 0; $i < $quantity; $i++) {
	        do {
	            // Generate a random numeric string of the required length
	            $random_string = substr(str_shuffle('0123456789'), 0, $raffle_length);
	            $raffle_code = $raffle_prefix . $random_string;

	            // Check for uniqueness in the database
	            $whereCon = ['where' => ['raffle_tickets' => $raffle_code]];
	            $isDuplicate = $this->common_model->getParticularFieldByMultipleCondition(['raffle_tickets'], $tblName, $whereCon);
	        } while (!empty($isDuplicate) || in_array($raffle_code, $raffles));

	        // Add to the list of generated raffles
	        $raffles[] = $raffle_code;
	    }

	    return $raffles;
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
}	