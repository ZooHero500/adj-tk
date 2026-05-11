<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email Preview — {{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            background: #1e293b;
            color: #f8fafc;
            border-bottom: 1px solid #334155;
            flex-shrink: 0;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #334155;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            transition: background 0.15s;
        }

        .back-btn:hover { background: #475569; color: #f1f5f9; }

        .back-btn svg { width: 14px; height: 14px; }

        .preview-header h1 {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            flex: 1;
        }

        .preview-badge {
            font-size: 11px;
            padding: 3px 8px;
            background: #0f172a;
            color: #64748b;
            border-radius: 999px;
            border: 1px solid #334155;
        }

        .preview-frame {
            flex: 1;
            width: 100%;
            border: none;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="preview-header">
        <a href="{{ $backUrl }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
        <h1>PornTk Admin - {{ $title }}</h1>
        <span style="display: flex; align-items: center; gap: 1rem;">
            <span class="preview-badge">Admin Preview</span>
            <a href="{{$frameUrl}}" class="back-btn">View Raw</a>
        </span>
    </div>

    <iframe
        class="preview-frame"
        src="{{ $frameUrl }}"
        title="Email Preview"
    ></iframe>
</body>
</html>
