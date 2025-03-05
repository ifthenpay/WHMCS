<link rel="stylesheet" href="{$stylesPath}">

<div class="ifthenpay_details_container">

	<div class="ifthenpay_details_body">
		<div class="ifthenpay_details_title">
			<span>{$payWith}</span>
			<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
		</div>
		<div class="ifthenpay_details">
			<p class="ifthenpay_mb_0 ifthenpay_color_green">{$paymentProcessCompleted}</p>
			<p>{$waitForVerification}</p>
		</div>
	</div>
</div>