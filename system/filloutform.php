<?php 

session_start();
include('globalfunctions.php');

$SessionId = $_POST['CLICKA_currentSessionId'];
$CurrentUserId = getSessionUser($SessionId);

$FormId = getSessionVar($SessionId,"currentlySelectedForm");

$getForm = $Mysql->query("SELECT * FROM forms WHERE Id='$FormId'");
$rsForm = $getForm->fetch_assoc();

$BackgroundColour = $rsForm['BackgroundColour'];
$FormBackground = $rsForm['FormBackground'];

$getFormInputs = $Mysql->query("SELECT * FROM forms_inputs WHERE FormId='$FormId' ORDER BY SortNum ASC");


?>
<script type="text/javascript">

function scriptLoaded() {

       $('#formChosen').modal('hide');

    document.getElementById('topbartext').innerHTML = "<button type=\"button\" class=\"btn btn-warning\" onclick=\"exitFormEntry()\" style=\"height:20px;padding-top:0px;\">Exit</button> Fill Out: <?php echo $rsForm['FormName']; ?>"
    document.getElementById('sidenav').style.display="none"
    document.body.style.backgroundColor = "#<?php echo $BackgroundColour ?>";
    document.getElementById('page-wrapper').style.marginLeft="0px"
    document.getElementById('page-wrapper').style.backgroundColor="#<?php echo $BackgroundColour ?>"
}

</script>
<div class="row" style="padding-top:25px;" align="center">
	<?php echo $rsForm['HeaderCode']; ?>

</div>
<div class="row" align="center" style="margin-top:15px;" >
	<div class="table-responsive table-bordered">
        <table class="table" style="background-color:#<?php echo $FormBackground ?>;">
            
            <tbody>
            	<?php
            	while($rsInput = $getFormInputs->fetch_assoc()) { 
            	?>
                <tr>
                    <td width="20%"><strong><?php echo $rsInput['InputDisplayName']; ?></strong></td>

                    <!-- DIFFERENT FIELD TYPES -->
                    <?php if($rsInput['ElementType']=="text") { ?>
                    <td><input type="text" class="form-control" id="<?php echo $rsInput['InputFieldName']; ?>"></td>
                    
                    <?php } else if($rsInput['ElementType']=="email") { ?>
                    <td><input type="email" class="form-control" id="<?php echo $rsInput['InputFieldName']; ?>"></td>

                    <?php } else if($rsInput['ElementType']=="phone") { ?>
                    <td><input type="tel" class="form-control" id="<?php echo $rsInput['InputFieldName']; ?>"></td>

                    <?php } else if($rsInput['ElementType']=="submit-wide") { ?>
                    <td><button type="button" class="btn btn-primary btn-lg btn-block"><?php echo $rsInput['ElementParams']; ?></button></td>

                    <?php } else { ?>
                    <td><input class="form-control" id="<?php echo $rsInput['InputFieldName']; ?>"></td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row" align="center">
	<?php echo $rsForm['FooterCode']; ?>
</div>