import './imageslideshow.scss';
import './slide-content.scss';
import { resizeObserver } from "./slide-content";

document.addEventListener('DOMContentLoaded', () => {

  resizeObserver(
    document.querySelector<HTMLElement>('.imageslideshow'),
    document.querySelector<HTMLElement>('.slide-content')
  )
})
