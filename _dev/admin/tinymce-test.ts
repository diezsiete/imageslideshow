// import tinymce from "./tinymce/tinymce-classic-trans";
import SlideEditor from "./slide-editor/slide-editor";
// import TinyMCEEditor, { defaultOptions, inlineOptions } from './tinymce/tinymce-editor';
import './imageslideshow.scss'
import './tinymce-test.scss'

import ResizableElement from "./resizable-element/resizable-element";

// import Dropzoned from './file-upload/dropzoned';

window.addEventListener('load', () => {
  // tinymce();
  const slideEditor = new SlideEditor({
    onSlideImageUploaded: response => {
      console.log(response)
    },
    onResize: (styleInset) => {
      console.log(styleInset)
    },
    onBlur: editor => {
      console.log(editor.getContent())
    }
  });

  slideEditor.init()
  slideEditor.setSlideImage('/modules/imageslideshow/images/banner-pedro.png');

  // const resizable = new ResizableElement('.resizable-element');

  // new TinyMCEEditor(defaultOptions({selector: '.tinymce-editor'}));
  // new TinyMCEEditor(inlineOptions({
  //   selector: '.tinymce-editor-inline-body',
  //   fixed_toolbar_container: '.tinymce-editor-inline-toolbar',
  // }));

  // const btn = document.getElementById('dzd-button') as HTMLButtonElement;
  // const dzd = new Dropzoned(btn);
  // const btn2 = document.getElementById('dz-button-2') as HTMLButtonElement;
  // btn2.addEventListener('click', () => dzd.click())
})
