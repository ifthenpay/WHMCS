{if $status == 'ok'}
<p>
	Your order is complete!
		<br /><br />
		Please use the data below to pay for your order.
		<div class="panel panel-ifthenpayConfirmPage">
			<div class="panel-heading">
				<h5>Pay by {$paymentMethod}</h5>
			</div>
			<div class="panel-body">
				<div class="paymentLogo">
					{if $paymentMethod|lower !== 'credit card'}
						<img src="{$paymentLogo}">
					{else}
						<img src="{$paymentLogo}" style="max-width: 45%">
					{/if}
				</div>
				<div class="paymentData">
					{if $paymentMethod|lower === 'multibanco'}
					<ul class="list-group">
						<li class="list-group-item">
							Entity:
							<span class="badge">{$entidade}</span>
						</li>
						<li class="list-group-item">
							Reference:
							<span class="badge">{$referencia}</span>
						</li>
						<li class="list-group-item">
							Total to pay:
							<span class="badge">{$totalToPay}</span>
						</li>
					</ul>
					{elseif $paymentMethod|lower === 'mbway' || $paymentMethod|lower === 'mb way'}
						<ul class="list-group">
							<li class="list-group-item">
								Phone:
								<span class="badge">{$telemovel}</span>
							</li>
							<li class="list-group-item">
								Order:
								<span class="badge">{$orderId}</span>
							</li>
							<li class="list-group-item">
								Total to Pay:
								<span class="badge">{$totalToPay}</span>
							</li>
						</ul>
						{if $resendMbwayNotificationControllerUrl !== ''}
							<div>
								<h5>Not receive MBway notification?</h5>
								<a class="btn btn-primary" href="{$resendMbwayNotificationControllerUrl}">Resend MBway notification</a>
							</div>
						{/if}
					{elseif $paymentMethod|lower === 'payshop'}
						<ul class="list-group">
							<li class="list-group-item">
								Reference:
								<span class="badge">{$referencia}</span>
							</li>
							<li class="list-group-item">
								Deadline:
								<span class="badge">{$validade}</span>
							</li>
							<li class="list-group-item">
								Total to Pay:
								<span class="badge">{$totalToPay}</span>
							</li>
						</ul>
						{elseif $paymentMethod|lower === 'credit card'}
							<ul class="list-group">
								<li class="list-group-item">
									Total to Pay:
									<span class="badge">{$totalToPay}</span>
								</li>
							</ul>
					{/if}
				</div>
			</div>
		</div>
</p>
{else}
	<p class="warning">
		We have noticed that there is a problem with your order. If you think this is an error, you can contact our expert customer support team.
	</p>
{/if}