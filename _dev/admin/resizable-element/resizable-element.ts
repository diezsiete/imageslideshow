import './resizable-element.scss'

type InsetType = { top: number, right: number, bottom: number, left: number };

export default class ResizableElement {
  private readonly resizable: HTMLElement|null;
  private readonly container: HTMLElement|null;

  private inset: InsetType = { top: 0, right: 0, bottom: 0, left: 0 };
  private startInset: InsetType = { top: 0, right: 0, bottom: 0, left: 0 };
  private isDragging = false;
  private currentHandle: string|undefined = undefined;
  private startPos = { x: 0, y: 0 };
  private handlesCreated = false;

  private insetStyleChangeListeners: ((insetStyle: string) => void)[] = [];

  constructor(resizable: string|HTMLElement) {
    this.resizable = resizable instanceof HTMLElement ? resizable : document.querySelector<HTMLElement>(resizable);
    this.container = this.resizable?.parentElement ?? null;

    if (this.resizable && this.container) {

      this.inset = this.getInitialInset(this.resizable);

      const resizableElement = this.resizable;
      const containerElement = this.container;

      this.resizable.addEventListener('click', (e) => {
        if (e.target instanceof HTMLElement && !e.target.classList.contains('handle')) {
          if (!this.handlesCreated) {
            this.createHandles(resizableElement);
            this.handlesCreated = true;
          }
          resizableElement.classList.add('active');
        }
      });
      document.addEventListener('click', (e) => {
        if (e.target instanceof Node && !resizableElement.contains(e.target)) {
          resizableElement.classList.remove('active');
        }
      });

      document.addEventListener('mousemove', (e) => {
        if (!this.isDragging || !this.currentHandle) return;

        const rect = containerElement.getBoundingClientRect();
        const dx = e.clientX - this.startPos.x;
        const dy = e.clientY - this.startPos.y;

        const deltaXPercent = Math.round((dx / rect.width) * 100);
        const deltaYPercent = Math.round((dy / rect.height) * 100);

        switch (this.currentHandle) {
          case 'left':
            this.inset.left = this.clamp(this.startInset.left + deltaXPercent, 0, 100 - this.inset.right);
            break;
          case 'right':
            this.inset.right = this.clamp(this.startInset.right - deltaXPercent, 0, 100 - this.inset.left);
            break;
          case 'top':
            this.inset.top = this.clamp(this.startInset.top + deltaYPercent, 0, 100 - this.inset.bottom);
            break;
          case 'bottom':
            this.inset.bottom = this.clamp(this.startInset.bottom - deltaYPercent, 0, 100 - this.inset.top);
            break;
        }

        this.updateInset();
      });

      document.addEventListener('mouseup', () => {
        this.isDragging = false;
        this.currentHandle = undefined;
      });

      this.updateInset();
    }
  }

  onInsetStyleChange(listener: (inset: string) => void): void {
    this.insetStyleChangeListeners.push(listener);
  }

  private updateInset() {
    if (this.resizable) {
      const styleInset = `${this.inset.top}% ${this.inset.right}% ${this.inset.bottom}% ${this.inset.left}%`;
      this.resizable.style.inset = styleInset;
      this.insetStyleChangeListeners.forEach(listener => listener(styleInset))
    }
  }

  private clamp(value: number, min: number, max: number) {
    return Math.max(min, Math.min(max, value));
  }

  private getInitialInset(resizable: HTMLElement): InsetType {
    let top = 0;
    let right = 0;
    let bottom = 0;
    let left = 0;
    const styleInset = resizable.style.inset
    if (styleInset) {
      const parts = styleInset.trim().split(/\s+/).map(token => parseFloat(token));
      if (!parts.some(n => Number.isNaN(n))) {
        switch (parts.length) {
          case 1:
            top = right = bottom = left = parts[0];
            break;
          case 2:
            [top, right] = parts;
            bottom = top;
            left = right;
            break;
          case 3:
            [top, right, bottom] = parts;
            left = right;
            break;
          case 4:
            [top, right, bottom, left] = parts;
            break;
        }
      }
    }

    return { top, right, bottom, left };
  }

  private createHandles(resizable: HTMLElement) {
    for (const side of ['top', 'right', 'bottom', 'left']) {
      const handle = document.createElement('div')
      handle.classList.add('handle', side)
      handle.dataset.side = side;

      handle.addEventListener('mousedown', (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = true;
        this.currentHandle = (e.currentTarget as HTMLElement).dataset.side;
        this.startPos = { x: e.clientX, y: e.clientY };
        this.startInset = { ...this.inset };
      })

      resizable.appendChild(handle);
    }
  }
}
