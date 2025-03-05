<link rel="stylesheet" href="{$stylesPath}">



<div class="ifthenpay_form_container">
	<div class="ifthenpay_form_title">
		<span>{$payWith}</span>
		<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
	</div>
	<form method="post" action="viewinvoice.php?id={$invoiceId}" class="ifthenpay_pix_form">
		<div style="display: flex; flex-direction:column">
			<label for="pix_name">{$nameLabel}</label>
			<input class="form-control" name="ifthenpaypix_name" id="pix_name" />
			<label for="pix_cpf">{$cpfLabel}</label>
			<input class="form-control" name="ifthenpaypix_cpf" id="pix_cpf" />
			<label for="pix_email">{$emailLabel}</label>
			<input class="form-control" name="ifthenpaypix_email" id="pix_email" />
		</div>
		<button type="submit" class="btn btn-success">{$payBtn}</button>
	</form>
</div>

<script>
	var msg_pix_invalid_name = "{$msgNameInvalid}";
	var msg_pix_invalid_cpf = "{$msgCpfInvalid}";
	var msg_pix_invalid_email = "{$msgEmailInvalid}";
</script>
<script type="module" src="{$jsFilePath}"></script>