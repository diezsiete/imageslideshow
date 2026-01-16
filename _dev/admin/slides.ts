import './imageslideshow.scss'

document.addEventListener('DOMContentLoaded', () => {
  // toggle slide status
  document.querySelectorAll<HTMLAnchorElement>('.js-toggle-link').forEach(link => link.addEventListener('click', e => {
    e.preventDefault()
    submitAsForm((e.currentTarget as HTMLAnchorElement).href)
  }));

  // delete slide buttons
  document.querySelectorAll<HTMLAnchorElement>('.js-delete-link').forEach(link =>
    link.addEventListener('click', e => deleteSlideHandler(e))
  );
})

function deleteSlideHandler(e: PointerEvent) {
  e.preventDefault()
  const currentTarget = e.currentTarget as HTMLAnchorElement
  const confirmMessage = currentTarget.dataset.confirmMessage || 'Delete?'
  if (confirm(confirmMessage)) {
    submitAsForm(currentTarget.href, 'DELETE')
  }
}

function submitAsForm(action: string, method: 'POST'|'GET'|'DELETE' = 'POST') {
  const isGetOrPostMethod = ['GET', 'POST'].includes(method);
  const form = document.createElement('form');
  form.action = action;
  form.method = isGetOrPostMethod ? method : 'POST';
  if (!isGetOrPostMethod) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '_method';
    input.value = method;
    form.appendChild(input);
  }
  document.body.appendChild(form);
  form.submit();
}
