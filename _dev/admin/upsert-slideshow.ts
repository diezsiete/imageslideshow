import './imageslideshow.scss'

const { $ } = window as any;

window.addEventListener('load', () => {
  // same as admin-dev/themes/new-theme/js/components/text-to-link-rewrite-copier.ts
  $(document).on('input', '.js-copier-source-title', (event: any) => {
    if (!$(event.currentTarget).closest('form').data('id')) {
      $('.js-copier-destination-friendly-url').val(
        (window as any).str2url($(event.currentTarget).val(), 'UTF-8'),
      );
    }
  });
})
