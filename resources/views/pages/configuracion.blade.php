@extends('layouts.app')
@section('content')
<main class="scroll">
  <div style="display:flex;align-items:center;gap:16px;padding:24px 16px;background:white;border-bottom:1px solid #e5e7eb">
    <a href="{{ route('perfil') }}" style="background:none;border:none;cursor:pointer;padding:4px;color:#111827;display:flex;align-items:center">
      <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
    </a>
    <h1 style="font-size:18px;font-weight:700;margin:0">Configuración</h1>
  </div>

  @if(session('status'))
    <div style="margin:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#166534;font-size:14px;font-weight:600">
      {{ session('status') }}
    </div>
  @endif

  <form action="{{ route('configuracion.guardar') }}" method="POST" style="padding:16px">
    @csrf
    <div style="background:white;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;margin-bottom:16px">
      <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:white;border-bottom:1px solid #f3f4f6">
        <span style="font-size:15px;font-weight:600">Notificaciones Push</span>
        <input type="checkbox" name="notificaciones_push" {{ !empty($preferencias['notificaciones_push']) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#4c9540;cursor:pointer"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:white">
        <span style="font-size:15px;font-weight:600">Sonido de nuevos pedidos</span>
        <input type="checkbox" name="sonido_pedidos" {{ !empty($preferencias['sonido_pedidos']) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:#4c9540;cursor:pointer"><script>(function(c){if(!c||c.dataset.validacionCampo)return;c.dataset.validacionCampo='1';var e=c.nextElementSibling&&c.nextElementSibling.classList.contains('field-error-inline')?c.nextElementSibling:document.createElement('small');e.className='field-error-inline';e.style.cssText='display:block;color:#c62828;font-size:12px;margin-top:4px;min-height:1em;';if(!e.parentNode)c.insertAdjacentElement('afterend',e);function v(){var x=c.required&&!c.value.trim(),i=x||!c.validity.valid;e.textContent=i?(x?'Este campo es obligatorio.':c.validationMessage):'';c.setAttribute('aria-invalid',i?'true':'false');return!i}['input','change','blur'].forEach(function(t){c.addEventListener(t,v)});if(c.form)c.form.addEventListener('submit',function(a){if(!v())a.preventDefault()})})(document.currentScript.previousElementSibling);</script>
      </div>
    </div>

    <button type="submit" style="display:flex;align-items:center;justify-content:center;background:#4c9540;color:white;padding:12px;border-radius:10px;font-weight:700;cursor:pointer;width:100%;border:none;font-family:'Inter',sans-serif;font-size:15px">
      Guardar preferencias
    </button>
  </form>
</main>
@endsection
