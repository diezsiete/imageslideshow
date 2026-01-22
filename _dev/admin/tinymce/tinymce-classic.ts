import tinymce from 'tinymce/tinymce';

// Import themes
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

// Import plugins (add the ones you need)
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
// import 'tinymce/plugins/lists';
// import 'tinymce/plugins/link';
// import 'tinymce/plugins/table';

// import Dropzoned from './../file-upload/dropzoned';

export default function() {

  // const btn = document.getElementById('dzd-button') as HTMLButtonElement;
  // const dzd = new Dropzoned(btn);

  const baseAdminUrl = (window as any).baseAdminDir as string;

  // Initialize TinyMCE
  tinymce.init({
    license_key: 'gpl',
    selector: '#mytextarea', // or your textarea selector
    base_url: '/modules/imageslideshow/public/tinymce', // Important: points to where assets were copied
    suffix: '.min',
    // plugins: 'lists link image table code',
    plugins: 'code image',
    // toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    toolbar: 'slideimage | selectiveDateButton | image | code | example',
    height: 500,
    promotion: false, // remove the “Get all features”
    menubar: false,
    statusbar: false,
    content_style: 'body { background-image: url("/modules/imageslideshow/images/banner-pedro.png"); background-repeat: no-repeat; background-position: top; background-size: cover; }',
    external_filemanager_path: `${baseAdminUrl}filemanager/`,
    filemanager_title: 'File manager',
    external_plugins: {
      // filemanager: `${baseAdminUrl}filemanager/plugin.js`,
      filemanager: `/modules/imageslideshow/public/tinymce-plugin-filemanager.js`,
      slideimage: `/modules/imageslideshow/public/admin/tinymce-plugin-slideimage.js`,
    },
    // https://www.tiny.cloud/docs/tinymce/latest/image/#file_picker_callback
    /*
      URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
      images_upload_url: 'postAcceptor.php',
      here we add custom filepicker only to Image dialog
    */
    file_picker_types: 'image',
    /* and here's our custom image picker*/
    file_picker_callback: (cb, value, meta) => {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      input.addEventListener('change', (e) => {
        const file = (e.target as any).files[0];

        const reader = new FileReader();
        reader.addEventListener('load', () => {
          /*
            Note: Now we need to register the blob in TinyMCEs image blob
            registry. In the next release this part hopefully won't be
            necessary, as we are looking to handle it internally.
          */
          const id = 'blobid' + (new Date()).getTime();
          const blobCache =  (tinymce as any).activeEditor.editorUpload.blobCache;
          const base64 = (reader as any).result.split(',')[1];
          const blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);

          /* call the callback and populate the Title field with the file name */
          cb(blobInfo.blobUri(), { title: file.name });
        });
        reader.readAsDataURL(file);
      });

      input.click();
    },

    setup: editor => {
      editor.ui.registry.addButton('selectiveDateButton', {
        icon: 'image',
        tooltip: 'Insert Current Date',
        onAction: (_) => {
          console.log('ok');
        },
      });
    }

  });

}
