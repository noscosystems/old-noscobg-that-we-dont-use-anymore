<?php

$form->attributes = array('class' => 'form-horizontal');
echo $form->renderBegin();
$widget = $form->activeFormWidget;
?>

<div class="page-header">
    <h1>User profile <small>Please enter your changes</small></h1>
</div>

<?php if(Yii::app()->user->hasFlash('account.myaccount.success')): ?>
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::app()->user->getFlash('account.myaccount.success'); ?>
    </div>
<?php endif; ?>

<?php
if($widget->errorSummary($form)){
    echo '<div class="alert alert-danger">' . $widget->errorSummary($form) . '</div>';
}
?>

<div class="row">
    <div class="col-sm-3 control-label">Change username:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'username', array('class' => 'form-control', 'value'=>$user->username) ); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-3 control-label">Change firstname:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'firstname', array('class' => 'form-control', 'value'=>$user->firstname) ); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-3 control-label">Change middlename:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'middlename', array('class' => 'form-control', 'value'=>$user->middlename) ); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-3 control-label">Change lastname:</div>
    <div class="col-sm-6">
        <?php echo $widget->input($form, 'lastname', array('class' => 'form-control', 'value'=>$user->lastname) ); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-2 col-sm-offset-3">
        <?php echo $widget->button($form, 'submit', array('class' => 'btn btn-lg btn-success') ); ?>
    </div>
</div>
<?php echo $form->renderEnd(); ?>
</div><!-- form -->