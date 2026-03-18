var mc = {
    ajax_url: g5_url + '/plugin/mc/mc.ajax.php',
    config: {
        list_selector: "",
        list_selector_use: ""
    }
};
(function ($, mc) {



    mc.handle = function (elm) {
        var el = $(elm);
        var wrap = el.closest('.mc-controls');
        var data = wrap.data();
        var column_name = data['name'];
        var ori_input = wrap.find(':input[name="' + column_name + '"]');
        var mode = data.mode;
        var elements, values;
        var input = data.input;
        var separator = mode === 'list' ? '|' : ',';
        var url = g5_url + '/plugin/mc/mc.ajax.php';
        var f = $(elm.form);
        var finalize = function () {
        };


        //console.log(data, data.type, data.type !== 'category');
        if (input === 'checkbox') {
            elements = wrap.find(':checkbox[data-name="' + column_name + '"]:checked');
            values = [];
            elements.each(function () {
                values.push($(this).val());
            });
            ori_input.val(values.join(separator));
            ori_input.trigger('change');
            finalize();
            return;
        } else if (input === 'select' && data.type !== 'category' && el.has('[multiple]')) {
            ori_input.val(el.val().join(separator));
            ori_input.trigger('change');
            finalize();
            return;
        }


        if (!data.multiple || mode === 'list') {
            ori_input.val(el.val());
            ori_input.trigger('change');

        }

        data['path'] = el.val();

        var fake_inputs = wrap.find(':input[data-name="' + column_name + '"]');
        var fake_index = fake_inputs.index(el);
        var next_el = $(fake_inputs[fake_index + 1]);

        var is_last = fake_inputs.length === fake_index + 1;

        if (is_last) {

            finalize();
            return;
        }

        // data.value= data['path'];
        //data.name = data['path']? 'path':'mc';


        $.ajax({
            url: url,
            data: data,
            type: "get",
            dataType: "json",
            cache: false,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("Api", "mc");
            },
            success: function (res) {
                if (res && res.hasOwnProperty('data')) {
                    if (next_el.length) {
                        next_el.find('option:not([value=""])').remove();
                        if (res.data.length) {
                            for (var i = 0; i < res.data.length; i++) {
                                $('<option value="' + res.data[i].path + '">' + res.data[i].title + '</option>').appendTo(next_el);
                            }
                            next_el.attr('disabled', false);
                        } else {
                            next_el.attr('disabled', true);
                        }
                    }
                }

            }

        });
    }
    mc.handleMultiple = function (elm) {
        var el = $(elm);
        var wrap = el.closest('.mc-controls');
        var data = wrap.data();
        var column_name = data['name'];
        var ori_input = wrap.find(':input[name="' + column_name + '"]');

        var fake_inputs = wrap.find(':input[data-name="' + column_name + '"]');
        var last;
        for (var i = 0; i < fake_inputs.length; i++) {
            if (fake_inputs[i].value !== '') {
                last = fake_inputs[i];
            }
        }
        if (last) {
            last = $(last);
        } else {
            return;
        }
        var values = ori_input.val();
        if (values) {
            values = values.split(',');
        } else {
            values = [];
        }
        var value = last.val();
        if ($.inArray(value, values) !== -1) {
            return;
        }

        var multiple = wrap.find('.mc-control-multiple');
        $('<span onclick="mc.multipleDelete(this)" data-value="' + value + '">' + value + '<em>✗</em></span>').appendTo(multiple);
        values.push(value);
        ori_input.val(values.join(','));


    }
    mc.multipleDelete = function (elm) {
        var el = $(elm);
        var wrap = el.closest('.mc-controls');
        var data = wrap.data();
        var column_name = data['name'];
        var ori_input = wrap.find(':input[name="' + column_name + '"]');
        var value = el.data('value');
        var values = ori_input.val();
        if (values) {
            values = values.split(',');
            values = $.grep(values, function (v) {
                return v != value;
            });
            ori_input.val(values.join(','));
            el.remove();
        }
    }


    /**
     * list 모드 선택값 삭제
     * @param mode
     * @param obj
     */
    mc.removeCheckedItem = function (mode, obj) {
        var elm = $(obj);
        var container = $(obj).closest('.mc');
        var name = elm.data('name');
        var value = elm.data('value');
        var controls = container.find('.mc-controls[data-name="' + name + '"]');
        var input_type = controls.data('input');
        var els;
        switch (input_type) {
            case 'radio':
                els = controls.find(':input[name="' + name + '"]');
                els.removeAttr('checked').trigger('change');
                break;
            case 'checkbox':
                els = controls.find(':input[data-name="' + name + '"][value="' + value + '"]');
                els.removeAttr('checked').trigger('change');
                break;
            case 'select':
                els = controls.find('select[name="' + name + '"]');
                if (!els.length) {
                    els = controls.find('select[data-name="' + name + '"]');
                }
                $.each(els.get().reverse(), function () {
                    $(this).val('').trigger('change');
                });
                var data = $(els.get(0)).closest('.mc-controls').data();

                if(data['input']==='select' && data['mode']==='list' && data['root'] && els.length>1){ // 버튼검색모드일경우 멀티카테고리 아이템삭제처리
                    for(var i=1;i<els.length;i++){
                        $(els.get(i)).find("option[value!='']").remove();
                        $(els.get(i)).prop('disabled',true);
                    }
                }
                break;
            case 'between':
                els = controls.find('input[name="' + name + '[]"]');
                if (!els.length) {
                    els = controls.find('input[data-name="' + name + '"]');
                }
                $.each(els.get().reverse(), function () {
                    $(this).val('').trigger('change');
                });
                break;
            case 'range':
                if (mc.config.mode === 'list') {
                    els = controls.find('input[name="' + name + '"]');
                    els.val('');
                    if($(elm).closest('.mc-control-row').data('searchmode')==='btn'){

                    }else {
                        $(els.get(0).form).trigger('submit');
                    }
                }
                break;
        }
        elm.remove();
        if (mode === 'list') {

        }
    };

    /**
     * 목록보기 체크 아이템 삭제
     */
    mc.resetCheckedItem = function () {
        if (mc.config.list_href) {
            location.href = mc.config.list_href;
        }
    };


    /**
     * list 모드 범위체크
     * @param elm
     */
    window.mc_betweenHandle = function (elm) {
        var name = $(elm).data('name');
        var container = $(elm).closest('.mc-control-row');
        var input = container.find('input[name="' + name + '"]');
        var els = container.find(':input[data-name="' + name + '"]');
        input.val('');
        var values = [];
        $.each(els, function (i, field) {
            var value = $(field).val();
            values.push(value);
        });
        if (values.join('') !== '') {
            input.val(values.join('~'));
        }
        if (mc.config.mode === 'list') {
            if($(elm).closest('.mc-control-row').data('searchmode')==='btn'){

            }else {
                $(elm.form).trigger('submit');
            }
        }
    }


})(jQuery, mc);

