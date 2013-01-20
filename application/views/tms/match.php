<h1><a href="/tms/matches/">Matches</a> &#9656; <span id="title-name"><?=$this->data["match"]["name"]?></span></h1>
<div id='message'></div>
<!--<?php
	$fields = array("name","description","directions");
	$labels = array("Match Name","Description","Directions");
	$types = array("text","textfield","textfield");
	$types = array("text","textfield","textfield");
	$widths = array("15%","40%","20%");

	echo "<table>";
	for($i=0;$i<count($fields);$i++){
		echo "\t<tr>";
		echo "\t\t<th style='width:{$widths[0]}'>{$labels[$i]}</th>";
		$value = htmlspecialchars($this->data['venue'][$fields[$i]], ENT_QUOTES);
		if($types[$i]=="text")
			echo "\t\t<td style='width:{$widths[1]}'>
							<input 
								id='form-{$fields[$i]}'
								type='text'
								onkeyup='changed(\"{$fields[$i]}\")'
								oldvalue='{$value}'
								value='{$value}'>
						</td>";
		else if($types[$i]=="textfield")
			echo "\t\t<td style='width:{$widths[1]}'>
							<textarea 
								id='form-{$fields[$i]}'
								onkeyup='changed(\"{$fields[$i]}\")'
								oldvalue='{$value}'>{$value}</textarea>
						</td>";
		echo "\t\t<td id='edit-{$fields[$i]}' style='visibility:hidden;width:{$widths[2]}'><button onclick='update(\"{$fields[$i]}\")'>Update</button><button onclick='cancel(\"{$fields[$i]}\")'>Cancel</button></td>";
		echo "\t</tr>";
	}
	echo "</table>";
?>-->

<?php echo print_r($this->data["match"]);?>

<script type='text/javascript'>
	function changed(fieldname){
		input = $("#form-"+fieldname);
		if(input.val()!=input.attr('oldvalue'))
			$("#edit-"+fieldname).css("visibility", "visible");
	}
	function cancel(fieldname){
		input = $("#form-"+fieldname);
		input.val(input.attr('oldvalue'));
		$("#edit-"+fieldname).css("visibility", "hidden");
	}
	function update(fieldname){
		var form_data = {};
		form_data[fieldname] = $("#form-"+fieldname).val();
		jQuery.ajax({
			url: "/db_venues/update_venue/<?=$this->data['venue']['venueID']?>",
			type: 'POST',
			async : false,
			data: form_data,
			success: function(msg) {
				if(msg['success']){
					$("#edit-"+fieldname).css("visibility", "hidden");
					if(fieldname=="name"){
						$("#title-name").html($("#form-"+fieldname).val());
					}
				} else {
					alert("Could not update the field. Please contact support.");
				}
			}
		});
	}
</script>