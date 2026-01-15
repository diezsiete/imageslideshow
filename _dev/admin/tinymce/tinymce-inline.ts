import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/code';

export default function() {
  // const headingConfig = {
  //   license_key: 'gpl',
  //   base_url: '/modules/imageslideshow/public/tinymce', // Important: points to where assets were copied
  //   selector: '.tinymce-heading',
  //   menubar: false,
  //   inline: true,
  //   plugins: [
  //     'lists',
  //   ],
  //   toolbar: 'undo redo | bold italic underline',
  //   valid_elements: 'strong,em,span[style],a[href]',
  //   valid_styles: {
  //     '*': 'font-size,font-family,color,text-decoration,text-align'
  //   },
  // };

  const bodyConfig = {
    license_key: 'gpl',
    base_url: '/modules/imageslideshow/public/tinymce', // Important: points to where assets were copied
    suffix: '.min',
    selector: '.slide-content',
    menubar: false,
    inline: true,
    plugins: [
      'link', 'lists', 'code'
      // 'autolink', 'tinymcespellchecker'
    ],
    // toolbar: [
    //   'undo redo | bold italic underline | blocks fontfamily fontsize',
    //   'forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
    // ],
    toolbar: "blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link | lineheight outdent indent | forecolor backcolor removeformat | code | ltr rtl",
    toolbar_persist: true,
    // valid_elements: 'p[style],strong,em,span[style],a[href],ul,ol,li',
    // valid_styles: {
    //   '*': 'font-size,font-family,color,text-decoration,text-align'
    // },
    fixed_toolbar_container: '.tinymce-inline-toolbar',
    content_css: '/modules/imageslideshow/public/admin/slide-content.css'
  };

  // tinymce.init(headingConfig);
  tinymce.init(bodyConfig);
}
