<div class="pollContainer" style="min-width:{$width}%;max-width:{$width}%;border-width:0 0;padding: 0 0;">
	<span>{$poll->question}</span>
	
	<div class="pollInnerContainer">
		{if $poll->canSeeResult()}
			{assign var='__pollView' value='result'}
			{include file='pollResult'}
		{else}
			{assign var='__pollView' value='vote'}
			{include file='pollVote'}
		{/if}
	</div>
</div>
{if $__wcf->session->getPermission('user.profile.poll.canSeePollPage')}
	<div>
		<a href="{link controller='PollsList'}{/link}" class="button small"><span>{lang}wcf.user.featuredPoll.more{/lang}</span></a>
	</div>
{/if}