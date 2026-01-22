import tinymce from 'tinymce/tinymce';

// Import themes
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

// Import plugins (add the ones you need)
import 'tinymce/plugins/code';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
// import 'tinymce/plugins/table';

import ResizableElement from "../resizable-element/resizable-element";

export default function() {

  const resizable = new ResizableElement('.tinymce-classic-trans-wrapper .resizable-element');

  // Initialize TinyMCE
  tinymce.init({
    license_key: 'gpl',
    selector: '#tinymce-classic-trans', // or your textarea selector
    base_url: '/modules/imageslideshow/public/tinymce', // Important: points to where assets were copied
    suffix: '.min',
    // plugins: 'lists link image table code',
    plugins: 'code link lists',
    // toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | lineheight outdent indent | forecolor backcolor removeformat | code | ltr rtl',
    height: 500,
    promotion: false, // remove the “Get all features”
    branding: false, // remove the “Build with tinyMCE”
    menubar: false,
    statusbar: false,
    resize: 'both',

    setup: function (editor) {
      editor.on('PostRender', function () {
        const toolbar = editor.getContainer().querySelector('.tox-editor-header');
        if (toolbar) {
          document.querySelector('.tinymce-classic-trans-toolbar')?.appendChild(toolbar);
        }
      });
    }
  });

}
