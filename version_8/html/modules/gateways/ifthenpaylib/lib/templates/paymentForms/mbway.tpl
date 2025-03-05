<link rel="stylesheet" href="{$stylesPath}">


<div class="ifthenpay_form_container">
	<div class="ifthenpay_form_title">
		<span>{$payWith}</span>
		<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
	</div>
	<form method="post" action="viewinvoice.php?id={$invoiceId}" class="ifthenpay_mbway_form">
		<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
			{$mobileCodeSelectHtml}
			<input class="form-control ifthenpay_w_10_rem" name="mobile_number"
			placeholder="{$mobileNumberPlaceholder}" />
			<input style="display:none;" name="payment_id" value="{$randHash}"/>
			<button type="submit" class="btn btn-success">{$payBtn}</button>
		</div>
	</form>
</div>

<script>
	var msg_mbway_invalid_number = "{$msg_mbway_invalid_number}";
</script>
<script type="module" src="{$jsFilePath}"></script>