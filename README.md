
<body>

  <h1>Club CMS</h1>
  <p>Un sistema de gestión de contenido (<strong>CMS</strong>), construido con <strong>Laravel 12.x</strong>, <strong>PHP 8.2</strong>, <strong>MySQL 8.1</strong> y desplegado mediante <strong>Docker</strong> + <strong>Docker Compose</strong>, con <strong>Caddy</strong> como servidor web.</p>

  <h2>Descripción del proyecto</h2>
  <p>Este proyecto proporciona una <strong>API REST</strong> para la gestión de un club deportivo. Consta de dos áreas principales:</p>
  <ul>
    <li><strong>Panel de gestión:</strong> Control de acceso, usuarios, permisos y administración general de la aplicación.</li>
    <li><strong>Gestión de club polideportivo:</strong> CRUD de socios, pistas, reservas, y lógica de negocio para el sistema de reservas.</li>
  </ul>

  <h2>Funcionalidad exigida</h2>
  <ul>
    <li><strong>CRUD de Usuarios:</strong> Registro, login, logout y gestión de roles/ permisos.</li>
    <li><strong>CRUD de Deportes:</strong> (Tenis, Pádel, Fútbol, Baloncesto...)</li>
    <li><strong>CRUD de Pistas:</strong> Cada pista asociada a un único deporte.</li>
    <li><strong>CRUD de Socios:</strong> Gestión de datos de los miembros del club.</li>
    <li><strong>CRUD de Reservas:</strong> Un socio reserva una pista para una franja horaria.</li>
    <li><strong>Buscador de Pistas:</strong> Dada una fecha, deporte y socio, devuelve las pistas disponibles.</li>
    <li><strong>Listado de Reservas del Día:</strong> Dada una fecha, devuelve todas las reservas confirmadas con datos de pista, socio y deporte.</li>
  </ul>

  <h2>Lógica de negocio</h2>
  <ul>
    <li>Reservas disponibles entre las <strong>08:00</strong> y las <strong>22:00</strong> de lunes a domingo en franjas de 1 hora.</li>
    <li>No se permiten dos reservas de la misma pista en la misma franja horaria.</li>
    <li>Un socio no puede reservar más de 2 pistas simultáneamente y un máximo de 3 reservas por día.</li>
  </ul>

  <h2>Entrega y documentación</h2>
  <ul>
    <li>Scripts de base de datos:
      <ul>
        <li><code>schema.sql</code> – Estructura completa de tablas.</li>
        <li><code>database/seeders</code> – Datos iniciales de ejemplo mediante migración con seeders.</li>
      </ul>
    </li>
    <li>Especificación de la API en Swagger (JSON): <code>storage/api-docs/api-docs.json</code></li>
    <li>Esquema entidad-relación de la base de datos (PDF): <code>er-diagram.pdf</code></li>
    <li>Colección Postman para pruebas: <code>ClubCMS.postman_collection.json</code></li>
    <li>Archivo de instrucciones (este README).</li>
  </ul>

  <h2>Características principales</h2>
  <ul>
    <li>API RESTful documentada con <strong>Swagger UI</strong> en <code>/api/documentation</code></li>
    <li><p>Se utiliza <strong>Laravel Passport</strong> para OAuth2 y gestión de tokens de acceso.</p></li>
    <li>Migraciones, seeders y factories para desarrollo y demo</li>
    <li>Despliegue con Docker</li>
    <li><p>Aplicación API REST. No hay interfaz web excepto la documentación de la API con <strong>Swagger UI</strong>.</p></li>
    <li><p>Documentación disponible en <a href="http://localhost/api/documentation">/api/documentation</a>.</p></li>
  </ul>

  <h2>Requisitos / Versiones</h2>
  <ul>
    <li>Docker ≥ 20.10</li>
    <li>Docker Compose ≥ 1.29</li>
    <li>Git ≥ 2.30</li>
    <li>Laravel 12.x</li>
    <li>PHP 8.2</li>
    <li>MySQL 8.1</li>
  </ul>

  <h2>Estructura del repositorio</h2>
  <pre><code>├── Caddyfile
