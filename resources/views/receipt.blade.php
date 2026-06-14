<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $order->number }} — ANIMEX WEAR</title>
<link rel="stylesheet" href="{{ asset('css/foundation.css') }}">
<style>
  * { box-sizing: border-box; }
  body { font-family: var(--font-body, Arial, sans-serif); color: #111; margin: 0; background: #f2f2f2; }
  .page { max-width: 820px; margin: 0 auto; padding: 24px 16px 60px; }

  /* editor panel (screen only) */
  .editor { background: #fff; border: 1px solid #e2e2e2; padding: 26px 28px; margin-bottom: 22px; }
  .editor h2 { font-family: var(--font-display, Arial); font-weight: 800; text-transform: uppercase; letter-spacing: -.01em; font-size: 18px; margin: 0 0 4px; }
  .editor .hint { font-family: var(--font-mono, monospace); font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: #999; margin: 0 0 18px; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .grid .full { grid-column: 1 / -1; }
  .field label { display: block; font-family: var(--font-mono, monospace); font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: #777; margin: 0 0 6px; }
  .field input, .field select { width: 100%; padding: 11px 12px; border: 1px solid #ccc; font-family: var(--font-body, Arial); font-size: 14px; background: #fff; }
  .field input:focus, .field select:focus { outline: none; border-color: #000; }
  .editor .actions { display: flex; gap: 10px; margin-top: 18px; align-items: center; }
  .saved { font-family: var(--font-mono, monospace); font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: #0a7d28; }

  .toolbar { display: flex; gap: 10px; justify-content: flex-end; margin-bottom: 14px; }
  .btn { font-family: var(--font-display, Arial); font-weight: 700; text-transform: uppercase; letter-spacing: .06em; font-size: 12px; padding: 11px 20px; border: 1px solid #000; background: #000; color: #fff; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
  .btn--ghost { background: #fff; color: #000; }

  /* receipt sheet */
  .sheet { background: #fff; padding: 56px 56px 40px; box-shadow: 0 10px 40px rgba(0,0,0,.10); }
  .rcpt-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 22px; }
  .rcpt-logo { font-family: var(--font-display, Arial); font-weight: 900; font-size: 26px; letter-spacing: -.02em; text-transform: uppercase; }
  .rcpt-logo span { font-weight: 300; }
  .rcpt-meta { text-align: right; font-family: var(--font-mono, monospace); font-size: 11px; letter-spacing: .08em; text-transform: uppercase; line-height: 1.9; color: #444; }
  .rcpt-meta b { color: #000; }
  .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 30px 0; }
  .blk h4 { font-family: var(--font-mono, monospace); font-size: 10px; letter-spacing: .18em; text-transform: uppercase; color: #888; margin: 0 0 8px; }
  .blk p { margin: 0; line-height: 1.6; font-size: 14px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th { text-align: left; font-family: var(--font-mono, monospace); font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: #888; border-bottom: 1px solid #ddd; padding: 10px 0; }
  th.r, td.r { text-align: right; }
  td { padding: 14px 0; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: top; }
  td .sku { font-family: var(--font-mono, monospace); font-size: 11px; color: #999; }
  .totals { margin-left: auto; width: 280px; margin-top: 18px; }
  .totals .row { display: flex; justify-content: space-between; font-family: var(--font-mono, monospace); font-size: 13px; padding: 7px 0; }
  .totals .row.grand { border-top: 2px solid #000; margin-top: 8px; padding-top: 14px; font-size: 17px; font-weight: 700; }
  .pill { display: inline-block; font-family: var(--font-mono, monospace); font-size: 10px; letter-spacing: .12em; text-transform: uppercase; padding: 4px 9px; background: #000; color: #fff; }
  .pill--paid { background: #0a7d28; }
  .foot { margin-top: 46px; border-top: 1px solid #eee; padding-top: 18px; text-align: center; font-family: var(--font-mono, monospace); font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: #999; }

  @media print {
    body { background: #fff; }
    .page { max-width: none; padding: 0; }
    .no-print { display: none !important; }
    .sheet { box-shadow: none; padding: 0; }
  }
</style>
</head>
<body>
<div class="page">

  <div class="toolbar no-print">
    <a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('index') }}" class="btn btn--ghost">← Back to orders</a>
    <button class="btn" onclick="window.print()">🖨 Print / Save PDF</button>
  </div>

  {{-- Editable details (screen only) --}}
  @php($addr = $order->shipping_address ?? [])
  <form class="editor no-print" method="POST" action="{{ route('order.receipt.update', $order) }}">
    @csrf
    @method('PUT')
    <h2>Edit receipt details</h2>
    <p class="hint">Update contact, delivery &amp; status — saved to the order, then printed.</p>

    @if(isset($errors) && $errors->any())
      <div class="saved" style="color:#b11919;margin-bottom:12px;">{{ $errors->first() }}</div>
    @endif

    <div class="grid">
      <div class="field"><label>Customer name</label><input name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $order->email) }}" required></div>
      <div class="field full"><label>Delivery address</label><input name="address" value="{{ old('address', $addr['address'] ?? '') }}"></div>
      <div class="field"><label>City</label><input name="city" value="{{ old('city', $addr['city'] ?? $order->city) }}"></div>
      <div class="field"><label>Postcode</label><input name="postcode" value="{{ old('postcode', $addr['postcode'] ?? '') }}"></div>
      <div class="field"><label>Country</label><input name="country" value="{{ old('country', $addr['country'] ?? '') }}"></div>
      <div class="field">
        <label>Fulfillment status</label>
        <select name="status">
          @foreach(['unfulfilled','processing','fulfilled','cancelled'] as $st)
            <option value="{{ $st }}" @selected(old('status', $order->status) === $st)>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>Payment status</label>
        <select name="payment_status">
          @foreach(['pending','paid','refunded'] as $ps)
            <option value="{{ $ps }}" @selected(old('payment_status', $order->payment_status) === $ps)>{{ ucfirst($ps) }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="actions">
      <button type="submit" class="btn">Save details</button>
      @if(session('saved'))<span class="saved">✓ {{ session('saved') }}</span>@endif
    </div>
  </form>

  {{-- Printable receipt --}}
  <div class="sheet">
    <div class="rcpt-head">
      <div>
        <div class="rcpt-logo">ANIMEX<span> WEAR</span></div>
        <div style="font-family:var(--font-mono,monospace);font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:#999;margin-top:6px;">Anime, made into fashion</div>
      </div>
      <div class="rcpt-meta">
        <div>Receipt <b>{{ $order->number }}</b></div>
        <div>{{ optional($order->placed_at)->format('M j, Y · H:i') }}</div>
        <div><span class="pill {{ $order->payment_status === 'paid' ? 'pill--paid' : '' }}">{{ strtoupper($order->payment_status) }}</span></div>
      </div>
    </div>

    <div class="grid2">
      <div class="blk">
        <h4>Billed To</h4>
        <p><b>{{ $order->customer_name }}</b></p>
        <p>{{ $order->email }}</p>
      </div>
      <div class="blk">
        <h4>Deliver To</h4>
        <p>{{ $addr['address'] ?? '—' }}</p>
        <p>{{ $addr['city'] ?? $order->city }} {{ $addr['postcode'] ?? '' }}</p>
        <p>{{ $addr['country'] ?? '' }}</p>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Size</th>
          <th class="r">Qty</th>
          <th class="r">Price</th>
          <th class="r">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
        <tr>
          <td>{{ $item->name }}<br><span class="sku">{{ $item->sku }}</span></td>
          <td>{{ $item->size ?? '—' }}</td>
          <td class="r">{{ $item->qty }}</td>
          <td class="r">AED {{ number_format($item->price, 2) }}</td>
          <td class="r">AED {{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="totals">
      <div class="row"><span>Subtotal</span><span>AED {{ number_format($order->subtotal, 2) }}</span></div>
      @if($order->discount > 0)<div class="row"><span>Discount</span><span>−AED {{ number_format($order->discount, 2) }}</span></div>@endif
      <div class="row"><span>Shipping</span><span>{{ $order->shipping > 0 ? 'AED '.number_format($order->shipping, 2) : 'FREE' }}</span></div>
      <div class="row"><span>Tax</span><span>AED {{ number_format($order->tax, 2) }}</span></div>
      <div class="row grand"><span>Total</span><span>AED {{ number_format($order->total, 2) }}</span></div>
    </div>

    <div class="foot">
      Thank you for shopping ANIMEX WEAR · Numbered limited editions · 30-day returns on unworn items<br>
      This is a computer-generated receipt for order {{ $order->number }}.
    </div>
  </div>
</div>
</body>
</html>
