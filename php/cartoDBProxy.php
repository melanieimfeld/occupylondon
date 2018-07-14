<?php
session_cache_limiter('nocache');
$cache_limiter = session_cache_limiter();
//$dataurl is a placeholder for $queryurl in callProxy.php
function goProxy($dataURL) 
{
	$baseURL = 'http://melanieimfeld.cartodb.com/api/v2/sql?';
	//  					^ CHANGE THE 'CARTODB-USER-NAME' to your cartoDB url!
	$api = '&api_key=4d2bf36b9cb5870c59599c2ae837931dd4c08f27';
	//				 ^ENTER YOUR API KEY HERE!
	$url = $baseURL.'q='.urlencode($dataURL).$api;
	$result = file_get_contents ($url);
	return $result;
}
?>