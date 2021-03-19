<div id="ifthenpayUpgradeModuleDiv">
    {if $updateIfthenpayModuleAvailable}
        <img src="{$updateSystemIcon}" alt="update system icon">
        <h2>New update is available!</h2>
        <div class="text-left bulletPoints">
            {$upgradeModuleBulletPoints}
        </div>
        <a href="{$moduleUpgradeUrlDownload}" download id="downloadUpdateModuleIfthenpay" class="btn btn-danger btn-lg btn-block" target="_blank">Download Update Module</a>
    {else}
        <img src="{$updatedModuleIcon}" alt="update system icon">
        <h2>Your module is up to date!</h2>
    {/if}
</div>