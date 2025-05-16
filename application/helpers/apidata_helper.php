<?php
if(!function_exists('apache_request_headers')) {
	function apache_request_headers() {
		static $arrHttpHeaders;
		if (!$arrHttpHeaders) {
			$arrCasedHeaders = array(
				// HTTP
				'Dasl'             => 'DASL',
				'Dav'              => 'DAV',
				'Etag'             => 'ETag',
				'Mime-Version'     => 'MIME-Version',
				'Slug'             => 'SLUG',
				'Te'               => 'TE',
				'Www-Authenticate' => 'WWW-Authenticate',
				// MIME
				'Content-Md5'      => 'Content-MD5',
				'Content-Id'       => 'Content-ID',
				'Content-Features' => 'Content-features',
			);
			$arrHttpHeaders = array();
			foreach($_SERVER as $strKey => $mixValue) {
				if('HTTP_' !== substr($strKey, 0, 5)) {
					continue;
				}
				$strHeaderKey = strtolower(substr($strKey, 5));
				if(0 < substr_count($strHeaderKey, '_')) {
					$arrHeaderKey = explode('_', $strHeaderKey);
					$arrHeaderKey = array_map('ucfirst', $arrHeaderKey);
					$strHeaderKey = implode('-', $arrHeaderKey);
				}
				else {
					$strHeaderKey = ucfirst($strHeaderKey);
				}
				if(array_key_exists($strHeaderKey, $arrCasedHeaders)) {
					$strHeaderKey = $arrCasedHeaders[$strHeaderKey];
				}
				$arrHttpHeaders[$strHeaderKey] = $mixValue;
			}
			if (isset($arrHttHeaders['Authorization']) && $arrHttHeaders['Authorization']) {
				if (!isset($_SERVER['PHP_AUTH_USER'])) {
					list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
				}
			}
		}
		return $arrHttpHeaders;
	}
}
/*
 * check api header data
 */
if (!function_exists('getApiHeaderData')) { 
	function getApiHeaderData(){ 
		$headerData	=	apache_request_headers();  
        return $headerData;
	} 
} 
/*
 * check apiKey valid or not
 */
if (!function_exists('requestAuthenticate')) { 
	function requestAuthenticate($apiKey='',$method=''){ 
		// return true;
		if($method):
			header('Access-Control-Allow-Origin: *');
	        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
	        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	        $reqMethod = $_SERVER['REQUEST_METHOD'];
	        if ($reqMethod == $method):
				$headerData	=	apache_request_headers();  
		        if($headerData['Apidate']):
		            $apiKey      =	md5("Dealzarabia".$headerData['Apidate']);
			    endif;
				if($apiKey == $headerData['apikey'] || $apiKey == $headerData['Apikey']): 
			        return true;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		else:
			return false;
		endif;
	} 
} 

/*
 * check apiKey valid or not
 */
if (!function_exists('requestAuthenticate_test')) { 
	function requestAuthenticate_test($apiKey='',$method=''){ 
		if($method):
			header('Access-Control-Allow-Origin: *');
	        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
	        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	        $reqMethod = $_SERVER['REQUEST_METHOD'];
			
	        if ($reqMethod == $method):
				
				$headerData	=	apache_request_headers();  
				if($headerData['Apidate']):
		            $apiKey      =	md5("Dealzarabia".$headerData['Apidate']);
			    endif;
				return $headerData;
				if($apiKey == $headerData['apikey'] || $apiKey == $headerData['Apikey']): 
			        return true;
				else:
					return false;
				endif;
			else:
				return false;
			endif;
		else:
			return false;
		endif;
	} 
} 
/*
 * json outPut
 */
if (!function_exists('outPut')) {
	function outPut($code=0,$status=0,$message='',$returnData=array()){
		$data					=	array();
		$result 				= 	array();
		if($code==0){
			$data['code'] 		= 	$code;
			$data['status'] 	= 	$status;
			$data['message'] 	= 	$message;
			$data['result'] 	=	(object) $result;
		}else{
			$data['code'] 		= 	$code;
			$data['status'] 	= 	$status;
			$data['message'] 	= 	$message;
			$data['result'] 	= 	$returnData;
		}
		header('Content-type: application/json');
		return json_encode($data);
	}
}

/*
 * json outPut
 */
if (!function_exists('logOutPut')) {
	function logOutPut($returnData=array()){
		header('Content-type: application/json');
		return json_encode($returnData);
	}
}	

/*
 * generate session id
 */
if (!function_exists('generateSessionId')) {
	function generateSessionId(){
		$uniqueId = uniqid(rand(),TRUE);
		return md5($uniqueId);
	}
}