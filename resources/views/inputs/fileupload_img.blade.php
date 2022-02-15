<div class="img-update-template" id="img-update-template">
    <span class="text-danger">เฉพาะไฟล์ที่มีนามสกุล jpg,jepg และ png (กว้าง {{$width ?? 9999}} px , สูง {{$height ?? 9999}} px)</span>
    <div class="custom-file mb-1">
        <input 
            type="file" 
            class="custom-file-input"
            id="customFileImg"
            name="{{$name}}"
            width="{{$width ?? 9999}}"
            height="{{$height ?? 9999}}"
            data-msg-minwidth="ขนาดความกว้างของภาพขั้นต่ำ {{$width ?? 0}} px"
            data-msg-minheight="ขนาดความสูงของภาพขั้นต่ำ {{$height ?? 0}} px"
            data-msg-required="กรุณาเลือกไฟล์"
            data-msg-extension="กรุณาเลือกไฟล์ที่มีนามสกุล jpg,jepg และ png เท่านั้น"
            @if ($multiple ?? false)
                multiple
            @endif
            @isset($required)
                @if ($required)
                    required
                @endif
            @endisset>
        <label class="custom-file-label" for="customFile">Choose file...</label>
    </div>
    <div class="image-preview" style="display: {{ is_null($image) ? 'none' : 'block'}}">
        @if ($image)
            <div class="row col-12">
                <div class="">
                    <img
                        src="{{asset("storage/".$image->file_path)}}"
                        id="image-preview"
                        class="img-thumbnail">
                </div>
                <div class="col d-flex align-items-center">
                    <p class="mb-0">
                        <span id="filename">
                            {{ $image->file_origin_name }}
                        </span>
                        (<span>
                            <strong id="filesize">
                                {{ Helper::filesize_formatted($image->file_size) }}
                            </strong>
                        </span>)
                    </p>
                </div> 
            </div>
        @endif
    </div>
</div>

<textarea id="image-display-template" style="display: none">
    <div class="row col-12">
        <div class="">
            <img
                src="{0}"
                class="img-thumbnail">
        </div>
        <div class="col d-flex align-items-center">
            <p class="mb-0">
                <span id="filename">{1}</span>
                (<span>
                    <strong id="filesize">{2}</strong>
                    , ขนาด กว้าง {3} px , สูง {4} px
                </span>)
            </p>
        </div> 
    </div>
</textarea>