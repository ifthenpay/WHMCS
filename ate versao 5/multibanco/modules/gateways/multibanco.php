<?php

function multibanco_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"Pagamentos Por Multibanco"),
	 "Entidade" => array("FriendlyName" => "Entidade", "Type" => "text", "Size" => "20", "Description" => "Entidade fornecida pela IfthenPay. Mais informa&ccedil;&otilde;es <a href=\"https://www.ifthenpay.com\" target=\"_blank\">aqui</a>.",),
	 "SubEntidade" => array("FriendlyName" => "SubEntidade", "Type" => "text", "Size" => "20", "Description" => "Sub-Entidade fornecida pela Ifthen. Mais informa&ccedil;&otilde;es <a href=\"https://www.ifthenpay.com\" target=\"_blank\">aqui</a>.",),
	 "ChaveAntiPhishing" => array("FriendlyName" => "Chave Anti-Phishing", "Type" => "text", "Size" => "50", "Value"=>antiPhishingGen(20),"Description" => "<br />Indique aqui uma chave anti-phishing para callback ou deixe a que o sistema sugere. <br /><br />Depois de guardar as defini&ccedil;&otilde;es dever&aacute; comunicar &agrave; Ifthen os seguintes dados para activar o Callback:<br /><ul><li><strong>&bull;</strong> Entidade</li><li><strong>&bull;</strong> Sub-Entidade</li><li><strong>&bull;</strong> 4 &uacute;ltimos digitos da chave de backoffice</li><li><strong>&bull;</strong> O url de Callback: <strong>".GetUrl()."modules/gateways/callback/multibanco.php?chave=[CHAVE_ANTI_PHISHING]&entidade=[ENTIDADE]&referencia=[REFERENCIA]&valor=[VALOR]</strong></li><li><strong>&bull;</strong> E a chave anti-phishing introduzida</li></ul>Dever&aacute; comunicar para o email <a href=\"mailto:ifthenpay@ifthenpay.com\" target=\"_blank\">ifthen@ifthensoftware.com</a> com o assunto <strong>Activar Callback</strong>.",),
    );
	return $configarray;
}

function GetUrl(){
	$table = "tblconfiguration";
	$fields = "value";
	$where = array("setting"=>"SystemURL");
	$result = select_query($table,$fields,$where);
	$data = mysql_fetch_array($result);
	$value = $data['value'];

	return $value;
}

function multibanco_link($params) {

	# Gateway Specific Variables
	$gatewayentidade = $params['Entidade'];
	$gatewaysubentidade = $params['SubEntidade'];


	# Invoice Variables
	$invoiceid = $params['invoiceid'];
	$description = $params["description"];
    $amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

	$referencia = GenerateMbRef($gatewayentidade,$gatewaysubentidade,$invoiceid,$amount);

	# Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];
	$address1 = $params['clientdetails']['address1'];
	$address2 = $params['clientdetails']['address2'];
	$city = $params['clientdetails']['city'];
	$state = $params['clientdetails']['state'];
	$postcode = $params['clientdetails']['postcode'];
	$country = $params['clientdetails']['country'];
	$phone = $params['clientdetails']['phonenumber'];

	# System Variables
	$companyname = $params['companyname'];
	$systemurl = $params['systemurl'];
	$currency = $params['currency'];

	# Enter your code submit to the gateway...

	$code = '
	<div style="width: 200px;	color: #666; font-size: 11px; line-height: 12px; padding: 10px;	border: solid 1px #222;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td valign="top" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px;">
						<img src="https://ifthenpay.com/img/logo_mb.png" border="0" style="width: 30px;">
					</td>
					<td valign="middle" width="100%" style="padding-left: 10px; border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 12px; font-family: Verdana;">
						Pagamento por Multibanco
					</td>
				</tr>
			</tbody>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td valign="top" align="left" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						<strong>Entidade:</strong>
					</td>
					<td valign="top" align="right" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						'.$gatewayentidade.'
					</td>
				</tr>
				<tr>
					<td valign="top" align="left" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						<strong>Refer&ecirc;ncia:</strong>
					</td>
					<td valign="top" align="right" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						'.$referencia.'
					</td>
				</tr>
				<tr>
					<td valign="top" align="left" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						<strong>Valor:</strong>
					</td>
					<td valign="top" align="right" style="border-bottom: solid 1px #222; padding-top: 5px; padding-bottom: 5px; font-size: 11px; font-family: Verdana;">
						&euro;&nbsp;'.$amount.'
					</td>
				</tr>
			</tbody>
		</table>
	</div>';

	# Insere os dados de multibanco numa tabela para posterior valida��o
	insert_references_on_database($params['name'],$gatewayentidade,$referencia,$amount,$invoiceid);

	return $code;
}

