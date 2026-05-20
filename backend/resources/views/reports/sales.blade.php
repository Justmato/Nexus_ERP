<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e5e5; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-weight: bold; text-align: right; margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <p class="meta">Periodo: {{ $from }} — {{ $to }}</p>
    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->folio }}</td>
                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                <td>{{ $sale->customer?->name ?? 'Mostrador' }}</td>
                <td>${{ number_format($sale->total, 2) }}</td>
                <td>{{ $sale->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="total">Total general: ${{ number_format($total, 2) }}</p>
</body>
</html>
