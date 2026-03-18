(function ($, window) {
    var api = {};

    var parser = function (data) {
        if (data) {
            if (data.alert) {
                alert(data.alert);
            }
            if (data.reload) {
                location.reload();
            }
        }
    };

    var exec = function (mode, data) {
        data.mode = mode;
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (data) {
                parser(data);
            }
        })
    };

    api.remove = function (mc) {
        if (confirm("삭제하시겠습니까?")) exec('remove', {mc: mc});
    };
    api.moveUp = function (mc) {
        if (mc > 1) exec('moveUp', {mc: mc});
    };
    api.moveDown = function (mc) {
        if (mc > 1) exec('moveDown', {mc: mc});
    };
    api.removeConfigColumn = function (bo_table, column) {
        if (confirm("삭제하시겠습니까?")) {
            exec('removeConfigColumn', {bo_table: bo_table, column: column});
        }
    };
    api.boardColumnMove = function (bo_table, column, pos) {
        exec('boardColumnMove', {bo_table: bo_table, column: column, move: pos === 1 ? 1 : -1});
    };

    api.handleSubmit = function (evt) {
        evt.preventDefault();
        if (evt.target.tagName !== 'FORM') {
            alert('올바른 요청이 아닙니다.');
            return;
        }
        var form = $(evt.target);
        form.find("button[type=submit]").prop('disabled', true);
        var data = form.serialize();
        $.ajax({
            url: 'ajax.php',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (data) {
                parser(data);
                if (data) {
                    if (!data.reload) {
                        form.find("button[type=submit]").prop('disabled', false);
                    }
                }
            }
        });
    };

    api.handleSelectboxAdmin = function (obj) {
        var elm = $(obj);
        var val = elm.val();
        var input = $('input[name="data[root]"]');
        var send_data = {column: 'mc'}
        send_data['mc'] = val;
        send_data['root'] = 1;
        send_data['with_total_child'] = 1; // 자식의 갯수 포함 가져오기
        send_data['data_type'] = 'category';
        input.val(val);
        if (!val) {
            elm.find(' ~ select').remove();
            return;
        }
        $.ajax({
            url: g5_url + "/plugin/mc/mc.ajax.php",
            data: send_data,
            type: "get",
            cache: false,
            dataType: "json",
            success: function (res) {
                if (res && res.hasOwnProperty('data')) {

                    elm.find(' ~ select').remove();
                    var sel = $('<select onchange="mcApi.handleSelectboxAdmin(this)">').insertAfter(elm);

                    var is_all_last = true;
                    for (var i = 0; i < res.data.length; i++) {
                        var d = res.data[i];
                        var disabled = '';
                        if (d.total_child > 0) {
                            is_all_last = false;

                        } else {
                            disabled = ' disabled';
                        }
                        $('<option ' + disabled + ' value="' + d.mc + '">' + d.title + '(' + d.total_child + ')</option>').appendTo(sel);
                    }
                    if (!is_all_last) {
                        $('<option value="" selected>선택하실 수 있습니다</option>').prependTo(sel);
                    } else {
                        $('<option value="" selected>더이상 선택하실 수 없습니다</option>').prependTo(sel);
                    }
                }
            }
        });
    };

    $(function () {
        $("form.mc-ajax-form").on("submit", api.handleSubmit);

        $('form.mc-add-column').on('submit', function () {
            var send_data = $(this).serialize();
            $.ajax({
                url: g5_url + "/plugin/mc/adm/ajax.php",
                type: "post",
                dataType: "json",
                data: send_data,
                success: function (res) {
                    if (res && res.reload) {
                        location.reload();
                    }
                }
            });
            return false;
        });
    });
    if (window.mcApi) {
        $.extend(window.mcApi, api);
    } else {
        window.mcApi = api;
    }
})(jQuery, window);