function mc_handle_btn(elm) {
    var el = $(elm);
    var group = el.closest('.mc-group-wrap');
    var data = el.data();
    data.input = 'button';

    var url = g5_url + '/plugin/mc/ajax.php';
    $.ajax({
        url: url,
        data: data,
        type: "get",
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Api", "mc");
        },
        success: function (res) {
            if (res.html) {
                if (!group.length) {
                    group = $('<div class="mc-group-wrap"></div>').wrap(el);
                }
                if (!group.find('.mc-group').length) {
                    $(res.html).appendTo(group);
                    var input = group.find('input[name="' + data.inputName + '"]');
                    if (input.length) {

                    }
                }
            }
        }
    });
}

function mc_handle(elm) {
    var el = $(elm);
    var group = el.closest('.mc-group');

    var name = el.data('name') || group.data('name');
    var input = group.find(':hidden[name="' + name + '"]');
    var value = el.val();
    var els = group.find('select[data-name="' + name + '"]');
    var index = els.index(el);
    if (value) {
        input.val(value);
    }
    if (els.length === index + 1) {
        return;
    }
    var data = group.data();
    data.categoryValue = value;
    data.with_total_child = 1;

    var categoryColumn = data.categoryColumn === 'mc' ? 'mc' : 'path';
    var next_el = $(els[index + 1]);
    if (value === '') {
        next_el.find('option:not([value=""])').remove();
        return;
    }
    var url = g5_url + '/plugin/mc/mc.ajax.php';
    $.ajax({
        url: url,
        data: data,
        type: "get",
        dataType: "json",
        cache: false,
        beforeSend: function (xhr) {
            xhr.setRequestHeader("Api", "mc");
        },
        success: function (res) {
            if (res && res.hasOwnProperty('data')) {
                if (next_el.length) {
                    next_el.find('option:not([value=""])').remove();
                    if (res.data.length) {
                        for (var i = 0; i < res.data.length; i++) {
                            var title = res.data[i].title;
                            var total_child, disabled;
                            if (data.with_total_child && res.data[i].hasOwnProperty('total_child')) {
                                total_child = res.data[i].total_child;
                                title += '(' + res.data[i].total_child + ')';
                                disabled = total_child > 0 ? '' : ' disabled';
                            } else {
                                disabled = '';
                            }

                            $('<option value="' + res.data[i][categoryColumn] + '" ' + disabled + '>' + title + '</option>').appendTo(next_el);
                        }
                        next_el.attr('disabled', false);
                    } else {
                        next_el.attr('disabled', true);
                    }
                }
            }
        }
    });
}

