<!-- ========================================
     FOOTER - Modern Design with Settings
     ======================================== -->
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <!-- About Section -->
            <div class="footer-section">
                <h3><i class="fas fa-info-circle"></i> Giới thiệu</h3>
                <p><?php echo e($setting['site-title'] ?? 'Phần mềm đánh giá dinh dưỡng'); ?></p>
                <p style="margin-top: 15px;"><?php echo e($setting['site-description'] ?? 'Hệ thống đánh giá tình trạng dinh dưỡng cho trẻ em và người lớn theo tiêu chuẩn WHO'); ?></p>
            </div>

            <!-- Contact Section -->
            <div class="footer-section">
                <h3><i class="fas fa-address-book"></i> Liên hệ</h3>
                <div class="footer-info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo e($setting['address'] ?? 'Địa chỉ chưa cập nhật'); ?></span>
                </div>
                <div class="footer-info-item">
                    <i class="fas fa-phone"></i>
                    <span><a href="tel:<?php echo e($setting['phone']); ?>"><?php echo e($setting['phone'] ?? '0123456789'); ?></a></span>
                </div>
                <div class="footer-info-item">
                    <i class="fas fa-envelope"></i>
                    <span><a href="mailto:<?php echo e($setting['email']); ?>"><?php echo e($setting['email'] ?? 'info@dinhduong.vn'); ?></a></span>
                </div>
                <?php if(isset($setting['website']) && $setting['website']): ?>
                <div class="footer-info-item">
                    <i class="fas fa-globe"></i>
                    <span><a href="<?php echo e($setting['website']); ?>" target="_blank"><?php echo e($setting['website']); ?></a></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links Section -->
            <div class="footer-section">
                <h3><i class="fas fa-link"></i> Liên kết nhanh</h3>
                <ul>
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="/tu-0-5-tuoi">Đánh giá 0-5 tuổi</a></li>
                    <li><a href="/tu-5-19-tuoi">Đánh giá 5-19 tuổi</a></li>
                    <?php if(auth()->check()): ?>
                        <li><a href="/admin">Quản trị hệ thống</a></li>
                        <li><a href="/admin/history">Lịch sử khảo sát</a></li>
                        <li><a href="/admin/dashboard/statistics">Thống kê</a></li>
                    <?php else: ?>
                        <li><a href="/auth/login">Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Support Section -->
            <div class="footer-section">
                <h3><i class="fas fa-headset"></i> Hỗ trợ</h3>
                <ul>
                    <li><a href="#">Hướng dẫn sử dụng</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                    <li><a href="#">Tiêu chuẩn WHO</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Điều khoản sử dụng</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; <?php echo e(date('Y')); ?> <?php echo e($setting['site-title'] ?? 'Phần mềm đánh giá dinh dưỡng'); ?>. All rights reserved.</p>
            <p>
                <?php if(isset($setting['app_version']) && $setting['app_version']): ?>
                    Version <?php echo e($setting['app_version']); ?> | 
                <?php endif; ?>
                Developed with <i class="fas fa-heart" style="color: #e74c3c;"></i> 
                <?php if(isset($setting['developer']) && $setting['developer']): ?>
                    by <?php echo e($setting['developer']); ?>

                <?php endif; ?>
            </p>
            
            <!-- Social Media Links (if configured) -->
            <?php if(isset($setting['facebook']) || isset($setting['zalo']) || isset($setting['youtube'])): ?>
            <div class="social-links">
                <?php if(isset($setting['facebook']) && $setting['facebook']): ?>
                    <a href="<?php echo e($setting['facebook']); ?>" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                <?php endif; ?>
                <?php if(isset($setting['zalo']) && $setting['zalo']): ?>
                    <a href="<?php echo e($setting['zalo']); ?>" target="_blank" title="Zalo">
                        <i class="fab fa-zalo"></i>
                    </a>
                <?php endif; ?>
                <?php if(isset($setting['youtube']) && $setting['youtube']): ?>
                    <a href="<?php echo e($setting['youtube']); ?>" target="_blank" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="back-to-top" title="Về đầu trang" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: all 0.3s;
    z-index: 999;
">
    <i class="fas fa-arrow-up"></i>
</button>

<style>
    .nuti-modal#validate_statistic .close {
        margin-top: 3px;
    }
    .nuti-modal#validate_statistic .modal-title {
        font-size: 20px;
        font-weight: bold;
    }
    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
    }
    .back-to-top.show {
        display: flex !important;
    }
</style>
<!-- Validate end -->
<!-- Preloader start -->
<div class="nuti_loader">
    <div class="nuti_loader_item"></div>
</div>
<!-- Preloader end -->
<script>
    var _ajaxRemoveHistoryIdUrl = "";
    
    // Back to Top Button
    window.addEventListener('scroll', function() {
        const backToTop = document.getElementById('backToTop');
        if (backToTop && window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else if (backToTop) {
            backToTop.classList.remove('show');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const backToTop = document.getElementById('backToTop');
        if (backToTop) {
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
<script src="<?php echo e(asset('/web/js/b47b5bf.js')); ?>"></script>
<script src="<?php echo e(asset('/web/js/bmi-fix.js')); ?>"></script>
<script src="<?php echo e(asset('/web/frontend/js/custom2.js')); ?>"></script>
<script src="<?php echo e(asset('/web/frontend/plugins/datatimepickerbootstrap/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('/web/frontend/plugins/datatimepickerbootstrap/bootstrap-datetimepicker.min.js')); ?>"></script>

<!-- Initialize Lucide Icons for Wizard Form -->
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>

<?php echo $__env->yieldPushContent('foot'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/layouts/footer.blade.php ENDPATH**/ ?>