# Bids Lookup Plugin (Demo Version)

WordPress plugin that enables an AJAX-powered search interface for filtering and paginating **licitaciones** (public bids/tenders) by keyword, date range, type, location, and more.

> ⚠️ This is a demo version for technical demonstration purposes only. It includes no real credentials and is not suitable for production use.

---

## 🚀 Features

- Registers the `[licitaciones_busqueda]` shortcode to display the search form and results.
- Supports filtering by:
  - Keyword (`buscador`)
  - Date range (`fecha_desde`, `fecha_hasta`)
  - Taxonomy terms (`tipo-de-obra`, `lugar`)
  - "Only today" toggle (`ver_solo_hoy`)
  - Sort by opening date (`fecha_apertura`, ASC/DESC)
- AJAX-powered results with loading spinner and JSON response.
- Paginated result cards (5 per page), including:
  - Title, opening date, type, location, client (comitente), and a “Ver más” link.
- Role-based access control:
  - Only visible to users with role `administrator` or `customer`.
- Clean code separation:
  - `licitaciones-plugin.php`: main loader  
  - `inc/shortcode.php`: shortcode logic and asset enqueueing  
  - `inc/ajax-handler.php`: backend AJAX filter  
  - `assets/css/licitaciones-styles.css`: styles  
  - `assets/js/licitaciones-scripts.js`: AJAX and pagination logic

---

## 📦 Tech Stack

- PHP (WordPress 5.0+)
- JavaScript (vanilla ES6 + `fetch`)
- AJAX via `admin-ajax.php`
- WordPress APIs: `add_shortcode`, `WP_Query`, `wp_send_json`
- Custom meta and taxonomies

---

## 🔧 Setup

```bash
git clone https://github.com/yourusername/licitaciones-plugin-demo.git
```

1. Copy to `/wp-content/plugins/licitaciones-plugin`
2. Activate via WP Admin → Plugins
3. Ensure your site includes:
   - A custom post type `licitacion`
   - Taxonomies: `tipo-de-obra` and `lugar`
   - Custom fields: `fecha_apertura` (YYYY-MM-DD) and optionally `comitente`

---

## ▶️ Usage

Place the shortcode:

```
[licitaciones_busqueda]
```

Anywhere on your site to render the form and results.

**AJAX action:** `filtrar_licitaciones`

### Expected POST parameters:

```json
{
  "paged": 1,
  "buscador": "escuela",
  "fecha_desde": "2023-10-01",
  "fecha_hasta": "2023-11-01",
  "tipo_de_obra": "refaccion",
  "lugar": "caba",
  "orden": "DESC",
  "ver_solo_hoy": "0"
}
```

### Returns:

```json
{
  "licitaciones": [
    {
      "titulo": "Escuela Técnica N°5",
      "fecha_apertura": "2023-10-15",
      "tipo_de_obra": "Refacción",
      "lugar": "CABA",
      "comitente": "Ministerio de Educación",
      "url": "https://tu-sitio.com/licitacion/escuela-tecnica-5"
    }
  ],
  "current_page": 1,
  "total_pages": 3
}
```

---

## 🧪 Testing Locally

To test on a local/staging environment:

- Ensure you have some `licitacion` posts with the required fields.
- Load a page with `[licitaciones_busqueda]`
- Try different filters and check result updates, spinner, and pagination

---

## 🔐 Production Notes

This demo version lacks some hardening for live usage. A production-ready version should include:

- Nonce verification (`check_ajax_referer`)
- Custom REST API endpoint instead of `admin-ajax.php`
- Better error handling and user feedback
- Input sanitization (already included)
- Rate limiting or debounce for requests

---

## 🛑 License

All rights reserved.

This code is published for **non-commercial, educational, and technical demonstration** only.  
You may **not** copy, reuse, modify, or deploy this plugin in a production environment without **explicit written permission** from the author.

For inquiries or collaboration, feel free to reach out.