<?php HTML::include_js('js/jquery.js') ?>
<?php HTML::include_js('bundles/slideshow/slideshow.js') ?>
<style>
* { margin:0; padding:0; }
.slideshow {
	position:relative;
}
.slideshow .slideshow-left, .slideshow .slideshow-right {
	cursor:pointer;
	z-index:1000;
}
.slideshow .slideshow-hidden {
	position:relative;
	height:100%;
	overflow:hidden;
	
	width:200px;
}
.slideshow .slideshow-container {
	list-style-type:none;
	width:20000px;
}
.slideshow .slideshow-container li {
	float:left;
}
</style>
<div class="slideshow">
	<!-- non-moving stuff like arrows here -->
	<div class="slideshow-left">Précédent</div>
	<div class="slideshow-right">Suivant</div>
	<!-- end -->
	<!-- slideshow elements here -->
	<div class="slideshow-hidden">
		<ul class="slideshow-container">
			<?php if($slideshow->image1->exists()): ?>
			<li>
				<img src="<?php echo $slideshow->image1 ?>" width="<?php echo Config::get('slideshow', 'width') ?>"/>
			</li>
			<?php endif ?>
			<?php if($slideshow->image2->exists()): ?>
			<li>
				<img src="<?php echo $slideshow->image2 ?>" width="<?php echo Config::get('slideshow', 'width') ?>"/>
			</li>
			<?php endif ?>
			<?php if($slideshow->image3->exists()): ?>
			<li>
				<img src="<?php echo $slideshow->image3 ?>" width="<?php echo Config::get('slideshow', 'width') ?>"/>
			</li>
			<?php endif ?>
			<?php if($slideshow->image4->exists()): ?>
			<li>
				<img src="<?php echo $slideshow->image4 ?>" width="<?php echo Config::get('slideshow', 'width') ?>"/>
			</li>
			<?php endif ?>
		</ul>
	</div>
	<!-- end -->
</div>