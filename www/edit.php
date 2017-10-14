<?php


			session_start();
		# title
		$page_title = "Edit Product";

		# load db connection
		include 'includes/db.php';

		# load function
		include 'includes/functions.php';

		# include header
		include 'includes/view.php';

		$flag = ['Trending', 'Top-Selling', 'Recently-Viewed-Items'];

		LoginCheck();

			

			if(isset($_GET['book_id'])){
				$bkid = $_GET['book_id'];
			}
			$item = getBookByID($conn, $_GET['book_id']);

			$cat = getCategoryByID($conn, $item['cat_id']);



		if(array_key_exists('edit', $_POST)){
	

				if(empty($_POST['til'])) {
					$errors = "Enter new title";
				}
				if(empty($_POST['auth'])) {
					$errors = "Enter new Author";
				}
				if(empty($_POST['cat'])) {
					$errors = "Select New Category";
				}
				if(empty($_POST['pri'])) {
					$errors = "Enter new Price";
				}
				if(empty($_POST['yer'])) {
					$errors = "Enter new Year of publication";
				}
				if(empty($_POST['bn'])) {
					$errors = "Enter new ISBN";
				}

				if(empty($_POST['ty'])) {
					$errors = "Select Type";
				}

				if(empty($errors)){

				$clean = array_map('trim', $_POST);
				$clean['bk'] = $bkid;
				editPro($conn, $clean);

			}
		}
?>
		<div class="wrapper">
		<h1 id="register-label">Edit Product</h1>
		<hr>
		<div id="stream">

				

			

				<form id="register" action = "<?php echo "edit.php?book_id=".$_GET['book_id']; ?>" method ="POST">

				<div>
				<label>Book Title</label>
				<input type="text" name="til" placeholder="Book Title" value="<?php echo $item['title']; ?>">
				</div>

				<div>
				<label>Author</label>
				<input type="text" name="auth" placeholder="Book Author" value="<?php echo $item['author']; ?>">
				</div>

				<div>
				<label>Select Category</label>
				<select name="cat">
					<option value="<?php echo $cat['category_id']; ?>"> <?php echo $cat['category_name']; ?> </option>
					<?php 
						$sel = doEditSelectCategory($conn, $cat['category_name']);
						echo $sel;
					?>
				</select>
				</div>

				<div>
				<label>Price</label>
				<input type="text" name="pri" placeholder="Price" value="<?php echo $item['price']; ?>">
				</div>

				<div>
				<label>Year of Production</label>
				<input type="text" name="yer" placeholder="Year" value="<?php echo $item['year']; ?>">
				</div>

				<div>
				<label>ISBN</label>
				<input type="text" name="bn" placeholder="ISBN" value="<?php echo $item['isbn']; ?>">
				</div>

					<div>
				<label>Types</label>
							<select name="ty">
									
									<?php foreach ($flag as $fl) { ?>
									<option value="<?php echo $fl; ?>"> <?php echo $fl; ?> </option>
									<?php } ?>
							</select>
					</div>

				<input type="submit" name="edit" value="edit">

				</form>
					<h4 class="jumpto">Upload new image:
					 <a href="<?php echo "imageEdit.php?book_id=".$item['book_id'];?>">Edit</a></h4>
				</div>
				</div>
	<?php
		# inlude footer
		include 'includes/footer.php';
	?>