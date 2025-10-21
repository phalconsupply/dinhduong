<div class="row row-cols-xl-4 row-cols-md-2 row-cols-1">
    <div class="col mt-4">
        <a href="#!" class="features feature-primary d-flex justify-content-between align-items-center rounded shadow p-3">
            <div class="d-flex align-items-center">
                <div class="icon text-center rounded-pill">
                    <i class="uil uil-history fs-4 mb-0"></i>
                </div>
                <div class="flex-1 ms-3">
                    <h6 class="mb-0 text-muted">Lượt khảo sát</h6>
                    <p class="fs-5 text-dark fw-bold mb-0">
                        <span class="counter-value" data-target="{{$count['total_survey']}}">{{$count['total_survey']}}</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
    <!--end col-->
    <div class="col mt-4">
        <a href="#!" class="features feature-primary d-flex justify-content-between align-items-center rounded shadow p-3">
            <div class="d-flex align-items-center">
                <div class="icon text-center rounded-pill">
                    <i class="uil uil-history fs-4 mb-0"></i>
                </div>
                <div class="flex-1 ms-3">
                    <h6 class="mb-0 text-muted">Khảo sát của tôi</h6>
                    <p class="fs-5 text-dark fw-bold mb-0"><span class="counter-value" data-target="{{$count['total_my_survey']}}">{{$count['total_my_survey']}}</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
    <!--end col-->
    <div class="col mt-4">
        <a href="#!" class="features feature-primary d-flex justify-content-between align-items-center rounded shadow p-3">
            <div class="d-flex align-items-center">
                <div class="icon text-center rounded-pill">
                    <i class="uil uil-user-circle fs-4 mb-0 text-danger"></i>
                </div>
                <div class="flex-1 ms-3">
                    <h6 class="mb-0 text-muted">Có nguy cơ</h6>
                    <p class="fs-5 text-dark fw-bold mb-0">
                        <span class="counter-value" data-target="{{$count['total_risk']}}">{{$count['total_risk']}}</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
    <!--end col-->
    <div class="col mt-4">
        <a href="#!" class="features feature-primary d-flex justify-content-between align-items-center rounded shadow p-3">
            <div class="d-flex align-items-center">
                <div class="icon text-center rounded-pill">
                    <i class="uil uil-user-circle fs-4 mb-0 text-success"></i>
                </div>
                <div class="flex-1 ms-3">
                    <h6 class="mb-0 text-muted">Bình thường</h6>
                    <p class="fs-5 text-dark fw-bold mb-0">
                        <span class="counter-value" data-target="{{$count['total_normal']}}">{{$count['total_normal']}}</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
    <!--end col-->

</div>
