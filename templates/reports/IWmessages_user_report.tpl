<!--<a href="{modurl modname="IWmessages" type="user" func="main"}">{gt text="Messages"}</a>:<br>-->
<ul>
{foreach from=$messages item="message"}
    <li>
	<span style="font-weight:bold;">{$message.subject}</span>
        <span style="margin-left:20px;"> from  <span style="color:blue">{$message.from_userName}</span></span>
	<span style="margin-left:10px;"> - &nbsp;{$message.msg_time_tx}</span>
</li>
{/foreach}
</ul>