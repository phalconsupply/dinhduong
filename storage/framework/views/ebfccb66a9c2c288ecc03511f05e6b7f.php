<?php if($slug == 'tu-0-5-tuoi'): ?>
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">
        <div id="avatar-wapper" class="orange" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <?php if($item->thumb != ''): ?>
                <img id="avatar-preview" src="<?php echo e($item->thumb); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php else: ?>
            <img id="avatar-preview" src="<?php echo e(asset('/web/frontend/images/ava01.png')); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php endif; ?>
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trẻ từ 0-5 tuổi</h4>
        </div>
    </div>
<?php elseif($slug == 'tu-5-19-tuoi'): ?>
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">

        <div id="avatar-wapper" class="pink" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <?php if($item->thumb != ''): ?>
                <img id="avatar-preview" src="<?php echo e($item->thumb); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php else: ?>
                <img id="avatar-preview" src="<?php echo e(asset('/web/frontend/images/ava01.png')); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php endif; ?>
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trẻ từ 5-19 tuổi</h4>
        </div>
    </div>
<?php elseif($slug == 'tu-19-tuoi'): ?>
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">

        <div id="avatar-wapper" class="yellow" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <?php if($item->thumb != ''): ?>
                <img id="avatar-preview" src="<?php echo e($item->thumb); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php else: ?>
                <img id="avatar-preview" src="<?php echo e(asset('/web/frontend/images/ava01.png')); ?>" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            <?php endif; ?>
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trên 19 tuổi</h4>
        </div>
    </div>
<?php endif; ?>

<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/sections/form-avatar.blade.php ENDPATH**/ ?>