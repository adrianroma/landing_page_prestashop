{extends file='page.tpl'}

{block name='page_title'}
  {$auto_page->title}
{/block}

{block name='page_content_container'}
  <section id="content" class="page-content page-auto page-auto-{$auto_page->id}">
    {block name='auto_content'}
      {$auto_page->content nofilter}
    {/block}
  </section>
{/block}
