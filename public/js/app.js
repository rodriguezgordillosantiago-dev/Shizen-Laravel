/* ── SHIZEN HOME - JavaScript Principal ──────────────────────────────────
   Contiene únicamente las funciones esenciales para el flujo de la app:
   - Carrito de compras y Checkout en vivo
   - Notificaciones y estado de pedidos en tiempo real
   - Modales de usuario, perfil y notificaciones
   - Interacciones de interfaz (menú móvil, mostrar contraseña, estrellas)
────────────────────────────────────────────────────────────────────────── */

/* ── Carrito y Checkout ────────────────────────────────────────────── */
var cart = JSON.parse(
  localStorage.getItem("shizenCart") || "[]",
);
var activeCartBusinessId = null;
var selectedCheckoutBusinessId = null;

var UI_TEXT = {
  emptyCart: "Tu carrito está vacío.",
  decrease: "Disminuir cantidad",
  increase: "Aumentar cantidad",
};

function formatMoney(value) {
  return "$" + Number(value).toLocaleString("es-CO");
}

function saveCart() {
  localStorage.setItem("shizenCart", JSON.stringify(cart));
  updateCartCount();
  syncCartToDB();
}

function syncCartToDB() {
  var syncUrl =
    window.shizenSyncCartUrl || "php/sync_carrito.php";
  fetch(syncUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": window.shizenCsrfToken || "",
      "Accept": "application/json",
    },
    body: JSON.stringify(cart),
  }).catch(function (err) {
    console.warn("Cart DB sync error:", err);
  });
}

function loadCartFromDB() {
  var syncUrl =
    window.shizenSyncCartUrl || "php/sync_carrito.php";
  fetch(syncUrl)
    .then(function (res) {
      if (!res.ok)
        throw new Error(
          "Error cargando carrito (" + res.status + ")",
        );
      return res.json();
    })
    .then(function (data) {
      if (
        data &&
        data.logged_in &&
        Array.isArray(data.items)
      ) {
        cart = data.items
          .map(function (item) {
            var id = Number(item.id);
            var quantity = Number(item.quantity);
            var price = Number(item.price);
            if (
              !Number.isFinite(id) ||
              id <= 0 ||
              !Number.isFinite(quantity) ||
              quantity <= 0 ||
              !Number.isFinite(price) ||
              price < 0
            ) {
              return null;
            }
            return {
              id: id,
              businessId: Number(item.businessId) || 0,
              quantity: quantity,
              price: price,
              name: String(item.name || "Producto"),
              image: String(
                item.image || "assets/logo.png",
              ),
              restaurant: String(
                item.restaurant || "Restaurante Shizen",
              ),
              addedAt: Number(item.addedAt) || Date.now(),
            };
          })
          .filter(function (item) {
            return item !== null;
          });

        localStorage.setItem(
          "shizenCart",
          JSON.stringify(cart),
        );
        updateCartCount();
        renderCart();
      }
    })
    .catch(function (err) {
      console.warn("Cart DB load error:", err);
    });
}

function updateCartCount() {
  var total = cart.reduce(function (sum, item) {
    return sum + item.quantity;
  }, 0);
  ["cartCount", "cartCountMenu"].forEach(function (id) {
    var count = document.getElementById(id);
    if (count) count.textContent = total;
  });
}

function groupCartByRestaurant() {
  var groupsMap = {};
  cart.forEach(function (item) {
    var key = String(
      item.businessId || item.restaurant || "general",
    );
    if (!groupsMap[key]) {
      groupsMap[key] = {
        businessId: item.businessId || 0,
        restaurantName:
          item.restaurant || "Restaurante Shizen",
        updatedAt: item.addedAt || 0,
        items: [],
      };
    }
    groupsMap[key].items.push(item);
    if ((item.addedAt || 0) > groupsMap[key].updatedAt) {
      groupsMap[key].updatedAt = item.addedAt || 0;
    }
  });

  var groupsArray = [];
  for (var k in groupsMap) {
    if (
      Object.prototype.hasOwnProperty.call(groupsMap, k)
    ) {
      groupsArray.push(groupsMap[k]);
    }
  }
  groupsArray.sort(function (a, b) {
    return b.updatedAt - a.updatedAt;
  });
  return groupsArray;
}

function showCartList() {
  activeCartBusinessId = null;
  selectedCheckoutBusinessId = null;
  renderCart();
}

