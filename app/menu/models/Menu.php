<?php
class Menu extends \Coxis\Core\Model {
	public static $properties = array(
		'name',
	);
	
	public static $relations = array(	
		'items' => array(
			'has'	=>	'many',
			'model'	=>	'MenuItem',
		),
	);
	
	public static $behaviors = array(	
	);
		
	public static $meta = array(
	);
	
	public function __toString() {
		return (string)$this->name;
	}

	public function childs() {
		return $this->items()->where(array('parent_id'=>0))->get();
	}

	public function show() {
		HTML::codeStart();
		?>
		<style>
		#menu, #menu * {
			z-index:1000;
			padding:0;
			margin:0;
		}
		#menu, #menu ul {
			list-style-type:none;
		}
		#menu li {
			background-color:white;
			padding:5px 0px 5px 0px;
			text-align:center;
			width:50px;
			border:1px solid #000;
			margin-right:10px;
			float:left;
			position:relative;
		}
		#menu .active > a {
			border:1px solid red;
		}
		#menu > li ul {
			display:none;
		}
		#menu > li ul {
			position:absolute;
		}
		#menu > li > ul {
			border-top:10px solid transparent;
			top:30px;
			left:0;
		}
		#menu > li ul li {
			margin-bottom:10px;
		}
		#menu li:hover > ul {
			display:block;
		}
		#menu ul ul {
			top:-1px;
			left:50px;
		}
		</style>
		<?php
		HTML::codeEnd();
		?>
		<ul id="menu">
			<?php
			foreach($this->childs() as $child):
			$this->showItem($child);
			endforeach
			?>
		</ul>
		<?php
	}

	public function showItem($item) {
		?>
		<li<?php echo $this->active($item) ? ' class="active"':'' ?>>
			<?php if($item->type != 'none'): ?>
			<a href="<?php echo $item->url() ?>"><?php echo $item ?></a>
			<?php else: ?>
			<?php echo $item ?>
			<?php endif ?>
			<?php if($childs = $item->childs): ?>
			<ul>
				<?php
				foreach($childs as $child):
				$this->showItem($child);
				endforeach ?>
			</ul>
			<?php endif ?>
		</li>
		<?php
	}

	public function active($item) {
		return $item->url() == \URL::get();
		// ...
	}
}