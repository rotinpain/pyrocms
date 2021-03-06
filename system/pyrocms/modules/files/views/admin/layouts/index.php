<style type="text/css">

#files_browser {
	display: table;
	width: 100%;
	margin-bottom: 20px;
	background-color: #FFFFFF;
	color: #333333;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
	border-radius: 5px;
	border: 1px solid #CCCCCC;
}
#files_left_pane {
	width: 15%;
	display: table-cell;
	border-right: 1px solid #CCCCCC;
	padding: 10px 0;
	
}
#files_left_pane ul,
#files_left_pane ul li,
#files_toolbar ul,
#files_toolbar ul li {
	list-style: none;
	padding: 0px;
	margin: 0px;
}
#files_browser h3 {
	margin: 0px;
	padding: 3px 0 5px 0px;	
}
#files_left_pane h3 {
	padding-left: 10px;
}
#files_right_pane {
	width: 85%;
	display: table-cell;
	padding: 10px;
}
#files_left_pane li a {
	padding: 10px;
	background: transparent;
	display: block;
	text-decoration: none;
	color: #666666;
}
#files_left_pane li a:hover {
	background-color: #F4F4F4;
	color: #333333;
}
#files_left_pane li.current a {
	background-color: #3a4043;
	color: #FFFFFF;
}
#files_toolbar ul li {
	display: inline-block;
	padding-left: 5px;
}
#files_toolbar label {
	font-weight: bold;
}
</style>
<div id="files_browser">
	<div id="files_left_pane">
		<h3><?php echo lang('files.folders.title'); ?></h3>
		<?php echo $template['partials']['nav']; ?>
	</div>
	<div id="files_right_pane">
	</div>
</div>
<script type="text/javascript">
(function($) {
	$(function() {
		$("#files_left_pane li a").click(function() {
			curr_url = $(this).attr("href");
			curr_text = $(this).text();
			$(this).text("Loading...");
			$("#files_right_pane").load(curr_url);
			$(this).parent().parent().find('li').removeClass('current');
			$(this).parent().addClass('current');
			$(this).text(curr_text);
			return false;
		});

		$("#files_left_pane li:first-child a").click();
	});
})(jQuery);
</script>