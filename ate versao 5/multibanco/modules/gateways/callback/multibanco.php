<?php

# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

function right($value, $count){
		return substr($value, ($count*-1));
	}

	function left($string, $count){
		return substr($string, 0, $count);
	}
	
$gatewaymodule = "multibanco"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

$status=0;

$chaveantiphishing=$GATEWAY["ChaveAntiPhishing"];

$invoiceid ="";

if(isset($_REQUEST['chave'])){
	if($chaveantiphishing==$_REQUEST['chave']){
		$entidade = $_REQUEST['entidade'];
		$referencia = $_REQUEST['referencia'];
		$valor = $_REQUEST['valor'];
		
		$datahora = date("Y-m-d H:i:s");
		
		$chaveantiphishingAux ="";
		
		for ($i = 0; $i < strlen ($chaveantiphishing)-3; $i++) {
			$chaveantiphishingAux .="X";
		}
		
		$chaveantiphishingAux .= right($chaveantiphishing,3);
		
		$arr = array("chave" => $chaveantiphishingAux, "entidade" => $entidade, "referencia" => $referencia, "valor" => $valor, "data" => $datahora);
		
		$table = "tblmultibanco";
		$fields = "orderid";
		$where = array("entidade"=>$entidade,"referencia"=>str_replace(' ', '', $referencia),"valor"=>$valor,"estado"=>0);
		$sort = "id";
		$sortorder = "DESC";
		$limits = "0,1";
		$result = select_query($table,$fields,$where,$sort,$sortorder,$limits);
		$data = mysql_fetch_array($result);
		
		if(sizeof($data)>1){
			$invoiceid = $data['orderid'];
			
			
			$transid = $_REQUEST['entidade'].$_REQUEST['referencia'].$invoiceid;
			$amount = $_REQUEST['valor'];
			$fee = "0";
			
			$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

			checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

			# Successful
			addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
			logTransaction($GATEWAY["name"],$arr,"Sucesso: pagamento realizado com sucesso"); # Save to Gateway Log: name, data array, status
			
			$table = "tblmultibanco";
			$update = array("estado"=>1,"datapago"=>$datahora);
			$where = array("entidade"=>$entidade,"referencia"=>str_replace(' ', '', $referencia),"valor"=>$valor,"estado"=>0);
			update_query($table,$update,$where);
		}else {
			# Unsuccessful
			logTransaction($GATEWAY["name"],$arr,"Falhou: Ou nao existe na base de dados ou ja foi pago..."); # Save to Gateway Log: name, data array, status
		}
	}
}
?>