function mc_search(form) {
    var f = $(form);
    var search_form = $('form[name=fsearch]');
    var elements = search_form.serializeArray().filter(function (i) {
        return i.value;
    });

    f.find('.mc-search-hidden').remove();
    var keyword_exists = search_form.find(':input[name=stx]').val() !== '';
    for (var i = 0; i < elements.length; i++) {
        var name = elements[i].name;
        if (keyword_exists || name === 'bo_table' || name === 'sca') {
            var value = elements[i].value;
            if (value) {
                $('<input type="hidden" class="mc-search-hidden" name="' + name + '" value="' + value + '">').prependTo(f);
            }
        }
    }
    return true;
}

/**
 * 목록검색 자동 확장
 */
mc.autoload_list = function () {
    var config = mc.config;

    if (config.mode === 'list' && config.list_selector && config.list_selector_use) {
        $(function () {
            var trigger = function () {
                $('#mc-search-form').find(':input[name]').on('change', function () {
                    var elm = $(this);
                    if(elm.closest('.mc-control-row').data('searchmode')==='btn'){
                        // 버튼검색모드일경우 자동 submit 하지 않는다
                    }else {
                        $('#mc-search-form').trigger('submit');
                    }
                });
            };
            if ($('.mc').length) {
                trigger();
                return;
            }
            var data = {bo_table: g5_bo_table};
            var container = $(config.list_selector); // 페이지내 삽입컨테이너를 설정한경우 해당 위치에 입력한다.
            if (container.length) {
                $.ajax({
                    url: g5_url + '/plugin/mc/ajax.list_skin.php',
                    data: data,
                    type: 'get',
                    cache: false,
                    dataType: 'html',
                    success: function (data) {
                        if (container.length) {
                            var outside_form = container.closest('form');
                            if (outside_form.length) { // 퐁 안인경우
                                $(data).insertBefore(outside_form);
                            } else {
                                $(data).prependTo(container);
                            }
                            trigger();
                        }
                    }
                });
            }
        });
    }
};

mc.autoload_write = function () {
    var config = mc.config;
    if (config.mode === 'write' && config.write_selector && config.write_selector_use) {
        $(function () {
            if ($('.mc').length) {
                return;
            }
            var data = {bo_table: g5_bo_table, wr_id: config.wr_id};
            var container = $(config.write_selector); // 페이지내 삽입컨테이너를 설정한경우 해당 위치에 입력한다.
            if (container.length) {
                $.ajax({
                    url: g5_url + '/plugin/mc/ajax.write_skin.php',
                    data: data,
                    type: 'get',
                    cache: false,
                    dataType: 'html',
                    success: function (data) {
                        if (container.length) {
                            $(data).prependTo(container);
                        }
                    }
                });
            }
        });
    }
};


mc.autoload_view = function () {
    var config = mc.config;
    if (config.mode === 'view' && config.view_selector && config.view_selector_use) {
        $(function () {
            if ($('.mc').length) {
                return;
            }
            var data = {bo_table: g5_bo_table, wr_id: config.wr_id};
            var container = $(config.view_selector); // 페이지내 삽입컨테이너를 설정한경우 해당 위치에 입력한다.
            if (container.length) {
                $.ajax({
                    url: g5_url + '/plugin/mc/ajax.view_skin.php',
                    data: data,
                    type: 'get',
                    cache: false,
                    dataType: 'html',
                    success: function (data) {
                        if (container.length) {
                            $(data).prependTo(container);
                        }
                    }
                });
            }
        });
    }
};


function wrestExtension(fld, css) {

    if (!wrestTrim(fld)) return;

    var str = css.split("="); // ext=?? <-- str[1]
    var src = fld.value.split(".");
    var ext = src[src.length - 1];

    if (wrestFld == null) {
        if (ext.toLowerCase() < str[1].toLowerCase()) {
            wrestMsg = wrestItemname(fld) + " : ." + str[1] + " 파일만 가능합니다.\n";
            wrestFld = fld;
        }
    }
}