<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet">
    <?php echo $this->layout->get_meta_tags() ?>
    <?php echo $this->layout->get_title() ?>
    <?php echo $this->layout->get_favicon() ?>
    <?php echo $this->layout->get_schema() ?>
    <?php
        $path = [
            'assets/plugins/bootstrap/dist/css/bootstrap.min.css',
            'assets/plugins/AdminLTE/dist/css/AdminLTE.min.css',
            'assets/plugins/AdminLTE/dist/css/skins/skin-'.$this->config->item('skin').'.min.css',
            'assets/plugins/font-awesome/css/font-awesome.min.css',
            'assets/plugins/alertify-js/build/css/alertify.min.css',
            'assets/plugins/alertify-js/build/css/themes/default.min.css',
            'assets/plugins/iCheck/skins/square/blue.css',
            'assets/plugins/flag-icon-css/css/flag-icon.min.css',
        ];
        if (isset($grocery_css)) {
            foreach ($grocery_css as $key => $value) {
                $path[] = $value;
            }
        }
        if (isset($css_plugins)) {
            foreach ($css_plugins as $key => $value) {
                $path[] = $value;
            }
        }
        $path[] = 'assets/css/a-design.css';
    ?>
    <?php $this->layout->set_assets($path, 'styles') ?>
    <?php echo $this->layout->get_assets('styles') ?>
