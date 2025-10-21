

<div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded shadow border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="LoginForm-title">Giới thiệu</h5>
                <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>
            </div>
            <div class="modal-body">
                <div class="text-center"><strong>{{$setting['app_title']}}</strong></div>
                <div class="text-center">Liên hệ:<strong>{{$setting['app_phone']}}</strong></div>
                <div class="text-center">Phiên bản: <strong>{{$setting['app_version']}}</strong></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-warning" data-dismiss="modal" data-bs-dismiss="modal" >Đóng</button>
            </div>
        </div>
    </div>
</div>
