<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mini Ticket Tracker</title>
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; color: #111827; padding: 2rem; }
        .container { max-width: 960px; margin: 0 auto; }
        h1 { font-size: 1.4rem; margin-bottom: 1.5rem; }

        /* Stats */
        .stats { display: flex; gap: 1rem; margin-bottom: 1.75rem; }
        .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem 1.5rem; flex: 1; text-align: center; }
        .stat-card .count { font-size: 2rem; font-weight: 700; }
        .stat-card .label { font-size: 0.8rem; color: #6b7280; margin-top: 2px; }

        /* Create form */
        .form-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem; }
        .form-card h2 { font-size: 0.95rem; margin-bottom: 1rem; }
        .form-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .form-row input[name=title] { flex: 1; min-width: 200px; }
        .form-row textarea { width: 100%; margin-top: 0.5rem; }
        input, textarea, select { border: 1px solid #d1d5db; border-radius: 6px; padding: 0.45rem 0.65rem; font-size: 0.875rem; font-family: inherit; }
        input:focus, textarea:focus, select:focus { outline: 2px solid #6366f1; outline-offset: 1px; }
        button { background: #111827; color: #fff; border: none; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.875rem; cursor: pointer; white-space: nowrap; }
        button:hover { background: #374151; }

        /* Table */
        .table-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f9fafb; }
        th { padding: 0.6rem 1rem; text-align: left; font-size: 0.75rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
        td { padding: 0.75rem 1rem; font-size: 0.875rem; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .ticket-title { font-weight: 500; }
        .ticket-desc { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }

        /* Badges */
        .badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 600; }
        .badge-low        { background: #f3f4f6; color: #374151; }
        .badge-medium     { background: #fef9c3; color: #854d0e; }
        .badge-high       { background: #fee2e2; color: #991b1b; }
        .badge-open       { background: #dbeafe; color: #1d4ed8; }
        .badge-in-progress{ background: #fef3c7; color: #92400e; }
        .badge-closed     { background: #d1fae5; color: #065f46; }

        /* Status select */
        .status-select { border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px; padding: 0.3rem 0.5rem; font-size: 0.8rem; cursor: pointer; }

        /* HTMX indicator */
        .htmx-indicator { opacity: 0; transition: opacity 200ms; }
        .htmx-request .htmx-indicator { opacity: 1; }
        .htmx-request.htmx-indicator  { opacity: 1; }

        /* Pagination */
        .pagination-info { font-size: 0.8rem; color: #9ca3af; margin-top: 0.75rem; }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>

    <script>
        document.body.addEventListener('htmx:configRequest', function (evt) {
            if (evt.detail.verb !== 'get') {
                evt.detail.headers['X-CSRF-TOKEN'] =
                    document.querySelector('meta[name="csrf-token"]').content;
            }
        });
    </script>
</body>
</html>
