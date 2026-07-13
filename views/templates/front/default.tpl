{extends file='page.tpl'}

{block name='page_title'}
  {l s='Auto' d='Modules.Auto.Shop'}
{/block}

{block name='page_content'}
  {if $auto_categories}
    <ul class="auto-category-list">
      {foreach from=$auto_categories item=auto_category}
        <li><a href="{$auto_category.link}">{$auto_category.name}</a></li>
      {/foreach}
    </ul>
  {else}
    <p>{l s='No categories are available yet.' d='Modules.Auto.Shop'}</p>
  {/if}
{/block}
