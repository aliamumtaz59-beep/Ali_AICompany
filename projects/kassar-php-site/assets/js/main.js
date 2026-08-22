(function () {
  "use strict";

  // Mobile nav toggle
  var toggle = document.getElementById("nav-toggle");
  var mobileNav = document.getElementById("mobile-nav");
  if (toggle && mobileNav) {
    toggle.addEventListener("click", function () {
      var open = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!open));
      mobileNav.hidden = open;
    });
    mobileNav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        toggle.setAttribute("aria-expanded", "false");
        mobileNav.hidden = true;
      });
    });
  }

  // Scroll reveal
  var revealEls = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && revealEls.length) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    revealEls.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    revealEls.forEach(function (el) {
      el.classList.add("is-visible");
    });
  }

  // Brands directory: category filter + search
  var brandsRoot = document.querySelector("[data-brands-directory]");
  if (brandsRoot) {
    var catButtons = brandsRoot.querySelectorAll("[data-brand-cat]");
    var searchInput = brandsRoot.querySelector("[data-brand-search]");
    var cards = brandsRoot.querySelectorAll("[data-brand-card]");
    var countEl = brandsRoot.querySelector("[data-brand-count]");
    var emptyEl = brandsRoot.querySelector("[data-brand-empty]");
    var activeCat = "All";

    function applyFilter() {
      var query = (searchInput ? searchInput.value : "").trim().toLowerCase();
      var visible = 0;
      cards.forEach(function (card) {
        var cat = card.getAttribute("data-category");
        var name = (card.getAttribute("data-name") || "").toLowerCase();
        var desc = (card.getAttribute("data-description") || "").toLowerCase();
        var matchesCat = activeCat === "All" || cat === activeCat;
        var matchesQuery = query === "" || name.indexOf(query) !== -1 || desc.indexOf(query) !== -1;
        var show = matchesCat && matchesQuery;
        card.hidden = !show;
        if (show) visible++;
      });
      if (countEl) {
        countEl.textContent = visible + (visible === 1 ? " brand found" : " brands found");
      }
      if (emptyEl) {
        emptyEl.hidden = visible !== 0;
      }
    }

    catButtons.forEach(function (btn) {
      btn.addEventListener("click", function () {
        activeCat = btn.getAttribute("data-brand-cat");
        catButtons.forEach(function (b) {
          b.classList.toggle("is-active", b === btn);
        });
        applyFilter();
      });
    });

    if (searchInput) {
      searchInput.addEventListener("input", applyFilter);
    }

    applyFilter();
  }

  // Contact page: mode tabs (buy / supply / retail)
  var modeTabs = document.querySelectorAll("[data-contact-mode]");
  if (modeTabs.length) {
    var panels = document.querySelectorAll("[data-contact-panel]");
    modeTabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var mode = tab.getAttribute("data-contact-mode");
        modeTabs.forEach(function (t) {
          t.classList.toggle("is-active", t === tab);
        });
        panels.forEach(function (panel) {
          panel.hidden = panel.getAttribute("data-contact-panel") !== mode;
        });
      });
    });
  }

  // Enquiry forms: submit via fetch to api/enquiry.php, show inline success/error.
  document.querySelectorAll("[data-enquiry-form]").forEach(function (form) {
    var type = form.getAttribute("data-enquiry-type");
    var errorEl = form.querySelector("[data-enquiry-error]");
    var submitBtn = form.querySelector("[data-enquiry-submit]");
    var submitLabel = submitBtn ? submitBtn.textContent : "Submit";
    var successEl = form.parentElement.querySelector("[data-enquiry-success]");

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (errorEl) {
        errorEl.hidden = true;
        errorEl.textContent = "";
      }

      var formData = new FormData(form);
      var payload = { type: type };
      formData.forEach(function (value, key) {
        payload[key] = String(value).trim();
      });

      var missing = [];
      form.querySelectorAll("[required]").forEach(function (field) {
        if (!payload[field.name]) {
          var label = form.querySelector('label[for="' + field.id + '"]');
          missing.push(label ? label.textContent.replace("*", "").trim() : field.name);
        }
      });
      if (missing.length > 0) {
        if (errorEl) {
          errorEl.textContent = "Please fill in: " + missing.join(", ");
          errorEl.hidden = false;
        }
        return;
      }

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Submitting…";
      }

      fetch("api/enquiry.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      })
        .then(function (res) {
          return res.json().then(function (body) {
            if (!res.ok) {
              throw new Error(body.error || "Something went wrong. Please try again.");
            }
            return body;
          });
        })
        .then(function () {
          form.reset();
          form.hidden = true;
          if (successEl) successEl.hidden = false;
        })
        .catch(function (err) {
          if (errorEl) {
            errorEl.textContent = err.message || "Something went wrong. Please try again.";
            errorEl.hidden = false;
          }
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = submitLabel;
          }
        });
    });
  });

  document.querySelectorAll("[data-enquiry-again]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var wrapper = btn.closest("[data-enquiry-success]");
      if (!wrapper) return;
      wrapper.hidden = true;
      var form = wrapper.parentElement.querySelector("[data-enquiry-form]");
      if (form) form.hidden = false;
    });
  });

  // Back to top
  var backToTop = document.getElementById("back-to-top");
  if (backToTop) {
    var toggleBackToTop = function () {
      backToTop.classList.toggle("is-visible", window.scrollY > 480);
    };
    window.addEventListener("scroll", toggleBackToTop, { passive: true });
    toggleBackToTop();
    backToTop.addEventListener("click", function () {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  // Page transition: briefly dim+lift the page before navigating to another
  // internal page, instead of an instant jump-cut. Never touches page-load
  // timing (the incoming page always paints at full opacity from the
  // start), so there is no flash risk — only the outgoing page animates.
  var TRANSITION_MS = 180;
  document.addEventListener("click", function (e) {
    if (e.defaultPrevented || e.button !== 0) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

    var link = e.target.closest("a[href]");
    if (!link) return;

    var href = link.getAttribute("href");
    if (!href || href.charAt(0) === "#") return;
    if (link.target && link.target !== "" && link.target !== "_self") return;
    if (link.hasAttribute("download")) return;
    if (link.origin !== window.location.origin) return;

    e.preventDefault();
    document.body.classList.add("is-navigating");
    window.setTimeout(function () {
      window.location.href = link.href;
    }, TRANSITION_MS);
  });

  window.addEventListener("pageshow", function () {
    document.body.classList.remove("is-navigating");
  });
})();
