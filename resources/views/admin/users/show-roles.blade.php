@extends('admin.users.show')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('show-content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-12">
{{--                <div class="row">--}}
{{--                    <div class="col-6">--}}
{{--                        <form action="" method="GET">--}}
{{--                            <div class="row mb-3">--}}
{{--                                <div class="col-10">--}}
{{--                                    <div class="form-icon position-relative">--}}
{{--                                        <i data-feather="search" class="fea icon-sm icons"></i>--}}
{{--                                        <input name="keyword" id="keyword" type="text" class="form-control ps-5" value="{{app()->request->get('keyword')}}" placeholder="Từ khóa">--}}

{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-2 row">--}}
{{--                                    <div class="">--}}
{{--                                        <button type="submit" class="btn btn-sm form-control btn-success text-white d-inline"><i data-feather="search" class="fea icon-sm icons"></i> Tìm</button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}
{{--                    </div>--}}
{{--                    <div class="col-6 ta-r">--}}
{{--                        <nav aria-label="breadcrumb" class="d-inline-block">--}}
{{--                            <ul class="breadcrumb bg-transparent rounded mb-0 p-0">--}}
{{--                                <li class="breadcrumb-item text-capitalize">--}}
{{--                                    <button href="{{route('admin.users.create')}}" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#addRole"  >--}}
{{--                                        <i class="ti ti-plus icons icon-sm"></i> Thêm vai trò</button></li>--}}
{{--                            </ul>--}}
{{--                        </nav>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="table-responsive shadow rounded">
                    <table id="history-table" class="table table-center bg-white mb-0">
                        <thead>
                        <tr>
                            <th class="border-bottom ">ID</th>
                            <th class="border-bottom ">Ảnh đơn vị</th>
                            <th class="border-bottom ">Thuộc đơn vị</th>
                            <th class="border-bottom ">Vai trò</th>
                            <th class="border-bottom ">Thuộc bộ phân</th>
                            <th class="border-bottom ">Ngày cấp</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $unit = $user->unit;
//                                dd($role->id);
                            @endphp
                            <tr>
                                <td class="">{{$unit->id}}</td>
                                <td>
                                    @if($unit->thumb)
                                        <a href="{{route('admin.units.show_history', $unit)}}"><img src="{{$unit->thumb}}" class="img-thumbnail" width="80px" /></a>
                                    @else
                                        <a href="{{route('admin.units.show_history', $unit)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
                                    @endif
                                </td>
                                <td class=""><a href="{{route('admin.units.show', ['unit' => $unit->id])}}">{{$unit->name}}</a></td>
                                <td class="">{{v('role.'.$user->role)}}</td>
                                <td class="">{{$user->department}}</td>
                                <td class="">{{$user->created_at->format('d-m-Y')}}</td>

{{--                                <td>--}}
{{--                                    <div class="btn-group dropdown-primary me-2 mt-2">--}}
{{--                                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                                            Menu--}}
{{--                                        </button>--}}
{{--                                        <div class="dropdown-menu">--}}
{{--                                            <form class="dropdown-item" action="{{route('admin.unit_users.destroy', ['unitUser'=>$role])}}" method="POST" onsubmit="return confirm('Xác nhận xóa vai trò này?');">--}}
{{--                                                <input type="hidden" name="_method" value="DELETE">--}}
{{--                                                <input type="hidden" name="_token" value="{{csrf_token()}}">--}}
{{--                                                <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa vai trò</button>--}}
{{--                                            </form>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                            </tr>

                        </tbody>
                    </table>
                    <div class="mt-2 d-flex">
                        <div class="mx-auto">
{{--                            {{ $roles->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4") }}--}}
                        </div>
                    </div>
                </div>
            </div><!--end col-->
        </div><!--end row-->
{{--        <div class="modal fade" id="addRole" tabindex="-1" aria-labelledby="addRole-title" aria-hidden="true">--}}
{{--            <div class="modal-dialog modal-dialog-centered">--}}
{{--                <div class="modal-content rounded shadow border-0">--}}
{{--                    <div class="modal-header border-bottom">--}}
{{--                        <h5 class="modal-title" id="LoginForm-title">Thêm vai trò</h5>--}}
{{--                        <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>--}}
{{--                    </div>--}}
{{--                    <form action="{{route('admin.unit_users.store')}}" method="POST">--}}
{{--                        @csrf--}}
{{--                        <input type="hidden" name="user_id" value="{{$user->id}}">--}}
{{--                        <div class="modal-body">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="mb-3">--}}
{{--                                        <label class="form-label">Thuộc đơn vị <span class="text-danger">*</span></label>--}}
{{--                                        <div class="form-icon position-relative">--}}
{{--                                            <i data-feather="shield" class="fea icon-sm icons"> </i>--}}
{{--                                            <select name="unit_id" id="district_code" class="form-select form-control text-end" aria-label="Default select example" required>--}}
{{--                                                <option value="">Chọn đơn vị</option>--}}
{{--                                                @foreach($units ?? [] as $unit)--}}
{{--                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div><!--end col-->--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <div class="mb-3">--}}
{{--                                        <label class="form-label">Vai trò <span class="text-danger">*</span></label>--}}
{{--                                        <div class="form-icon position-relative">--}}
{{--                                            <i data-feather="star" class="fea icon-sm icons"> </i>--}}
{{--                                            <select name="role" id="district_code" class="form-select form-control text-end" aria-label="Default select example" required>--}}
{{--                                                <option value="">Chọn vai trò</option>--}}
{{--                                                <option value="manager">Quản lý</option>--}}
{{--                                                <option value="employee">Nhân viên</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div><!--end col-->--}}
{{--                            </div>--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-12">--}}
{{--                                    <div class="mb-3">--}}
{{--                                        <label class="form-label">Thuộc bộ phân</label>--}}
{{--                                        <div class="form-icon position-relative">--}}
{{--                                            <i data-feather="users" class="fea icon-sm icons"> </i>--}}
{{--                                            <input name="department" id="department" type="text" class="form-control ps-5" value="{{old('department')}}" placeholder="Bộ phận">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div><!--end col-->--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" >Đóng</button>--}}
{{--                            <button type="submit" class="btn btn-sm btn-primary">Lưu</button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

@endsection
