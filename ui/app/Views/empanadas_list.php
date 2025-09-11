<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>🥟 Gestión de Empanadas</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    td[contenteditable="true"]:focus {
      outline: 2px solid rgb(59 130 246);
      border-radius: .25rem;
      padding: .25rem;
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-800">
  <div class="max-w-6xl mx-auto p-6">
    <header class="flex items-center justify-between mb-6">
      <h1 class="text-3xl md:text-4xl font-bold tracking-tight flex items-center gap-3">
        <span class="text-amber-500">🥟</span> Gestión de Empanadas
      </h1>
      <span class="hidden md:inline-flex items-center gap-2 text-sm text-slate-500">
        <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full"></span> API conectada
      </span>
    </header>

    <!-- Crear -->
    <section class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl p-4 md:p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
        <span class="text-amber-500">🥟</span> Nueva empanada
      </h2>

      <form id="form" class="grid grid-cols-1 md:grid-cols-5 gap-3" novalidate>
        <input name="name" placeholder="Nombre" required
          class="col-span-1 md:col-span-2 px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        <select name="type" required
          class="px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <option value="">Tipo…</option>
          <option>Horno</option>
          <option>Frita</option>
        </select>
        <input name="filling" placeholder="Relleno" required
          class="px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        <input name="price" type="number" step="0.01" min="0.01" placeholder="Precio" required
          class="px-3 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
        <button class="md:col-span-5 md:justify-self-end inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition shadow">
          Agregar
        </button>
      </form>
      <p id="msg" class="mt-3 text-sm text-slate-500 hidden"></p>
    </section>

    <!-- Tabla -->
    <section class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700 uppercase text-xs tracking-wide">
            <tr>
              <th class="text-left px-4 py-3">ID</th>
              <th class="text-left px-4 py-3">Nombre</th>
              <th class="text-left px-4 py-3">Tipo</th>
              <th class="text-left px-4 py-3">Agotada</th>
              <th class="text-left px-4 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="empty" class="hidden p-6 text-center text-slate-500">
        No hay empanadas aún. ¡Agrega la primera! 🥟
      </div>
    </section>
  </div>

  <script>
    const API = (p = '') => (location.protocol + '//' + location.hostname + ':3000') + p;
    const API_KEY = ''; 
    if (API_KEY) $.ajaxSetup({
      headers: {
        'X-API-KEY': API_KEY
      }
    });

    const showMsg = (txt, ok = true) => {
      $('#msg').removeClass('hidden')
        .text(txt)
        .toggleClass('text-emerald-600', ok)
        .toggleClass('text-rose-600', !ok);
      setTimeout(() => $('#msg').addClass('hidden'), 2000);
    };

    const render = (list = []) => {
      const $tb = $('#tbody').empty();
      if (!list.length) return $('#empty').removeClass('hidden');
      $('#empty').addClass('hidden');

      list.forEach(e => {
        $tb.append(`
          <tr class="border-t border-slate-100 hover:bg-slate-50">
            <td class="px-4 py-3 font-semibold text-slate-700">${e.id}</td>
            <td class="px-4 py-3"><span class="inline-block" contenteditable data-f="name">${e.name ?? ''}</span></td>
            <td class="px-4 py-3"><span class="inline-block" contenteditable data-f="type">${e.type ?? ''}</span></td>
            <td class="px-4 py-3">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" data-f="is_sold_out" ${e.is_sold_out ? 'checked':''} class="w-4 h-4 accent-amber-500">
                <span class="text-slate-600">Agotada</span>
              </label>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <button data-act="save" data-id="${e.id}" class="px-3 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">Guardar</button>
                <button data-act="del"  data-id="${e.id}" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition">Eliminar</button>
              </div>
            </td>
          </tr>
        `);
      });
    };

    // Listar
    const load = () => {
      $.get(API('/api/empanadas'))
        .done(render)
        .fail(() => showMsg('Error al listar', false));
    };

    // Validaciones
    const onlyLetters = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/;

    // Crear
    $('#form').on('submit', function(ev) {
      ev.preventDefault();
      const fd = new FormData(this);
      let {
        name = '', type = '', filling = '', price = ''
      } = Object.fromEntries(fd.entries());
      name = name.trim();
      type = type.trim();
      filling = filling.trim();
      price = Number(price);

      //validaciones
      if (!name || !onlyLetters.test(name)) return showMsg('Nombre: solo letras y espacios', false);
      if (!type || !['Horno', 'Frita'].includes(type)) return showMsg('Tipo inválido', false);
      if (!filling) return showMsg('Relleno es obligatorio', false);
      if (!Number.isFinite(price) || price <= 0) return showMsg('Precio debe ser > 0', false);

      $.ajax({
          url: API('/api/empanada'),
          method: 'POST',
          data: JSON.stringify({
            name,
            type,
            filling,
            price
          }),
          contentType: 'application/json',
          processData: false
        })
        .done(() => {
          this.reset();
          load();
          showMsg('Empanada creada ✔');
        })
        .fail(() => showMsg('Error al crear', false));
    });

    // Guardar
    //  Eliminar
    $(document).on('click', '#tbody button', function() {
      const $btn = $(this);
      const id = $btn.data('id');
      const act = $btn.data('act');
      const $tr = $btn.closest('tr');

      if (act === 'del') {
        $.ajax({
            url: API(`/api/empanada/${id}`),
            method: 'DELETE'
          })
          .done(() => {
            load();
            showMsg('Eliminada ✔');
          })
          .fail(() => showMsg('No se pudo eliminar', false));
      }

      if (act === 'save') {
        const name = $tr.find('[data-f="name"]').text().trim();
        const type = $tr.find('[data-f="type"]').text().trim();
        const sold = $tr.find('[data-f="is_sold_out"]').is(':checked');

        if (!name || !onlyLetters.test(name)) return showMsg('Nombre inválido (solo letras)', false);
        if (!['Horno', 'Frita'].includes(type)) return showMsg('Tipo debe ser Horno o Frita', false);

        $.ajax({
            url: API(`/api/empanada/${id}`),
            method: 'PUT', //ojito error
            data: JSON.stringify({
              name,
              type,
              is_sold_out: sold
            }),
            contentType: 'application/json',
            processData: false
          })
          .done(() => {
            load();
            showMsg('Actualizada ✔');
          })
          .fail(() => showMsg('Error al actualizar', false));
      }
    });

    // init
    load();
  </script>
</body>

</html>