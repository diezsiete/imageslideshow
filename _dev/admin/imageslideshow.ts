window.addEventListener('load', () => {
  const grid = new (window as any).prestashop.component.Grid('slideshow');
  grid.addExtension(new (window as any).prestashop.component.GridExtensions.ColumnTogglingExtension());
  grid.addExtension(new (window as any).prestashop.component.GridExtensions.SubmitRowActionExtension());
})
