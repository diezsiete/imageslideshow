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
  onSlideImageUploaded?: (response: SuccessResponse) => void,
  onResize?: (styleInset: string) => void,
  onBlur?: (editor: SliderEditor) => void,
}

export default class SliderEditor {
  private imageUploadedListener: SlideEditorOptions['onSlideImageUploaded']
  private blurListener: SlideEditorOptions['onBlur']
  private imageslideshow: HTMLElement|null;
  private slideImage: HTMLImageElement;
  private resizable: ResizableElement;
  private slideContent: HTMLElement|null;
  private dropzoned: Dropzoned;
  private loaderOverlay: HTMLElement|null;
  private editor: Editor|null = null;

  constructor(options?: SlideEditorOptions) {
    this.imageUploadedListener = options?.onSlideImageUploaded;
    this.blurListener = options?.onBlur;

    this.imageslideshow = document.querySelector<HTMLElement>('.slide-editor .imageslideshow');

    let slideImage = document.querySelector<HTMLImageElement>('.slide-editor .imageslideshow > img');
    if (!slideImage) {
      slideImage = document.createElement('img');
      this.imageslideshow?.prepend(slideImage);
    }
    this.slideImage = slideImage;

    this.resizable = new ResizableElement('.slide-editor .resizable-element');
    if (options?.onResize) {
      this.resizable.onStyleInsetChange(styleInset => options.onResize!(styleInset));
    }

    this.dropzoned = new Dropzoned('#slide-editor-dzd-trigger', {
      error: () => this.loading(false),
      sending: () => this.loading(true),
      success: response => this.dzdSuccess(response),
    })

    this.slideContent = document.querySelector<HTMLElement>('.slide-editor .slide-content')

    this.loaderOverlay = document.querySelector<HTMLElement>('.slide-editor .loader-overlay');
  }

  init() {
    const tinyOptions: RawEditorOptions = {
      license_key: 'gpl',
      base_url: '/modules/imageslideshow/public/tinymce',
      suffix: '.min',
      selector: '.slide-content',
      menubar: false,
      inline: true,
      plugins: [
        'link', 'lists', 'code'
      ],
      toolbar: "slideimage | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | lineheight | forecolor backcolor removeformat | code",
      toolbar_persist: true,
      fixed_toolbar_container: '.slide-editor-toolbar',
      content_css: '/modules/imageslideshow/public/admin/slide-content.css',
      slide_editor: this,
      setup: editor => {
        editor.ui.registry.addButton('slideimage', {
          icon: 'image',
          tooltip: 'Insert Slide Image',
          onAction: (_) => this.dropzoned.click(),
        });

        editor.on('blur', () => this.blurListener?.(this))
      }
    };

    tinymce.init(tinyOptions).then(editors => this.editor = editors[0] ?? null)
  }

  setSlideImage(src: string) {
    this.imageslideshow?.classList.remove('imageless')
    this.slideImage.src = src;
  }

  async fetchSlideImage(path: string) {
    const src = await this.dropzoned.fetchImage(path);
    if (src) {
      this.setSlideImage(src);
    }
  }

  setResizeInset(styleInset: string) {
    this.resizable.setStyleInset(styleInset);
  }

  getContent(): string {
    return this.editor ? this.editor.getContent() : '';
  }
  setContent(content: string) {
    if (this.slideContent) {
      this.slideContent.innerHTML = content;
    }
  }

  getDropzoned(): Dropzoned {
    return this.dropzoned
  }

  private async dzdSuccess(response: SuccessResponse) {
    const src = await this.dropzoned.fetchImage(response);
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
