<?php //echo $this->Html->css('../js/tableexport/src/stable/css/tableexport.min.css'); ?>
<?php //echo $this->Html->script('tableexport/src/stable/js/js-xlsx/xlsx.core.min.js'); ?>
<?php //echo $this->Html->script('tableexport/src/stable/js/FileSaver.js'); ?>
<?php //echo $this->Html->script('tableexport/src/stable/js/tableexport.js'); ?>
<?php echo $this->Html->script('tableExports/tableExport.js'); ?>
<?php echo $this->Html->script('tableExports/jquery.base64.js'); ?>

<div class="row">
    <form id="diverseformvalidate" role="form" method="post">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading bg-white">Paste Your HTML<br>
        </div>
        <div class="panel-body">
            <div class="form-group">
				<?php if(isset($notagfound) && $notagfound != "") { ?>
				<div id="messages">
					<div>
						<div id="multiFlash.0Message" class="msg">
							<div class="alert ng-scope am-fade alert-danger"><?php echo $notagfound; ?></div>
						</div>            
					</div>    
				</div>
				<?php } ?>
				<textarea name="data[Validation][content]" class="form-control" rows="10" cols="30" id=""></textarea>
				<?php if($emptyfield != "") { ?>
				<div class="error-message"><?php echo $emptyfield; ?></div>
				<?php } ?>
				<button id="validateButton" class="btn btn-addon btn-success m-b" title="Validate" type="button" style="margin-top: 10px"><i class='fa fa-thumbs-up'></i>Validate</button>                    
            </div>
        </div>
      </div>
    </div>
    </form>
    <div id="diffdata" class="col-lg-12">
      <?php if(isset($outputTags) && count($outputTags) > 0) { 
		echo '<div class="panel panel-default"><div class="panel-heading bg-white"><span class="pull-right"><a class="exportExcel"><i class="icon fa fa-download"></i> Download CSV</a></span> Results<br></div><div class="panel-body"><div class="form-group"><div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr class="bg-light"><th style="width: 50px">S.No</th><th>Field</th><th>Type</th><th>Rule</th><th>Message</th></tr></thead><tbody>';
		$i = 0;
		foreach($outputTags as $output) {
			$i++;
			$colSpan = 1;
			if(count($output['rules']) > 1) {
				$colSpan = count($output['rules']);
			}
			echo '<tr>';
			echo '<td colspan="'.$colSpan.'">'.$i.'</td>';
			echo '<td colspan="'.$colSpan.'">'.$output['name'].'</td>';
			echo '<td colspan="'.$colSpan.'">'.$output['type'].'</td>';
			if(count($output['rules']) > 0) {
				foreach($output['rules'] as $rule) {
					if($rule['ruleType'] == "Custom") {
						echo '<td style="background:red; color: #fff">';
					} else {
						echo '<td>';
					}
					echo '<div data-toggle="tooltip" data-placement="left" title="" data-original-title="'.$rule['description'].'">'.$rule["rule"];
					if($rule['tag'] != "true") {
						echo ': '.$rule['tag'];
					}
					echo '</div>';
					echo '</td>';
					echo '<td>';
					echo $rule["message"];
					echo '</td>';
				} 
			}
			echo '</tr>';
		}
		echo '</tbody></table></div></div></div></div></div>';
	  } ?>
    </div>
 </div>

 <script type="text/javascript">
 $('#validateButton').click(function(){
	$("#diverseformvalidate").submit();
 });
 
 $(document).ready(function() {
	$(".exportExcel").click(function() {
		//$(".table").tableExport({formats: ["xlsx"], "fileName": "Rules"});
		$('.table').tableExport({type:'excel', escape:'false', tableName:'Rules', separator: ','});
	});
 });
 
 </script>