├── dockerfile
├── docker-compose.yml
└── club_cms
      ├── app
      │   ├── Http
      │   ├── Models
      │   ├── OpenApi
      │   └── Providers
      ├── artisan
      ├── bootstrap
      ├── composer.json
      ├── composer.lock
      ├── config
      ├── database
      │   ├── database.sqlite
      │   ├── factories
      │   ├── migrations
      │   └── seeders
      ├── package.json
      ├── phpunit.xml
      ├── public
      ├── README.md
      ├── resources
      ├── routes
      │   ├── api.php
      │   ├── console.php
      │   └── web.php
      ├── storage
      │   ├── api-docs
      │   ├── app
      │   ├── framework
      │   ├── logs
      │   ├── oauth-private.key
      │   └── oauth-public.key
      ├── tests
      ├── vendor
      └── vite.config.js</code></pre>

  <h2>Instalación y puesta en marcha</h2>
  <p><em>El archivo <code>.env</code> ya viene incluido y configurado en el repositorio. En principio <strong>no es necesario</strong> copiar ni editar nada para el despliegue inicial.</em></p>
  <ol>
    <li>
      <strong>Clona el proyecto:</strong>
      <pre><code>git clone https://github.com/tu-usuario/club_cms.git
cd club_cms</code></pre>
    </li>
    <li>
      <strong>Arranca los contenedores:</strong>
      <pre><code>docker-compose up -d</code></pre>
      <p>Servicios levantados:</p>
      <ul>
        <li><code>club_cms_webserver</code> (Caddy puertos 80/443)</li>
        <li><code>club_cms_php</code> (PHP-FPM 8.2)</li>
        <li><code>club_cms_database</code> (MySQL 8.1)</li>
      </ul>
    </li>
    <li>
      <strong>Instalación de dependencias:</strong>
      <pre><code>docker-compose exec club_cms_php composer install</code></pre>
    </li>
    <li>
      <strong>Crea y rellena la base de datos de demo:</strong>
      <pre><code>docker-compose exec club_cms_php php artisan migrate:fresh --seed</code></pre>
    </li>
    <li>
      <strong>Ejecuta en terminal para obtener el Personal accsess client</strong>
      <pre><code>php artisan passport:client --personal</code></pre>
      <strong>Aparecerá el formulario</strong>
      <strong>Ingresa nombre o pulsa enter para continuar con 'Laravel'</strong>
      <pre>What should we name the client? [Laravel]</pre>
      <strong>Pulsamos enter otra vez'</strong>
      <pre>Which user provider should this client use to retrieve users? [users]</pre>
      <strong>Y se habrá confugurado el nuevo cliente personal</strong>
      <pre> INFO  New client created successfully.</pre>
    </li>
  </ol>

  <h2>Uso diario</h2>
  <ul>
    <li>Accede a la aplicación en: <a href="http://localhost">http://localhost</a></li>
    <li>Swagger UI en: <a href="http://localhost/api/documentation">/api/documentation</a></li>
    <li>Colección Postman: <code>postman/ClubCMS.postman_collection.json</code></li>
  </ul>
  <h3>Shells de contenedores</h3>
  <ul>
    <li>PHP:
      <pre><code>docker-compose exec club_cms_php sh</code></pre>
    </li>
    <li>MySQL:
      <pre><code>docker-compose exec club_cms_database mysql -u root -p</code></pre>
    </li>
  </ul>
  <h3>Comandos útiles de Artisan</h3>
  <pre><code>docker-compose exec club_cms_php php artisan route:clear</code></pre>
  <pre><code>docker-compose exec club_cms_php php artisan route:list</code></pre>
  <p>Para detener y eliminar todo:</p>
  <pre><code>docker-compose down -v</code></pre>

  <h2>Contacto</h2>
  <ul>
    <li>GitHub: <a href="https://github.com/CleanMatyx">CleanMatyx</a></li>
    <li>Email: <a href="mailto:mtsbrr07@gmail.com">Correo</a></li>
  </ul>

  <p style="text-align:center; margin-top:2em;">¡Gracias por usar <strong>Club CMS</strong>!</p>

</body>
