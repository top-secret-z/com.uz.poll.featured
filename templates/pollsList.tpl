{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='headContent'}
    {if $pageNo < $pages}
        <link rel="next" href="{link controller='PollsList'}pageNo={@$pageNo+1}{/link}">
    {/if}
    {if $pageNo > 1}
        <link rel="prev" href="{link controller='PollsList'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
    {/if}
{/capture}

{if WCF_VERSION|substr:0:3 >= '5.5'}
    {capture assign='contentInteractionPagination'}
        {pages print=true assign=pagesLinks controller='PollsList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
    {/capture}

    {include file='header'}
{else}
    {include file='header'}

    {hascontent}
        <div class="paginationTop">
            {content}
                {pages print=true assign=pagesLinks controller="PollsList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
            {/content}
        </div>
    {/hascontent}
{/if}

{if $items}
    <div class="section tabularBox">
        <table class="table">
            <thead>
                <tr>
                    <th class="columnText columnTime{if $sortField === 'time'} active {@$sortOrder}{/if}" colspan="2"><a rel="nofollow" href="{link controller='PollsList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'DESC'}ASC{else}DESC{/if}{/link}">{lang}wcf.user.featuredPoll.time{/lang}</a></th>
                    <th class="columnText columnQuestion{if $sortField === 'question'} active {@$sortOrder}{/if}"><a rel="nofollow" href="{link controller='PollsList'}pageNo={@$pageNo}&sortField=question&sortOrder={if $sortField == 'question' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.user.featuredPoll.question{/lang}</a></th>
                    <th class="columnText columnStats{if $sortField == 'votes'} active {@$sortOrder}{/if}"><a rel="nofollow" href="{link controller='PollsList'}pageNo={@$pageNo}&sortField=votes&sortOrder={if $sortField == 'votes' && $sortOrder == 'DESC'}ASC{else}DESC{/if}{/link}">{lang}wcf.user.featuredPoll.votes{/lang}</a></th>

                    {event name='columnHeads'}
                </tr>
            </thead>

            <tbody>
                {foreach from=$objects item=poll}
                    <tr>
                        <td class="columnIcon">
                            {if $poll->status == 1}
                                <span class="icon icon24 fa-check jsTooltip pointer" title="{lang}wcf.user.featuredPoll.voted{/lang}"></span>
                            {elseif $poll->status == 0}
                                <span class="icon icon24 fa-circle-o jsTooltip pointer" title="{lang}wcf.user.featuredPoll.voted.not{/lang}"></span>
                            {else}
                                <span class="icon icon24 fa-ban jsTooltip pointer" title="{lang}wcf.user.featuredPoll.voted.forbidden{/lang}"></span>
                            {/if}
                        </td>
                        <td class="columnText columnTime">{@$poll->time|time}</td>
                        {if $poll->hasLink}
                            <td class="columnText columnQuestion"><a href="{$poll->link}" class="messageGroupLink">{$poll->question}</a></td>
                        {else}
                            <td class="columnText columnQuestion">{if $poll->status != 2}{$poll->question}{else}{$poll->question|truncate:5}{/if}</td>
                        {/if}
                        <td class="columnText columnStats">{@$poll->votes}</td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
    {hascontent}
        <div class="paginationBottom">
            {content}{@$pagesLinks}{/content}
        </div>
    {/hascontent}

    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

{include file='footer'}
