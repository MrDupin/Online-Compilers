<!DOCTYPE html>

<?php

	$inputText = "";
	$outputText = "";

	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$addr = str_replace('.','_',$_SERVER['REMOTE_ADDR']);
		$code = $addr.".cpp";
		$executable = $addr.".exe";
		$results = $addr.".txt";

		$inputText = $_POST['input'];

		if(isset($_POST['run'])){
			CopyToFile($code);

			$line = "g++ ".$code." -o ".$executable." 2> ".$results;
			echo exec($line); //Create executable

			if(file_exists($executable)){
				$line = "./".$executable." >> ".$results." 2>&1";
				echo exec($line); //Run executable
				DeleteFile($executable);
			}

			

			$outputText = CopyToOutput($results);

			if($outputText == ""){
				$outputText = $outputText."\n\n\n---Code run successfully---";
			}

			DeleteFile($code);
			DeleteFile($results);
		}
	}

	
	function CopyToFile($name){
		$codeFile = fopen($name, "w");
		chmod($name,0777);

		$toWrite = $_POST['input'];

		fwrite($codeFile,$toWrite);

		fclose($codeFile);
	}

	function CopyToOutput($name){
		$outFile = fopen($name,"r");
	
		$temp = fread($outFile,filesize($name));

		fclose($outFile);

		return $temp;
	}

	function DeleteFile($name){
		$line = "rm ".$name;
		echo exec($line);
	}

?>

<html>
	<head>
		<title>CppOnline</title>

		<style>
			#t{
				-moz-tab-size: 4;
				-o-tab-size: 4;
				tab-size: 4;
			}

			textarea{
				background-color: #f9f9eb
			}
		</style>
	</head>

	<body bgcolor="#ccccff">
		<header>
			<h3>C++ Online</h3>
			Write your code on the left textarea, press "Run" and the results of your code will appear on the right.
			<br><br>
		</header>

		<form method="post" action="cpp.php">
			<header>
				<input type="submit" name="run" value="Run"/>
			</header>
			
			<textarea name="input" id="t" rows="40" cols="55" float:left onkeydown=tabbing(event)><?php echo $inputText;?></textarea>
			<textarea name="output" id="t" rows="40" cols="55" float:right onkeydown=tabbing(event)><?php echo $outputText;?></textarea>
		</form>
	</body>

	<script>
		var textareas = document.getElementsByTagName('textarea');
		var count = textareas.length;
		for(var i=0;i<count;i++){
    			textareas[i].onkeydown = function(e){
        			if(e.keyCode==9 || e.which==9){
            				e.preventDefault();
            				var s = this.selectionStart;
            				this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
            				this.selectionEnd = s+1;
        			}
    			}
		}
	</script>
</html>