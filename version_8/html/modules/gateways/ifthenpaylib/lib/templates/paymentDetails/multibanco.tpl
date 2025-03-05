<link rel="stylesheet" href="{$stylesPath}">

<div class="ifthenpay_details_container">

	<div class="ifthenpay_details_body">
		<div class="ifthenpay_details_title">
			<span>{$payWith}</span>
			<img src="{$paymentLogo}" alt="{$paymentMethod}" title="{$paymentMethod}" height="40px">
		</div>
		<div class="ifthenpay_details">
			<ul class="list-group">
				<li class="list-group-item">
					{$entityLabel}
					<span class="badge">{$entity}</span>
				</li>
				<li class="list-group-item">
					{$referenceLabel}
					<span class="badge">{$reference}</span>
				</li>
				{if $deadline}
					<li class="list-group-item">
						{$deadlineLabel}
						<span class="badge">{$deadline}</span>
					</li>
				{/if}
				<li class="list-group-item">
					{$amountLabel}
					<span class="badge">{$amount}</span>
				</li>
			</ul>
		</div>
	</div>
</div>