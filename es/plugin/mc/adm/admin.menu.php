<?php
/**
 * 그누보드 상단 관리자 출력 처리 함수
 * @param int $no
 */

$_i = array_slice(get_included_files(), -2, 1);
function mc_admin_menu($no = 800)
{
    global $menu;
    $admin_url = G5_PLUGIN_URL . '/mc/adm';
    $menu["menu" . $no] = array(
        array($no . '000', '멀티카테고리관리', '' . $admin_url . '/config.php', 'mc'),
        array($no . '100', '게시판 관리', '' . $admin_url . '/config.php', 'mc_config'),
        array($no . '200', '카테고리 관리', '' . $admin_url . '/list.php', 'mc_list'),
        array($no . '200', '카테고리 관리(트리형)', '' . $admin_url . '/tree.php', 'mc_tree'),
        array($no . '400', '설치/삭제', '' . $admin_url . '/setup.php', 'mc_setup'),
    );
}
mc_admin_menu(substr(pathinfo($_i[0], PATHINFO_FILENAME), 10));