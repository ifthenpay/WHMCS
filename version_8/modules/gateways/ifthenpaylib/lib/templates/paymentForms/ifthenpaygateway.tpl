<link rel="stylesheet" href="{$stylesPath}">


<div class="ifthenpay_form_container">
	<div class="ifthenpay_form_title">
		<span>{$payBy}</span>
		{$paymentLogoStr}
	</div>
	<form method="post" action="viewinvoice.php?id={$invoiceId}">
		<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
			<input type="hidden" name="ifthenpaygateway" value="true"/>
			<button type="submit" class="btn btn-success">{$payBtn}</button>
		</div>
	</form>
</div>
