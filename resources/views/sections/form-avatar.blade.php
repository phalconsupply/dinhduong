@if($slug == 'tu-0-5-tuoi')
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">
        <div id="avatar-wapper" class="orange" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            @if($item->thumb != '')
                <img id="avatar-preview" src="{{$item->thumb}}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @else
            <img id="avatar-preview" src="{{ asset('/web/frontend/images/ava01.png') }}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @endif
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trẻ từ 0-5 tuổi</h4>
        </div>
    </div>
@elseif($slug == 'tu-5-19-tuoi')
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">

        <div id="avatar-wapper" class="pink" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            @if($item->thumb != '')
                <img id="avatar-preview" src="{{$item->thumb}}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @else
                <img id="avatar-preview" src="{{ asset('/web/frontend/images/ava01.png') }}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @endif
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trẻ từ 5-19 tuổi</h4>
        </div>
    </div>
@elseif($slug == 'tu-19-tuoi')
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">

        <div id="avatar-wapper" class="yellow" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            @if($item->thumb != '')
                <img id="avatar-preview" src="{{$item->thumb}}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @else
                <img id="avatar-preview" src="{{ asset('/web/frontend/images/ava01.png') }}" alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
            @endif
            <i class="icon camera-icon"></i>
            <h4 id="title-name" class="pro5-name desc">Hồ sơ<br>trên 19 tuổi</h4>
        </div>
    </div>
@endif

