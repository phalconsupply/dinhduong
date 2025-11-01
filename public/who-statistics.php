<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Chỉ Số WHO - Đánh Giá Dinh Dưỡng Trẻ Em</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            line-height: 1.6;
        }

        /* Header Styles */
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-top {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 0;
        }

        .header-top .container-header {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .logo-section img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: white;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .logo-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .logo-text p {
            margin: 0;
            font-size: 14px;
            color: rgba(255,255,255,0.95);
        }

        .logo-text p a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .logo-text p a:hover {
            text-decoration: underline;
        }

        .horizontal-menu {
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .horizontal-menu .container-header {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .nav-menu li {
            position: relative;
        }

        .nav-menu li a {
            display: block;
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .nav-menu li a:hover {
            background: #f8f9ff;
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .nav-menu li.current a {
            color: #667eea;
            background: #f8f9ff;
            border-bottom-color: #667eea;
        }

        .nav-menu li a i {
            margin-right: 8px;
        }

        /* Main Container */
        .page-container {
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.95;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 50px;
        }

        .section-title {
            font-size: 2em;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .tab-button {
            padding: 12px 24px;
            border: none;
            background: #f0f0f0;
            color: #333;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s;
        }

        .tab-button:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .tab-button.active {
            background: #667eea;
            color: white;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 1em;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9ff;
            transition: background 0.3s;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .color-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
            min-width: 80px;
            text-align: center;
        }

        .badge-green { background: #4CAF50; color: white; }
        .badge-orange { background: #FF9800; color: white; }
        .badge-red { background: #F44336; color: white; }
        .badge-cyan { background: #00BCD4; color: white; }
        .badge-blue { background: #2196F3; color: white; }
        .badge-gray { background: #9E9E9E; color: white; }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .summary-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }

        .summary-card h3 {
            font-size: 3em;
            color: #667eea;
            margin-bottom: 10px;
        }

        .summary-card p {
            color: #666;
            font-size: 1.1em;
        }

        .color-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: #f8f9ff;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .legend-item.green { border-left-color: #4CAF50; }
        .legend-item.orange { border-left-color: #FF9800; }
        .legend-item.red { border-left-color: #F44336; }
        .legend-item.cyan { border-left-color: #00BCD4; }
        .legend-item.blue { border-left-color: #2196F3; }
        .legend-item.gray { border-left-color: #9E9E9E; }

        .color-box {
            width: 30px;
            height: 30px;
            border-radius: 6px;
        }

        .comparison-table {
            margin: 30px 0;
        }

        .comparison-table td.check {
            text-align: center;
            font-size: 1.5em;
        }

        .note-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .note-box h4 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .note-box ul {
            margin-left: 20px;
            color: #856404;
        }

        .note-box li {
            margin: 8px 0;
        }

        .footer {
            background: #f8f9ff;
            padding: 30px;
            text-align: center;
            color: #666;
            border-top: 2px solid #e0e0e0;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
            .tab-button {
                display: none;
            }
            .tab-content {
                display: block !important;
                page-break-after: always;
            }
        }

        @media (max-width: 768px) {
            /* Header Top - Compact */
            .header-top {
                padding: 8px 0;
            }
            
            .header-top .container-header {
                padding: 0 10px;
            }
            
            /* Logo Section - Compact */
            .logo-section {
                flex-direction: row;
                text-align: left;
                gap: 10px;
            }
            
            .logo-section img {
                width: 40px;
                height: 40px;
                padding: 5px;
            }
            
            .logo-text h1 {
                font-size: 14px;
                line-height: 1.2;
            }
            
            .logo-text p {
                font-size: 11px;
                line-height: 1.2;
            }
            
            /* Horizontal Menu - Scrollable */
            .horizontal-menu {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .horizontal-menu .container-header {
                padding: 0;
            }
            
            .nav-menu {
                flex-wrap: nowrap;
                min-width: min-content;
            }
            
            .nav-menu li {
                flex-shrink: 0;
            }
            
            .nav-menu li a {
                padding: 12px 15px;
                font-size: 0.85em;
                white-space: nowrap;
                border-bottom-width: 2px;
            }
            
            .nav-menu li a i {
                margin-right: 5px;
                font-size: 0.9em;
            }
            
            /* Page Container */
            .page-container {
                padding: 10px;
            }
            
            .container {
                border-radius: 12px;
            }
            
            /* Header */
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5em;
            }
            
            .header p {
                font-size: 0.9em;
            }
            
            /* Content */
            .content {
                padding: 15px;
            }
            
            /* Tabs */
            .tabs {
                flex-direction: column;
                gap: 8px;
            }
            
            .tab-button {
                padding: 10px 15px;
                font-size: 0.9em;
            }
            
            /* Tables */
            table {
                font-size: 0.85em;
            }
            
            th, td {
                padding: 8px 10px;
            }
            
            /* Section Title */
            .section-title {
                font-size: 1.5em;
            }
            
            /* Summary Cards */
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .summary-card {
                padding: 15px;
            }
            
            .summary-card h3 {
                font-size: 2em;
            }
        }
        
        @media (max-width: 480px) {
            .logo-section img {
                height: 30px;
            }
            
            .logo-text h1 {
                font-size: 0.8em;
            }
            
            .logo-text p {
                font-size: 0.7em;
            }
            
            .nav-menu li a {
                padding: 10px 12px;
                font-size: 0.8em;
            }
            
            .header h1 {
                font-size: 1.2em;
            }
            
            table {
                font-size: 0.75em;
            }
            
            th, td {
                padding: 6px 8px;
            }
        }
        
        @media (max-width: 480px) {
            .logo-section img {
                width: 35px;
                height: 35px;
            }
            
            .logo-text h1 {
                font-size: 12px;
            }
            
            .logo-text p {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Menu -->
    <header class="main-header">
        <div class="header-top">
            <div class="container-header">
                <div class="logo-section">
                    <a href="/"><img src="/uploads/app/logo.png" alt="Logo" onerror="this.style.display='none'"></a>
                    <div class="logo-text">
                        <h1><a href="/" style="color: white; text-decoration: none;">Phần mềm đánh giá dinh dưỡng</a></h1>
                        <p><i class="fas fa-phone"></i> Hotline: <a href="tel:0987909090" style="color: white;">098 790 90 90</a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="horizontal-menu">
            <div class="container-header">
                <ul class="nav-menu">
                    <li>
                        <a href="/">
                            <i class="fas fa-baby"></i> Từ 0-5 tuổi
                        </a>
                    </li>
                    <li>
                        <a href="/tu-5-19-tuoi">
                            <i class="fas fa-child"></i> Từ 5-19 tuổi
                        </a>
                    </li>
                    <li>
                        <a href="/tu-19-tuoi">
                            <i class="fas fa-user"></i> Trên 19 tuổi
                        </a>
                    </li>
                    <li class="current">
                        <a href="/who-statistics.php">
                            <i class="fas fa-book-medical"></i> Chỉ dẫn phân loại
                        </a>
                    </li>
                    <li>
                        <a href="/kythuatcando.php">
                            <i class="fas fa-ruler-combined"></i> Kỹ thuật cân đo
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="page-container">
    <div class="container">
        <div class="header">
            <h1>📊 THỐNG KÊ CHỈ SỐ WHO</h1>
            <p>Đánh Giá Tình Trạng Dinh Dưỡng Trẻ Em Dưới 5 Tuổi</p>
            <p style="font-size: 0.9em; margin-top: 10px;">Ngày cập nhật: <?php echo date('d/m/Y'); ?></p>
        </div>

        <div class="content">
            <!-- Summary Statistics -->
            <div class="section">
                <h2 class="section-title">📈 Tổng Quan</h2>
                <div class="summary-grid">
                    <div class="summary-card">
                        <h3>4</h3>
                        <p>Chỉ Số WHO</p>
                    </div>
                    <div class="summary-card">
                        <h3>12</h3>
                        <p>Loại Kết Luận</p>
                    </div>
                    <div class="summary-card">
                        <h3>6</h3>
                        <p>Mã Màu Cảnh Báo</p>
                    </div>
                    <div class="summary-card">
                        <h3>9</h3>
                        <p>Phân Loại Z-Score</p>
                    </div>
                </div>
            </div>

            <!-- Color Legend -->
            <div class="section">
                <h2 class="section-title">🎨 Bảng Màu Cảnh Báo</h2>
                <div class="color-legend">
                    <div class="legend-item green">
                        <div class="color-box" style="background: #4CAF50;"></div>
                        <div>
                            <strong>Xanh (Green)</strong><br>
                            <small>Bình thường</small>
                        </div>
                    </div>
                    <div class="legend-item orange">
                        <div class="color-box" style="background: #FF9800;"></div>
                        <div>
                            <strong>Cam (Orange)</strong><br>
                            <small>Cảnh báo - Mức độ vừa</small>
                        </div>
                    </div>
                    <div class="legend-item red">
                        <div class="color-box" style="background: #F44336;"></div>
                        <div>
                            <strong>Đỏ (Red)</strong><br>
                            <small>Nguy hiểm - Cần can thiệp</small>
                        </div>
                    </div>
                    <div class="legend-item cyan">
                        <div class="color-box" style="background: #00BCD4;"></div>
                        <div>
                            <strong>Lam nhạt (Cyan)</strong><br>
                            <small>Cao hơn bình thường</small>
                        </div>
                    </div>
                    <div class="legend-item blue">
                        <div class="color-box" style="background: #2196F3;"></div>
                        <div>
                            <strong>Xanh dương (Blue)</strong><br>
                            <small>Cao bất thường</small>
                        </div>
                    </div>
                    <div class="legend-item gray">
                        <div class="color-box" style="background: #9E9E9E;"></div>
                        <div>
                            <strong>Xám (Gray)</strong><br>
                            <small>Chưa có dữ liệu</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs for 4 Indicators -->
            <div class="section">
                <h2 class="section-title">📋 Chi Tiết 4 Chỉ Số WHO</h2>
                
                <div class="tabs">
                    <button class="tab-button active" onclick="showTab('weight-age')">Cân Nặng/Tuổi</button>
                    <button class="tab-button" onclick="showTab('height-age')">Chiều Cao/Tuổi</button>
                    <button class="tab-button" onclick="showTab('weight-height')">Cân Nặng/Chiều Cao</button>
                    <button class="tab-button" onclick="showTab('bmi-age')">BMI/Tuổi</button>
                </div>

                <!-- Weight for Age -->
                <div id="weight-age" class="tab-content active">
                    <h3 style="color: #667eea; margin-bottom: 20px;">1️⃣ Cân Nặng Theo Tuổi (Weight-for-Age)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Khoảng Z-score</th>
                                <th>Kết Luận</th>
                                <th>Màu Sắc</th>
                                <th>Code Result</th>
                                <th>Zscore Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>&lt; -3SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>underweight_severe</code></td>
                                <td>&lt; -3SD</td>
                            </tr>
                            <tr>
                                <td>-3SD đến -2SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>underweight_moderate</code></td>
                                <td>-3SD đến -2SD</td>
                            </tr>
                            <tr>
                                <td>-2SD đến -1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-2SD đến -1SD</td>
                            </tr>
                            <tr>
                                <td>-1SD đến Median</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-1SD đến Median</td>
                            </tr>
                            <tr>
                                <td>Median đến +1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>Median đến +1SD</td>
                            </tr>
                            <tr>
                                <td>+1SD đến +2SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>+1SD đến +2SD</td>
                            </tr>
                            <tr>
                                <td>+2SD đến +3SD</td>
                                <td><strong>Trẻ thừa cân</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>overweight</code></td>
                                <td>+2SD đến +3SD</td>
                            </tr>
                            <tr>
                                <td>&gt; +3SD</td>
                                <td><strong>Trẻ béo phì</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>obese</code></td>
                                <td>&gt; +3SD</td>
                            </tr>
                            <tr>
                                <td>N/A</td>
                                <td>Chưa có dữ liệu</td>
                                <td><span class="color-badge badge-gray">⚫ Gray</span></td>
                                <td><code>unknown</code></td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Height for Age -->
                <div id="height-age" class="tab-content">
                    <h3 style="color: #667eea; margin-bottom: 20px;">2️⃣ Chiều Cao Theo Tuổi (Height-for-Age)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Khoảng Z-score</th>
                                <th>Kết Luận</th>
                                <th>Màu Sắc</th>
                                <th>Code Result</th>
                                <th>Zscore Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>&lt; -3SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể còi, mức độ nặng</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>stunted_severe</code></td>
                                <td>&lt; -3SD</td>
                            </tr>
                            <tr>
                                <td>-3SD đến -2SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>stunted_moderate</code></td>
                                <td>-3SD đến -2SD</td>
                            </tr>
                            <tr>
                                <td>-2SD đến -1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-2SD đến -1SD</td>
                            </tr>
                            <tr>
                                <td>-1SD đến Median</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-1SD đến Median</td>
                            </tr>
                            <tr>
                                <td>Median đến +1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>Median đến +1SD</td>
                            </tr>
                            <tr>
                                <td>+1SD đến +2SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>+1SD đến +2SD</td>
                            </tr>
                            <tr>
                                <td>+2SD đến +3SD</td>
                                <td><strong>Trẻ cao hơn bình thường</strong></td>
                                <td><span class="color-badge badge-cyan">🔵 Cyan</span></td>
                                <td><code>above_2sd</code></td>
                                <td>+2SD đến +3SD</td>
                            </tr>
                            <tr>
                                <td>≥ +3SD</td>
                                <td><strong>Trẻ cao bất thường</strong></td>
                                <td><span class="color-badge badge-blue">🔵 Blue</span></td>
                                <td><code>above_3sd</code></td>
                                <td>≥ +3SD</td>
                            </tr>
                            <tr>
                                <td>N/A</td>
                                <td>Chưa có dữ liệu</td>
                                <td><span class="color-badge badge-gray">⚫ Gray</span></td>
                                <td><code>unknown</code></td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Weight for Height -->
                <div id="weight-height" class="tab-content">
                    <h3 style="color: #667eea; margin-bottom: 20px;">3️⃣ Cân Nặng Theo Chiều Cao (Weight-for-Height)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Khoảng Z-score</th>
                                <th>Kết Luận</th>
                                <th>Màu Sắc</th>
                                <th>Code Result</th>
                                <th>Zscore Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>&lt; -3SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>underweight_severe</code></td>
                                <td>&lt; -3SD</td>
                            </tr>
                            <tr>
                                <td>-3SD đến -2SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>underweight_moderate</code></td>
                                <td>-3SD đến -2SD</td>
                            </tr>
                            <tr>
                                <td>-2SD đến -1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-2SD đến -1SD</td>
                            </tr>
                            <tr>
                                <td>-1SD đến Median</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-1SD đến Median</td>
                            </tr>
                            <tr>
                                <td>Median đến +1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>Median đến +1SD</td>
                            </tr>
                            <tr>
                                <td>+1SD đến +2SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>+1SD đến +2SD</td>
                            </tr>
                            <tr>
                                <td>+2SD đến +3SD</td>
                                <td><strong>Trẻ thừa cân</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>overweight</code></td>
                                <td>+2SD đến +3SD</td>
                            </tr>
                            <tr>
                                <td>≥ +3SD</td>
                                <td><strong>Trẻ béo phì</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>obese</code></td>
                                <td>≥ +3SD</td>
                            </tr>
                            <tr>
                                <td>N/A</td>
                                <td>Chưa có dữ liệu</td>
                                <td><span class="color-badge badge-gray">⚫ Gray</span></td>
                                <td><code>unknown</code></td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- BMI for Age -->
                <div id="bmi-age" class="tab-content">
                    <h3 style="color: #667eea; margin-bottom: 20px;">4️⃣ BMI Theo Tuổi (BMI-for-Age)</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Khoảng Z-score</th>
                                <th>Kết Luận</th>
                                <th>Màu Sắc</th>
                                <th>Code Result</th>
                                <th>Zscore Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>&lt; -3SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>wasted_severe</code></td>
                                <td>&lt; -3SD</td>
                            </tr>
                            <tr>
                                <td>-3SD đến -2SD</td>
                                <td><strong>Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>wasted_moderate</code></td>
                                <td>-3SD đến -2SD</td>
                            </tr>
                            <tr>
                                <td>-2SD đến -1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-2SD đến -1SD</td>
                            </tr>
                            <tr>
                                <td>-1SD đến Median</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>-1SD đến Median</td>
                            </tr>
                            <tr>
                                <td>Median đến +1SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>Median đến +1SD</td>
                            </tr>
                            <tr>
                                <td>+1SD đến +2SD</td>
                                <td>Trẻ bình thường</td>
                                <td><span class="color-badge badge-green">🟢 Green</span></td>
                                <td><code>normal</code></td>
                                <td>+1SD đến +2SD</td>
                            </tr>
                            <tr>
                                <td>+2SD đến +3SD</td>
                                <td><strong>Trẻ thừa cân</strong></td>
                                <td><span class="color-badge badge-orange">🟠 Orange</span></td>
                                <td><code>overweight</code></td>
                                <td>+2SD đến +3SD</td>
                            </tr>
                            <tr>
                                <td>&gt; +3SD</td>
                                <td><strong>Trẻ béo phì</strong></td>
                                <td><span class="color-badge badge-red">🔴 Red</span></td>
                                <td><code>obese</code></td>
                                <td>&gt; +3SD</td>
                            </tr>
                            <tr>
                                <td>N/A</td>
                                <td>Chưa có dữ liệu</td>
                                <td><span class="color-badge badge-gray">⚫ Gray</span></td>
                                <td><code>unknown</code></td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nutrition Status Classification -->
            <div class="section">
                <h2 class="section-title">🏥 Phân Loại Tình Trạng Dinh Dưỡng Tổng Hợp</h2>
                
                <div class="note-box" style="background: #e3f2fd; border-left-color: #2196F3;">
                    <h4 style="color: #1565C0;">📋 Giới Thiệu</h4>
                    <p style="color: #1565C0; margin: 10px 0;">
                        <strong>"Phân loại tình trạng dinh dưỡng"</strong> (hay <strong>"Tình trạng dinh dưỡng"</strong> trong kết quả) là 
                        <strong>KẾT LUẬN TỔNG HỢP</strong> dựa trên việc phân tích đồng thời <strong>CẢ 3 CHỈ SỐ WHO</strong>:
                    </p>
                    <ul style="color: #1565C0; margin-left: 20px;">
                        <li><strong>Cân nặng/Tuổi (W/A)</strong> - Đánh giá tổng thể</li>
                        <li><strong>Chiều cao/Tuổi (H/A)</strong> - Suy dinh dưỡng mãn tính</li>
                        <li><strong>Cân nặng/Chiều cao (W/H)</strong> - Suy dinh dưỡng cấp tính</li>
                    </ul>
                </div>

                <h3 style="margin: 30px 0 15px; color: #667eea;">Bảng Phân Loại Chi Tiết</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35%;">Tình Trạng Dinh Dưỡng</th>
                            <th style="width: 40%;">Điều Kiện (3 Chỉ Số)</th>
                            <th style="width: 15%;">Mức Độ</th>
                            <th style="width: 10%;">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Suy dinh dưỡng phối hợp -->
                        <tr style="background: #ffebee;">
                            <td><strong>🔴 Suy dinh dưỡng phối hợp</strong></td>
                            <td>
                                <strong>H/A &lt; -2SD</strong> (Thấp còi) <strong>VÀ</strong><br>
                                <strong>W/H &lt; -2SD</strong> (Gầy còm)
                            </td>
                            <td><span class="color-badge badge-red">Nặng nhất</span></td>
                            <td><code>malnutrition_combined</code></td>
                        </tr>

                        <!-- 2. Gầy còm nặng -->
                        <tr style="background: #ffebee;">
                            <td><strong>🔴 Suy dinh dưỡng gầy còm nặng</strong></td>
                            <td>
                                <strong>W/H &lt; -3SD</strong><br>
                                (H/A bình thường)
                            </td>
                            <td><span class="color-badge badge-red">Rất nặng</span></td>
                            <td><code>wasted_severe</code></td>
                        </tr>

                        <!-- 3. Gầy còm -->
                        <tr style="background: #fff3e0;">
                            <td><strong>🟠 Suy dinh dưỡng gầy còm</strong></td>
                            <td>
                                <strong>-3SD &le; W/H &lt; -2SD</strong><br>
                                (H/A bình thường)
                            </td>
                            <td><span class="color-badge badge-orange">Vừa</span></td>
                            <td><code>wasted</code></td>
                        </tr>

                        <!-- 4. Thấp còi nặng -->
                        <tr style="background: #ffebee;">
                            <td><strong>🔴 Suy dinh dưỡng thấp còi nặng</strong></td>
                            <td>
                                <strong>H/A &lt; -3SD</strong><br>
                                (W/H bình thường)
                            </td>
                            <td><span class="color-badge badge-red">Rất nặng</span></td>
                            <td><code>stunted_severe</code></td>
                        </tr>

                        <!-- 5. Thấp còi -->
                        <tr style="background: #fff3e0;">
                            <td><strong>🟠 Suy dinh dưỡng thấp còi</strong></td>
                            <td>
                                <strong>-3SD &le; H/A &lt; -2SD</strong><br>
                                (W/H bình thường)
                            </td>
                            <td><span class="color-badge badge-orange">Vừa</span></td>
                            <td><code>stunted</code></td>
                        </tr>

                        <!-- 6. Nhẹ cân nặng -->
                        <tr style="background: #ffebee;">
                            <td><strong>🔴 Suy dinh dưỡng nhẹ cân nặng</strong></td>
                            <td>
                                <strong>W/A &lt; -3SD</strong><br>
                                (H/A và W/H bình thường)
                            </td>
                            <td><span class="color-badge badge-red">Rất nặng</span></td>
                            <td><code>underweight_severe</code></td>
                        </tr>

                        <!-- 7. Nhẹ cân -->
                        <tr style="background: #fff3e0;">
                            <td><strong>🟠 Suy dinh dưỡng nhẹ cân</strong></td>
                            <td>
                                <strong>-3SD &le; W/A &lt; -2SD</strong><br>
                                (H/A và W/H bình thường)
                            </td>
                            <td><span class="color-badge badge-orange">Vừa</span></td>
                            <td><code>underweight</code></td>
                        </tr>

                        <!-- 8. Béo phì -->
                        <tr style="background: #ffebee;">
                            <td><strong>🔴 Béo phì</strong></td>
                            <td>
                                <strong>W/A &gt; +3SD</strong> HOẶC<br>
                                <strong>W/H &gt; +3SD</strong>
                            </td>
                            <td><span class="color-badge badge-red">Nguy cơ cao</span></td>
                            <td><code>obese</code></td>
                        </tr>

                        <!-- 9. Thừa cân -->
                        <tr style="background: #fff3e0;">
                            <td><strong>🟠 Thừa cân</strong></td>
                            <td>
                                <strong>W/A &gt; +2SD</strong> HOẶC<br>
                                <strong>W/H &gt; +2SD</strong><br>
                                (Nhưng không béo phì)
                            </td>
                            <td><span class="color-badge badge-orange">Cảnh báo</span></td>
                            <td><code>overweight</code></td>
                        </tr>

                        <!-- 10. Vượt tiêu chuẩn -->
                        <tr style="background: #e0f7fa;">
                            <td><strong>🔵 Bình thường, vượt tiêu chuẩn</strong></td>
                            <td>
                                <strong>H/A &gt; +2SD</strong><br>
                                (Các chỉ số khác bình thường)
                            </td>
                            <td><span class="color-badge badge-cyan">Tốt</span></td>
                            <td><code>over_standard</code></td>
                        </tr>

                        <!-- 11. Bình thường -->
                        <tr style="background: #e8f5e9;">
                            <td><strong>🟢 Bình thường</strong></td>
                            <td>
                                <strong>W/A:</strong> -2SD đến +2SD<br>
                                <strong>H/A:</strong> -2SD đến +2SD<br>
                                <strong>W/H:</strong> -2SD đến +2SD
                            </td>
                            <td><span class="color-badge badge-green">Tốt</span></td>
                            <td><code>normal</code></td>
                        </tr>

                        <!-- 12. Chưa đủ dữ liệu -->
                        <tr>
                            <td><strong>⚫ Chưa có đủ dữ liệu</strong></td>
                            <td>Thiếu 1 hoặc nhiều chỉ số</td>
                            <td><span class="color-badge badge-gray">N/A</span></td>
                            <td><code>unknown</code></td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin: 30px 0 15px; color: #667eea;">Thứ Tự Ưu Tiên Phân Loại</h3>
                <div class="note-box" style="background: #fff9c4; border-left-color: #fbc02d;">
                    <h4 style="color: #f57f17;">⚠️ Quy Tắc Phân Loại (Theo Thứ Tự)</h4>
                    <ol style="color: #f57f17; margin-left: 20px; line-height: 2;">
                        <li><strong>Kiểm tra đủ dữ liệu</strong> → Nếu thiếu → "Chưa có đủ dữ liệu"</li>
                        <li><strong>SDD Phối hợp</strong> (H/A < -2SD VÀ W/H < -2SD) → Ưu tiên cao nhất</li>
                        <li><strong>SDD Gầy còm</strong> (W/H < -2SD) → Cấp tính, cần can thiệp nhanh</li>
                        <li><strong>SDD Thấp còi</strong> (H/A < -2SD) → Mãn tính</li>
                        <li><strong>SDD Nhẹ cân</strong> (W/A < -2SD) → Tổng quát</li>
                        <li><strong>Béo phì</strong> (W/A hoặc W/H > +3SD)</li>
                        <li><strong>Thừa cân</strong> (W/A hoặc W/H > +2SD)</li>
                        <li><strong>Vượt tiêu chuẩn</strong> (H/A > +2SD)</li>
                        <li><strong>Bình thường</strong> (Cả 3 chỉ số trong khoảng -2SD đến +2SD)</li>
                    </ol>
                </div>

                <h3 style="margin: 30px 0 15px; color: #667eea;">Ví Dụ Minh Họa</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tình Huống</th>
                            <th>W/A</th>
                            <th>H/A</th>
                            <th>W/H</th>
                            <th>Kết Luận</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Trường hợp 1</strong></td>
                            <td>-1.5 SD<br><small>(Bình thường)</small></td>
                            <td>-2.8 SD<br><small>(Thấp còi vừa)</small></td>
                            <td>-2.5 SD<br><small>(Gầy còm vừa)</small></td>
                            <td><span class="color-badge badge-red">🔴 SDD Phối hợp</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trường hợp 2</strong></td>
                            <td>-1.2 SD<br><small>(Bình thường)</small></td>
                            <td>-0.5 SD<br><small>(Bình thường)</small></td>
                            <td>-3.2 SD<br><small>(Gầy còm nặng)</small></td>
                            <td><span class="color-badge badge-red">🔴 SDD Gầy còm nặng</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trường hợp 3</strong></td>
                            <td>-1.8 SD<br><small>(Bình thường)</small></td>
                            <td>-2.3 SD<br><small>(Thấp còi vừa)</small></td>
                            <td>+0.5 SD<br><small>(Bình thường)</small></td>
                            <td><span class="color-badge badge-orange">🟠 SDD Thấp còi</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trường hợp 4</strong></td>
                            <td>-2.5 SD<br><small>(Nhẹ cân vừa)</small></td>
                            <td>-0.8 SD<br><small>(Bình thường)</small></td>
                            <td>-1.2 SD<br><small>(Bình thường)</small></td>
                            <td><span class="color-badge badge-orange">🟠 SDD Nhẹ cân</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trường hợp 5</strong></td>
                            <td>+0.5 SD<br><small>(Bình thường)</small></td>
                            <td>+0.8 SD<br><small>(Bình thường)</small></td>
                            <td>+3.5 SD<br><small>(Béo phì)</small></td>
                            <td><span class="color-badge badge-red">🔴 Béo phì</span></td>
                        </tr>
                        <tr>
                            <td><strong>Trường hợp 6</strong></td>
                            <td>+0.2 SD<br><small>(Bình thường)</small></td>
                            <td>+2.7 SD<br><small>(Cao vượt trội)</small></td>
                            <td>-0.5 SD<br><small>(Bình thường)</small></td>
                            <td><span class="color-badge badge-cyan">🔵 Bình thường, vượt tiêu chuẩn</span></td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td><strong>Trường hợp 7</strong></td>
                            <td>+0.8 SD<br><small>(Bình thường)</small></td>
                            <td>-0.5 SD<br><small>(Bình thường)</small></td>
                            <td>+1.2 SD<br><small>(Bình thường)</small></td>
                            <td><span class="color-badge badge-green">🟢 Bình thường</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Important Notes -->
            <div class="section">
                <h2 class="section-title">💡 Lưu Ý Quan Trọng</h2>
                
                <div class="note-box">
                    <h4>📌 Về "Gầy còm" vs "Nhẹ cân" vs "Thấp còi"</h4>
                    <ul>
                        <li><strong>"Gầy còm"</strong> (Wasted): Dùng cho W/H và BMI/A - Phản ánh suy dinh dưỡng <strong>cấp tính</strong> (gần đây), cân nặng không đủ so với chiều cao hiện tại</li>
                        <li><strong>"Nhẹ cân"</strong> (Underweight): Dùng cho W/A - Cân nặng không đủ so với tuổi, có thể do thấp còi hoặc gầy còm hoặc cả hai</li>
                        <li><strong>"Thấp còi/Còi"</strong> (Stunted): Dùng cho H/A - Phản ánh suy dinh dưỡng <strong>mãn tính</strong> (kéo dài), chiều cao không đủ so với tuổi</li>
                    </ul>
                </div>

                <div class="note-box">
                    <h4>🚨 Về Mức Độ Cảnh Báo</h4>
                    <ul>
                        <li><strong>🔴 RED (Đỏ)</strong>: Mức độ NẶNG hoặc BÉO PHÌ → <strong>Cần can thiệp khẩn cấp</strong></li>
                        <li><strong>🟠 ORANGE (Cam)</strong>: Mức độ VỪA hoặc THỪA CÂN → <strong>Cần theo dõi và can thiệp</strong></li>
                        <li><strong>🟢 GREEN (Xanh)</strong>: BÌNH THƯỜNG → <strong>Duy trì chế độ dinh dưỡng</strong></li>
                        <li><strong>🔵 CYAN/BLUE (Lam)</strong>: CAO BẤT THƯỜNG → <strong>Cần kiểm tra nguyên nhân</strong></li>
                        <li><strong>⚫ GRAY (Xám)</strong>: CHƯA CÓ DỮ LIỆU → <strong>Cần bổ sung đo đạc</strong></li>
                    </ul>
                </div>

                <div class="note-box">
                    <h4>📊 Về Z-score Category</h4>
                    <ul>
                        <li>Hiển thị ở cột "Kết quả" với format: <em>(Median đến +1SD)</em></li>
                        <li>Giúp xác định chính xác vị trí trẻ trong phổ phân phối chuẩn WHO</li>
                        <li>Mỗi chỉ số có <strong>9 categories</strong> từ &lt; -3SD đến &gt; +3SD</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>Hệ Thống Đánh Giá Dinh Dưỡng Trẻ Em</strong></p>
            <p>Dựa trên tiêu chuẩn WHO Child Growth Standards</p>
            <p style="margin-top: 10px; font-size: 0.9em;">© <?php echo date('Y'); ?> - Tài liệu tham khảo nội bộ</p>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Remove active from all buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Highlight active button
            event.target.classList.add('active');
        }
    </script>
    
    </div> <!-- /page-container -->
</body>
</html>