function insert_references_on_database($nome, $entidade, $referencia, $valor, $orderid){

	$tabela = "tblmultibanco";

	check_ifmb_table_exist($tabela);

	$fields = "entidade";
	$where = array("nomepag"=>$nome,"entidade"=>$entidade,"referencia"=>str_replace(' ', '', $referencia),"valor"=>$valor,"orderid"=>$orderid);
	$result = select_query($tabela,$fields,$where);
	$data = mysql_fetch_array($result);

	if(sizeof($data)<2){
		if(strlen (str_replace(' ', '', $referencia))==9){
			$values = array("nomepag"=>$nome,"entidade"=>$entidade,"referencia"=>str_replace(' ', '', $referencia),"valor"=>$valor,"orderid"=>$orderid);
			$newid = insert_query($tabela,$values);
		}
	}

}

function check_ifmb_table_exist($tabela){
	$existeTabela = mysql_num_rows(mysql_query("SHOW TABLES LIKE '".$tabela."'"));

	if($existeTabela == 0)
	{
	   //DO SOMETHING! IT EXISTS!

	   $createTableIfmb = 'CREATE TABLE IF NOT EXISTS `'.$tabela.'` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomepag` varchar(255) NOT NULL,
  `entidade` varchar(5) NOT NULL,
  `referencia` varchar(9) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `orderid` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT \'0\',
  `dataencomenda` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datapago` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT=\'tabela criada pela ifthen para gerir referencias\' AUTO_INCREMENT=1 ;';

		MYSQL_QUERY($createTableIfmb) ;

	}
}

function antiPhishingGen($length=50)
		{
			$key = '';
			list($usec, $sec) = explode(' ', microtime());
			mt_srand((float) $sec + ((float) $usec * 100000));

			$inputs = array_merge(range('z','a'),range(0,9),range('A','Z'));

			for($i=0; $i<$length; $i++)
			{
				$key .= $inputs{mt_rand(0,61)};
			}
			return $key;
		}


//INICIO TRATAMENTO DEFINI��ES REGIONAIS
	function format_number($number)
	{
		$verifySepDecimal = number_format(99,2);

		$valorTmp = $number;

		$sepDecimal = substr($verifySepDecimal, 2, 1);

		$hasSepDecimal = True;

		$i=(strlen($valorTmp)-1);

		for($i;$i!=0;$i-=1)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if($hasSepDecimal!=True){
			$valorTmp=number_format($valorTmp,2);

			$i=(strlen($valorTmp)-1);

			for($i;$i!=1;$i--)
			{
				if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
					$hasSepDecimal = True;
					$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
					break;
				}
			}
		}

		for($i=1;$i!=(strlen($valorTmp)-1);$i++)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" "){
				$valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if (strlen(strstr($valorTmp,'@'))>0){
			$valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
		}

		return $valorTmp;
	}
	//FIM TRATAMENTO DEFINI��ES REGIONAIS


	//INICIO REF MULTIBANCO

function GenerateMbRef($ent_id, $subent_id, $order_id, $order_value)
{


		$order_id ="0000".$order_id;

		$order_value =  format_number($order_value);

		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));


	if ($order_value < 1){
                 echo "Lamentamos mas � imposs�vel gerar uma refer�ncia MB para valores inferiores a 1 Euro";
                 return;
           }
           if ($order_value >= 1000000){
                 echo "<b>AVISO:</b> Pagamento fraccionado por exceder o valor limite para pagamentos no sistema Multibanco<br>";
           }
           while ($order_value >= 1000000){
                 GenerateMbRef($order_id++, 999999.99);
                 $order_value -= 999999.99;
           }


        //c�lculo dos check digits


           $chk_str = sprintf('%05u%03u%04u%08u', $ent_id, $subent_id, $order_id, round($order_value*100));

           $chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);

           for ($i = 0; $i < 20; $i++)
           {
                 $chk_int = substr($chk_str, 19-$i, 1);
                 $chk_val += ($chk_int%10)*$chk_array[$i];
           }

           $chk_val %= 97;

           $chk_digits = sprintf('%02u', 98-$chk_val);

       return $subent_id." ".substr($chk_str, 8, 3)." ".substr($chk_str, 11, 1).$chk_digits;

    }

/*
function multibanco_capture($params) {

    # Gateway Specific Variables
	$gatewayentidade = $params['Entidade'];
	$gatewaysubentidade = $params['SubEntidade'];

    # Invoice Variables
	$invoiceid = $params['invoiceid'];
	$amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

	$referencia = GenerateMbRef($gatewayentidade,$gatewaysubentidade,$invoiceid,$amount);

    # Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];
	$address1 = $params['clientdetails']['address1'];
	$address2 = $params['clientdetails']['address2'];
	$city = $params['clientdetails']['city'];
	$state = $params['clientdetails']['state'];
	$postcode = $params['clientdetails']['postcode'];
	$country = $params['clientdetails']['country'];
	$phone = $params['clientdetails']['phonenumber'];

	# Perform Transaction Here & Generate $results Array, eg:
	$results = array();
	$results["status"] = "success";
    $results["transid"] = "12345";
	$results["referencia"] = $referencia;

	# Return Results
	if ($results["status"]=="success") {
		return array("status"=>"success","transid"=>$results["transid"],"rawdata"=>$results);
	} elseif ($gatewayresult=="declined") {
        return array("status"=>"declined","rawdata"=>$results);
    } else {
		return array("status"=>"error","rawdata"=>$results);
	}

}


function multibanco_refund($params) {

    # Gateway Specific Variables
	$gatewayusername = $params['username'];
	$gatewaytestmode = $params['testmode'];

    # Invoice Variables
	$transid = $params['transid']; # Transaction ID of Original Payment
	$amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

    # Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];
	$address1 = $params['clientdetails']['address1'];
	$address2 = $params['clientdetails']['address2'];
	$city = $params['clientdetails']['city'];
	$state = $params['clientdetails']['state'];
	$postcode = $params['clientdetails']['postcode'];
	$country = $params['clientdetails']['country'];
	$phone = $params['clientdetails']['phonenumber'];

	# Card Details
	$cardtype = $params['cardtype'];
	$cardnumber = $params['cardnum'];
	$cardexpiry = $params['cardexp']; # Format: MMYY
	$cardstart = $params['cardstart']; # Format: MMYY
	$cardissuenum = $params['cardissuenum'];

	# Perform Refund Here & Generate $results Array, eg:
	$results = array();
	$results["status"] = "success";
    $results["transid"] = "12345";

	# Return Results
	if ($results["status"]=="success") {
		return array("status"=>"success","transid"=>$results["transid"],"rawdata"=>$results);
	} elseif ($gatewayresult=="declined") {
        return array("status"=>"declined","rawdata"=>$results);
    } else {
		return array("status"=>"error","rawdata"=>$results);
	}

}
*/

?>
