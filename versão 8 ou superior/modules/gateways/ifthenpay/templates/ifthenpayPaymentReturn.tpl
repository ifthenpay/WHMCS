{if $status == 'ok'}
<br><br>
	{$LANG.paymentReturnTitle}
	{if ($paymentMethod|lower === 'mbway' || $paymentMethod|lower === 'mb way') && $mbwayCountdownShow === 'true'}
		<div class="panel mbwayCountdownPanel">
			<div class="panel-body">
				<h3>{$confirmMbwayPaymentTitle}</h3>
				{include file="./spinner.tpl"}
				<div id="countdownMbway">
					<h3 id="countdownMinutes"></h3>
					<h3>:</h3>
					<h3 id="countdownSeconds"></h3>
				</div>
				<p>{$mbwayExpireTitle}</p>
			</div>
		</div>
		<div id="confirmMbwayOrder" class="panel" style="display:none;">
			<div class="panel-heading">
			<img src="{$mbwayOrderConfirmUrl}" alt="confirm order icon">
			</div>
			<div class="panel-body">
				<h3>{$mbwayOrderPaid}</h3>
				<p>{$mbwayPaymentConfirmed}</p>
			</div>
		</div>
	{else}
		<div class="panel mbwayCountdownPanel" style="display:none;">
			<div class="panel-body">
				<h3>{$confirmMbwayPaymentTitle}</h3>
				{include file="./spinner.tpl"}
				<div id="countdownMbway">
					<h3 id="countdownMinutes"></h3>
					<h3>:</h3>
					<h3 id="countdownSeconds"></h3>
				</div>
				<p>{$mbwayExpireTitle}</p>
			</div>
		</div>
		<div id="confirmMbwayOrder" class="panel" style="display:none;">
			<div class="panel-heading">
			<img src="{$mbwayOrderConfirmUrl}" alt="confirm order icon">
			</div>
			<div class="panel-body">
				<h3>{$mbwayOrderPaid}</h3>
				<p>{$mbwayPaymentConfirmed}</p>
			</div>
		</div>
	{/if}
	<div class="panel panel-ifthenpayConfirmPage">
        <div class="panel-heading">
            <h5>{$ifthenpayPayBy}</h5>
        </div>
        <div class="panel-body">
            <div class="paymentLogo">
                {if $paymentMethod|lower !== 'credit card'}
                    <img src="{$paymentLogo}">
                {else}
                    <img src="{$paymentLogo}" style="max-width: 45%">
                {/if}
            </div>
			{include file="./ifthenpayPaymentData.tpl"}
		</div>
    </div>
{else}
	{include file="./ifthenpayErrorPayment.tpl"}
{/if}