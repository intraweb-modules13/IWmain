{insert name="getstatusmsg"}
<h1>{gt text="Intraweb main module"}</h1>
<h2>{gt text="User configurable values"}</h2>
<form  class="z-form" enctype="multipart/form-data" method="post" name="conf" id="conf" action="{modurl modname='IWmain' type='user' func='updateconfig'}">
    <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    <fieldset>
        <legend>{gt text="News and marked blocks"}</legend>
        <div>
            {gt text="I want that block will show details"}
            <input type="checkbox" {if $blockFlaggedDetails}checked="checked"{/if} name="blockFlaggedDetails" />
        </div>
    </fieldset>
    {if $crAc_UserReports}
        <fieldset><legend>{gt text="User reports"}</legend>    
            {if $everybodySubscribed}
                <div class="z-informationmsg">
                    {gt text="Everybody is subscribed to user reports"}
                </div>
            {else}
                <div>
                    {gt text="I want to be subscribed to thinks to see"}
                    <input type="checkbox" {if $subscribeNews}checked="checked"{/if} name="subscribeNews" />
                </div>
            {/if}
            {if $userMail == ''}
                <div class="z-errormsg">
                    {gt text="The system would be able to send you emails with a dayly resume of news, but it is not possible because your email has not been found. You can define your email address in"}
            {else}
                <div class="z-informationmsg">
                    {gt text="The subscrition mail is:"} {$userMail}
                    <br>{gt text="You can change your mail info in"}
            {/if}
            <a href="{modurl type='user' modname='users' func='changeEmail'}">
                {gt text=" this link."}
            </a>
            </div>
        </fieldset>
    {/if}
    <div class="z-center z-buttons">
        <a onclick="javascript: forms['conf'].submit();">
            {img modname='core' src='button_ok.png' set='icons/small' __alt="OK" __title="OK"} {gt text="OK"}
        </a>
    </div>
</form>