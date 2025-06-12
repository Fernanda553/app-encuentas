# App Encuestas

Sistema de votación en tiempo real para eventos y encuestas internas.

## Tecnologías

-   **Laravel 12** (framework principal)
-   **Filament 3** (panel administrativo)
-   **Livewire 3** (interfaces reactivas)
-   **Tailwind CSS** (diseño)
-   **PostgreSQL** (base de datos)
-   **Queue Jobs** (procesamiento asíncrono)
-   **Vite** (assets)

## Instalación

1. **Clona el repositorio:**

    ```bash
    git clone https://github.com/tu-usuario/app-encuestas.git
    cd app-encuestas
    ```

2. **Instala dependencias:**

    ```bash
    composer install
    npm install
    ```

3. **Configura el entorno:**

    ```bash
    cp .env.example .env
    ```

    Edita el archivo `.env` y asegúrate de tener:

    ```
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_de_tu_base
    DB_USERNAME=usuario
    DB_PASSWORD=contraseña
    ```

4. **Genera la clave de la app:**

    ```bash
    php artisan key:generate
    ```

5. **Ejecuta migraciones y seeders:**

    ```bash
    php artisan migrate --seed
    ```

6. **Compila los assets:**

    ```bash
    npm run build
    # o para desarrollo
    npm run dev
    ```

7. **Inicia las colas:**

    ```bash
    php artisan queue:work
    ```

8. **Inicia el servidor:**
    ```bash
    php artisan serve
    ```

## Uso

-   Accede a `/admin` para el panel Filament y gestiona encuestas, preguntas y respuestas.
-   Los usuarios pueden votar de forma anónima y ver resultados en tiempo real en la página principal.

## Características

-   Votación anónima (por IP + sesión)
-   Resultados en tiempo real (Livewire)
-   Prevención de votos duplicados
-   Procesamiento asíncrono de votos (Jobs/Queue)
-   Responsive y moderno (Tailwind)
-   Panel administrativo completo (Filament)

## Convenciones de commits

Usa [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) para tus mensajes de commit.

## Despliegue

Puedes desplegar en Laravel Cloud, Forge, o cualquier VPS compatible con PHP 8.2+ y PostgreSQL.
