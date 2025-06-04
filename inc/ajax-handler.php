<?php
/**
 * AJAX handler for filtering bids with pagination.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Process AJAX requests to filter bids.
 */
function filtrar_licitaciones() {
    // Get current user roles
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;

    // Only administrators or customers can access
    if (!in_array('administrator', $user_roles) && !in_array('customer', $user_roles)) {
        wp_send_json_error('Debe tener una suscripción para acceder al buscador de licitaciones.', 403);
    }

    $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;

    // Base WP_Query arguments
    $args = array(
        'post_type' => 'licitacion',
        'posts_per_page' => 5,
        'paged' => $paged,
    );

    // Build meta_query and tax_query
    $args['meta_query'] = array('relation' => 'AND');
    $args['tax_query'] = array('relation' => 'AND');

    // Sort by opening date
    if (!empty($_POST['orden']) && in_array($_POST['orden'], array('ASC', 'DESC'))) {
        $args['meta_key'] = 'fecha_apertura';
        $args['orderby'] = 'meta_value';
        $args['order'] = sanitize_text_field($_POST['orden']);
    }

    // Search by text
    if (!empty($_POST['buscador'])) {
        $args['s'] = sanitize_text_field($_POST['buscador']);
    }

    // Filter by type of work
    if (!empty($_POST['tipo_de_obra'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'tipo-de-obra',
            'field' => 'slug',
            'terms' => sanitize_text_field($_POST['tipo_de_obra']),
        );
    }

    // Filter by location
    if (!empty($_POST['lugar'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'lugar',
            'field' => 'slug',
            'terms' => sanitize_text_field($_POST['lugar']),
        );
    }

    // Filter by date range
    if (!empty($_POST['fecha_desde']) || !empty($_POST['fecha_hasta'])) {
        $fecha_desde = !empty($_POST['fecha_desde']) ? $_POST['fecha_desde'] : '1970-01-01';
        $fecha_hasta = !empty($_POST['fecha_hasta']) ? $_POST['fecha_hasta'] : date('Y-m-d');

        $args['meta_query'][] = array(
            'key' => 'fecha_apertura',
            'value' => array($fecha_desde, $fecha_hasta),
            'compare' => 'BETWEEN',
            'type' => 'DATE',
        );
    }

    // Show only today's bids
    if (!empty($_POST['ver_solo_hoy']) && $_POST['ver_solo_hoy'] === '1') {
        $today = date('Y-m-d');
        $args['meta_query'][] = array(
            'key' => 'fecha_apertura',
            'value' => $today,
            'compare' => '=',
            'type' => 'DATE',
        );
    }

    $query = new WP_Query($args);
    $licitaciones = array();

    while ($query->have_posts()) {
        $query->the_post();
        $licitaciones[] = array(
            'titulo' => get_the_title(),
            'fecha_apertura' => get_post_meta(get_the_ID(), 'fecha_apertura', true),
            'tipo_de_obra' => wp_get_post_terms(get_the_ID(), 'tipo-de-obra', array('fields' => 'names'))[0] ?? '',
            'lugar' => wp_get_post_terms(get_the_ID(), 'lugar', array('fields' => 'names'))[0] ?? '',
            'comitente' => get_post_meta(get_the_ID(), 'comitente', true),
            'url' => get_permalink(),
        );
    }

    wp_reset_postdata();

    wp_send_json(array(
        'licitaciones' => $licitaciones,
        'current_page' => $paged,
        'total_pages' => $query->max_num_pages,
    ));
}
add_action('wp_ajax_filtrar_licitaciones', 'filtrar_licitaciones');
add_action('wp_ajax_nopriv_filtrar_licitaciones', 'filtrar_licitaciones');
