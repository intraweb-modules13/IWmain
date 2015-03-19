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
    {elseif $noMailer}
    <div class="z-errormsg">
        {gt text="The Mailer module is not active and the cron can not be executed. You should active the Mailer module and configure it properly."}
    </div>

    {else}
    <form  class="z-form" enctype="application/x-www-form-urlencoded" method="post" id="cronMain" name='cronMain' action="{modurl modname='IWmain' type='admin' func='updateCronConfig'}">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <fieldset>
            <legend>{gt text="Cron settings"}</legend>
	<div class="z-formrow">
            <label for="cronPasswordActive">{gt text="Activate password for iwcron.php execution"}</label>
            <input type="checkbox" value="true" name="cronPasswordActive" {if $cronPasswordActive}checked{/if}/>
        </div>
        <div class="z-formrow">
            <label for="cronPasswordString">{gt text="iwcron.php password"}</label>
            <input type="text" name="cronPasswordString" value="{$cronPasswordString}" size="10" maxlength="10" />
        </div>
        </fieldset>
        <fieldset>
            <legend>{gt text="Cron actions"}</legend>
        <div class="z-formrow">
            <label for="crAc_UserReports">{gt text="User reports active"}</label>
            <input type="checkbox" value="true" id="crAc_UserReports_true" name="crAc_UserReports" {if $crAc_UserReports}checked{/if}/>
        </div>
         <div data-switch="crAc_UserReports" data-switch-value="true">
         <fieldset>
            <legend>{gt text="User reports"}</legend> 
            <div class="z-formrow">
            <label for="cronSubjectText">{gt text="Subject of user reports mail"}</label>
            <input type="text" name="cronSubjectText" value="{$cronSubjectText}" />
        </div>
            <div class="z-formrow">
            <label for="cronHeaderText">{gt text="Header text of the user reports mail"}</label>
            <textarea name="cronHeaderText" cols="70" rows="5">{$cronHeaderText}</textarea>
        </div>
        <div class="z-formrow">
            <label for="cronFooterText">{gt text="Footer text of the user reports mail"}</label>
            <textarea name="cronFooterText" cols="70" rows="5">{$cronFooterText}</textarea>
        </div> 
        <fieldset>
            <legend>{gt text="Modules reports"}</legend>
            <div class="z-formrow">
                <div style="padding:0px">
                    <span style="float:left;width:100px;margin-left:20px"><label for="crAc_UR_IWforums">{gt text="IWforums"}</label></span>
                    <span><input style="margin:0px;vertical-align:top" type="checkbox" value="true" name="crAc_UR_IWforums" {if $crAc_UR_IWforums}checked{/if}/></span>
                    <span data-switch="crAc_UR_IWforums" data-switch-value="true"><label style="vertical-align:top" for="crAc_UR_IWforums_hd">{gt text="Header:"}</label>
                        <textarea name="crAc_UR_IWforums_hd" cols="70" rows="2">{$crAc_UR_IWforums_hd}</textarea>
                    </span>
                </div>
            </div>
            <div class="z-formrow">
                <div style="padding:0px">
                    <span style="float:left;width:100px;margin-left:20px"><label for="crAc_UR_IWmessages">{gt text="IWmessages"}</label></span>
                    <span><input style="margin:0px;vertical-align:top" type="checkbox" value="true" name="crAc_UR_IWmessages" {if $crAc_UR_IWmessages}checked{/if}/></span>
                    <span data-switch="crAc_UR_IWmessages" data-switch-value="true"><label style="vertical-align:top" for="crAc_UR_IWmessages_hd">{gt text="Header:"}</label>
                        <textarea name="crAc_UR_IWmessages_hd" cols="70" rows="2">{$crAc_UR_IWmessages_hd}</textarea>
                    </span>
                </div>
            </div>
            <div class="z-formrow">
                <div style="padding:0px">
                    <span style="float:left;width:100px;margin-left:20px"><label for="crAc_UR_IWforms">{gt text="IWforms"}</label></span>
                    <span><input style="margin:0px;vertical-align:top" type="checkbox" value="true" name="crAc_UR_IWforms" {if $crAc_UR_IWforms}checked{/if}/></span>
                    <span data-switch="crAc_UR_IWforms" data-switch-value="true"><label style="vertical-align:top" for="crAc_UR_IWforms_hd">{gt text="Header:"}</label>
                        <textarea name="crAc_UR_IWforms_hd" cols="70" rows="2">{$crAc_UR_IWforms_hd}</textarea>
                    </span>
                </div>
            </div>
            <div class="z-formrow">
                <div style="padding:0px">
                    <span style="float:left;width:100px;margin-left:20px"><label for="crAc_UR_IWnoteboard">{gt text="IWnoteboard"}</label></span>
                    <span><input style="margin:0px;vertical-align:top" type="checkbox" value="true" name="crAc_UR_IWnoteboard" {if $crAc_UR_IWnoteboard}checked{/if}/></span>
                    <span data-switch="crAc_UR_IWnoteboard" data-switch-value="true"><label style="vertical-align:top" style="vertical-align:top" for="crAc_UR_IWnoteboard_hd">{gt text="Header:"}</label>
                        <textarea name="crAc_UR_IWnoteboard_hd" cols="70" rows="2">{$crAc_UR_IWnoteboard_hd}</textarea>
                    </span>
                </div>
            </div>
            
        </fieldset>
        <fieldset><legend>{gt text="Subscribers:"}</legend>
            <div class="z-formrow">
                <label for="cronEverybody">{gt text="All users subscribed"}</label>
                <input type="checkbox" value="true" id="everybodySubscribed" name="everybodySubscribed" {if $everybodySubscribed}checked{/if}/>
            </div>
            <div class="z-formrow" data-switch="everybodySubscribed" data-switch-value="false">
                <span style="float:left;margin-left:200px"><a href="{modurl modname='IWmain' type='admin' func='subscribeEverybody'}">{gt text="Subscribe everybody"}</a></span>
                <span style="float:left;margin-left:40px"><a href="{modurl modname='IWmain' type='admin' func='unsubscribeEverybody'}">{gt text="Unsubscribe everybody"}</a></span>
            </div>
        </fieldset>
        <fieldset><legend>{gt text="Frequency"}</legend>
            <div class="z-formrow">
                <label for="cronURfreq">{gt text="Minimum time between reports (hours)"}</label>
                <span style="float:left"><input type="text" name="cronURfreq" value="{$cronURfreq}" size="3"/></span>
            </div>
            <div class="z-informationmsg">
                {gt text="Last Users Report action: "}{$lastCronSuccessfullTime}
            </div>
        </fieldset> 
        
        </div>
        </fieldset>
        </fieldset>
	<div class="z-center z-buttons">
            <a onclick="javascript:document.cronMain.submit();">{img modname='core' src='button_ok.png' set='icons/small'   __alt="Save changes" __title="Save changes"} {gt text="Save changes"}</a>
        </div>
    </form>
	
    <div style="border: solid 2px #ddd; margin: 20px; padding: 20px;">{$cronResponse}</div>
    {if isset($executeCron) && $executeCron eq 1}
    <div class="z-errormsg">
        {gt text="The cron has not been executed for long time. Now could be a good moment to execute it."}
    </div>
    {/if}
	
    <div>
        <a href="{modurl modname='IWmain' type='admin' func='executeCron'}">
            {gt text="Execute the cron now"}
        </a>
    </div>
{/if}