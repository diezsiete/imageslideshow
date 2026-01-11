{if $slideshow.slides}
  <div class="imageslideshow-container">
    <div id="imageslideshow-{$slideshow.slug}" class="imageslideshow">
      {foreach $slideshow.slides as $slide}
        {if !$slide.url}
        <div class="image-container{($slide@first) ? ' current' : ''}">
        {else}
        <a href="{$slide.url}" title="{$slide.title}"{if $slide.target_blank} target="_blank"{/if} class="image-container{($slide@first) ? ' current' : ''}">
        {/if}

          <img src="{$slide.image_url}" alt="{$slide.legend|escape}" class="slide-img"/>

        {if $slide.url}
        </a>
        {else}
        </div>
        {/if}
      {/foreach}
    </div>
    {foreach $slideshow.slides as $slide}
      <div id="htmlcaption{$slide.id}" class="caption{($slide@first) ? ' current' : ''}">
        {if $slide.description}
          <div class="description">
            {$slide.description nofilter}
          </div>
        {/if}
      </div>
    {/foreach}
  </div>
{/if}
