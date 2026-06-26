<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto" action="">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <div class="d-sm-none d-lg-inline-block">Hi, <?php echo e(session ('name')); ?></div></a>
            <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-title">Halo, <?php echo e(session ('name')); ?></div>
            <a href=" <?php echo e(route('profile',['user_id' => session('user_id'), 'role' => session('role'), 'token' => session('token')])); ?> " class="dropdown-item has-icon"> 
                <i class="far fa-user"></i> Profil
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href=" <?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="nav-icon fas fa-sign-out-alt"></i> &nbsp; Log Out</a>
                <form id="logout-form" action=" <?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </li>
    </ul>
</nav>
<?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/partials/nav.blade.php ENDPATH**/ ?>