(function () {
  'use strict';

  const root = document.getElementById('writealt-app');
  if (!root) return;
  const state = { assets: [], filter: 'all', selected: new Set(), busy: new Set() };
  const grid = root.querySelector('[data-grid]');
  const status = root.querySelector('[data-status]');
  const csrf = root.dataset.csrfToken;

  const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));
  const post = async (url, body) => {
    const response = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf }, body: JSON.stringify(body || {}) });
    const payload = await response.json().catch(() => ({ ok: false, error: 'Craft returned an unreadable response.' }));
    if (!response.ok || payload.ok === false) throw new Error(payload.error || 'WriteAlt could not complete the request.');
    return payload;
  };
  const showStatus = (message, kind) => { status.textContent = message || ''; status.className = `writealt-status ${kind || ''}`; };
  const formatSize = (asset) => asset.sizeLabel || 'Unknown size';
  const isVisible = (asset) => state.filter === 'all'
    || (state.filter === 'missing' && !asset.alt)
    || (state.filter === 'needsOptimization' && asset.needsOptimization)
    || (state.filter === 'optimized' && asset.optimized);

  function render() {
    const visible = state.assets.filter(isVisible);
    root.querySelector('[data-count]').textContent = `${visible.length} image${visible.length === 1 ? '' : 's'} shown`;
    const selectAll = root.querySelector('[data-select-all]');
    const visibleIds = visible.map((asset) => asset.id);
    const selectedVisibleCount = visibleIds.filter((id) => state.selected.has(id)).length;
    selectAll.checked = visibleIds.length > 0 && selectedVisibleCount === visibleIds.length;
    selectAll.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < visibleIds.length;
    if (!visible.length) {
      grid.innerHTML = '<div class="writealt-empty">No image assets match this filter.</div>';
      return;
    }
    grid.innerHTML = visible.map((asset) => {
      const missing = !asset.alt;
      const selected = state.selected.has(asset.id);
      const busy = state.busy.has(asset.id);
      const statusText = missing ? 'Missing alt text' : 'Alt text saved';
      const optimizationText = asset.optimized ? 'Optimized' : (asset.needsOptimization ? 'Needs optimization' : '');
      return `<article class="writealt-card ${busy ? 'is-busy' : ''}">
        <div class="writealt-image-wrap">
          <label class="writealt-check"><input type="checkbox" data-select="${asset.id}" ${selected ? 'checked' : ''} aria-label="Select ${escapeHtml(asset.filename)}"><span></span></label>
          ${asset.url ? `<img src="${escapeHtml(asset.url)}" alt="" loading="lazy">` : '<div class="writealt-no-image">No preview</div>'}
        </div>
        <div class="writealt-card-body">
          <div class="writealt-card-status"><span class="badge ${missing ? 'missing' : 'saved'}">${statusText}</span>${optimizationText ? `<span class="badge ${asset.optimized ? 'optimized' : 'needs'}">${optimizationText}</span>` : ''}</div>
          <div class="writealt-title-row"><h2 title="${escapeHtml(asset.filename)}">${escapeHtml(asset.title || asset.filename)}</h2><span>${formatSize(asset)}</span></div>
          <p class="writealt-filename">${escapeHtml(asset.filename)}</p>
          <p class="writealt-alt ${missing ? 'muted missing-copy' : ''}">${missing ? 'Alt text not added yet.' : escapeHtml(asset.alt)}</p>
          <div class="writealt-card-actions">
            <button class="btn submit" type="button" data-action="generate" data-id="${asset.id}" ${busy ? 'disabled' : ''}>✦ ${missing ? 'Generate alt text' : 'Regenerate alt text'}</button>
            <button class="btn" type="button" data-action="optimize" data-id="${asset.id}" ${busy ? 'disabled' : ''}>⚡ ${asset.optimized ? 'Optimize again' : 'Optimize image'}</button>
          </div>
          ${asset.url ? `<a class="writealt-open" href="${escapeHtml(asset.url)}" target="_blank" rel="noreferrer">Open asset</a>` : ''}
        </div>
      </article>`;
    }).join('');
  }

  function updateAsset(updated) {
    state.assets = state.assets.map((asset) => asset.id === updated.id ? updated : asset);
    render();
  }

  async function run(id, action) {
    if (state.busy.has(id)) return;
    state.busy.add(id); render(); showStatus(action === 'generate' ? 'Generating alt text...' : 'Optimizing image...');
    try {
      const payload = await post(root.dataset[`${action}Url`], { id });
      if (payload.asset) updateAsset(payload.asset);
      showStatus(payload.message || (action === 'generate' ? 'Alt text saved to Craft.' : 'Image updated in place.'), 'success');
    } catch (error) {
      showStatus(error.message, 'error');
    } finally {
      state.busy.delete(id); render();
    }
  }

  async function runSelected(action) {
    const ids = [...state.selected];
    for (const id of ids) await run(id, action);
  }

  async function load() {
    showStatus('Loading Craft image assets...');
    try {
      const [assetsPayload, creditsPayload] = await Promise.all([fetch(root.dataset.assetsUrl, { headers: { 'X-CSRF-Token': csrf } }).then((r) => r.json()), fetch(root.dataset.creditsUrl, { headers: { 'X-CSRF-Token': csrf } }).then((r) => r.json())]);
      if (!assetsPayload.ok) throw new Error(assetsPayload.error || 'Could not load Craft assets.');
      state.assets = assetsPayload.assets || [];
      root.querySelector('[data-credits]').textContent = creditsPayload.ok && creditsPayload.credits != null ? `${creditsPayload.credits} credits` : 'Credits unavailable';
      root.querySelector('[data-connection]').textContent = creditsPayload.ok ? 'Connected to WriteAlt' : 'API key required';
      root.querySelector('[data-connection]').className = `writealt-connection ${creditsPayload.ok ? 'connected' : 'disconnected'}`;
      showStatus(''); render();
    } catch (error) { showStatus(error.message, 'error'); root.querySelector('[data-connection]').textContent = 'Not connected'; render(); }
  }

  root.addEventListener('click', (event) => {
    const filter = event.target.closest('[data-filter]');
    if (filter) { state.filter = filter.dataset.filter; root.querySelectorAll('[data-filter]').forEach((button) => button.classList.toggle('active', button === filter)); render(); return; }
    const action = event.target.closest('[data-action]');
    if (!action) return;
    if (action.dataset.action === 'refresh') load();
    if (action.dataset.action === 'generate') run(Number(action.dataset.id), 'generate');
    if (action.dataset.action === 'optimize') run(Number(action.dataset.id), 'optimize');
    if (action.dataset.action === 'generate-selected') runSelected('generate');
    if (action.dataset.action === 'optimize-selected') runSelected('optimize');
  });
  root.addEventListener('change', (event) => { if (event.target.matches('[data-select]')) { const id = Number(event.target.dataset.select); event.target.checked ? state.selected.add(id) : state.selected.delete(id); } });
  root.addEventListener('change', (event) => {
    if (!event.target.matches('[data-select-all]')) return;
    state.assets.filter(isVisible).forEach((asset) => {
      if (event.target.checked) state.selected.add(asset.id);
      else state.selected.delete(asset.id);
    });
    render();
  });
  load();
}());
