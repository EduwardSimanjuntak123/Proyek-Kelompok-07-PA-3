<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e($pengaturan->name ?? config('app.name')); ?></title>

  
  <?php echo $__env->make('includes.style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->yieldPushContent('style'); ?>
</head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
        
        <?php echo $__env->make('partials.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        
        <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <!-- Main Content -->
      <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
      </div>

      <?php echo $__env->renderWhen(session('role') === 'Dosen' && in_array(1, session('dosen_roles', []), true) && !request()->routeIs('ai.kelompok'), 'partials.agent-float', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

      
      <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
  </div>

  
  <?php echo $__env->make('includes.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->yieldPushContent('script'); ?>
</body>
</html>
<?php /**PATH E:\Semester 6\PA III\Project\Proyek-Kelompok-07-PA-3\ui laravel\resources\views/layouts/main.blade.php ENDPATH**/ ?>