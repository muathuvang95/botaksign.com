<?php
/**
* Template Name: Botak Tools

*/

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<div class="change-specialist border border-primary m-4 p-4">
	<h1 class="my-2">Change specialist</h1>
	<form method="post" class="p-4">
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">User id old</label>
			<input type="text" name="id-old" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">User id new</label>
			<input type="text" name="id-new" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<select class="form-select" name="type" aria-label="Default select example">
				<option selected>Open this select menu</option>
				<option value="1">Get all user</option>
				<option value="2">Get all order</option>
				<option value="3">Change</option>
			</select>
		</div>
		<input type="hidden" name="change-specialist" value="change-specialist">
		<button class="btn btn-primary">Submit</button>
	</form>

	<?php
	global $wpdb;
	if(isset($_POST['change-specialist']) && $_POST['change-specialist'] == 'change-specialist' ) {
		$id_old = isset($_POST['id-old']) ? $_POST['id-old'] : '';
		$id_new = isset($_POST['id-new']) ? $_POST['id-new'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		switch ($type) {
			case '1':
				$query1 = "SELECT * FROM `wp_usermeta` WHERE `meta_key` LIKE 'specialist' AND `meta_value` LIKE " . $id_old;
				$result1 = $wpdb->get_results($query1);
				echo '<pre>';
				var_dump($result1);
				echo '<pre>';
				break;
			case '2':
				$query2 = "SELECT * FROM `wp_postmeta` WHERE `meta_key` LIKE '_specialist_id' AND `meta_value` LIKE " . $id_old;
				$result2 = $wpdb->get_results($query2);
				echo '<pre>';
				var_dump($result2);
				echo '<pre>';
				break;
			case '3':
				if($id_old && $id_new) {
					$wpdb->update(
						'wp_usermeta', 
						array(
							'meta_value'=>$id_new,
						),
						array(
							'meta_key'=> 'specialist',
							'meta_value'=> $id_old,
						)
					);
					$wpdb->update(
						'wp_postmeta', 
						array(
							'meta_value'=>$id_new,
						),
						array(
							'meta_key'=> '_specialist_id',
							'meta_value'=> $id_old,
						)
					);
				}
				break;
			
			default:
				// code...
				break;
		}
	}


	?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>