<?php
/**
 * Shortcode registration and enqueuing of frontend assets.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register and enqueue CSS and JS when shortcode is used.
 */
function licitaciones_enqueue_assets() {
    // Register styles and scripts
    wp_register_style('licitaciones-styles', plugin_dir_url(__FILE__) . '../assets/css/licitaciones-styles.css');
    wp_register_script('licitaciones-scripts', plugin_dir_url(__FILE__) . '../assets/js/licitaciones-scripts.js', array('jquery'), null, true);

    // Localize AJAX URL for the script
    wp_localize_script('licitaciones-scripts', 'licitacionesAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

/**
 * Shortcode callback that renders the form and results containers.
 *
 * @return string HTML of the interface.
 */
function licitaciones_busqueda_shortcode() {
    // Ensure assets are enqueued
    licitaciones_enqueue_assets();
    wp_enqueue_style('licitaciones-styles');
    wp_enqueue_script('licitaciones-scripts');

    ob_start();

    // Get current user roles
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;

    if (in_array('administrator', $user_roles) || in_array('customer', $user_roles)) {
        // Get taxonomy terms
        $tipos_de_obra = get_terms(array('taxonomy' => 'tipo-de-obra', 'hide_empty' => false));
        $lugares = get_terms(array('taxonomy' => 'lugar', 'hide_empty' => false));
        ?>
        <div id="licitaciones-contenedor">
            <form id="licitaciones-buscador">
                <!-- Button to view only today's bids -->
                <button type="button" id="toggle-licitaciones-dia" class="toggle-today-btn">Ver sólo licitaciones del día</button>

                <div>
                    <label for="buscador">Buscador</label>
                    <input type="text" id="buscador" name="buscador" placeholder="Ingrese palabras a buscar">
                </div>
                <div>
                    <label for="fecha_desde">Desde</label>
                    <input type="date" id="fecha_desde" name="fecha_desde">
                </div>
                <div>
                    <label for="fecha_hasta">Hasta</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta">
                </div>
                <div>
                    <label for="tipo_de_obra">Tipo de Obra</label>
                    <select id="tipo_de_obra" name="tipo_de_obra">
                        <option value="">Todos los tipos de obra</option>
                        <?php foreach ($tipos_de_obra as $tipo): ?>
                            <option value="<?php echo esc_attr($tipo->slug); ?>"><?php echo esc_html($tipo->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="lugar">Lugar</label>
                    <select id="lugar" name="lugar">
                        <option value="">Todos los lugares</option>
                        <?php foreach ($lugares as $lugar): ?>
                            <option value="<?php echo esc_attr($lugar->slug); ?>"><?php echo esc_html($lugar->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="search-btn">Buscar</button>
                <!-- Button to clear search filters -->
                <button type="button" id="limpiar-filtros" class="clear-btn">Limpiar filtros de búsqueda</button>
            </form>

            <div id="licitaciones-resultados-paginacion">
                <!-- Sorting selector -->
                <div class="ordenarPor">
                    <label for="orden">Ordenar por fecha de apertura:</label>
                    <select id="orden" name="orden">
                        <option value="" selected>Por defecto</option>
                        <option value="DESC">Más reciente primero</option>
                        <option value="ASC">Más antiguo primero</option>
                    </select>
                </div>
                <div id="licitaciones-resultados">
                    <!-- Results will be displayed here -->
                </div>
                <div id="paginacion">
                    <!-- Pagination -->
                </div>
            </div>
        </div>
        <?php
    } else {
        // Show message for other users
        ?>
        <p>Debe tener una suscripción para acceder al buscador de licitaciones, haga <a style="color: #007bff;" href="https://elconstructor.com/suscripciones/">click aquí</a> para ver las suscripciones.</p>
        <style>
            footer {
                position: absolute !important;
                bottom: 0 !important;
                left: 0 !important;  
            }
        </style>
        <?php
    }

    return ob_get_clean();
}
add_shortcode('licitaciones_busqueda', 'licitaciones_busqueda_shortcode');
