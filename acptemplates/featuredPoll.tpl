{include file='header' pageTitle='wcf.acp.featuredPoll.configuration'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.featuredPoll.configuration{/lang}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}{event name='contentHeaderNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{if $success|isset}
	<p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<form method="post" action="{link controller='featuredPoll'}{/link}">
	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.featuredPoll.display{/lang}</h2>
			<p class="sectionDescription">{lang}wcf.acp.featuredPoll.display.description{/lang}</p>
		</header>
		
		<dl>
			<dt><label for="width">{lang}wcf.acp.featuredPoll.display.width{/lang}</label></dt>
			<dd>
				<input type="number" id="width" name="width" value="{$width}" class="tiny" min="50" max="100" />
				<small>{lang}wcf.acp.featuredPoll.display.width.description{/lang}</small>
			</dd>
		</dl>
		
		<dl>
			<dt><label for="frequency">{lang}wcf.acp.featuredPoll.display.frequency{/lang}</label></dt>
			<dd>
				<input type="number" name="frequency" value="{$frequency}" class="tiny" min="5" max="32000" />
				<small>{lang}wcf.acp.featuredPoll.display.frequency.description{/lang}</small>
			</dd>
		</dl>
		
		<dl>
			<dt></dt>
			<dd>
				<label><input type="checkbox" id="isRandom" name="isRandom" value="1"{if $isRandom} checked="checked"{/if} /> {lang}wcf.acp.featuredPoll.display.isRandom{/lang}</label>
				<small>{lang}wcf.acp.featuredPoll.display.isRandom.description{/lang}</small>
			</dd>
		</dl>
	</section>
		
	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.featuredPoll.polls{/lang}</h2>
			<p class="sectionDescription">{lang}wcf.acp.featuredPoll.polls.description{/lang}</p>
		</header>
		
		<dl>
			<dt><label>{lang}wcf.acp.featuredPoll.new{/lang}</label></dt>
			<dd>
				<label><input type="checkbox" id="autoAdd" name="autoAdd" value="1"{if $autoAdd} checked="checked"{/if} /> {lang}wcf.acp.featuredPoll.autoAdd{/lang}</label>
			</dd>
		</dl>
		
		<dl>
			<dt><label>{lang}wcf.acp.featuredPoll.polls{/lang}</label></dt>
			<dd>
				{if !$polls|count}
					{lang}wcf.acp.featuredPoll.polls.noPolls{/lang}
				{else}
					<ul>
						{foreach from=$polls item=poll}
							<li>
								<input type="checkbox" name="pollIDs[]" value="{@$poll.pollID}" {if $poll.pollID|in_array:$pollIDs}checked="checked"{/if} />
								<span>{$poll.question} {if $poll.endTime}({@$poll.endTime|time}){/if}</span>
							</li>
						{/foreach}
					</ul>
				{/if}
			</dd>
		</dl>
	</section>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{csrfToken}
	</div>
</form>

{include file='footer'}