</head>
<body class="hold-transition skin-<?php echo $this->config->item('skin') ?> fixed">
    <!-- Site wrapper -->
    <div class="wrapper">  
        <header class="main-header">
            <a href="<?php echo site_url(); ?>" class="logo">
                <span class="logo-mini"><b>M</b>I</span>
                <span class="logo-lg">myIgniter</span>
            </a>

             <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu" >
                            <?php $user = $this->ion_auth->user()->row() ?>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php if (filter_var($user->photo,FILTER_VALIDATE_URL)): ?>
                                    <img src="<?php echo $user->photo; ?>" class="user-image" alt="<?php echo $user->full_name ?>"/>
                                <?php else: ?>
                                    <img src="<?php echo $user->photo == '' ? base_url('assets/img/logo/kotaxdev.png') : base_url('assets/uploads/image/'.$user->photo) ?>" class="user-image" alt="<?php echo $user->full_name ?>"/>
                                <?php endif; ?>
                                <span class="hidden-xs"><?php echo $user->username ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <?php if (filter_var($user->photo,FILTER_VALIDATE_URL)): ?>
                                        <img src="<?php echo $user->photo; ?>" class="img-circle" alt="<?php echo $user->full_name ?>"/>
                                    <?php else: ?>
                                        <img src="<?php echo $user->photo == '' ? base_url('assets/img/logo/kotaxdev.png') : base_url('assets/uploads/image/'.$user->photo) ?>" class="img-circle" alt="<?php echo $user->full_name ?>"/>
                                    <?php endif; ?>
                                    <p>
                                      <?php echo $user->full_name ?>
                                      <small><?php echo lang('last_login') ?> <?php echo ' '.date('d/m/Y H:i', $user->last_login); ?></small>
                                  </p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?php echo  site_url('myigniter/profile')?>" class="btn btn-default btn-flat"><?php echo lang('profile') ?></a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?php echo  site_url('logout')?>" class="btn btn-default btn-flat"><?php echo lang('logout') ?></a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="flag-icon flag-icon-<?php echo $this->session->userdata('lang_code') ? $this->session->userdata('lang_code') : 'us' ?>"></span> <?php echo lang('language') ?>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?php echo site_url('myigniter/sys_lang/arabic'); ?>"><span class="flag-icon flag-icon-eg"></span> Arabic</a></li>
                                <li><a href="<?php echo site_url('myigniter/sys_lang/indonesian'); ?>"><span class="flag-icon flag-icon-id"></span> Indonesian</a></li>
                                <li><a href="<?php echo site_url('myigniter/sys_lang/italian'); ?>"><span class="flag-icon flag-icon-it"></span> Italian</a></li>
                                <li><a href="<?php echo site_url('myigniter/sys_lang/spanish'); ?>"><span class="flag-icon flag-icon-eg"></span> Spanish</a></li>
                                <li><a href="<?php echo site_url('myigniter/sys_lang/english'); ?>"><span class="flag-icon flag-icon-us"></span> US English</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <aside class="main-sidebar">
            <section class="sidebar" id="menuSidebar">
                <form action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" class="form-control searchlist" id="searchSidebar" placeholder="Search..." autocomplete="off"/>
                        <span class="input-group-btn">
                            <button type='button' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <ul class="sidebar-menu list" id="menuList">
                </ul>
                <ul class="sidebar-menu list" id="menuSub">
                    <?php $menus = $this->layout->get_menu() ?>
                    <?php foreach ($menus as $menu): ?>
                        <li class="header"><?php echo $menu['label'] ?></li>
                        <?php if (is_array($menu['children'])): ?>
                            <?php foreach ($menu['children'] as $menu2): ?>
                                <li <?php echo $menu2['attr'] != '' ? ' id="'.$menu2['attr'].'" ' : '' ?> <?php echo is_array($menu2['children']) ? ' class="treeview" ' : '' ?>>
                                    <?php if (is_array($menu2['children'])): ?>
                                        <a href="<?php echo $menu2['link'] != '#' ? strpos($menu2['link'], 'http') !== false ? $menu2['link'] : site_url($menu2['link']) : '#' ?>" class="name">
                                            <i class="fa fa-<?php echo $menu2['icon'] ?>"></i> <span><?php echo $menu2['label'] ?></span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <?php foreach ($menu2['children'] as $menu3): ?>
                                                <li <?php echo $menu3['attr'] != '' ? ' id="'.$menu3['attr'].'" ' : '' ?>>
                                                    <?php if (is_array($menu3['children'])): ?>
                                                    <a href="<?php echo $menu3['link'] != '#' ? strpos($menu3['link'], 'http') !== false ? $menu3['link'] : site_url($menu3['link']) : '#' ?>" class="name">
                                                        <i class="fa fa-<?php echo $menu3['icon'] ?>"></i> <span><?php echo $menu3['label'] ?></span>
                                                        <i class="fa fa-angle-left pull-right"></i>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <?php foreach ($menu3['children'] as $menu4): ?>
                                                            <li <?php echo $menu4['attr'] != '' ? ' id="'.$menu4['attr'].'" ' : '' ?>>
                                                                <a href="<?php echo $menu4['link'] != '#' ? strpos($menu4['link'], 'http') !== false ? $menu4['link'] : site_url($menu4['link']) : '#' ?>" class="name">
                                                                    <i class="fa fa-<?php echo $menu4['icon'] ?>"></i> <span><?php echo $menu4['label'] ?></span>
                                                                </a>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                    <?php else: ?>
                                                    <a href="<?php echo $menu3['link'] != '#' ? strpos($menu3['link'], 'http') !== false ? $menu3['link'] : site_url($menu3['link']) : '#' ?>" class="name">
                                                        <i class="fa fa-<?php echo $menu3['icon'] ?>"></i> <span><?php echo $menu3['label'] ?></span>
                                                    </a>
                                                    <?php endif ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    <?php else: ?>
                                        <a href="<?php echo $menu2['link'] != '#' ? strpos($menu2['link'], 'http') !== false ? $menu2['link'] : site_url($menu2['link']) : '#' ?>" class="name">
                                            <i class="fa fa-<?php echo $menu2['icon'] ?>"></i> <span><?php echo $menu2['label'] ?>
                                        </a>
                                    <?php endif ?>
                                </li>
                            <?php endforeach ?>
                        <?php endif ?>
                    <?php endforeach ?>
                </ul>
            </section>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1><?php echo $title ?></h1>
                <?php $this->layout->breadcrumb($crumb) ?>
            </section>
            <!-- Main content -->
            <section class="content exspan-bottom">
                <?php echo $this->layout->get_wrapper('page') ?>
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> <?php echo $this->config->item('version') ?>
            </div>
            <strong>Copyright &copy; <?php echo date('Y') ?> <a href="http://www.kotaxdev.com">kotaxdev</a>.</strong> All rights reserved.
        </footer>
    </div><!-- ./wrapper -->
    <?php
        $baseJs = ['assets/plugins/jquery/dist/jquery.min.js'];
        if (isset($grocery_js)) {
            foreach ($grocery_js as $key => $value) {
                $baseJs[] = $value;
            }
        }
        $path = [
            'assets/plugins/bootstrap/dist/js/bootstrap.min.js',
            'assets/plugins/AdminLTE/dist/js/app.min.js',
            'assets/plugins/alertify-js/build/alertify.min.js',
            'assets/plugins/slimScroll/jquery.slimscroll.min.js',
            'assets/plugins/list.js/dist/list.min.js',
            'assets/plugins/iCheck/icheck.min.js'
        ];
        $path = array_merge($baseJs, $path);
        if (isset($js_plugins)) {
            foreach ($js_plugins as $key => $value) {
                $path[] = $value;
            }
        }
        $path[] = 'assets/js/a-design.js';
    ?>
    <?php $this->layout->set_assets($path, 'scripts') ?>
    <?php echo $this->layout->get_assets('scripts') ?>
</body>
</html>