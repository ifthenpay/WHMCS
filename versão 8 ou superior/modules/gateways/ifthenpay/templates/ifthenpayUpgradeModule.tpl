<div id="ifthenpayUpgradeModuleDiv">
    {if $updateIfthenpayModuleAvailable}
        <img src="{$updateSystemIcon}" alt="update system icon">
        <h2>{$ifthenpayNewUpdateTitle}</h2>
        <div class="text-left bulletPoints">
            {$upgradeModuleBulletPoints}
        </div>
        <a href="{$moduleUpgradeUrlDownload}" download id="downloadUpdateModuleIfthenpay" class="btn btn-danger btn-lg btn-block" target="_blank">{$downloadUpdateIfthenpay}</a>
    {else}
        <img src="{$updatedModuleIcon}" alt="update system icon">
        <h2>{$ifthenpayNoUpdate}</h2>
    {/if}
</div>