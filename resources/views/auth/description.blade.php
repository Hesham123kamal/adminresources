@php
$selectors=isset($selectors)?$selectors:'.description';
$not_required=isset($not_required)?true:false;
$labels=isset($labels)?$labels:[Lang::get('main.description')];
@endphp
<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript">
    tinymce.init({
        relative_urls : false,
        remove_script_host : false,
        document_base_url : "{{ URL('') }}",
        convert_urls : true,
        selector: "{{$selectors}}",
        theme: "modern",
        plugins: [
            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern "/*fullpage*/
        ],

        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",
        image_advtab: true,
        menubar: false,
        toolbar_items_size: 'small',
        style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ],
        //content_css: ['//fonts.googleapis.com/css?family=Indie+Flower'],
        font_formats: 'Cocon=cocon,Andale Mono=andale mono,monospace;Arial=arial,helvetica,sans-serif;Arial Black=arial black,sans-serif;Book Antiqua=book antiqua,palatino,serif;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,palatino,serif;Helvetica=helvetica,arial,sans-serif;Impact=impact,sans-serif;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco,monospace;Times New Roman=times new roman,times,serif;Trebuchet MS=trebuchet ms,geneva,sans-serif;Verdana=verdana,geneva,sans-serif;Webdings=webdings;Wingdings=wingdings,zapf dingbats;',

        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ],
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>
@foreach(explode(',',$selectors) as $key=>$value)
    <div class="form-group col-lg-12">
        <label class=" control-label" for="{{ltrim($value,'.')}}">{{ $labels[$key] }}  @if(!$not_required) <span class="required"> * </span> @endif</label>
        <textarea class="form-control {{ltrim($value,'.')}}" style="min-height: 300px;" id="{{ltrim($value,'.')}}"  name="{{ltrim($value,'.')}}" data-required="1" placeholder="{{ Lang::get('main.enter').$labels[$key] }}">@if(old(ltrim($value,'.'))){{ old(ltrim($value,'.')) }}@elseif(isset($posts)){!! $posts[$key] !!}@endif</textarea>
    </div>
@endforeach

