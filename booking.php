<?php
include "db.php";
?>

<?php
function confirm($result){
    global $con;
    if(!$result){
        die("Query failed".mysqli_error($con));
    }
}

if(isset($_GET['u'])){
	$the_room_category=$_GET['u'];
}
if(isset($_GET['cin'])){
	$check_in=$_GET['cin'];
}
if(isset($_GET['cout'])){
	$check_out=$_GET['cout'];
}
if(isset($_GET['branch'])){
	$branch_id=$_GET['branch'];
}

$query="SELECT $the_room_category.room_id FROM $the_room_category JOIN rooms ON $the_room_category.room_id=rooms.room_id WHERE (rooms.branch_id='{$branch_id}' AND $the_room_category.room_id NOT IN (SELECT forr.room_id FROM $the_room_category JOIN forr ON $the_room_category.room_id=forr.room_id WHERE (('{$check_in}' BETWEEN forr.check_in_date AND forr.check_out_date) OR ('{$check_out}' BETWEEN forr.check_in_date AND forr.check_out_date)))) LIMIT 1";
    $select_all_rooms=mysqli_query($con, $query);
    while($row=mysqli_fetch_assoc($select_all_rooms)){
        $room_id=$row['room_id'];
    }

if($the_room_category=="simple"){
    $amount="123";
}

if($the_room_category=="deluxe"){
    $amount="456";
}

if($the_room_category=="suite"){
    $amount="789";
}

$total=$amount*(ceil((strtotime($check_out)-strtotime($check_in))/86400));

if(isset($_POST['add_booking'])){
	$f_name=$_POST['f_name'];
	$l_name=$_POST['l_name'];
	$cust_email=$_POST['cust_email'];
	$cust_phone=$_POST['cust_phone'];
	$country=$_POST['country'];
	$dob=$_POST['dob'];
	$passport_no=$_POST['passport_no'];
	$payment_type=$_POST['payment_type'];
	$n_rooms=$_POST['n_rooms'];
	$n_adults=$_POST['n_adults'];
	$n_children=$_POST['n_children'];

	$query="SELECT * FROM customer WHERE cust_id=(SELECT MAX(cust_id) FROM customer)";
	$select_cust_id=mysqli_query($con, $query);
        while($row=mysqli_fetch_assoc($select_cust_id)){
            $the_cust_id=$row['cust_id'];
    }

    $the_cust_id=$the_cust_id+1;

    $booking_id = substr(str_shuffle(str_repeat("0123456789", 5)), 0, 5);
    $bill_id = substr(str_shuffle(str_repeat("0123456789", 6)), 0, 6);
    $booking_date = date('Y-m-d H:i:s');

    $query="INSERT INTO customer(cust_id, cust_email, cust_phone, passport_no, country, dob, f_name, l_name) VALUES('{$the_cust_id}', '{$cust_email}', '{$cust_phone}', '{$passport_no}', '{$country}', '{$dob}', '{$f_name}', '{$l_name}')";
  	$create_query=mysqli_query($con,$query);
	confirm($create_query);

	$query="INSERT INTO booking(cust_id, Booking_id, room_id, branch_id) VALUES('{$the_cust_id}', '{$booking_id}', '{$room_id}', '{$branch_id}')";
  	$create_query=mysqli_query($con,$query);
	confirm($create_query);

	$query="INSERT INTO bill(cust_id, Bill_id, Amount, Payment_type) VALUES('{$the_cust_id}', '{$bill_id}', '{$total}', '{$payment_type}')";
  	$create_query=mysqli_query($con,$query);
	confirm($create_query);

	$query="INSERT INTO forr(Booking_id, room_id, check_in_date, check_out_date) VALUES('{$booking_id}', '{$room_id}', '{$check_in}', '{$check_out}')";
  	$create_query=mysqli_query($con,$query);
	confirm($create_query);

	$query="INSERT INTO generates(Booking_id, Bill_id, booking_date) VALUES('{$booking_id}', '{$bill_id}', '{$booking_date}')";
  	$create_query=mysqli_query($con,$query);
	confirm($create_query);
	
	header("Location: confirmation.php?u={$booking_id}&cat={$the_room_category}");
}
	
?>


<html>
	<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="license" href="https://www.opensource.org/licenses/mit-license/">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

		<script src="script.js"></script>
		<style>
			.section {
	position: relative;
	height: 100%;
	background-image: url('./images/background2.jpg');
	background-size:cover;
	
}

.section .section-center {
	position: relative;
	top: 50%;
	left: 0;
	right: 0;
	-webkit-transform: translateY(-50%);
	transform: translateY(-50%);
}

#booking {
	font-family: 'Raleway', sans-serif;
}

