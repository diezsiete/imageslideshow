import tinymce from "./tinymce/tinymce-inline";
// import TinyMCEEditor, { defaultOptions, inlineOptions } from './tinymce/tinymce-editor';
import './imageslideshow.scss'
import './tinymce-test.scss'

import ResizableElement from "./resizable-element/resizable-element";


window.addEventListener('load', () => {
  tinymce();

  const resizable = new ResizableElement('.resizable-element');

  // new TinyMCEEditor(defaultOptions({selector: '.tinymce-editor'}));
  // new TinyMCEEditor(inlineOptions({
  //   selector: '.tinymce-editor-inline-body',
  //   fixed_toolbar_container: '.tinymce-editor-inline-toolbar',
  // }));
})
