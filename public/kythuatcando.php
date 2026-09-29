<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng Dẫn Kỹ Thuật Cân Đo - Phần mềm đánh giá dinh dưỡng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Dropdown Menu Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 220px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1001;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            top: 100%;
            left: 0;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-weight: 500;
            border-bottom: none !important;
            transition: all 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f8f9ff;
            color: #667eea;
        }

        .dropdown-content a.active {
            background-color: #f8f9ff;
            color: #667eea;
            font-weight: 600;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown > a:after {
            content: ' ▼';
            font-size: 0.8em;
            margin-left: 5px;
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
            justify-content: center;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-button {
            padding: 12px 24px;
            border: none;
            background: transparent;
            color: #333;
            cursor: pointer;
            border-radius: 0;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .tab-button:hover {
            background: #f8f9ff;
            color: #667eea;
        }

        .tab-button.active {
            background: #f8f9ff;
            color: #667eea;
            border-bottom-color: #667eea;
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

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .step-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9ff;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .step-item h3 {
            font-size: 1.2em;
            color: #667eea;
            margin-bottom: 8px;
        }

        .step-item p {
            color: #555;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .info-box h4 {
            color: #1976D2;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .info-box ul {
            list-style: none;
            padding-left: 0;
        }

        .info-box ul li {
            padding: 5px 0;
            color: #0d47a1;
        }

        .info-box ul li:before {
            content: "▸";
            color: #2196F3;
            margin-right: 10px;
        }

        .toggle-btn {
            cursor: pointer;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
        }

        .toggle-btn.active {
            background: #667eea;
            color: white;
        }

        .toggle-content {
            display: none;
            margin-top: 20px;
        }

        .toggle-content.active {
            display: block;
        }

        .zscore-card {
            cursor: pointer;
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .zscore-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .zscore-card h4 {
            color: #667eea;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .zscore-info {
            display: none;
            color: #555;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }

        .zscore-card.open .zscore-info {
            display: block;
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
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tbody tr:hover {
            background: #f8f9ff;
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-width: 700px;
            margin: 30px auto;
            height: 350px;
        }

        .error-row {
            cursor: pointer;
        }

        .error-details {
            display: none;
            background: #f8f9ff;
        }

        .error-row.open .error-details {
            display: table-row;
        }

        ul.bullet-list {
            list-style: none;
            padding-left: 0;
        }

        ul.bullet-list li {
            padding: 8px 0;
            position: relative;
            padding-left: 30px;
        }

        ul.bullet-list li:before {
            content: "▸";
            color: #667eea;
            position: absolute;
            left: 0;
            font-weight: bold;
            font-size: 1.2em;
        }

        .hidden-mobile {
            display: table-cell;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .hidden-mobile {
                display: none;
            }
            .header-top {
                padding: 8px 0;
            }
            
            .header-top .container-header {
                padding: 0 10px;
            }
            
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
            
            .page-container {
                padding: 10px;
            }
            
            .container {
                border-radius: 12px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5em;
            }
            
            .content {
                padding: 15px;
            }
            
            .tabs {
                flex-direction: column;
                gap: 5px;
            }
            
            .tab-button {
                padding: 10px 15px;
                text-align: left;
            }
            
            .chart-container {
                height: 250px;
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
                    <li class="dropdown current">
                        <a href="#">
                            <i class="fas fa-book"></i> Documents
                        </a>
                        <div class="dropdown-content">
                            <a href="/who-statistics.php">
                                <i class="fas fa-book-medical"></i> Chỉ dẫn phân loại WHO
                            </a>
                            <a href="/kythuatcando.php" class="active">
                                <i class="fas fa-ruler-combined"></i> Kỹ thuật cân đo
                            </a>
                            <a href="/huong-dan-danh-gia-dinh-duong.html">
                                <i class="fas fa-chart-line"></i> Hướng dẫn đánh giá dinh dưỡng
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="page-container">
    <div class="container">
        <div class="header">
            <h1>📏 Kỹ Thuật Cân Đo Chuẩn WHO</h1>
            <p>Hướng dẫn tương tác để đảm bảo tính chính xác của Z-Score</p>
        </div>

        <div class="content">
        <nav class="tabs">
            <button class="tab-button active" data-tab="overview">Tổng Quan</button>
            <button class="tab-button" data-tab="weight">Cân Nặng</button>
            <button class="tab-button" data-tab="height">Chiều Dài/Cao</button>
            <button class="tab-button" data-tab="head">Vòng Đầu</button>
            <button class="tab-button" data-tab="errors">Ghi Chép & Lỗi</button>
            <button class="tab-button" data-tab="conclusion">Kết Luận</button>
        </nav>

        <main>
            <div id="overview" class="tab-content active">
                <section class="section">
                    <h2 class="section-title">1. Giới thiệu</h2>
                    <p style="margin-bottom: 20px;">Để tính toán chỉ số Z-Score chính xác theo chuẩn của Tổ chức Y tế Thế giới (WHO), việc thực hiện cân đo đúng kỹ thuật là yếu tố quyết định. Sai sót nhỏ trong khâu đo lường có thể làm lệch kết quả đánh giá tình trạng dinh dưỡng của trẻ, dẫn đến sai định hướng trong can thiệp y tế hoặc tư vấn dinh dưỡng.</p>
                </section>
                <section class="section">
                    <h2 class="section-title">2. Nguyên tắc chung</h2>
                    <p style="margin-bottom: 20px;">Để đảm bảo kết quả chính xác nhất, luôn tuân thủ các nguyên tắc cơ bản sau đây trước và trong khi đo:</p>
                    <ul class="bullet-list">
                        <li>Thực hiện cân đo vào buổi sáng, trước bữa ăn hoặc cách ít nhất 2 giờ sau khi ăn.</li>
                        <li>Trẻ mặc quần áo mỏng nhẹ, bỏ giày dép, mũ, và các phụ kiện.</li>
                        <li>Dụng cụ cân đo phải được đặt trên mặt phẳng, hiệu chỉnh (zero) trước mỗi lần sử dụng.</li>
                        <li>Mỗi chỉ số nên được đo ít nhất 2 lần, lấy giá trị trung bình nếu có chênh lệch.</li>
                    </ul>
                </section>
            </div>

            <div id="weight" class="tab-content">
                <section class="section">
                    <h2 class="section-title">3. Kỹ thuật cân nặng</h2>
                    <p style="margin-bottom: 20px;">Quy trình cân nặng chuẩn cho trẻ, đặc biệt là trẻ đã có thể đứng vững:</p>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h3>Chuẩn bị dụng cụ</h3>
                            <p>Sử dụng cân điện tử hoặc cân đồng hồ có độ chính xác 0,1 kg. Đặt cân trên bề mặt cứng, phẳng và ổn định.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h3>Thực hiện</h3>
                            <p>Cho trẻ đứng thẳng giữa bàn cân, hai tay buông tự nhiên, mắt nhìn thẳng.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h3>Đọc kết quả</h3>
                            <p>Đọc và ghi lại kết quả chính xác đến 0,1 kg.</p>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <h4><i class="fas fa-exclamation-circle"></i> Lưu ý đặc biệt</h4>
                        <ul>
                            <li>Với trẻ dưới 2 tuổi, cân khi trẻ nằm bằng cân điện tử trẻ sơ sinh hoặc cân treo chuyên dụng.</li>
                            <li>Nếu cân cùng mẹ (do trẻ không hợp tác), cần trừ trọng lượng mẹ (đã cân trước đó) để xác định cân nặng thực của trẻ.</li>
                        </ul>
                    </div>
                </section>
            </div>

            <div id="height" class="tab-content">
                <section class="section">
                    <h2 class="section-title">4. Kỹ thuật đo chiều dài / chiều cao</h2>
                    <p style="margin-bottom: 20px;">Kỹ thuật đo sẽ khác nhau tùy thuộc vào độ tuổi của trẻ. Chọn đúng phương pháp dưới đây:</p>
                    
                    <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 30px; flex-wrap: wrap;">
                        <button class="toggle-btn active" data-toggle="height-lying">Trẻ dưới 2 tuổi (Đo nằm)</button>
                        <button class="toggle-btn" data-toggle="height-standing">Trẻ từ 2 tuổi (Đo đứng)</button>
                    </div>

                    <div id="height-lying" class="toggle-content active">
                        <h3 style="font-size: 1.3em; color: #667eea; margin-bottom: 15px;">a. Trẻ dưới 2 tuổi – Đo chiều dài nằm</h3>
                        <p style="margin-bottom: 20px;">Sử dụng thước đo nằm chuyên dụng có tấm chắn đầu cố định và bàn đỡ chân di động.</p>
                        
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <p>Trẻ nằm ngửa, đầu chạm tấm chắn đầu cố định.</p>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <p>Giữ đầu ở tư thế thẳng, mắt nhìn thẳng lên trần (cần 1 người hỗ trợ giữ đầu).</p>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <p>Duỗi thẳng chân, ép bàn chân vuông góc với thước bằng tấm đỡ di động.</p>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <p>Đọc kết quả chính xác đến 0,1 cm trên vạch chia của thước.</p>
                        </div>
                    </div>

                    <div id="height-standing" class="toggle-content">
                        <h3 style="font-size: 1.3em; color: #667eea; margin-bottom: 15px;">b. Trẻ từ 2 tuổi trở lên – Đo chiều cao đứng</h3>
                        <p style="margin-bottom: 20px;">Sử dụng thước đo thẳng đứng được cố định chắc chắn trên tường.</p>
                        
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <p>Trẻ đứng thẳng, lưng áp sát thước. Gót chân, mông, vai và chẩm (phần sau đầu) chạm vào mặt phẳng của thước.</p>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <p>Mắt nhìn thẳng về phía trước, theo đường Frankfurt (đường nối từ mép dưới hốc mắt đến điểm cao nhất của tai).</p>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <p>Gạt thước chặn đầu vuông góc, áp nhẹ vào đỉnh đầu và đọc số đo chính xác đến 0,1 cm.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div id="head" class="tab-content">
                <section class="section">
                    <h2 class="section-title">5. Kỹ thuật đo vòng đầu</h2>
                    <p style="margin-bottom: 20px;">Kỹ thuật này chủ yếu áp dụng cho trẻ dưới 2 tuổi để theo dõi sự phát triển của não bộ.</p>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div>
                            <h3>Dụng cụ</h3>
                            <p>Sử dụng thước dây mềm, không giãn, có độ chính xác đến 0,1 cm.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div>
                            <h3>Cách đo</h3>
                            <p>Đặt thước dây vòng quanh đầu, đi qua điểm cao nhất của trán (phía trên lông mày) và phần nhô nhất phía sau đầu (chẩm).</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div>
                            <h3>Đọc kết quả</h3>
                            <p>Đảm bảo thước nằm ngang, không xiên. Đọc kết quả chính xác đến 0,1 cm.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div id="errors" class="tab-content">
                <section class="section">
                    <h2 class="section-title">6. Ghi chép và Nhập liệu</h2>
                    <p style="margin-bottom: 20px;">Việc ghi chép cẩn thận và nhập liệu chính xác là bước cuối cùng để hoàn tất quy trình:</p>
                    <ul class="bullet-list" style="margin-bottom: 30px;">
                        <li>Ghi ngay kết quả sau mỗi lần đo, tránh ước lượng hoặc ghi nhớ.</li>
                        <li>Nếu có sai lệch > 0,2 kg (cân nặng) hoặc > 0,5 cm (chiều cao) giữa các lần đo, cần thực hiện đo lại lần thứ ba.</li>
                        <li>Sau khi hoàn tất, nhập dữ liệu vào phần mềm tính Z-Score WHO.</li>
                    </ul>

                    <h3 style="font-size: 1.5em; color: #667eea; margin-bottom: 15px;">Các chỉ số Z-Score chính</h3>
                    <p style="margin-bottom: 20px;">Đây là các chỉ số dinh dưỡng quan trọng được tính toán từ số đo của trẻ. Nhấp vào từng chỉ số để xem mô tả:</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-bottom: 40px;">
                        <div class="zscore-card">
                            <h4>Cân nặng theo tuổi (W/A)</h4>
                            <div class="zscore-info">
                                <p>Phản ánh tình trạng thiếu cân (underweight) chung, không phân biệt thiếu dinh dưỡng cấp hay mạn tính.</p>
                            </div>
                        </div>
                        <div class="zscore-card">
                            <h4>Chiều cao theo tuổi (H/A)</h4>
                            <div class="zscore-info">
                                <p>Phản ánh tình trạng còi cọc (stunting), là hậu quả của thiếu dinh dưỡng mạn tính hoặc kéo dài.</p>
                            </div>
                        </div>
                        <div class="zscore-card">
                            <h4>Cân nặng theo chiều cao (W/H)</h4>
                            <div class="zscore-info">
                                <p>Phản ánh tình trạng gầy còm (wasting), là biểu hiện của thiếu dinh dưỡng cấp tính. Cũng dùng để xác định thừa cân, béo phì.</p>
                            </div>
                        </div>
                        <div class="zscore-card">
                            <h4>BMI theo tuổi (BMI/A)</h4>
                            <div class="zscore-info">
                                <p>Tương tự W/H, dùng để sàng lọc tình trạng gầy còm, thừa cân và béo phì, đặc biệt hữu ích cho trẻ lớn.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <h2 class="section-title">7. Một số lỗi thường gặp</h2>
                    <p style="margin-bottom: 20px;">Nhận diện và khắc phục lỗi là rất quan trọng. Nhấp vào từng lỗi (trên mobile) để xem chi tiết hậu quả và cách khắc phục.</p>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Lỗi Phổ Biến</th>
                                    <th class="hidden-mobile">Hậu Quả</th>
                                    <th class="hidden-mobile">Cách Khắc Phục</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="error-row">
                                    <td style="font-weight: 600;">Cân không đặt trên mặt phẳng</td>
                                    <td class="hidden-mobile">Trọng lượng sai lệch</td>
                                    <td class="hidden-mobile">Kiểm tra độ cân bằng trước khi đo</td>
                                </tr>
                                <tr class="error-details">
                                    <td colspan="3" style="padding: 15px;">
                                        <p><strong>Hậu quả:</strong> Trọng lượng sai lệch</p>
                                        <p><strong>Khắc phục:</strong> Kiểm tra độ cân bằng trước khi đo</p>
                                    </td>
                                </tr>
                                <tr class="error-row">
                                    <td style="font-weight: 600;">Trẻ mặc nhiều quần áo</td>
                                    <td class="hidden-mobile">Cân nặng cao hơn thực tế</td>
                                    <td class="hidden-mobile">Cho trẻ mặc đồ mỏng, tháo phụ kiện</td>
                                </tr>
                                <tr class="error-details">
                                    <td colspan="3" style="padding: 15px;">
                                        <p><strong>Hậu quả:</strong> Cân nặng cao hơn thực tế</p>
                                        <p><strong>Khắc phục:</strong> Cho trẻ mặc đồ mỏng, tháo phụ kiện</p>
                                    </td>
                                </tr>
                                <tr class="error-row">
                                    <td style="font-weight: 600;">Không duỗi thẳng chân khi đo chiều dài</td>
                                    <td class="hidden-mobile">Chiều dài thấp hơn thực tế</td>
                                    <td class="hidden-mobile">Người đo phụ giữ cố định đầu, người còn lại duỗi chân</td>
                                </tr>
                                <tr class="error-details">
                                    <td colspan="3" style="padding: 15px;">
                                        <p><strong>Hậu quả:</strong> Chiều dài thấp hơn thực tế</p>
                                        <p><strong>Khắc phục:</strong> Người đo phụ giữ cố định đầu, người còn lại duỗi chân</p>
                                    </td>
                                </tr>
                                <tr class="error-row">
                                    <td style="font-weight: 600;">Đọc sai vạch chia trên thước</td>
                                    <td class="hidden-mobile">Lệch giá trị</td>
                                    <td class="hidden-mobile">Đảm bảo mắt song song với điểm đọc</td>
                                </tr>
                                <tr class="error-details">
                                    <td colspan="3" style="padding: 15px;">
                                        <p><strong>Hậu quả:</strong> Lệch giá trị</p>
                                        <p><strong>Khắc phục:</strong> Đảm bảo mắt song song với điểm đọc</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <section class="section">
                    <h3 style="font-size: 1.5em; color: #667eea; margin-bottom: 15px;">Minh Họa Tác Động Của Lỗi</h3>
                    <p style="margin-bottom: 20px;">Biểu đồ dưới đây minh họa cách các lỗi nhỏ có thể làm sai lệch kết quả cân nặng thực tế của trẻ (ví dụ: cân nặng thực là 10.0 kg).</p>
                    <div class="chart-container">
                        <canvas id="errorChart"></canvas>
                    </div>
                </section>
            </div>

            <div id="conclusion" class="tab-content">
                <section class="section">
                    <h2 class="section-title">8. Kết luận</h2>
                    <p style="margin-bottom: 20px;">Thực hiện đúng kỹ thuật cân đo là bước đầu tiên và quan trọng nhất để đảm bảo độ chính xác của chỉ số Z-Score WHO.</p>
                    <p>Mỗi con số đo lường chính xác không chỉ phản ánh tình trạng phát triển thể chất của trẻ, mà còn là cơ sở khoa học vững chắc để theo dõi sức khỏe, phát hiện sớm các vấn đề như suy dinh dưỡng, thừa cân hay rối loạn tăng trưởng, từ đó có can thiệp kịp thời và hiệu quả.</p>
                </section>
            </div>
        </main>
        
        </div> <!-- /content -->
    </div> <!-- /container -->
    </div> <!-- /page-container -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-button');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');
                });
            });

            const heightToggles = document.querySelectorAll('.toggle-btn');
            const heightContents = document.querySelectorAll('.toggle-content');

            heightToggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    heightToggles.forEach(t => t.classList.remove('active'));
                    heightContents.forEach(c => c.classList.remove('active'));

                    toggle.classList.add('active');
                    document.getElementById(toggle.dataset.toggle).classList.add('active');
                });
            });

            const zscoreCards = document.querySelectorAll('.zscore-card');
            zscoreCards.forEach(card => {
                card.addEventListener('click', () => {
                    card.classList.toggle('open');
                });
            });

            const errorRows = document.querySelectorAll('.error-row');
            errorRows.forEach(row => {
                row.addEventListener('click', () => {
                    if (window.innerWidth < 768) {
                        row.classList.toggle('open');
                    }
                });
            });

            const ctx = document.getElementById('errorChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [
                            'Lỗi: Cân không phẳng (-0.2kg)', 
                            'Thực tế (Đúng)', 
                            'Lỗi: Trẻ mặc áo khoác (+0.3kg)'
                        ],
                        datasets: [{
                            label: 'Cân nặng (kg)',
                            data: [9.8, 10.0, 10.3],
                            backgroundColor: [
                                '#f87171',
                                '#0d9488',
                                '#f87171'
                            ],
                            borderColor: [
                                '#ef4444',
                                '#0f766e',
                                '#ef4444'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Minh Họa Tác Động Của Lỗi Đo Lường (Ví dụ)'
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: false,
                                min: 9.5,
                                max: 10.5,
                                title: {
                                    display: true,
                                    text: 'Cân nặng (kg)'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

</body>
</html>
