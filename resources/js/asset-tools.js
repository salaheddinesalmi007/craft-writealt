(function () {
  'use strict';

  const root = document.querySelector('[data-writealt-asset-tools]');
  if (!root) return;
  const button = root.querySelector('[data-writealt-generate]');
  const status = root.querySelector('[data-writealt-asset-status]');
  const assetId = root.dataset.assetId;

  const setStatus = (message, kind) => {
    status.textContent = message || '';
    status.className = `writealt-asset-tools-status ${kind || ''}`;
  };

  button.addEventListener('click', async () => {
    button.disabled = true;
    button.textContent = 'Generating alt text...';
    setStatus('');
    try {
      const response = await fetch(root.dataset.generateUrl, {
        method: 'POST',
        headers: { 'X-CSRF-Token': root.dataset.csrfToken, 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: new URLSearchParams({ id: assetId }),
      });
      const payload = await response.json().catch(() => ({ ok: false, error: 'Craft returned an unreadable response.' }));
      if (!response.ok || payload.ok === false) throw new Error(payload.error || 'WriteAlt could not generate alt text.');

      const alt = payload.asset?.alt || '';
      document.querySelectorAll('[name="alt"], textarea[id*="alt"]').forEach((field) => {
        field.value = alt;
        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
      });
      button.textContent = '↻ Regenerate alt text with WriteAlt';
      setStatus('Alt text generated. Save the asset to keep the change.', 'success');
    } catch (error) {
      button.textContent = '✦ Generate alt text with WriteAlt';
      setStatus(error.message, 'error');
    } finally {
      button.disabled = false;
    }
  });
}());
