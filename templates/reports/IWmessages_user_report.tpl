<a href="{modurl moname="IWmessages" type="user" func="main"}">{gt text="Messages"}</a>:<br>
{foreach from=$messages item="message"}
<div>
	<span style="float:left;width:100px">{$message.subject}</span>
	<span style="float:left;width:100px"> from {$message.from_userid}</span>
	<span> - &nbsp;{$message.msg_time_tx}</span>
</div>
{/foreach}
