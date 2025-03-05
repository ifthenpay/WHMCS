<select name="gateway" onchange="submit()" class="form-control select-inline">
    {foreach from=$userGateways item=gateway}
        {if $gateway != 'ifthenpay'}
            {if $gateway == $userSelectedGateway}
                <option value="ifthenpay" selected="selected">{$gateway|ucfirst}</option>
            {else}
                <option value="ifthenpay">{$gateway|ucfirst}</option>
            {/if}
        {/if}
    {/foreach}
</select>