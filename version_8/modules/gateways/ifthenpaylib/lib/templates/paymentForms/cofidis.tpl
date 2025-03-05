<link rel="stylesheet" href="{$stylesPath}">


<div class="ifthenpay_form_container">
	<div class="ifthenpay_form_title">
		<span>{$payWith}</span>
		<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
	</div>

	<div class="ifthenpay_cofidis_form_info">
		<p class="ifthenpay_cofidis_form_info_title"><strong>{$cofidisDescLine1}</strong><span>{$cofidisDescLine2}</span></p>
		<p>{$cofidisDescLine3}</p>
		<p>{$cofidisDescLine4}</p>
		<p>{$cofidisDescLine5}</p>
	</div>
	<form method="post" action="viewinvoice.php?id={$invoiceId}">
		<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
			<input type="hidden" name="cofidis" value="true" />
			<button type="submit" class="btn btn-success">{$payBtn}</button>
		</div>
	</form>
</div>