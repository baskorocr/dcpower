<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Product Labels</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5mm;
        }
        .label {
            border: 1px solid #000;
            padding: 5mm;
            text-align: center;
            page-break-inside: avoid;
        }
        .qr-code {
            margin: 3mm auto;
        }
        .serial {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        .project {
            font-size: 8pt;
            color: #666;
            margin-top: 1mm;
        }
    </style>
</head>
<body>
    <div class="labels-grid">
        @foreach($products as $product)
        <div class="label">
            <div class="qr-code">
                {!! QrCode::size(150)->margin(0)->generate($product->serial_number) !!}
            </div>
            <div class="serial">{{ $product->serial_number }}</div>
            <div class="project">{{ $product->project->name }}</div>
        </div>
        @endforeach
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