function selectCartGroup(businessId) {
  activeCartBusinessId = businessId;
  selectedCheckoutBusinessId = businessId;
  renderCart();
}

function addToCart(product) {
  var busId = product.businessId || product.id_negocio || 0;
  var restName =
    product.restaurant ||
    (busId
      ? "Restaurante #" + busId
      : "Restaurante Shizen");
  var now = Date.now();

  var existing = cart.find(function (item) {
    return item.id === product.id;
  });
  if (existing) {
    existing.quantity += 1;
    existing.addedAt = now;
    if (!existing.businessId) existing.businessId = busId;
    if (!existing.restaurant)
      existing.restaurant = restName;
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      restaurant: restName,
      businessId: busId,
      image: product.image || "assets/logo.png",
      quantity: 1,
      addedAt: now,
    });
  }
  saveCart();
  renderCart();
}

function addPromoToCart(promoId) {
  if (typeof getActivePromosList === "function") {
    var list = getActivePromosList();
    var p = list.find(function (item) {
      return item.id === promoId;
    });
    if (p) {
      addToCart({
        id: p.dbId || p.id,
        name: p.title,
        price: p.rawPriceNum || 0,
        restaurant: p.restaurant,
        image: p.img,
      });
    }
  }
}

function cartTotal() {
  return cart.reduce(function (sum, item) {
    return sum + item.price * item.quantity;
  }, 0);
}

function renderCart() {
  var itemsContainer = document.getElementById("cartItems");
  var total = document.getElementById("cartTotal");
  var totalRow = document.getElementById("cartTotalRow");
  var checkout = document.getElementById("checkoutButton");
  if (!itemsContainer || !total) return;

  if (!cart.length) {
    activeCartBusinessId = null;
    itemsContainer.innerHTML =
      '<p class="cart-empty">' + UI_TEXT.emptyCart + "</p>";
    total.textContent = formatMoney(0);
    if (totalRow) totalRow.style.display = "none";
    if (checkout) {
      checkout.disabled = true;
      checkout.style.display = "none";
    }
    return;
  }

  var groups = groupCartByRestaurant();

  if (activeCartBusinessId !== null) {
    var targetGroup = groups.find(function (g) {
      return (
        String(g.businessId) ===
        String(activeCartBusinessId)
      );
    });

    if (!targetGroup) {
      activeCartBusinessId = null;
      renderCart();
      return;
    }

    var groupSubtotal = targetGroup.items.reduce(function (
      sum,
      item,
    ) {
      return sum + item.price * item.quantity;
    }, 0);

    var itemsHtml = targetGroup.items
      .map(function (item) {
        return (
          '<div class="cart-item">' +
          '<img class="cart-item-image" src="' +
          (item.image || "assets/logo.png") +
          '" alt="' +
          item.name +
          '">' +
          '<div class="cart-item-info">' +
          "<strong>" +
          item.name +
          "</strong>" +
          "<small>" +
          formatMoney(item.price) +
          " / unidad</small>" +
          "<span>Subtotal: " +
          formatMoney(item.price * item.quantity) +
          "</span>" +
          "</div>" +
          '<div class="cart-quantity">' +
          '<button type="button" aria-label="' +
          UI_TEXT.decrease +
          '" onclick="changeCartQuantity(' +
          item.id +
          ',-1)">−</button>' +
          "<b>" +
          item.quantity +
          "</b>" +
          '<button type="button" aria-label="' +
          UI_TEXT.increase +
          '" onclick="changeCartQuantity(' +
          item.id +
          ',1)">+</button>' +
          "</div>" +
          "</div>"
        );
      })
      .join("");

    itemsContainer.innerHTML =
      '<button type="button" class="btn-back-carts" onclick="showCartList()">← Volver a mis carritos</button>' +
      '<div class="cart-group-card" data-business-id="' +
      targetGroup.businessId +
      '">' +
      '<div class="cart-group-header">' +
      '<div class="cart-group-title">🏬 ' +
      targetGroup.restaurantName +
      "</div>" +
      "</div>" +
      '<div class="cart-group-items">' +
      itemsHtml +
      "</div>" +
      '<div class="cart-group-footer">' +
      '<div class="cart-group-subtotal"><span>Subtotal Pedido:</span><strong>' +
      formatMoney(groupSubtotal) +
      "</strong></div>" +
      '<div class="cart-group-actions"><button type="button" class="btn-cart-clear" onclick="clearBusinessCart(' +
      targetGroup.businessId +
      ')">🗑️ Vaciar</button></div>' +
      "</div>" +
      "</div>";

    total.textContent = formatMoney(groupSubtotal);
    if (totalRow) totalRow.style.display = "";
    if (checkout) {
      checkout.disabled = false;
      checkout.style.display = "";
    }
    return;
  }

  itemsContainer.innerHTML = groups
    .map(function (group, gIdx) {
      var isNewestGroup = gIdx === 0 && groups.length > 1;
      var groupSubtotal = group.items.reduce(function (
        sum,
        item,
      ) {
        return sum + item.price * item.quantity;
      }, 0);
      var totalItemsCount = group.items.reduce(function (
        sum,
        item,
      ) {
        return sum + item.quantity;
      }, 0);
      var cartNumber = groups.length - gIdx;

      return (
        '<div class="cart-selector-box" onclick="selectCartGroup(' +
        group.businessId +
        ')">' +
        '<div class="cart-box-header">' +
        '<span class="cart-box-name">🛒 Carrito #' +
        cartNumber +
        " — 🏬 " +
        group.restaurantName +
        "</span>" +
        (isNewestGroup
          ? '<span class="cart-group-badge">🔥 MÁS RECIENTE</span>'
          : "") +
        "</div>" +
        '<div class="cart-box-details">' +
        '<span class="cart-box-count">📦 ' +
        totalItemsCount +
        " plato(s)</span>" +
        '<span class="cart-box-total">Total: <strong>' +
        formatMoney(groupSubtotal) +
        "</strong></span>" +
        "</div>" +
        '<div class="cart-box-arrow">Ver platos y pagar ➜</div>' +
        "</div>"
      );
    })
    .join("");

  if (totalRow) totalRow.style.display = "none";
  if (checkout) {
    checkout.disabled = true;
    checkout.style.display = "none";
  }
}

