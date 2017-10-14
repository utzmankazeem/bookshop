<?php
		# for inserting into database
		function doAdminRegister($dbconn, $input) {

			# prepared statement
			$stmt = $dbconn->prepare("INSERT INTO admin(firstname, lastname, email, password, hash) VALUES(:fn, :ln, :e, :p, :h)");			

			#bind params
			$data = [
					':fn' => $input['fname'],
					':ln' => $input['lname'],
					':e' => $input['email'],
					':p' => $input['pword'],
					':h' => $input['password']
					];

			# execute prepared statement
			$stmt->execute($data);

			redirect("login.php?msg=Admin Successfully Created");

		}


		function doesEmailExist($dbconn, $email) {
			$result = false;

			$stmt = $dbconn->prepare("SELECT email FROM admin WHERE email=:e");

			#bind params
			$stmt->bindParam(":e", $email);
			$stmt->execute();

			# get number of rows returned
			$count = $stmt->rowCount();

			if($count > 0) {
				$result = true;
			}
			return $result;
		}


		function displayErrors($dummy, $what) {
					$result = "";
						
			if(isset($dummy[$what])) {
				
				$result = '<span class="err">'. $dummy[$what]. '</span>';

			}
					return $result; 
		}


		function adminLogin($dbconn, $enter) {
					
			$result = [];
			
			# prepared statement
			$statement = $dbconn->prepare("SELECT * FROM admin WHERE email=:em");
			
			# bind params
			$statement->bindParam(":em", $enter['email']);
			$statement->execute();

			$row = $statement->fetch(PDO::FETCH_ASSOC);
			
			$count = $statement->rowCount();

			if($count !== 1 || !password_verify($enter['password'], $row['hash'])){
					
			# error handler, so if this is false, handle it and exit no need for else
				redirect("login.php?msg=invalid email or password");
				exit();
			} else {
				$result[] = true;
				$result[] = $row;
			}
					
				return $result;
		}	


		function redirect($loca){
			header("Location: ".$loca);
		}

	

	function LoginCheck() {
		if(!isset($_SESSION['id']) && !isset($_SESSION['email'])) {

			header("Location:login.php");
		}
	}

	function insertCategory($dbconn, $inn) {

		# prepare statement
		$stmt = $dbconn->prepare("INSERT INTO categories(category_name, date_created) VALUES(:c, NOW())");

		# bind Params and execute statement
		$stmt->bindParam(":c", $inn['cat']);
		$stmt->execute();

	}


	function UploadFile($file, $name, $uploadDir) {
		$data = [];
	$rnd = rand (0000000000,9999999999);

	$strip_name = str_replace ("","",$file[$name]['name']);

	$filename = $rnd.$strip_name;
	$destination = $uploadDir .$filename;

	if (!move_uploaded_file($file[$name]['tmp_name'], $destination)){
	$data[] = false;
	} else {
	$data[] = true;
	$data[] = $destination;
	}

	return $data;
}


	function forProduct($dbconn, $dirty, $dest){

		$state = $dbconn->prepare("SELECT category_id FROM categories WHERE category_name=:ca");

		$state->bindParam(":ca", $dirty['category']);
		$state->execute();

		$row = $state->fetch(PDO::FETCH_ASSOC);

		$id = $row['category_id']; 

		$stmt = $dbconn->prepare("INSERT INTO books(title, author, cat_id, price, year, isbn, image_path, flag) 
													VALUES(:ti, :au, :cat, :pr, :yr, :is, :fi, :fg)");			

			#bind params
		$data = [
				':ti' => $dirty['title'],
				':au' => $dirty['author'],
				':cat' => $id,
				':pr' => $dirty['price'],
				':yr' => $dirty['year'],
				':is' => $dirty['isbn'],
				':fi' => $dest,
				':fg' => $dirty['flag']
				];

			$stmt->execute($data);
		}


	function viewProduct($dbconn){
				$result = "";

		$stmt = $dbconn->prepare("SELECT * FROM books");
		$stmt->execute();

	
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

					$bk_id = $row['book_id'];
					$title = $row['title'];
					$author = $row['author'];
					$price = $row['price'];
					$year = $row['year'];
					$isbn = $row['isbn'];

					$statement = $dbconn->prepare("SELECT category_name FROM categories WHERE category_id=:ci");
					
					$statement->bindParam(":ci", $row['category_id']);
					$statement->execute();
					$row1 = $statement->fetch(PDO::FETCH_ASSOC);

					$result .= '<tr><td>'.$row['title'].'</td>';
					$result .= '<td>'.$row['author'].'</td>';
					$result .= '<td>'.$row1['category_name'].'</td>';
					$result .= '<td>'.$row['price'].'</td>';
					$result .= '<td>'.$row['year'].'</td>';
					$result .= '<td>'.$row['isbn'].'</td>';
					$result .= '<td><img src="'.$row['image_path'].'" height="60" width="60"></td>';
					$result .= "<td><a href='edit.php?book_id=$bk_id'>edit</a></td>";
					$result .=	"<td><a href='delete_product.php?book_id=$bk_id'>delete</a></td></tr>";
				}

					return $result;
		
	}

	function viewCat($sr){
		$result = "";

		while($row = $sr->fetch(PDO::FETCH_ASSOC)){

			$cat_id = $row['category_id'];
			$cat_name = $row['category_name'];

			$result .= '<tr><td>'.$cat_id.'</td>';
			$result .= '<td>'.$row['category_name'].'</td>';
			$result .= "<td><a href='edit_cate.php?category_id=$cat_id'>edit</a></td>";
			$result .=	"<td><a href='delete_cate.php?category_id=$cat_id'>delete</a></td></tr>";
          		
		}
		return $result;
	}

	function delCat($dbconn, $what){
		$stmt = $dbconn->prepare("DELETE FROM categories WHERE category_id=:c");
									$stmt->bindParam(":c", $what);
									$stmt->execute();
									
				redirect("view_category.php");
	}

	function delPro($dbconn, $what){
		$stmt = $dbconn->prepare("DELETE FROM books WHERE book_id=:c");
									$stmt->bindParam(":c", $what);
									$stmt->execute();

						redirect("view_product.php");
	}

	function editCat($dbconn, $edible){

		$stmt = $dbconn->prepare("UPDATE categories SET category_name=:cn WHERE category_id=:ci");

		$data = [
					':cn'=> $edible['cat'],
					':ci'=> $edible['cid']
				];
		$stmt->execute($data);

		redirect("view_category.php");
	}

	function editPro($dbconn, $edible, $dest){

		$stmt = $dbconn->prepare("UPDATE books SET title=:ti, author=:au, cat_id=:ci, price=:pr, year=:yr, isbn=:is, image_path=:fp, 
								flag=:fl WHERE book_id=:b");

		$data = [
					':ti'=> $edible['til'],
					':au'=> $edible['auth'],
					':ci'=> $edible['cat'],
					':pr'=>	$edible['pri'],
					':yr'=> $edible['yer'],
					':is'=> $edible['bn'],
					':fp'=> $dest,
					':fl'=> $edible['ty'],
					':b'=> $edible['bk']

				];

		$stmt->execute($data);

		redirect("view_product.php");
	}


		function getBookByID($dbconn, $bookID) {
			$stmt = $dbconn->prepare("SELECT * FROM books WHERE book_id=:id");
			$stmt->bindParam(':id', $bookID);

			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
			return $row;
	}
		
	function doEditSelectCategory($dbconn,$catName){


						$statement = $dbconn->prepare("SELECT * FROM categories");
						$statement->execute();

						$result = "";
						while($row = $statement->fetch(PDO::FETCH_ASSOC)) { 

							$cat_id = $row['category_id'];
							$cat_name = $row['category_name'];

							#to skip the category_name chosen

							if($cat_name == $catName) { continue; }

						$result .= "<option value='$cat_id'>$cat_name</option>";

		}


		return $result;					
					
	}

		

	function getCategoryByID($dbconn, $catid){

			$stmt = $dbconn->prepare("SELECT * FROM categories WHERE category_id=:id");
			$stmt->bindParam(':id', $catid);

			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
		
			return $row;
	
	}

	function doesUserEmailExist($dbconn, $email) {
			$result = false;

			$stmt = $dbconn->prepare("SELECT email FROM users WHERE email=:e");

			#bind params
			$stmt->bindParam(":e", $email);
			$stmt->execute();

			# get number of rows returned
			$count = $stmt->rowCount();

			if($count > 0) {
				$result = true;
			}
			return $result;
		}

		function doUserRegister($dbconn, $input) {

			# hashing password
			$hash = password_hash($input['password'], PASSWORD_BCRYPT);

			# prepared statement
			$stmt = $dbconn->prepare("INSERT INTO users(firstname, lastname, email, username, password, hash) VALUES(:fn, :ln, :e, :ur, :p, :h)");			

			#bind params
			$data = [
					':fn' => $input['firstname'],
					':ln' => $input['lastname'],
					':e' => $input['email'],
					':ur' => $input['username'],
					':p' => $input['pword'],
					':h' => $hash
					];

			# execute prepared statement
			$stmt->execute($data);

				redirect("user_login.php?msg=You Have Been Successfully Registered");
		}

		function displayErrorsUser($dummy, $what) {
					$result = "";
						
			if(isset($dummy[$what])) {
				
				$result = '<p class="form-error">'. $dummy[$what]. '</p>';

			}
					return $result; 
		}


		function UserLogin($dbconn, $enter) {
					

			$result = [];

			
			# prepared statement
			$statement = $dbconn->prepare("SELECT user_id, username, hash FROM users WHERE email=:em");
			
			# bind params
			$statement->bindParam(":em", $enter['email']);
			$statement->execute();

			$row = $statement->fetch(PDO::FETCH_ASSOC);
			
			$count = $statement->rowCount();

			if($count !== 1 || !password_verify($enter['password'], $row['hash'])){
					
				redirect("user_login.php?msg=invalid email or password");
				exit();
			} else{
				$result[] = true;
				$result[] = $row;
			}
					
				return $result;
		}	

		function topSelling($dbconn, $cb) {

			$tim = "Top-Selling";

			$stmt = $dbconn->prepare("SELECT * FROM books WHERE flag=:gt");

			$stmt->bindParam(':gt', $tim);

			$stmt->execute();

			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$cb($row);
	}

		function trending($dbconn, $cb){
			 $trend = "Trending";

		     $stmt = $dbconn->prepare("SELECT * FROM books WHERE flag=:tr");

		     $stmt->bindParam(':tr', $trend);

		     $stmt->execute(); 

		     $cb($stmt); 

		}


		# function to view comment or review by a user
		function ViewReview($dbconn, $bookid) {

					$result = "";

			$stmt = $dbconn->prepare("SELECT * FROM review WHERE book_id=:bk");

			$stmt->bindParam(':bk', $bookid);

			$stmt->execute();

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$statement = $dbconn->prepare("SELECT firstname, lastname FROM users WHERE user_id=:di");
					
					$statement->bindParam(":di", $row['user_id']);
					$statement->execute();
					$row1 = $statement->fetch(PDO::FETCH_ASSOC);

					$fname = $row1['firstname'];
					$lname = $row1['lastname'];

					$f = substr($fname, 0, 1);
					$l = substr($lname, 0, 1);

		 $result .= '<li class="review">
         	 		<div class="avatar-def user-image">
            		<h4 class="user-init">'.$f.$l.'</h4>
         	 		</div>
         	 		<div class="info">
            		<h4 class="username">'.$fname." ".$lname.'</h4>
            		<p class="comment">'.$row['review'].'</p>
          			</div>
        			</li>';
			}
			return $result;
		}


		function insertIntoReview($dbconn, $userID, $bookid, $input){

			$stmt = $dbconn->prepare("INSERT INTO review(user_id, book_id, review, date) VALUES(:us, :bk, :re, now())");

				$data = [':us' => $userID,
						 ':bk' => $bookid,
						 're' => $input['review'],
						];
				$stmt->execute($data);
		}


		function addToCart($dbconn, $userID, $bookID, $input) {


			$stmt = $dbconn->prepare("INSERT INTO cart(quantity, user_id, book_id) VALUES(:qu, :ui, :bi)");

			$data = [ ':qu'=> $input['quantity'],
					  ':ui'=> $userID,
					  ':bi'=> $bookID,
					];
			$stmt->execute($data);
		}

		

	# function for editing items in cart
	function editCart($dbconn, $cart){

		$stmt = $dbconn->prepare("UPDATE cart SET quantity=:qy WHERE cart_id=:ci");

		$data = [
					':qy'=> $cart['qty'],
					':ci'=> $cart['cartid']
				];
		$stmt->execute($data);

		redirect("cart.php");
	}


	# function for deleting item in cart
	function delCart($dbconn, $cart) {

		$stmt = $dbconn->prepare("DELETE FROM cart WHERE cart_id=:c");
									$stmt->bindParam(":c", $cart);
									$stmt->execute();
									
				redirect("cart.php");
	}

	function insertIntoRecentlyViewed($dbconn, $userID, $bookID) {

		$statement = $dbconn->prepare("SELECT * FROM recentlyViewed WHERE book_id=:bk AND user_id=:ud");
		$statement->bindParam(':bk', $bookID);
		$statement->bindParam(':ud', $userID);
		$statement->execute();
		$count = $statement->rowCount();

			if($count == 0) {
		$stmt = $dbconn->prepare("INSERT INTO recentlyViewed(book_id, user_id) VALUES(:bi, :ui)");

		$data = [
					':bi'=>$bookID,
					':ui'=>$userID
				];
		
		$stmt->execute($data);
		}
	}

	function culNav($page){

		$curPage = basename($_SERVER['SCRIPT_FILENAME']);

		if($curPage == $page) {
			echo 'class="selected"';
		}
	}

	function firstPreview($dbconn) {
		$stmt = $dbconn->prepare("SELECT * FROM categories LIMIT 0, 1");
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_BOTH)[0];
		
		}

	function editProImage($dbconn, $input, $destination){

		$stmt = $dbconn->prepare("UPDATE books SET image_path=:fp WHERE book_id=:b");

		$data = [
					':fp'=> $destination,
					':b'=> $input['bk']
				];

		$stmt->execute($data);

	}

	function selectFromCart($dbconn, $userID){
		$stmt =$dbconn->prepare("SELECT * FROM cart WHERE user_id=:id");
        $stmt->bindParam(':id', $userID);
		$stmt->execute();

		return $stmt;
	}

?>