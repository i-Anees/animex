<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ANIMEX WEAR — Anime Streetwear</title>
<link rel="icon" href="{{ asset('img/brand/monogram.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('css/foundation.css') }}">
<link rel="stylesheet" href="{{ asset('store/kit.css') }}">
<script defer src="https://unpkg.com/react@18.3.1/umd/react.production.min.js" crossorigin="anonymous"></script>
<script defer src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.production.min.js" crossorigin="anonymous"></script>
<script>
  window.__ANIMEX_DATA__ = @json($data);
  window.__CSRF__ = "{{ csrf_token() }}";
  window.__ORDER_URL__ = "{{ route('orders.place') }}";
</script>
<script defer src="{{ asset('store/dist/data.js') }}"></script>
<script defer src="{{ asset('store/dist/ui.js') }}"></script>
<script defer src="{{ asset('store/dist/chrome.js') }}"></script>
<script defer src="{{ asset('store/dist/Home.js') }}"></script>
<script defer src="{{ asset('store/dist/Shop.js') }}"></script>
<script defer src="{{ asset('store/dist/Product.js') }}"></script>
<script defer src="{{ asset('store/dist/Checkout.js') }}"></script>
<script defer src="{{ asset('store/dist/App.js') }}"></script>
</head>
<body>
<div id="root"></div>
</body>
</html>
