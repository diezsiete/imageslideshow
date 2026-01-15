{if $slideshow.slides}
  <div class="imageslideshow">
    <div id="imageslideshow-{$slideshow.slug}" class="slide-container">
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
    <div id="htmlcaption1" class="caption current">
      <div class="slide-content" style="left: 33%; top: 20%">
        <h1><span style="color: #ffffff; font-size: 36pt;">Obra reunida</span></h1>
        <h2><span style="color: #f1c40f;">Juan Rulfo</span></h2>
        <p><span style="color: #ffffff;">El llano en llamas, Pedro Páramo y El gallo de oro.</span></p>
      </div>
    </div>
    {*{foreach $slideshow.slides as $slide}
      <div id="htmlcaption{$slide.id}" class="caption{($slide@first) ? ' current' : ''}">
        {if $slide.description}
          <div class="description">
            {$slide.description nofilter}
          </div>
        {/if}
      </div>
    {/foreach}*}
  </div>
{/if}
