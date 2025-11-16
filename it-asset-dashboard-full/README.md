# IT Asset Management – WordPress‑Ready Static Template

Pure HTML + CSS (no build tools). Minimal vanilla JS for modals only.
Pages:
- Dashboard (index.html)
- Employees (employees.html) + Add Employee (employees-add.html)
- Assets (assets.html) + Add Asset (assets-add.html)
- Search (search.html, search-result.html)
- Licenses (licenses.html)
- Reports & Clearance (reports.html)

## Use in WordPress
1) Copy the `assets/` folder and any page HTML into your plugin or theme template.
2) For a plugin admin page, load an HTML file inside a PHP callback via `add_menu_page` and echo its contents, or refactor markup as a PHP template.
3) The stylesheet is `assets/css/styles.css`. Script `assets/js/app.js` handles modals.
4) No external dependencies (Tailwind/Bootstrap). Safe to enqueue via `wp_enqueue_style/script`.