.booking-form {
	position: relative;
	max-width: 940px;

	width:100%;
	margin: auto;
	padding: 40px;
	overflow: hidden;
	background-image: url('./images/background.jpg');
	background-size:cover;
	border-radius: 5px;
	z-index: 20;
}

.booking-form::before {
	content: '';
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	top: 0;
	background: rgba(0, 0, 0, 0.7);
	z-index: -1;
}

.booking-form .form-header {
	text-align: center;
	position: relative;
	margin-bottom: 30px;
}

.booking-form .form-header h1 {
	font-weight: 700;
	text-transform: capitalize;
	font-size: 42px;
	margin: 0px;
	color: #fff;
}

.booking-form .form-group {
	position: relative;
	
}

.booking-form .form-control {
	background-color: rgba(255, 255, 255, 0.2);
	height: 60px;
	padding: 0px 25px;
	border: none;
	border-radius: 40px;
	color: #fff;
	-webkit-box-shadow: 0px 0px 0px 2px transparent;
	box-shadow: 0px 0px 0px 2px transparent;
	-webkit-transition: 0.2s;
	transition: 0.2s;
}

.booking-form .form-control::-webkit-input-placeholder {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form .form-control:-ms-input-placeholder {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form .form-control::placeholder {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form .form-control:focus {
	-webkit-box-shadow: 0px 0px 0px 2px #ff8846;
	box-shadow: 0px 0px 0px 2px #ff8846;
}

.booking-form input[type="text"].form-control {
	padding: 16px;
	width:250px; 
	float:left;
	
	margin-right:30px;
}

.booking-form input[type="text"].form-control:invalid {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form input[type="text"].form-control+.form-label {
	opacity: 1;
	top: 10px;
}

.booking-form input[type="date"].form-control {
	padding-top: 16px;
	width:250px; 
	float:left;
	
	margin-right:30px;
}

.booking-form input[type="date"].form-control:invalid {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form input[type="date"].form-control+.form-label {
	opacity: 1;
	top: 10px;
}

.booking-form input[type="email"].form-control {
	padding-top: 16px;
	width:250px; 
	float:left;
	
	margin-right:30px;
}

.booking-form input[type="email"].form-control:invalid {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form input[type="email"].form-control+.form-label {
	opacity: 1;
	top: 10px;
}



.booking-form select.form-control {
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	width:150px;
	float:left;
	margin-right:30px;
	
	
}

.booking-form select.form-control:invalid {
	color: rgba(255, 255, 255, 0.5);
}

.booking-form select.form-control+.select-arrow {
	position: absolute;
	right: 15px;
	top: 50%;
	-webkit-transform: translateY(-50%);
	transform: translateY(-50%);
	width: 32px;
	line-height: 32px;
	height: 32px;
	text-align: center;
	pointer-events: none;
	color: rgba(255, 255, 255, 0.5);
	font-size: 14px;
}

.booking-form select.form-control+.select-arrow:after {
	content: '\279C';
	display: block;
	-webkit-transform: rotate(90deg);
	transform: rotate(90deg);
}

.booking-form select.form-control option {
	color: #000;
}

.booking-form .form-label {
	position: absolute;
	top: -10px;
	left: 25px;
	opacity: 0;
	color: #ff8846;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 1.3px;
	height: 15px;
	line-height: 15px;
	-webkit-transition: 0.2s all;
	transition: 0.2s all;
}

.booking-form .form-group.input-not-empty .form-control {
	padding-top: 16px;
}

.booking-form .form-group.input-not-empty .form-label {
	opacity: 1;
	top: 10px;
}

.booking-form .submit-btn {
	color: #fff;
	background-color: #e35e0a;
	font-weight: 700;
	height: 60px;
	padding: 10px 30px;
	width: 100%;
	border-radius: 40px;
	border: none;
	text-transform: uppercase;
	font-size: 16px;
	letter-spacing: 1.3px;
	-webkit-transition: 0.2s all;
	transition: 0.2s all;
}

.booking-form .submit-btn:hover,
.booking-form .submit-btn:focus {
	opacity: 0.9;
}

.row{
	display: table;
    width: 100%; /*Optional*/
    table-layout: fixed; /*Optional*/
    border-spacing: 10px; /*Optional*/
}
		</style>

<body>
	<div id="booking" class="section">
		<div class="section-center">
			<div class="container">
				<div class="row">
					<div class="booking-form">
						<div class="form-header">
							<h1>Make your reservation</h1>
						</div>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								
								<input class="form-control" type="text" placeholder="First Name" name="f_name" required>
								
								</div>

								<div class="form-group">
								<input class="form-control" type="text" placeholder="Last Name" name="l_name" required>
								
								
							</div>
							<div class="row">
								
									<div class="form-group" style="margin-top:10px">
										<input class="form-control" type="date" name="dob" required>
										<span class="form-label">Date of birth</span>
									
										<input class="form-control" type="text" placeholder="Country" name="country" required>
								
									</div>
								
								
							</div>
							<div class="row">
								
									<div class="form-group">
										<select class="form-control" name="n_rooms" required>
											<option value="" selected hidden>No of rooms</option>
											<option>1</option>
											<option>2</option>
											<option>3</option>
										</select>
										
									
										<select class="form-control" name="n_adults" required>
											<option value="" selected hidden>No of adults</option>
											<option>1</option>
											<option>2</option>
											<option>3</option>
										</select>
										
										<select class="form-control" name="n_children" required>
											<option value="" selected hidden>No of children</option>
											<option>0</option>
											<option>1</option>
											<option>2</option>
										</select>
										
									</div>
								
							</div>
							<div class="row">
									<div class="form-group" style="margin-top:10px">
										<input class="form-control" type="email" placeholder="Enter your Email" name="cust_email" required>
										<span class="form-label">Email</span>

										<input class="form-control" type="text" placeholder="Passport No" name="passport_no" required>
									</div>
								
							</div>	
									<div class="form-group" style="margin-top:10px; width:250px">
										<input class="form-control" type="number" placeholder="Enter you Phone" name="cust_phone" maxlength="10" required>
										<span class="form-label">Phone</span>

										
										<select class="form-control" style= "width:250px; margin-top:20px; margin-bottom:20px " name="payment_type" required>
											<option value="" selected hidden>Payment Method</option>
											<option>Cash</option>
											<option>Card</option>
											<option>Net Banking</option>
										</select>
									</div>
								
							
							
							<div class="form-btn">
		<input type="submit" class="submit-btn" name="add_booking" value="Book NOw">
	</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="js/jquery.min.js"></script>
	<script>
		$('.form-control').each(function () {
			floatedLabel($(this));
		});

		$('.form-control').on('input', function () {
			floatedLabel($(this));
		});

		function floatedLabel(input) {
			var $field = input.closest('.form-group');
			if (input.val()) {
				$field.addClass('input-not-empty');
			} else {
				$field.removeClass('input-not-empty');
			}
		}
	</script>
		
	<div><?php echo "{$total}" ?></div>
	

<div class="timer">
    <time id="countdown">Session will expire in 5:00</time>
</div>
			

</body>
<html>
<script src="js/jquery.min.js"></script>
	<script>
		$('.form-control').each(function () {
			floatedLabel($(this));
		});

		$('.form-control').on('input', function () {
			floatedLabel($(this));
		});

		function floatedLabel(input) {
			var $field = input.closest('.form-group');
			if (input.val()) {
				$field.addClass('input-not-empty');
			} else {
				$field.removeClass('input-not-empty');
			}
		}
	</script>
<script type="text/javascript">
	var seconds = 300;
    function secondPassed() {
        var minutes = Math.round((seconds - 30)/60),
        remainingSeconds = seconds % 60;

        if (remainingSeconds < 10) {
          remainingSeconds = "0" + remainingSeconds;
        }

        document.getElementById('countdown').innerHTML ="Session will expire in " + minutes + ":" + remainingSeconds;
        if (seconds == 0) {
          clearInterval(countdownTimer);
            //form1 is your form name
          window.location="search.php";
        } 
        else {
          seconds--;
        }
    }
    var countdownTimer = setInterval('secondPassed()', 1000);
</script>




<!--
	<div id="booking" class="section">
		<div class="section-center">
		<div class="container">
				
					<div class="booking-form">
						<div class="form-header">
							<h1>Make your reservation</h1>
						</div>
<form action="" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="form-group">
		<label for="f_name">First Name</label>
		<input type="text" va class="form-control" name="f_name" required>
	</div>
	<div class="form-group">
		<label for="l_name">Last Name</label>
		<input type="text" class="form-control" name="l_name" required>
	</div>
</div>
	
	<div class="form-group">
		<label for="cust_email">Email</label>
		<input type="email" class="form-control" name="cust_email" required>
	</div>
	<div class="form-group">
		<label for="cust_phone">Phone</label>
		<input type="text" class="form-control" name="cust_phone" minlength="10" maxlength="10" required>
	</div>
	<div class="form-group">
		<label for="country">Country</label>
		<input type="text" class="form-control" name="country" required>
	</div>
	<div class="form-group">
		<label for="dob">DOB</label>
		<input type="date" class="form-control" name="dob" required>
	</div>
	<div class="form-group">
		<label for="passport_no">Passport Number</label>
		<input type="text" class="form-control" name="passport_no" required>
	</div>
	<label for="payment_type">Payment Method</label>
	<select name="payment_type">
		<option value="cash">Cash</option>
		<option value="card">Card</option>
		<option value="netbanking">Netbanking</option>
	</select>


	<div class="form-btn">
								<button class="submit-btn">Book Now</button>
							</div>
-->