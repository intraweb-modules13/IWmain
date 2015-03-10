{include file="IWmain_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='configure.png' set='icons/large' __alt="Configure module"}</div>
    <h2>{gt text="Modify the configuration"}</h2>
    <form  class="z-form" enctype="application/x-www-form-urlencoded" method="post" id="conf" action="{modurl modname='IWmain' type='admin' func='updateconfig'}">
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        {if not $multizk}
        <div class="z-formrow">
            <label for="documentRoot">{gt text="Server files directori"}</label>
            <input type="text" name="documentRoot" value="{$documentRoot}" size="50" maxlength="255" />
            {if $noFolder}
            <div class="z-errormsg z-formnote">
                {gt text="Attached files directory has not been found"}
            </div>
            {/if}
            {if $noWriteabledocumentRoot}
            <div class="z-errormsg z-formnote">
                {gt text="This folder is not writeable."}
            </div>
            {/if}
        </div>
	{else}
	    <input type="text" name="documentRoot" value="{$documentRoot}" size="50" maxlength="255" />
        {/if}
        <div class="z-formrow">
            <label for="extensions">{gt text="Allowed extensions"}</label>
            <input type="text" name="extensions" value="{$extensions}" size="50" maxlength="255" />
        </div>
        <div class="z-formrow">
            <label for="maxsize">{gt text="Maximum size for uploaded files"}</label>
            <input type="text" name="maxsize" value="{$maxsize}" size="10" maxlength="10" />
        </div>
        <div class="z-formrow">
            <label for="usersvarslife">{gt text="Life time of users vars (days)"}</label>
            <input type="text" name="usersvarslife" value="{$usersvarslife}" size="10" maxlength="10" />
        </div>
        <fieldset>
            <legend>{gt text="Captcha system used in form for unregistered users"}</legend>
            <div class="z-formrow">
                <label for="captchaPrivateCode">{gt text="Private captcha code"}</label>
                <input id="captchaPrivateCode" name="captchaPrivateCode" value="{$captchaPrivateCode}" />
            </div>
            <div class="z-formrow">
                <label for="captchaPublicCode">{gt text="Public captcha code"}</label>
                <input id="captchaPublicCode" name="captchaPublicCode" value="{$captchaPublicCode}" />
            </div>
            <div class="z-formnote z-informationmsg">
                <div>
                    {gt text="The captcha system prevents robots can send notes to forms automatically. With this module successfully activated and you get linked to when a user sends an unregistered entry, is obliged to recognize and write a set of characters that can not recognize an automated system."}
                </div>
                <div>
                    {gt text="You can find more information about protection captcha system in"}: <a href="http://www.google.com/recaptcha" target="_blank">http://www.google.com/recaptcha</a>.
                </div>
            </div>
        </fieldset>

        <div class="z-center z-buttons">
            <a onclick="javascript: forms['conf'].submit();">{img modname='core' src='button_ok.png' set='icons/small'   __alt="Save changes" __title="Save changes"} {gt text="Save changes"}</a>
        </div>
    </form>
</div>
