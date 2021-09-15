<div class="paymentData">
    {if $paymentMethod|lower === 'multibanco'}
    <ul class="list-group">
        <li class="list-group-item">
            {$entityMultibanco}
            <span class="badge">{$entidade}</span>
        </li>
        <li class="list-group-item">
            {$ifthenpayReference} 
            <span class="badge">{$referencia}</span>
        </li>
        <li class="list-group-item">
            {$ifthenpayTotalToPay} 
            <span class="badge">{$totalToPay}</span>
        </li>
    </ul>
    {elseif $paymentMethod|lower === 'mbway' || $paymentMethod|lower === 'mb way'}
        <ul class="list-group">
            <li class="list-group-item">
                {$phoneMbway} 
                <span id="ifthenpayMbwayPhone" class="badge">{$telemovel}</span>
            </li>
            <li class="list-group-item">
                {$orderTitle}: 
                <span class="badge">{$orderId}</span>
            </li>
            <li class="list-group-item">
                {$ifthenpayTotalToPay}
                <span class="badge">{$totalToPay}</span>
            </li>
        </ul>
        {if $resendMbwayNotificationControllerUrl !== ''}
            <div>
                <h5>{$notReceiveMbwayNotification}</h5>
                <a id="resendMbwayNotificationMbway" class="btn btn-primary" href="{$resendMbwayNotificationControllerUrl}">{$resendMbwayNotification}</a>
                {include file="./spinner.tpl"}
            </div>
        {/if}
    {elseif $paymentMethod|lower === 'payshop'}
        <ul class="list-group">
            <li class="list-group-item">
                {$ifthenpayReference}
                <span class="badge">{$referencia}</span>
            </li>
            <li class="list-group-item">
                {$payshopDeadline}
                <span class="badge">{$validade}</span>
            </li>
            <li class="list-group-item">
                {$ifthenpayTotalToPay}
                <span class="badge">{$totalToPay}</span>
            </li>
        </ul>
        {elseif $paymentMethod|lower === 'credit card'}
            <ul class="list-group">
                <li class="list-group-item">
                    {$ifthenpayTotalToPay}
                    <span class="badge">{$totalToPay}</span>
                </li>
            </ul>
    {/if}
</div>