const DESIGN_WIDTH = 1280;
// const DESIGN_WIDTH = 1110;

export function resizeObserver(imageslideshow: HTMLElement|null, slide: HTMLElement|null) {
  if (imageslideshow && slide) {
    new ResizeObserver(() => {
      slide.style.setProperty('--scale', (imageslideshow.clientWidth / DESIGN_WIDTH) + '');
    }).observe(imageslideshow);
  }
}
