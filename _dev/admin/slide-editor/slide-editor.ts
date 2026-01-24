import tinymce, { Editor, RawEditorOptions } from 'tinymce/tinymce';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/code';
import Dropzoned, { SuccessResponse } from '../file-upload/dropzoned';
import ResizableElement from "../resizable-element/resizable-element";
import './slide-editor.scss';

type SlideEditorOptions = {
  selector?: string,
  onSlideImageUploaded?: (response: SuccessResponse) => void,
  onResize?: (styleInset: string) => void,
  onBlur?: (editor: SlideEditor) => void,
}

export default class SlideEditor {
  private imageUploadedListener: SlideEditorOptions['onSlideImageUploaded']
  private blurListener: SlideEditorOptions['onBlur']
  private element: HTMLElement|null;
  private imageslideshow: HTMLElement|null;
  private slideImage: HTMLImageElement;
  private resizable: ResizableElement;
  private slideContent: HTMLElement|null;
  private dropzoned: Dropzoned|null;
  private loaderOverlay: HTMLElement|null;
  private editor: Editor|null = null;

  constructor(options?: SlideEditorOptions) {
    this.imageUploadedListener = options?.onSlideImageUploaded;
    this.blurListener = options?.onBlur;

    this.element = document.querySelector<HTMLElement>(options?.selector ?? '.slide-editor');
    this.imageslideshow = this.element?.querySelector<HTMLElement>('.imageslideshow') ?? null;
    this.slideContent = this.element?.querySelector<HTMLElement>('.slide-content') ?? null
    this.loaderOverlay = this.element?.querySelector<HTMLElement>('.loader-overlay') ?? null;

    let slideImage = this.imageslideshow?.querySelector<HTMLImageElement>(':scope > img');
    if (!slideImage) {
      slideImage = document.createElement('img');
      this.imageslideshow?.prepend(slideImage);
    }
    this.slideImage = slideImage;

    this.resizable = new ResizableElement(this.element?.querySelector<HTMLElement>('.resizable-element') ?? null);
    if (options?.onResize) {
      this.resizable.onStyleInsetChange(styleInset => options.onResize!(styleInset));
    }

    const dzdTrigger = this.element?.dataset.dzdTrigger;
    const dropzonedContainer = dzdTrigger ? document.querySelector<HTMLElement>(dzdTrigger) : null;
    this.dropzoned = dropzonedContainer ? new Dropzoned(dropzonedContainer, {
      error: () => this.loading(false),
      sending: () => this.loading(true),
      success: response => this.dzdSuccess(response),
    }) : null;
  }

  async init() {
    const toolbarContainer = this.element?.querySelector<HTMLElement>('.slide-editor-toolbar');
    if (!this.slideContent || !toolbarContainer) return;

    const tinyOptions: RawEditorOptions = {
      license_key: 'gpl',
      base_url: '/modules/imageslideshow/public/tinymce',
      suffix: '.min',
      target: this.slideContent,
      menubar: false,
      inline: true,
      plugins: [
        'link', 'lists', 'code'
      ],
      toolbar: 'slideimage | blocks fontfamily fontsize | link | bold italic underline strikethrough | forecolor backcolor | align | numlist bullist lineheight removeformat | code',
      toolbar_mode: 'scrolling', // 'wrap',
      toolbar_persist: true,
      fixed_toolbar_container_target: toolbarContainer,
      content_css: '/modules/imageslideshow/public/admin/slide-content.css',
      setup: editor => {
        editor.ui.registry.addButton('slideimage', {
          icon: 'image',
          tooltip: 'Insert Slide Image',
          onAction: () => this.dropzoned?.click(),
        });

        editor.on('blur', () => this.blurListener?.(this))
      },
    };

    const editors = await tinymce.init(tinyOptions);
    this.editor = editors[0] ?? null;

    return this;
  }

  setSlideImage(src: string) {
    this.imageslideshow?.classList.remove('imageless')
    this.slideImage.src = src;
  }

  async fetchSlideImage(path: string) {
    const src = await this.dropzoned?.fetchImage(path);
    if (src) {
      this.setSlideImage(src);
    }
  }

  setResizeInset(styleInset: string) {
    this.resizable.setInset(styleInset);
  }

  getContent(): string {
    return this.editor ? this.editor.getContent() : '';
  }
  setContent(content: string) {
    if (this.slideContent) {
      this.slideContent.innerHTML = content;
    }
  }

  setDisabled(disabled: boolean) {
    this.editor?.options.set('disabled', disabled);
    this.resizable.setDisabled(disabled);
  }

  getImageslideshow(): HTMLElement|null {
    return this.imageslideshow;
  }

  private async dzdSuccess(response: SuccessResponse) {
    const src = await this.dropzoned!.fetchImage(response);
    if (src) {
      this.setSlideImage(src);
      this.imageUploadedListener?.(response);
    }
    this.loading(false);
  }

  private loading(show: boolean) {
    this.loaderOverlay?.classList.toggle('is-visible', show);
  }
}
