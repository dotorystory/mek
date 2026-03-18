<?php
include_once __DIR__ . '/lib.php';


spl_autoload_register(function ($class) {
    $part = explode('\\', $class);
    if (array_shift($part) === 'mc') {
        $file = MC_PLUGIN_PATH . '/lib/' . join('/', $part) . '.php';
        if (is_file($file)) {
            include $file;
        }
    }
});

function mc_common_hook()
{
    global $bo_table;

    add_stylesheet('<link rel="stylesheet" href="' . G5_PLUGIN_URL . '/mc/css/mc.css">', 12);
    if(MC_USE_RANGE_SEARCH) {
        add_stylesheet('<link rel="stylesheet" href="' . G5_PLUGIN_URL . '/mc/3thparty/nouislider.min.css">', 12);
    }

    add_javascript('<script src="' . G5_PLUGIN_URL . '/mc/script.js?ver=' . MC_VERSION . '"></script>', 12);
    add_javascript('<script src="' . G5_PLUGIN_URL . '/mc/3thparty/nouislider.min.js?ver=' . MC_VERSION . '"></script>', 12);

    $v = explode(realpath(G5_PATH), realpath($_SERVER['SCRIPT_FILENAME']));

    $installed = mc::isInstalled();
    if (!$installed || !$bo_table) return;
    $mc = mc_board($bo_table);
    if (!$mc) return;
    $config = $mc->getConfig();


    $separator = ',';
    if ($v[1] === '/bbs/board.php') {// list
        $separator = '|';
    }


    switch ($v[1]) {
        case '/bbs/write.php': // 게시판 글쓰기

            $_json = array();
            $_json['mode'] = 'write';
            if (isset($_GET['wr_id'])) {
                $_json['wr_id'] = (int)$_GET['wr_id'];
            }
            $_json['write_selector'] = $config['write_selector'];
            $_json['write_selector_use'] = $config['write_selector_use'];
            add_javascript('<script>$.extend(mc.config, ' . json_encode($_json) . '); mc.autoload_write();</script>', 12);

            break;
        case '/bbs/board.php': // 게시판 리스트, 내용보기
            if (!empty($_GET['wr_id'])) {// 내용보기
                $_json = array();
                $_json['mode'] = 'view';
                $_json['wr_id'] = (int)$_GET['wr_id'];
                $_json['view_selector'] = $config['view_selector'];
                $_json['view_selector_use'] = $config['view_selector_use'];


                add_javascript('<script>$.extend(mc.config, ' . json_encode($_json) . '); mc.autoload_view();</script>', 12);

            } else {

                foreach ($config['columns'] as $name => $column) {
                    if (isset($_GET[$name]) || array_key_exists($name, $_GET)) {
                        $_GET[$name] = (string)$_GET[$name];
                    }
                }

                $_json = array();
                $_json['mode'] = 'list';
                $_json['list_selector'] = $config['list_selector'];
                $_json['list_selector_use'] = $config['list_selector_use'];

                $_json['list_href'] = G54? get_pretty_url($bo_table):'./board.php?bo_table='.$bo_table;

                add_javascript('<script>$.extend(mc.config, ' . json_encode($_json) . '); mc.autoload_list();</script>', 12);


            }
            break;
        case '/bbs/write_update.php'; // 게시글 등록시 테이타 유효성 체크

            $config = mc_board($bo_table)->getConfig();
            $columns = $config['columns'];
            $values = array();
            foreach ($columns as $name => $attrs) {

                $_POST[$name] = str_replace('|', ',', trim($_POST[$name]));

                switch ($attrs['data_type']) {
                    case \mc\Column::DATASET_DATE:
                        $_POST[$name] = date('Y-m-d', strtotime($_POST[$name]));
                        break;
                    case \mc\Column::DATASET_NUMBER:
                        $_POST[$name] = (int)$_POST[$name];
                        break;
                }
                $values[$name] = $_POST[$name];
            }

            register_shutdown_function(function () use ($values) {
                global $wr_id, $write_table, $redirect_url;
                if ($values && $wr_id){
                    if(G54){
                        if($redirect_url) {
                            mc::update($write_table, $values, array('wr_id' => $wr_id));
                        }
                    }elseif(G53){
                        mc::update($write_table, $values, array('wr_id' => $wr_id));
                    }
                }
            });
            break;
    }
}


mc_common_hook();
