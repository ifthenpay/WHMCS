<link rel="stylesheet" href="{$stylesPath}">


<div class="ifthenpay_form_container">
	<div class="ifthenpay_form_title">
		<span>{$payWith}</span>
		<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
	</div>
	<form method="post" action="viewinvoice.php?id={$invoiceId}">
		<div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top:8px;">
			<input type="hidden" name="ccard" value="true"/>
			<button type="submit" class="btn btn-success">{$payBtn}</button>
		</div>
	</form>
</div>
