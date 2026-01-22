import './imageslideshow.scss'
import SliderEditor from "./slide-editor/slide-editor";
import FileUpload from "./file-upload/file-upload";

window.addEventListener('load', () => {
  const imageInput = new HiddenInput('#slide_image');
  const insetInput = new HiddenInput('#slide_inset');
  const descriptionInput = new HiddenInput('#slide_description')

  const slideEditor = new SliderEditor({
    onSlideImageUploaded: response => {
      imageInput.value = response.path;
    },
    onResize: (styleInset) => {
      insetInput.value = styleInset;
    },
    onBlur: editor => descriptionInput.value = editor.getContent()
  });

  slideEditor.init();

  if (imageInput.getData('fileName')) {
    void slideEditor.fetchSlideImage(imageInput.getData('fileName'))
  }
  if (insetInput.value) {
    slideEditor.setResizeInset(insetInput.value);
  }
  slideEditor.setContent(descriptionInput.value);


  FileUpload.init();
})

class HiddenInput {
  private readonly input: HTMLInputElement|null

  get value(): string {
    return this.input?.value ?? '';
  }

  set value(value: string) {
    if (this.input) {
      this.input.value = value;
    }
  }

  constructor(selector: string) {
    this.input = document.querySelector<HTMLInputElement>(selector);
  }

  getData(key: string): string {
    return this.input?.dataset[key] ?? '';
  }

}
