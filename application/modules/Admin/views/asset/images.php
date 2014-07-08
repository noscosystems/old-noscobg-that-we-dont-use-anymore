<?php
    /**
     * @var AssetController $this
     */
    $this->pageTitle = 'images';
    //$assetUrl = Yii::app()->assetManager->publish(Yii::app()->theme->basePath . '/assets');



?>




<?php $form->attributes=array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data' , 'id' => 'frm' ); ?>
<?php echo $form->renderBegin(); ?>
<?php $widget = $form->ActiveFormWidget; ?>

<?php if($widget->errorSummary($form)): ?>
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo $widget->errorSummary($form); ?>
    </div>
<?php endif; ?>	

<?php if (Yii::app()->user->hasFlash('success')){ ?>
	<div class="alert alert-success" >
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo Yii::app()->user->getFlash('success') ?>
	</div>
<?php }else if (Yii::app()->user->hasFlash('success')){ ?>
	<div class="alert alert-danger" >
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<?php echo Yii::app()->user->getFlash('success') ?>
	</div>
<?php } ?>

<?php echo $widget->input( $form, 'asset', array('value'=> 'notempty') ); ?>
<input name="image1" type="file" /><br>
<div class="row" >
<!-- <table class="table"> -->
	<!-- <tbody> -->
		<?php foreach ($images as $k => $v):?>
			<?php //if ($k==0 || $k%3==0): ?>
				<!-- <tr> -->
			<?php //endif; ?>
					<!-- <td> -->
					<div  class="col-sm-4" >
						<div class="image-hover">
							<?php echo CHtml::image(Yii::app()->assetManager->publish($v->url), $v->asset,
								array(
									'class' => 'img-rounded',
									'height' => '240',
									'width' => '300'
								));
							?>
							<div class="overlay">  
		        				<?php echo CHtml::link('Delete', array('DeleteImage', 'id' => $v->id), array('class' => 'btn btn-danger')); ?>
		        			</div>
						</div>
					</div>
					<!-- </td> -->
			
		<?php endforeach; ?>
				<!-- </tr> -->
				<?php if ($k==0 || $k%3==0): ?>
				</div>
				<?php endif; ?>
	<!-- </tbody> -->
<!-- </table> -->
<?php echo $widget->button($form, 'submit', array ( 'class' => 'btn btn-sm btn-success')); ?>
<?php echo $form->renderEnd(); ?>

<style>
/*table tr td:hover {
	background-color: #e8e8e8;
}*/
.image-hover{
	position:relative;
}
.overlay {
	/*text-align: center;*/
	top:0;
	left:0;
	width:100%;
	height:100%;
	z-index:50;
	display: none;
	position: absolute;
	background: rgba (0,0,0, 0.2);
	/*opacity:0.2;*/
	/*filter: alpha ( opacity=40 );*/
}

</style>

<script>
$(document).ready( function (){
	$(".image-hover").hover( function(){
			// What happens when the mouse is hovered
			$(this).children('.overlay').show();
		}, function(){
			// What happens the mouse leaves
			$(this).children('.overlay').hide();
		
		});
});

// document.getElementById('frm');
// document.getElementById('file');

// file.onchange=  function (){
// 	frm.submit();
// }
</script>