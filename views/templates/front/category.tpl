{extends file='page.tpl'}

{block name='page_title'}
  {$auto_category->name}
{/block}

{block name='page_content'}
  {if $auto_category->description}
    <div class="auto-category-description">
      {$auto_category->description nofilter}
    </div>
  {/if}

  {if $auto_pages}
    <ul class="auto-page-list">
      {foreach from=$auto_pages item=auto_page}
        <li><a href="{$auto_page.link}">{$auto_page.title}</a></li>
      {/foreach}
    </ul>
  {else}
    <p>{l s='No pages are available in this category yet.' d='Modules.Auto.Shop'}</p>
  {/if}
{/block}
