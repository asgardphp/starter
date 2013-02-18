(function(){CKEDITOR.dialog.add('vimeo',
	function(editor)
	{return{title:editor.lang.vimeo.title,minWidth:CKEDITOR.env.ie&&CKEDITOR.env.quirks?368:350,minHeight:240,
	onShow:function(){this.getContentElement('general','content').getInputElement().setValue('')},
	onOk:function(){
		       		val = this.getContentElement('general','content').getInputElement().getValue();
		       		// http://vimeo.com/54754006
		       		if(val.match(/vimeo.com\/([0-9]+)/) != null) {
       					val = val.match(/vimeo.com\/([0-9]+)/)[1];
       				}
       				// else {
       				// 	val = val.match(/http\:\/\/youtu\.be\/([\w-]{11})/)[1];
       				// }
					var text='<iframe src="http://player.vimeo.com/video/54754006?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="500" height="213" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> <p><a href="http://vimeo.com/'
					+ val
					+'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="500" height="213" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	this.getParentEditor().insertHtml(text)},
	contents:[{label:editor.lang.common.generalTab,id:'general',elements:
																		[{type:'html',id:'pasteMsg',html:'<div style="white-space:normal;width:500px;"><img style="margin:5px auto;" src="'
																		+CKEDITOR.getUrl(CKEDITOR.plugins.getPath('vimeo')
																		+'images/vimeo_large.png')
																		+'"><br />'+editor.lang.vimeo.pasteMsg
																		+'</div>'},{type:'html',id:'content',style:'width:340px;height:90px',html:'<input size="100" style="'+'border:1px solid black;'+'background:white">',focus:function(){this.getElement().focus()}}]}]}})})();


// <iframe src="http://player.vimeo.com/video/54754006?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff" width="500" height="213" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe> <p><a href="http://vimeo.com/54754006">Charles Bukowski's Nirvana</a> from <a href="http://vimeo.com/patrickb">Patrick Biesemans</a> on <a href="http://vimeo.com">Vimeo</a>.</p>