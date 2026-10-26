<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Widget demo — Central Hub</title>
    @fonts
    @vite(['resources/css/app.css'])
    <style>
        body { margin: 0; font-family: 'Instrument Sans', system-ui, sans-serif; background: #f8fafc; color: #0f172a; }
        .page { max-width: 42rem; margin: 0 auto; padding: 3rem 1.25rem; }
        h1 { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Fragrance Store</h1>
        <p>Demo storefront page with the Central Hub support widget loaded in the corner.</p>
        <p style="color:#64748b;font-size:0.875rem;">Click the Support button (bottom-right) to try the widget UI.</p>
    </div>

    <script src="{{ asset('widget.js') }}" data-site-key="pk_frg_live_demo" data-color="#4338CA" defer></script>
</body>
</html>
