<link rel="stylesheet" href="{$stylesPath}">

<div class="ifthenpay_details_container">

	<input type="hidden" style="display:none;" id="ifthenpay_mbway_invoiceid" value="{$invoiceId}" />

	<div class="ifthenpay_details_body">
		<div class="ifthenpay_details_title">
			<span>{$payWith}</span>
			<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
		</div>
		<div class="ifthenpay_details">

			<div id="ifthenpay_mbway_status">

				{if $showCountdown}
					<h1 id="ifthenpay_mbway_countdown">4:00</h1>
				{/if}
				<p>{$notificationSent}</p>
			</div>

		</div>
	</div>
</div>

<script type="module" src="{$jsFilePath}"></script>