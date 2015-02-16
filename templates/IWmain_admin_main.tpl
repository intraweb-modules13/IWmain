{include file="IWmain_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">
        {img modname='core' src='run.png' set='icons/large' __alt='Admin icon'}
    </div>
    <h2>{gt text="Programmed sequences information"}</h2>
    {if $noCron}
    <div class="z-errormsg">
        {gt text="The iwcron.php file does not exists in the root directory of your site. The programmed sequences are not available. The programmed sequences improve the work of Intraweb modules."}
    </div>
	{else}
		<form  class="z-form" enctype="application/x-www-form-urlencoded" method="post" id="cronMain" action="{modurl modname='IWmain' type='admin' func='updateCronConfig'}">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<div class="z-formrow">
            <label for="cronPasswordActive">{gt text="Activate password for iwcron.php execution"}</label>
            <input type="checkbox" name="cronPasswordActive" {if $cronPasswordActive}selected{/if}/>
        </div>
        <div class="z-formrow">
            <label for="cronPasswordString">{gt text="iwcron.php password"}</label>
            <input type="text" name="cronPasswordString" value="{$cronPasswordString}" size="10" maxlength="10" />
        </div>
		<div class="z-center z-buttons">
            <a onclick="javascript:document.cronMain.submit();">{img modname='core' src='button_ok.png' set='icons/small'   __alt="Save changes" __title="Save changes"} {gt text="Save changes"}</a>
        </div>
    </form>
	{/if}
    {if $noMailer}
    <div class="z-errormsg">
        {gt text="The Mailer module is not active and the cron can not be executed. You should active the Mailer module and configure it properly."}
    </div>
	{/if}
    {if !$noCron && !$noMailer}
    <div style="border: solid 2px #ddd; margin: 20px; padding: 20px;">{$cronResponse}</div>
    {if isset($executeCron) && $executeCron eq 1}
    <div class="z-errormsg">
        {gt text="The cron has not been executed for long time. Now could be a good moment to execute it."}
    </div>
    {/if}
	{/if}
    <div>
        <a href="{modurl modname='IWmain' type='admin' func='executeCron'}">
            {gt text="Execute the cron now"}
        </a>
    </div>
</div>
