{if $status == 'ok'}
    <br><br>
	Please use the data below to pay for your order.
    <div class="panel panel-ifthenpayConfirmPage">
        <div class="panel-heading">
            <h5>{$ifthenpayPayBy}</h5>
        </div>
        <div class="panel-body">
            <div class="paymentLogo">
                {if $paymentMethod|lower !== 'credit card'}
                    <img src="{$paymentLogo}">
                {else}
                    <img src="{$paymentLogo}" width="150">
                {/if}
            </div>
            {include file="./ifthenpayPaymentData.tpl"}
        </div>
    </div>
{else}
	{include file="./ifthenpayErrorPayment.tpl"}
{/if}