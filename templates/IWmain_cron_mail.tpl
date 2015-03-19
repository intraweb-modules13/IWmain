{if $HeaderText}<div>{$HeaderText}</div><hr style="border: 1px solid #bbb">{/if}
{if $IWforums && $userNews.forum}
    {if $IWforumsHd}<div style="margin-bottom:4px">{$IWforumsHd}</div>{/if}
    <div>{$userNews.forum}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWmessages && $userNews.messages}
    {if $IWmessagesHd}<div style="margin-bottom:4px">{$IWmessagesHd}</div>{/if}
    <div>{$userNews.messages}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWforms && $userNews.forms}
    {if $IWformsHd}<div style="margin-bottom:4px">{$IWformsHd}</div>{/if}
    <div>{$userNews.forms}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWnoteboard && $userNews.noteboard}
    {if $IWnoteboardHd}<div style="margin-bottom:4px">{$IWnoteboardHd}</div>{/if}
    <div>{$userNews.noteboard}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $FooterText}<div>{$FooterText}</div>{/if}