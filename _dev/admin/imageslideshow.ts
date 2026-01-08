import './imageslideshow.scss'

window.addEventListener('load', () => {
  const grid = new (window as any).prestashop.component.Grid('imageslideshow');
  grid.addExtension(new (window as any).prestashop.component.GridExtensions.ColumnTogglingExtension());
  grid.addExtension(new (window as any).prestashop.component.GridExtensions.SubmitRowActionExtension());
})
