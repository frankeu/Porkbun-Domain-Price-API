<?php
session_start();
if(isset($_SESSION["porkbun"]) && isset($_SESSION["porkbun"])){
	$parse = $_SESSION["porkbun"];
}else{
	$parse = file_get_contents('https://porkbun.com/products/domains');
	$_SESSION["porkbun"] = $parse;
}

preg_match_all('/ 	  <div class="col-xs-3"">\n	    <a href="\/tld\/(.*?)">\.(.*?)<\/a>\n 	  <\/div>\n 	  <div class="domainsPricingAllExtensionsItemPrice registration col-xs-3">\n	    (<small><s class="text-muted">\$(.*?)<\/s><\/small> <span class="badge badge-porkbun">(.*?)<\/span> \$<span class="sortValue">(.*?)<\/span>|\$<span class="sortValue">(.*?)<\/span>)\n 	  <\/div>\n 	  <div class="domainsPricingAllExtensionsItemPrice renewal col-xs-3">\n	    (<small><s class="text-muted">\$(.*?)<\/s><\/small> <span class="badge badge-porkbun">(.*?)<\/span> \$<span class="sortValue">(.*?)<\/span>|\$<span class="sortValue">(.*?)<\/span>)\n 	  <\/div>\n 	  <div class="domainsPricingAllExtensionsItemPrice transfer col-xs-3">\n	    (<small><s class="text-muted">\$(.*?)<\/s><\/small> <span class="badge badge-porkbun">(.*?)<\/span> \$<span class="sortValue">(.*?)<\/span>|\$<span class="sortValue">(.*?)<\/span>)\n 	  <\/div/', $parse, $data);


foreach($data[1] as $k => $v){
	//Get NEW pricing
	if(preg_match('/<small>(.*?)<\/small>/',$data[3][$k])){
		$new = array(
			'status' => TRUE, 
			'name' => $data[5][$k], 
			'oldPrice' => $data[4][$k], 
			'newPrice' => $data[6][$k] 
		);
	}else{
		$new = array(
			'status' => FALSE, 
			'name' => 'ori', 
			'price' => $data[7][$k]
		);
	}
	
	//Get RENEW pricing
	if(preg_match('/<small>(.*?)<\/small>/',$data[8][$k])){
		$renew = array(
			'status' => TRUE, 
			'name' => $data[10][$k], 
			'oldPrice' => $data[9][$k], 
			'newPrice' => $data[11][$k] 
		);
	}else{
		$renew = array(
			'status' => FALSE, 
			'name' => 'ori', 
			'price' => $data[12][$k]
		);
	}
	
	//Get TRANSFER pricing
	if(preg_match('/<small>(.*?)<\/small>/',$data[13][$k])){
		$transfer = array(
			'status' => TRUE, 
			'name' => $data[15][$k], 
			'oldPrice' => $data[14][$k], 
			'newPrice' => $data[16][$k] 
		);
	}else{
		$transfer = array(
			'status' => FALSE, 
			'name' => 'ori', 
			'price' => $data[17][$k]
		);
	}
	
	$result[] = array(
		'extension' => $data[1][$k],
		'new' => $new,
		'renew' => $renew,
		'transfer' => $transfer,
	);
}
header('Content-Type: application/json');
echo json_encode($result);