function clearBusinessCart(businessId) {
  cart = cart.filter(function (item) {
    return (
      String(item.businessId || 0) !== String(businessId)
    );
  });
  activeCartBusinessId = null;
  saveCart();
  renderCart();
}

function changeCartQuantity(id, delta) {
  var item = cart.find(function (entry) {
    return entry.id === id;
  });
  if (!item) return;
  item.quantity += delta;
  item.addedAt = Date.now();
  if (item.quantity <= 0) {
    cart = cart.filter(function (entry) {
      return entry.id !== id;
    });
  }
  saveCart();
  renderCart();
}

function openCart(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  [
    "loginModal",
    "profileModal",
    "notificationModal",
    "checkoutModal",
  ].forEach(function (id) {
    var overlay = document.getElementById(id);
    if (overlay) overlay.classList.remove("open");
  });
  var modal = document.getElementById("cartModal");
  if (modal) {
    modal.classList.add("open");
    modal.setAttribute("aria-hidden", "false");
  }
  renderCart();
}

function closeCart() {
  var modal = document.getElementById("cartModal");
  if (modal) {
    modal.classList.remove("open");
    modal.setAttribute("aria-hidden", "true");
  }
}

function handleCartOverlayClick(event) {
  if (event.target === document.getElementById("cartModal"))
    closeCart();
}

function openCheckout() {
  if (!cart.length) return;
  if (!window.shizenUser) {
    window.location.href =
      "php/login.php?redirect=index.php";
    return;
  }
  closeCart();
  var checkoutModal =
    document.getElementById("checkoutModal");
  if (checkoutModal) checkoutModal.classList.add("open");
}

function closeCheckout() {
  var checkoutModal =
    document.getElementById("checkoutModal");
  if (checkoutModal) checkoutModal.classList.remove("open");
}

function handleCheckoutOverlayClick(event) {
  if (
    event.target ===
    document.getElementById("checkoutModal")
  )
    closeCheckout();
}

function prepareCheckout(event) {
  if (!cart.length) {
    if (event) event.preventDefault();
    return;
  }
  var checkoutList = cart;
  if (selectedCheckoutBusinessId) {
    checkoutList = cart.filter(function (item) {
      return (
        String(item.businessId || 0) ===
        String(selectedCheckoutBusinessId)
      );
    });
  }
  if (!checkoutList.length) checkoutList = cart;

  var checkoutInput =
    document.getElementById("checkoutItems");
  if (checkoutInput) {
    checkoutInput.value = JSON.stringify(
      checkoutList.map(function (item) {
        return { id: item.id, quantity: item.quantity };
      }),
    );
  }
}

