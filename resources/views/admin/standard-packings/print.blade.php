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
        * {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        body {
            margin: 0;
            padding: 5mm;
            font-family: Arial, sans-serif;
            width: 100mm;
            height: 70mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-inside: avoid;
        }
        .project-name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2mm;
            text-align: center;
        }
        .packing-code {
            font-size: 14pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 2mm;
            text-align: center;
        }
        .quantity {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2mm;
            text-align: center;
            position: relative;
            width: 100%;
        }
        .qc-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 15mm;
            height: 15mm;
            border-radius: 50%;
            border: 2px solid black;
            background-color: white;
            color: black;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            position: absolute;
            right: 10mm;
            top: -8mm;
        }
        .barcode {
            text-align: center;
        }
        .barcode svg {
            width: 45mm;
            height: 45mm;
        }
    </style>
</head>
<body>
    <div class="barcode">
        {!! QrCode::size(600)->margin(0)->generate($standardPacking->packing_code) !!}
    </div>
    <div class="project-name">{{ $standardPacking->project->name }}</div>
    <div class="packing-code">{{ $standardPacking->packing_code }}</div>
    <div class="quantity">
        QTY: {{ $standardPacking->quantity }}
        <span class="qc-badge">QC<br>PASSED</span>
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
