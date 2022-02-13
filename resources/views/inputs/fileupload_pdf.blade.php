<div class="img-update-template" id="pdf-update-template_{{$id ?? 0}}">
    <span class="text-danger">เฉพาะไฟล์ที่มีนามสกุล pdf</span>
    <div class="custom-file mb-1">
        <input 
            type="file" 
            class="custom-file-input customFilePdf"
            name="{{$name}}"
            data-msg-required="กรุณาเลือกไฟล์"
            data-msg-extension="กรุณาเลือกไฟล์ที่มีนามสกุล pdf เท่านั้น"
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
    <div class="file-preview" style="display: {{ is_null($pdf ?? null) ? 'none' : 'block'}}"></div>
    @if ($pdf)
        <hr/>
        @foreach ($pdf as $f)
            <div class="row col-12 mb-1">
                <div class="">
                    <i class="fas fa-file-pdf text-danger" style="font-size: 3rem;"></i>
                </div>
                <div class="col d-flex align-items-center">
                    <p class="mb-0">
                        <a href="{{asset("storage/".$f->file_path)}}" target="_blank" >
                            <span id="filename">
                                {{ $f->file_origin_name}}
                            </span>
                            (<span>
                                <strong id="filesize">
                                    {{ Helper::filesize_formatted($f->file_size) }}
                                </strong>
                            </span>)
                        </a>
                    </p>
                    @if (isset($del) && $del)
                        <div class="form-check ml-2">
                            <input type="checkbox" class="form-check-input" id="delFile_{{$f->id}}" value="{{$f->id}}" name="delfiles[]">
                            <label class="form-check-label" for="delFile_{{$f->id}}">ลบ</label>
                        </div>
                    @endif
                </div> 
            </div>
        @endforeach
    @endif
</div>

<textarea id="files-display-template" style="display: none">
    <div class="row col-12 mb-1">
        <div class="">
            <i class="fas fa-file-pdf text-danger" style="font-size: 3rem;"></i>
        </div>
        <div class="col d-flex align-items-center">
            <p class="mb-0">
                <span id="filename">{0}</span>
                (<span>
                    <strong id="filesize">{1}</strong>
                </span>)
                <span class="badge badge-danger">new</span>
            </p>
        </div> 
    </div>
</textarea>