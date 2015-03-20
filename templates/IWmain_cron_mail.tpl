{if $HeaderText}<div>{$HeaderText}</div><hr style="border: 1px solid #bbb">{/if}
{if $IWforums && $userNews.IWforums}
    {if $IWforumsHd}<div style="margin-bottom:4px">{$IWforumsHd}</div>{/if}
    <div>{$userNews.IWforums}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWmessages && $userNews.IWmessages}
    {if $IWmessagesHd}<div style="margin-bottom:4px">{$IWmessagesHd}</div>{/if}
    <div>{$userNews.IWmessages}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWforms && $userNews.IWforms}
    {if $IWformsHd}<div style="margin-bottom:4px">{$IWformsHd}</div>{/if}
    <div>{$userNews.IWforms}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $IWnoteboard && $userNews.IWnoteboard}
    {if $IWnoteboardHd}<div style="margin-bottom:4px">{$IWnoteboardHd}</div>{/if}
    <div>{$userNews.IWnoteboard}</div>
    <hr style="border: 1px solid #eee">
{/if}
{if $FooterText}<div>{$FooterText}</div>{/if}