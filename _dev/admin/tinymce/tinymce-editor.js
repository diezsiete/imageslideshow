const { $ } = window;

export default class TinyMCEEditor {
  constructor(options = {}) {
    this.tinyMCELoaded = false;

    options.baseAdminUrl = window.baseAdminDir;

    this.#setupTinyMCE(options);
  }

  #setupTinyMCE(config) {
    if (typeof tinyMCE === 'undefined') {
      this.#loadAndInitTinyMCE(config);
    } else {
      this.#initTinyMCE(config);
    }
  }

  #loadAndInitTinyMCE(config) {
    if (this.tinyMCELoaded) {
      return;
    }

    this.tinyMCELoaded = true;
    const pathArray = config.baseAdminUrl.split('/');
    pathArray.splice(pathArray.length - 2, 2);
    const finalPath = pathArray.join('/');
    window.tinyMCEPreInit = {};
    window.tinyMCEPreInit.base = `${finalPath}/js/tiny_mce`;
    window.tinyMCEPreInit.suffix = '.min';
    $.getScript(`${finalPath}/js/tiny_mce/tinymce.min.js`, () => {
      this.#setupTinyMCE(config);
    });
  }

  #initTinyMCE(config) {
    const cfg = {
      init_instance_callback: (editor) => {
        editor.focus();
        // this.changeToMaterial();
      },
      ...config,
    };


    // Change icons in popups
    // $('body').on('click', '.mce-btn, .mce-open, .mce-menu-item', () => {
    //   this.changeToMaterial();
    // });

    window.tinyMCE.init(cfg);
    // this.watchTabChanges(cfg);
  }
}

export const defaultOptions = (customOptions = {})  => ({
  selector: '.rte',
  plugins: 'align colorpicker link lists advlist code',
  browser_spellcheck: true,
  toolbar1: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,formatselect',
  toolbar2: '',
  language: window.iso_user,
  skin: 'prestashop',
  mobile: {
    theme: 'mobile',
    plugins: ['lists', 'align', 'link', 'advlist', 'code'],
    toolbar: 'undo code colorpicker bold italic underline strikethrough blockquote link align bullist numlist formatselect styleselect',
  },
  menubar: false,
  statusbar: false,
  relative_urls: false,
  convert_urls: false,
  entity_encoding: 'raw',
  extended_valid_elements: 'em[class|name|id],@[role|data-*|aria-*]',
  valid_children: '+*[*]',
  valid_elements: '*[*]',
  rel_list: [{title: 'nofollow', value: 'nofollow'}],
  ...customOptions
})

export const inlineOptions = (customOptions = {}) => ({
  selector: '.rte',
  menubar: false,
  inline: true,
  plugins: [
    'link', 'lists', 'code', 'textcolor'
  ],
  // toolbar: [
  //   'undo redo | bold italic underline | fontselect fontsizeselect',
  //   'forecolor backcolor | alignleft aligncenter alignright alignfull | numlist bullist outdent indent'
  // ],
  toolbar: "undo,redo | formatselect fontselect fontsizeselect | bold italic underline strikethrough | align numlist bullist | link | lineheight outdent indent | forecolor backcolor removeformat",
  language: window.iso_user,
  skin: 'prestashop',
  toolbar_persist: true,
  ...customOptions
})
