(() => {
  const selector =
    'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]), select, textarea';
  function validar(campo, mostrar) {
    const vacio =
      campo.required && campo.value.trim() === "";
    const invalido = vacio || !campo.validity.valid;
    let mensaje =
      campo.nextElementSibling &&
      campo.nextElementSibling.classList.contains(
        "validacion-js",
      )
        ? campo.nextElementSibling
        : null;
    if (!mensaje) {
      mensaje = document.createElement("small");
      mensaje.className = "validacion-js";
      mensaje.style.cssText =
        "display:block;color:#dc2626;font-size:12px;margin-top:4px;font-weight:500;width:100%;flex-basis:100%;";
      campo.insertAdjacentElement("afterend", mensaje);
    }
    mensaje.textContent =
      invalido && mostrar
        ? vacio
          ? "Este campo es obligatorio."
          : campo.validationMessage
        : "";
    campo.setAttribute(
      "aria-invalid",
      invalido && mostrar ? "true" : "false",
    );
    return !invalido;
  }
  document.addEventListener("input", (e) => {
    if (
      e.target.matches(selector) &&
      e.target.dataset.validacionActiva
    )
      validar(e.target, true);
  });
  document.addEventListener("change", (e) => {
    if (
      e.target.matches(selector) &&
      e.target.dataset.validacionActiva
    )
      validar(e.target, true);
  });
  document.addEventListener("focusout", (e) => {
    if (e.target.matches(selector)) {
      e.target.dataset.validacionActiva = "true";
      validar(e.target, true);
    }
  });
  document.addEventListener(
    "invalid",
    (e) => {
      if (e.target.matches(selector)) {
        e.target.dataset.validacionActiva = "true";
        validar(e.target, true);
      }
    },
    true,
  );
  document.addEventListener(
    "submit",
    (e) => {
      const campos = [
        ...e.target.querySelectorAll(selector),
      ];
      let valido = true;
      campos.forEach((campo) => {
        campo.dataset.validacionActiva = "true";
        if (!validar(campo, true)) valido = false;
      });
      if (!valido) {
        e.preventDefault();
        const primero = campos.find(
          (campo) =>
            (campo.required && campo.value.trim() === "") ||
            !campo.validity.valid,
        );
        if (primero) {
          primero.focus();
          if (!primero.validity.valid)
            primero.reportValidity();
        }
      }
    },
    true,
  );
})();
