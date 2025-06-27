// app.js - PWA Capturista
// Punto de entrada principal para la lógica de la PWA

document.addEventListener('DOMContentLoaded', () => {
  renderLogin();
});

function renderLogin() {
  document.getElementById('app').innerHTML = `
    <div class="login-card">
      <h2>Iniciar Sesión</h2>
      <form id="loginForm">
        <input type="email" id="email" placeholder="Correo electrónico" required autofocus>
        <input type="password" id="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
      </form>
      <div id="loginMsg" class="login-msg"></div>
    </div>
  `;
  document.getElementById('loginForm').onsubmit = loginHandler;
}

let db;

async function loginHandler(e) {
  e.preventDefault();
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const msg = document.getElementById('loginMsg');
  msg.textContent = 'Verificando...';
  try {
    const res = await fetch('../api/login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    const data = await res.json();
    if (data.success) {
      localStorage.setItem('authToken', data.token);
      localStorage.setItem('usuario', JSON.stringify(data.usuario));
      msg.textContent = '¡Bienvenido, ' + data.usuario.nombre + '!';
      await abrirDB();
      await descargarYGuardarActividades();
      msg.textContent = 'Actividades descargadas y guardadas localmente.';
      renderActividades();
      // Aquí irá la navegación a la app principal
    } else {
      msg.textContent = data.message || 'Error de autenticación.';
    }
  } catch (err) {
    msg.textContent = 'Error de conexión.';
  }
}

function abrirDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('capturistaDB', 1);
    request.onupgradeneeded = function(e) {
      db = e.target.result;
      if (!db.objectStoreNames.contains('actividades_locales')) {
        db.createObjectStore('actividades_locales', { keyPath: 'id' });
      }
    };
    request.onsuccess = function(e) {
      db = e.target.result;
      resolve();
    };
    request.onerror = function() {
      reject('No se pudo abrir la base de datos local.');
    };
  });
}

async function descargarYGuardarActividades() {
  const token = localStorage.getItem('authToken');
  const res = await fetch('../api/actividades.php', {
    headers: { 'Authorization': 'Bearer ' + token }
  });
  const data = await res.json();
  if (data.success && Array.isArray(data.actividades)) {
    const tx = db.transaction('actividades_locales', 'readwrite');
    const store = tx.objectStore('actividades_locales');
    // Limpiar store antes de guardar nuevas actividades
    store.clear();
    data.actividades.forEach(act => store.put(act));
    await tx.complete;
  } else {
    throw new Error('No se pudieron descargar actividades.');
  }
}

function renderActividades() {
  document.getElementById('app').innerHTML = `
    <div class="actividades-card">
      <h2>Mis Actividades</h2>
      <ul id="listaActividades" class="actividades-list"></ul>
      <button onclick="renderLogin()" style="margin-top:24px">Cerrar sesión</button>
    </div>
  `;
  cargarActividadesLocales();
}

function cargarActividadesLocales() {
  const tx = db.transaction('actividades_locales', 'readonly');
  const store = tx.objectStore('actividades_locales');
  const req = store.getAll();
  req.onsuccess = function() {
    const actividades = req.result;
    const ul = document.getElementById('listaActividades');
    if (!actividades.length) {
      ul.innerHTML = '<li>No hay actividades descargadas.</li>';
      return;
    }
    ul.innerHTML = actividades.map(act => `
      <li class="actividad-item">
        <strong>${act.nombre}</strong><br>
        <span>${act.lugar}</span><br>
        <span>${act.fecha_inicio} a ${act.fecha_fin}</span>
      </li>
    `).join('');
  };
} 