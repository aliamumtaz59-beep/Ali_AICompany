(function () {
  "use strict";

  var STORAGE_KEY = "armadio-cart";

  function getCart() {
    try {
      var raw = window.localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      return [];
    }
  }

  function saveCart(items) {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch (e) {
      // Ignore storage write failures (e.g. private browsing quota).
    }
  }

  function addItem(product, quantity) {
    quantity = quantity || 1;
    var items = getCart();
    var existing = items.find(function (item) {
      return item.slug === product.slug;
    });
    if (existing) {
      existing.quantity += quantity;
    } else {
      items.push({
        slug: product.slug,
        name: product.name,
        price: product.price,
        gradient: product.gradient,
        quantity: quantity,
      });
    }
    saveCart(items);
    updateCartBadge();
    return items;
  }

  function removeItem(slug) {
    var items = getCart().filter(function (item) {
      return item.slug !== slug;
    });
    saveCart(items);
    updateCartBadge();
    return items;
  }

  function updateQuantity(slug, quantity) {
    var items = getCart();
    if (quantity <= 0) {
      items = items.filter(function (item) {
        return item.slug !== slug;
      });
    } else {
      items = items.map(function (item) {
        if (item.slug === slug) {
          item.quantity = quantity;
        }
        return item;
      });
    }
    saveCart(items);
    updateCartBadge();
    return items;
  }

  function clearCart() {
    saveCart([]);
    updateCartBadge();
  }

  function itemCount(items) {
    return items.reduce(function (sum, item) {
      return sum + item.quantity;
    }, 0);
  }

  function subtotal(items) {
    return items.reduce(function (sum, item) {
      return sum + item.quantity * item.price;
    }, 0);
  }

  function money(amount) {
    return "£" + amount.toFixed(2);
  }

  function updateCartBadge() {
    var badge = document.getElementById("cart-badge");
    if (!badge) return;
    var count = itemCount(getCart());
    if (count > 0) {
      badge.textContent = String(count);
      badge.hidden = false;
    } else {
      badge.hidden = true;
    }
  }

  window.ArmadioCart = {
    getCart: getCart,
    addItem: addItem,
    removeItem: removeItem,
    updateQuantity: updateQuantity,
    clearCart: clearCart,
    itemCount: itemCount,
    subtotal: subtotal,
    money: money,
    updateCartBadge: updateCartBadge,
  };

  document.addEventListener("DOMContentLoaded", function () {
    updateCartBadge();

    // Wire up "Add to cart" buttons on product cards.
    document.querySelectorAll("[data-add-to-cart]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var product = {
          slug: btn.getAttribute("data-slug"),
          name: btn.getAttribute("data-name"),
          price: parseFloat(btn.getAttribute("data-price")),
          gradient: btn.getAttribute("data-gradient"),
        };
        addItem(product);
        var originalText = btn.getAttribute("data-original-label") || btn.textContent;
        btn.setAttribute("data-original-label", originalText);
        btn.textContent = "Added to cart ✓";
        btn.classList.add("is-added");
        window.clearTimeout(btn._resetTimer);
        btn._resetTimer = window.setTimeout(function () {
          btn.textContent = originalText;
          btn.classList.remove("is-added");
        }, 1600);
      });
    });

    // Render the cart page, if present.
    var cartRoot = document.querySelector("[data-cart-root]");
    if (cartRoot) {
      renderCartPage(cartRoot);
    }
  });

  function renderCartPage(root) {
    var emptyState = root.querySelector("[data-cart-empty]");
    var successState = root.querySelector("[data-cart-success]");
    var filledState = root.querySelector("[data-cart-filled]");
    var itemsList = root.querySelector("[data-cart-items]");
    var subtotalEl = root.querySelector("[data-cart-subtotal]");
    var clearBtn = root.querySelector("[data-cart-clear]");
    var checkoutBtn = root.querySelector("[data-cart-checkout]");

    function render() {
      var items = getCart();

      if (items.length === 0) {
        emptyState.hidden = false;
        filledState.hidden = true;
        successState.hidden = true;
        return;
      }

      emptyState.hidden = true;
      filledState.hidden = false;

      itemsList.innerHTML = "";
      items.forEach(function (item) {
        var row = document.createElement("div");
        row.className = "cart-item";
        row.innerHTML =
          '<div class="cart-item__media ' + item.gradient + '"></div>' +
          '<div class="cart-item__info">' +
            '<h3 class="cart-item__name">' + escapeHtml(item.name) + "</h3>" +
            '<p class="cart-item__unit">' + money(item.price) + " each</p>" +
          "</div>" +
          '<div class="cart-item__qty">' +
            '<button type="button" class="cart-item__qty-btn" data-decrease aria-label="Decrease quantity">−</button>' +
            '<span class="cart-item__qty-value">' + item.quantity + "</span>" +
            '<button type="button" class="cart-item__qty-btn" data-increase aria-label="Increase quantity">+</button>' +
          "</div>" +
          '<div class="cart-item__totals">' +
            '<span class="cart-item__total">' + money(item.price * item.quantity) + "</span>" +
            '<button type="button" class="cart-item__remove" data-remove>Remove</button>' +
          "</div>";

        row.querySelector("[data-decrease]").addEventListener("click", function () {
          updateQuantity(item.slug, item.quantity - 1);
          render();
        });
        row.querySelector("[data-increase]").addEventListener("click", function () {
          updateQuantity(item.slug, item.quantity + 1);
          render();
        });
        row.querySelector("[data-remove]").addEventListener("click", function () {
          removeItem(item.slug);
          render();
        });

        itemsList.appendChild(row);
      });

      subtotalEl.textContent = money(subtotal(items));
    }

    function escapeHtml(str) {
      var div = document.createElement("div");
      div.textContent = str;
      return div.innerHTML;
    }

    if (clearBtn) {
      clearBtn.addEventListener("click", function () {
        clearCart();
        render();
      });
    }

    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", function () {
        filledState.hidden = true;
        successState.hidden = false;
      });
    }

    render();
  }
})();
