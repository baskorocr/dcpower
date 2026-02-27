<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Packing Label - {{ $standardPacking->packing_code }}</title>
    <style>
        @page {
            size: 100mm 70mm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 3mm;
            font-family: Arial, sans-serif;
            width: 100mm;
            height: 70mm;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .logo {
            width: 12mm;
            height: 12mm;
            background: #333;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 6pt;
        }
        .header-info {
            flex: 1;
            text-align: center;
            margin: 0 2mm;
        }
        .packing-qr {
            width: 15mm;
            height: 15mm;
        }
        .packing-qr svg {
            width: 15mm;
            height: 15mm;
        }
        .project-name {
            font-size: 7pt;
            font-weight: bold;
        }
        .packing-code {
            font-size: 12pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin: 0.5mm 0;
            letter-spacing: 0.5px;
        }
        .quantity {
            font-size: 8pt;
            font-weight: bold;
        }
        .items-title {
            font-size: 7pt;
            font-weight: bold;
            margin: 1mm 0;
            border-bottom: 1px solid #333;
            padding-bottom: 0.5mm;
        }
        .items-list {
            font-size: 6pt;
            max-height: 45mm;
            overflow: hidden;
        }
        .item {
            display: flex;
            align-items: center;
            padding: 0.5mm 0;
            border-bottom: 1px dotted #ddd;
        }
        .item-text {
            flex: 1;
            font-family: 'Courier New', monospace;
        }
        .item-barcode {
            width: 20mm;
            text-align: right;
        }
        .item-barcode svg {
            width: 8mm;
            height: 8mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">LOGO</div>
        <div class="header-info">
            <div class="project-name">{{ $standardPacking->project->name }}</div>
            <div class="packing-code">{{ $standardPacking->packing_code }}</div>
            <div class="quantity">QTY: {{ $standardPacking->quantity }}</div>
        </div>
        <div class="packing-qr">
            {!! QrCode::size(150)->margin(0)->generate($standardPacking->packing_code) !!}
        </div>
    </div>

    <div class="items-title">SERIAL NUMBERS:</div>
    <div class="items-list">
        @foreach($standardPacking->products as $index => $product)
        <div class="item">
            <div class="item-text">{{ $index + 1 }}. {{ $product->serial_number }}</div>
            <div class="item-barcode">
                {!! QrCode::size(80)->margin(0)->generate($product->serial_number) !!}
            </div>
        </div>
        @endforeach
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    </script>
</body>
</html>
