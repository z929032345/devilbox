<?php

//
// $_POST submit for sending a test email
//
if (isset($_GET['email']) && isset($_GET['subject']) && isset($_GET['message'])) {
	$mail = $_GET['email'];
	$subj = $_GET['subject'];
	$mess = $_GET['message'];
	mail($mail, $subj, $mess);
	header('Location: /mail.php');
	exit();
}

//
// Includes
//
require '../config.php';
require $VEN_DIR . DIRECTORY_SEPARATOR . 'Mail' . DIRECTORY_SEPARATOR .'Mbox.php';
require $VEN_DIR . DIRECTORY_SEPARATOR . 'Mail' . DIRECTORY_SEPARATOR .'mimeDecode.php';
require $LIB_DIR . DIRECTORY_SEPARATOR . 'Mail.php';
require $LIB_DIR . DIRECTORY_SEPARATOR . 'Sort.php';

//
// Setup Sort/Order
//

// Sort/Order settings
$defaultSort	= array('sort' => 'date', 'order' => 'DESC');
$allowedSorts	= array('date', 'subject', 'x-original-to');
$allowedOrders	= array('ASC', 'DESC');
$GET_sortKeys	= array('sort' => 'sort', 'order' => 'order');

// Get sort/order
$MySort = new \devilbox\Sort($defaultSort, $allowedSorts, $allowedOrders, $GET_sortKeys);
$sort = $MySort->getSort();
$order = $MySort->getOrder();

// Evaluate Sorters/Orderers
$orderDate	= '<a href="/mail.php?sort=date&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a>';
$orderTo	= '<a href="/mail.php?sort=x-original-to&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a>';
$orderSubj	= '<a href="/mail.php?sort=subject&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a>';

if ($sort == 'date') {
	if ($order == 'ASC') {
		$orderDate = '<a href="/mail.php?sort=date&order=DESC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-numeric-asc" aria-hidden="true"></i>';
	} else {
		$orderDate = '<a href="/mail.php?sort=date&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-numeric-desc" aria-hidden="true"></i> ';
	}
} else if ($sort == 'subject') {
	if ($order == 'ASC') {
		$orderSubj = '<a href="/mail.php?sort=subject&order=DESC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>';
	} else {
		$orderSubj = '<a href="/mail.php?sort=subject&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>';
	}
} else if ($sort == 'x-original-to') {
	if ($order == 'ASC') {
		$orderTo = '<a href="/mail.php?sort=x-original-to&order=DESC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>';
	} else {
		$orderTo = '<a href="/mail.php?sort=x-original-to&order=ASC#received"><i class="fa fa-sort" aria-hidden="true"></i></a> <i class="fa fa-sort-alpha-desc" aria-hidden="true"></i>';
	}
}


//
// Mbox Reader
//
$MyMbox = new \devilbox\Mail('/var/mail/mailtrap');

// If default sort is on, use NULL, so we do not have to sort the mails after retrieval,
// because they are being read in the default sort/order anyway
$sortOrderArr = $MySort->isDefault($sort, $order) ? null : array($sort => $order);
$messages = $MyMbox->get($sortOrderArr);

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php $FONT_AWESOME = TRUE; require '../include/head.php'; ?>
	</head>

	<body>
		<?php require '../include/navigation.php'; ?>

		<div class="container">
			<h1>Mail</h1>
			<br/>
			<br/>

			<div class="row">
				<div class="col-md-12">
					<h3>Send test Email</h3>
					<br/>
				</div>
			</div>


			<div class="row">
				<div class="col-md-12">

					<form class="form-inline">
						<div class="form-group">
							<label class="sr-only" for="exampleInputEmail1">Email to</label>
							<input name="email" type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter to email">
						</div>

						<div class="form-group">
							<label class="sr-only" for="exampleInputEmail2">Subject</label>
							<input name="subject" type="text" class="form-control" id="exampleInputEmail2" placeholder="Subject">
						</div>

						<div class="form-group">
							<label class="sr-only" for="exampleInputEmail3">Message</label>
							<input name="message" type="text" class="form-control" id="exampleInputEmail3" placeholder="Message">
						</div>

						<button type="submit" class="btn btn-primary">Send Email</button>
					</form>
					<br/>
					<br/>

				</div>
			</div>


			<div class="row">
				<div class="col-md-12">
					<h3 id="received">Received Emails</h3>
					<br/>
				</div>
			</div>



			<div class="row">
				<div class="col-md-12">
					<table class="table table-striped table-hover">
						<thead class="thead-inverse">
							<tr>
								<th>#</th>
								<th>Date <?php echo $orderDate;?></th>
								<th>To <?php echo $orderTo;?></th>
								<th>Subject <?php echo $orderSubj;?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($messages as $data): ?>
								<?php
									$message = $data['raw'];
									$structure = $data['decoded'];
								 ?>
								<tr id="<?php echo $data['num'];?>" class="subject">
									<td><?php echo $data['num'];?></td>
									<td>
										<?php echo date('H:i', strtotime($structure->headers['date']));?><br/>
										<small><?php echo date('Y-m-d', strtotime($structure->headers['date']));?></small>
									</td>
									<td><?php echo $structure->headers['x-original-to'];?></td>
									<td><?php echo $structure->headers['subject'];?></td>
								</tr>
								<tr></tr>
								<tr id="mail-<?php echo $data['num'];?>" style="display:none">
									<td></td>
									<td colspan="3">
										<pre><?php echo $message;?></pre>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>





		</div><!-- /.container -->

		<?php require '../include/footer.php'; ?>
		<script>
		$(function() {
			$('.subject').each(function() {
				$(this).click(function() {
					var id = ($(this).attr('id'));
					$('#mail-'+id).toggle();

				})
			})
			// Handler for .ready() called.
		});
		</script>
	</body>
</html>