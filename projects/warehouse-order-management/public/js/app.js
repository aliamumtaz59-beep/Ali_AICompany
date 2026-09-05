function makeSearchable(select) {
  if (typeof TomSelect === 'undefined' || select.tomselect || select.classList.contains('no-search')) return;
  new TomSelect(select, {
    maxOptions: null,
    score: function (search) {
      var term = search.toLowerCase();
      return function (item) {
        return item.text.toLowerCase().indexOf(term) !== -1 ? 1 : 0;
      };
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('sidebarToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      document.querySelector('.sidebar').classList.toggle('show');
    });
  }

  document.querySelectorAll('select.form-select').forEach(makeSearchable);
  initLiveFilter();
});

document.addEventListener('click', function (e) {
  var confirmBtn = e.target.closest('.confirm-delete');
  if (confirmBtn && !confirm('Are you sure you want to delete this item?')) {
    e.preventDefault();
  }
});

// Order form line management
function addOrderLine(products) {
  var tbody = document.getElementById('orderItemsBody');
  var idx = tbody.querySelectorAll('tr').length;
  var options = products.map(function (p) {
    var remaining = typeof p.remaining_qty === 'number' ? p.remaining_qty.toFixed(2) : '0.00';
    return '<option value="' + p.id + '" data-unit="' + p.unit + '">' + p.product_code + ' - ' + p.product_name + ' (Remaining: ' + remaining + ' PCS)</option>';
  }).join('');
  var row = document.createElement('tr');
  row.innerHTML =
    '<td><select class="form-select product-select" name="items[' + idx + '][product_id]" required>' +
    '<option value="">Select product</option>' + options + '</select></td>' +
    '<td><input type="number" step="0.01" min="0.01" class="form-control" name="items[' + idx + '][quantity]" required></td>' +
    '<td><input type="text" class="form-control unit-input" name="items[' + idx + '][unit]" required></td>' +
    '<td><input type="text" class="form-control" name="items[' + idx + '][remarks]"></td>' +
    '<td><button type="button" class="btn btn-sm btn-danger remove-line"><i class="bi bi-trash"></i></button></td>';
  tbody.appendChild(row);
  makeSearchable(row.querySelector('select'));
}

document.addEventListener('click', function (e) {
  if (e.target.closest('.remove-line')) {
    var row = e.target.closest('tr');
    if (document.querySelectorAll('#orderItemsBody tr').length > 1) {
      row.remove();
    } else {
      alert('At least one product line is required.');
    }
  }
});

document.addEventListener('change', function (e) {
  if (e.target.classList.contains('product-select')) {
    var unit = e.target.selectedOptions[0].getAttribute('data-unit');
    var row = e.target.closest('tr');
    var unitInput = row.querySelector('.unit-input');
    if (unit && unitInput && !unitInput.value) unitInput.value = unit;
  }
});

/**
 * Live-filter engine: any form with [data-live-filter] auto-applies on
 * select/date change (immediately) and text input (debounced), fetching
 * the same URL via AJAX and swapping the linked results container instead
 * of doing a full page navigation. Falls back to a normal GET submit if
 * JS or fetch is unavailable.
 */
function initLiveFilter() {
  var form = document.querySelector('form[data-live-filter]');
  if (!form) return;
  var resultsId = form.getAttribute('data-live-filter');
  var results = document.getElementById(resultsId);
  if (!results) return;

  var debounceTimer = null;

  function loadUrl(url, pushHistory) {
    results.classList.add('filter-loading');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.text(); })
      .then(function (html) {
        results.innerHTML = html;
        results.classList.remove('filter-loading');
        if (pushHistory) history.pushState({ liveFilter: true }, '', url);
      })
      .catch(function () {
        window.location.href = url;
      });
  }

  function currentFormUrl() {
    var query = new URLSearchParams(new FormData(form)).toString();
    return window.location.pathname + (query ? '?' + query : '');
  }

  function syncFormFromUrl() {
    var params = new URLSearchParams(window.location.search);
    form.querySelectorAll('select, input').forEach(function (field) {
      if (!field.name) return;
      var val = params.get(field.name) || '';
      if (field.tomselect) {
        field.tomselect.setValue(val, true);
      } else {
        field.value = val;
      }
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    clearTimeout(debounceTimer);
    loadUrl(currentFormUrl(), true);
  });

  form.querySelectorAll('select, input[type="date"]').forEach(function (field) {
    field.addEventListener('change', function () {
      clearTimeout(debounceTimer);
      loadUrl(currentFormUrl(), true);
    });
  });

  form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function (field) {
    field.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () { loadUrl(currentFormUrl(), true); }, 450);
    });
  });

  results.addEventListener('click', function (e) {
    var link = e.target.closest('.pagination a');
    if (!link) return;
    e.preventDefault();
    loadUrl(link.getAttribute('href'), true);
  });

  window.addEventListener('popstate', function () {
    syncFormFromUrl();
    loadUrl(window.location.pathname + window.location.search, false);
  });
}