/* ── Notificaciones de Pedidos en Vivo ────────────────────────────── */
function consultarNotificacionesEnVivo() {
  if (!window.shizenUser) return;
  var notifUrl =
    window.shizenNotifUrl ||
    "php/notificaciones.php?json=1";
  fetch(notifUrl, {
    headers: { Accept: "application/json" },
  })
    .then(function (res) {
      if (!res.ok) return null;
      return res.json();
    })
    .then(function (data) {
      if (data && data.success) {
        actualizarBadgeNotificaciones(
          data.unread_count || 0,
        );
      }
    })
    .catch(function (err) {
      console.warn(
        "Error consultando notificaciones en vivo:",
        err,
      );
    });
}

function actualizarBadgeNotificaciones(unreadCount) {
  var badge = document.getElementById("notifBadge");
  if (badge) {
    badge.textContent = unreadCount > 0 ? unreadCount : "";
    badge.style.display =
      unreadCount > 0 ? "inline-block" : "none";
  }
}

function iniciarNotificacionesEnVivo() {
  if (window.shizenUser) {
    consultarNotificacionesEnVivo();
    setInterval(consultarNotificacionesEnVivo, 8000);
  }
}

function openNotificationModal() {
  var modal = document.getElementById("notificationModal");
  if (!modal) return;
  modal.classList.add("open");

  var list = document.getElementById("notificationModalList");
  if (!list) return;
  list.replaceChildren();

  fetch(window.shizenNotifUrl || "php/notificaciones.php?json=1", {
    headers: { Accept: "application/json" },
  })
    .then(function (response) {
      if (!response.ok) throw new Error("No se pudieron cargar las notificaciones.");
      return response.json();
    })
    .then(function (data) {
      var notifications = Array.isArray(data.notifications)
        ? data.notifications
        : [];
      if (!data.success || notifications.length === 0) {
        var empty = document.createElement("p");
        empty.className = "notification-modal-empty";
        empty.textContent = "No tienes notificaciones todavía.";
        list.appendChild(empty);
        return;
      }

      notifications.forEach(function (notification) {
        var item = document.createElement("article");
        item.className = "notification-modal-item";
        if (!Number(notification.leida)) item.classList.add("unread");

        var title = document.createElement("strong");
        title.textContent = notification.titulo || "Actualización de pedido";
        var message = document.createElement("span");
        message.textContent = notification.mensaje || "";
        var time = document.createElement("time");
        time.textContent = notification.fecha_creacion || "";

        item.append(title, message, time);
        list.appendChild(item);
      });
    })
    .catch(function (error) {
      console.warn("Error cargando notificaciones:", error);
      var failed = document.createElement("p");
      failed.className = "notification-modal-empty";
      failed.textContent = "No fue posible cargar las notificaciones.";
      list.appendChild(failed);
    });
}

function closeNotificationModal() {
  var modal = document.getElementById("notificationModal");
  if (modal) modal.classList.remove("open");
}

function handleNotificationOverlayClick(event) {
  if (event.target.id === "notificationModal")
    closeNotificationModal();
}

/* ── Modales de Usuario, Perfil y Accesos ────────────────────────── */
var loginModalOpen = false;

function openProfileModal() {
  var modal = document.getElementById("profileModal");
  if (!modal) return;
  modal
    .querySelectorAll(".profile-field")
    .forEach(function (field) {
      field.setAttribute("readonly", "readonly");
    });
  var editButton = document.getElementById(
    "profileEditButton",
  );
  var saveButton = document.getElementById(
    "profileSaveButton",
  );
  if (editButton) editButton.hidden = false;
  if (saveButton) saveButton.hidden = true;
  modal.classList.add("open");
}

function closeProfileModal() {
  var modal = document.getElementById("profileModal");
  if (modal) modal.classList.remove("open");
}

function enableProfileEditing() {
  document
    .querySelectorAll("#profileModal .profile-field")
    .forEach(function (field) {
      field.removeAttribute("readonly");
    });
  var editButton = document.getElementById(
    "profileEditButton",
  );
  var saveButton = document.getElementById(
    "profileSaveButton",
  );
  if (editButton) editButton.hidden = true;
  if (saveButton) saveButton.hidden = false;
}

function handleProfileOverlayClick(event) {
  if (event.target.id === "profileModal")
    closeProfileModal();
}

