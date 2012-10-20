<?php
namespace Coxis\Core;

class Error {
	public static function report($msg, $backtrace=null) {
		set_error_handler(function(){});
	
		if(ob_get_length() > 0)
			ob_end_clean();
	
		\Response::setCode(500);
				
		ob_start();
		
		if($msg) {
			echo '<b>Message</b><br/>'."\n";
			echo $msg."<br/>\n<br/>\n";
		}
		static::print_backtrace($msg, $backtrace);
	
		$result = ob_get_contents();
		ob_end_clean();
		
		\Coxis\Core\Tools\Log::add('errors/log.html', $result);
		
		if(\Config::get('error_display') || \Config::get('error_display') === null)
			return new Result(array(), $result);
		else
			return new Result(array(), '<h1>Error</h1>Oops, something went wrong. Please report it to the administrator.');
	}
	
	public static function print_backtrace($msg='', $backtrace=null) {
		if(!$backtrace)
			$backtrace = debug_backtrace();
			
		if(php_sapi_name() == 'cli') {
			for($i=0; $i<sizeof($backtrace); $i++) {
				$trace = $backtrace[$i];
				if(isset($backtrace[$i+1]))
					$next = $backtrace[$i+1];
				else
					$next = $backtrace[sizeof($backtrace)-1];
				
				if(isset($trace['file']))
					echo 'File:  '.$trace['file'].' ('.$trace['line'].')'."\n";
			}
		}
		else {
			echo '<b>Backtrace</b><br/>'."\n";
			?>
			<script src="<?php echo \URL::to('js/jquery.js') ?>"></script>
			<style>
			.spanargs {
				cursor:pointer;
			}
			.current_line {
				display:inline-block;
			}
			</style>
			<script>
			$(function(){
				$('.spanargs').unbind("click").click(function(e){//todo WTF?
					if($(e.currentTarget).parent().find('div').first().css('display') == 'block') {
						$(e.currentTarget).parent().find('div').first().css('display', 'none');
						$(e.currentTarget).find('span').text('+');
					}
					else {
						$(e.currentTarget).parent().find('div').first().css('display', 'block');
						$(e.currentTarget).find('span').text('-');
					}
				});
			});
			</script>
			<?php
			for($i=0; $i<sizeof($backtrace); $i++) {
				$trace = $backtrace[$i];
				if(isset($backtrace[$i+1]))
					$next = $backtrace[$i+1];
				else
					$next = $backtrace[sizeof($backtrace)-1];
				
				if(isset($trace['file']))
					echo 'File:  <a href="code:'.$trace['file'].':'.$trace['line'].'">'.$trace['file'].'</a> ('.$trace['line'].')'."<br/>\n";
				if(isset($next['class']))
					echo 'At: '.$next['class'].$next['type'].$next['function']."()<br/>\n";
				else
					echo 'At: '.$next['function']."()<br/>\n";
				echo '<div><span class="spanargs"><span>+</span>Args:</span>'."<br/>\n";
				echo '<div style="display:none">';
				foreach($next['args'] as $arg) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-';
					if(is_array($arg)) {
						var_dump($arg);
					}
					elseif(is_string($arg))
						echo $arg;
					else
						var_dump($arg);
					echo "<br/>\n";
				}
				echo '</div>';
				echo '</div>';
				echo '<div><span class="spanargs"><span>+</span>Code:</span>'."<br/>\n";
				echo '<div style="display:none">';
				
				if(isset($trace['line'])) {
					$start = $trace['line']-3;
					if($start < 1)
						$start = 1;
					$end = $trace['line']+3;
					
					$pos = $trace['line']-$start;
					
					$code = '';
					
					$handle = fopen($trace['file'], "r");
					if($handle) {
						for($j=1; $j<$start; $j++)
							fgets($handle, 4096);
						for($j=$start; $j<=$end; $j++)
							$code .= fgets($handle, 4096);
								
						fclose($handle);
					}
					
					ob_start();
					#todo highlight all file and then extract lines, instead of the opposite
					//~ highlight_string('<?php'."\n".$code);
					$code = ob_get_contents();
					ob_end_clean();
					
					echo '<code>';
					foreach(array_slice(explode('<br />', $code), 1) as $k=>$line)
						if($pos == $k)
							echo '<span style="float:left; display:inline-block; width:50px; color:#000">'.($start++).'</span>'.'<div class="current_line" style="display:inline-block; background-color:#ccc;">'.$line.'</div><br>';
						else
							echo '<span style="float:left; display:inline-block; width:50px; color:#000">'.($start++).'</span>'.$line.'<br>';
					echo '</code>';
				}
				
				echo '</div>';
				echo '</div>';
				echo "<br/>\n";
				echo '<hr/>';
				echo "<br/>\n";
			}
		}
	}
}
