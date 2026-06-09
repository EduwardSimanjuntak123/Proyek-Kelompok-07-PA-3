<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 80px;
            font-weight: bold;
            color: #f5576c;
            margin-bottom: 20px;
            line-height: 1;
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .btn-back {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(245, 87, 108, 0.4);
            color: white;
        }

        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #f5576c;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            text-align: left;
            font-size: 14px;
            color: #666;
        }

        .loading-indicator {
            display: inline-block;
            margin-top: 15px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f5576c;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">🔧</div>
        <div class="error-code">503</div>
        <div class="error-title">Layanan Sedang Tidak Tersedia</div>
        <div class="error-message">
            Server sedang dalam pemeliharaan. Sistem akan segera kembali online.
            <br><br>
            <small>Kami minta maaf atas ketidaknyamanannya.</small>
        </div>
        <div class="loading-indicator">
            <div class="spinner"></div>
            <span>Kami sedang melakukan update...</span>
        </div>
        <a href="{{ url('/') }}" class="btn-back" style="margin-top: 30px;">← Coba Lagi</a>
        <div class="error-details">
            <strong>Informasi:</strong>
            <ul style="margin-bottom: 0; margin-top: 10px;">
                <li>Pemeliharaan berkala sistem</li>
                <li>Estimasi waktu: kurang dari 1 jam</li>
                <li>Coba akses kembali dalam beberapa menit</li>
            </ul>
        </div>
    </div>
</body>

</html>