function openLoginModal() {
  var modal = document.getElementById("loginModal");
  if (!modal) {
    window.location.assign("php/login.php");
    return;
  }
  modal.classList.add("open");
  var ingresoButton = document.getElementById("ingresoBtn");
  if (ingresoButton) ingresoButton.classList.add("active");
  loginModalOpen = true;
}

function closeLoginModal() {
  var modal = document.getElementById("loginModal");
  if (modal) modal.classList.remove("open");
  var ingresoButton = document.getElementById("ingresoBtn");
  if (ingresoButton)
    ingresoButton.classList.remove("active");
  loginModalOpen = false;
}

function toggleLoginModal() {
  if (loginModalOpen) closeLoginModal();
  else openLoginModal();
}

function handleLoginOverlayClick(e) {
  if (e.target === document.getElementById("loginModal"))
    closeLoginModal();
}

function openAccessModal() {
  openLoginModal();
}

function goToRegistration() {
  closeLoginModal();
  var registrationSection =
    document.getElementById("registro");
  if (!registrationSection) {
    window.location.assign("index.php#registro");
    return;
  }
  registrationSection.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}

/* ── UI Helpers (Password Toggle, Menu Móvil, Estrellas) ────────── */
function togglePassword(button) {
  var input = button.parentElement.querySelector("input");
  if (!input) return;
  var showPassword = input.type === "password";
  input.type = showPassword ? "text" : "password";
  button.classList.toggle("is-visible", showPassword);
  button.setAttribute(
    "aria-label",
    showPassword
      ? "Ocultar contraseña"
      : "Mostrar contraseña",
  );
  button.setAttribute(
    "title",
    showPassword
      ? "Ocultar contraseña"
      : "Mostrar contraseña",
  );
}

function toggleMobileMenu() {
  var menu = document.getElementById("mobileMenu");
  if (!menu) return;
  menu.classList.toggle("open");
  var btn = document.getElementById("hamburgerBtn");
  if (btn)
    btn.textContent = menu.classList.contains("open")
      ? "✕"
      : "☰";
}

function initializeRatingStars() {
  document
    .querySelectorAll(".rating-stars")
    .forEach(function (group) {
      var stars = Array.from(
        group.querySelectorAll(".rating-star"),
      );
      stars.forEach(function (star) {
        star
          .querySelector("input")
          .addEventListener("change", function () {
            var selected = Number(this.value);
            stars.forEach(function (entry) {
              entry.classList.toggle(
                "is-selected",
                Number(
                  entry.querySelector("input").value,
                ) <= selected,
              );
            });
          });
      });
    });
}

function validateSection(sectionId) {
  var section = document.getElementById(sectionId);
  if (!section) return true;
  var fields = section.querySelectorAll(
    "input, select, textarea",
  );
  for (var i = 0; i < fields.length; i++) {
    var field = fields[i];
    if (
      field.type !== "checkbox" &&
      field.required &&
      !field.value.trim()
    ) {
      field.setCustomValidity("Este campo es obligatorio.");
    } else {
      field.setCustomValidity("");
    }
    if (!field.checkValidity()) {
      field.reportValidity();
      field.focus();
      return false;
    }
  }
  return true;
}

function nextStep(prefix) {
  if (!validateSection(prefix + "-step1")) return;
  var step1 = document.getElementById(prefix + "-step1");
  var step2 = document.getElementById(prefix + "-step2");
  if (step1) step1.classList.add("hidden");
  if (step2) step2.classList.remove("hidden");
}

function submitJoin(prefix) {
  if (!validateSection(prefix + "-step2")) return;
  var step2 = document.getElementById(prefix + "-step2");
  var success = document.getElementById(
    prefix + "-success",
  );
  if (step2) step2.classList.add("hidden");
  if (success) success.classList.remove("hidden");
}

function resetJoinForm(prefix) {
  var step1 = document.getElementById(prefix + "-step1");
  var step2 = document.getElementById(prefix + "-step2");
  var success = document.getElementById(
    prefix + "-success",
  );
  if (step1) step1.classList.remove("hidden");
  if (step2) step2.classList.add("hidden");
  if (success) success.classList.add("hidden");
}

/* ── Inicialización ────────────────────────────────────────────────── */
function initializeApp() {
  updateCartCount();
  loadCartFromDB();
  iniciarNotificacionesEnVivo();
  initializeRatingStars();
  if (window.loginError) openLoginModal();
}

Promise.resolve(window.shizenLayoutReady).then(
  initializeApp,
);

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeLoginModal();
});
