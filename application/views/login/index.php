<?php
$form->attributes = array('class' => 'form-horizontal');
echo $form->renderBegin();
$widget = $form->activeFormWidget;
?>

<?php if($widget->error($form, 'username') || $widget->error($form, 'password')): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Errors Found!</strong> Your form has the following errors:<br />
            <?php
            echo $widget->error($form, 'username');
            echo $widget->error($form, 'password');
            ?>
    </div>
<?php endif; ?>

<br />

<div class="col-sm-offset-2 col-sm-10 page-header">
  <h1>Login <small>Please enter your login credentials</small></h1>
</div>

<div class="row">
    <div class="col-sm-3 control-label">Username:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'username', array('class' => 'form-control') ); ?>
    </div>
</div>

<br />

<div class="row">
    <div class="col-sm-3 control-label">Password:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'password', array('class' => 'form-control') ); ?>
    </div>
</div>

<br />

<div class="row">
    <div class="col-sm-2 col-sm-offset-3">
        <?php echo $widget->button($form, 'submit', array('class' => 'btn btn-lg btn-success',) ); ?>
    </div>
</div>
<?php echo $form->renderEnd(); ?>