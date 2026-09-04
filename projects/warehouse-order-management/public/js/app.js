document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.getElementById('sidebarToggle');
  if (toggle) {
    toggle.addEventListener('click', function () {
      document.querySelector('.sidebar').classList.toggle('show');
    });
  }

  document.querySelectorAll('.confirm-delete').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm('Are you sure you want to delete this item?')) {
        e.preventDefault();
      }
    });
  });
});

// Order form line management
function addOrderLine(products) {
  var tbody = document.getElementById('orderItemsBody');
  var idx = tbody.querySelectorAll('tr').length;
  var options = products.map(function (p) {
    return '<option value="' + p.id + '" data-unit="' + p.unit + '">' + p.product_code + ' - ' + p.product_name + '</option>';
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
