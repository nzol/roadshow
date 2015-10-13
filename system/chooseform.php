<?php 

session_start();
include('globalfunctions.php');

$SessionId = $_POST['CLICKA_currentSessionId'];
$CurrentUserId = getSessionUser($SessionId);

$getEligibleForms = $Mysql->query("SELECT * FROM forms_permissions WHERE UserId='$CurrentUserId'");


?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Choose Form to Use</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<div class="row">
    <div class="col-lg-12">
        
		<table class="table">
            <tbody>
                <?php
                while($rsEligible = $getEligibleForms->fetch_assoc()) { 
                	$EligibleFormId =$rsEligible['FormId'];

                	$getForm = $Mysql->query("SELECT * FROM forms WHERE Id='$EligibleFormId'");
                	$rsForm = $getForm->fetch_assoc();

                ?>
                <tr onclick="chooseForm('<?php echo $EligibleFormId ?>')" style="cursor:pointer;">
                    <td style="width:10%;"><i class="fa fa-tasks fa-5x"></i></td>
                    <td style="vertical-align:middle;"><strong style="font-size:18px;"><?php echo $rsForm['FormName']; ?></strong></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- /.col-lg-12 -->
</div>

