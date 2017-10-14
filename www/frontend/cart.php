<?php
		
   
    # title
		$page_title = "Cart";

		# body id for css
		$body_id = "cart";

		# load db connection
		include '../includes/db.php';

		# load function
		include '../includes/functions.php';

		# include header
		include '../includes/user_header.php'; 

		if(isset($_SESSION['id'])){
      $uid = $_SESSION['id'];
    }


?>

 <div class="main">
    <table class="cart-table">
      <thead>
        <tr>
          <th><h3>Item</h3></th>
          <th><h3>Price</h3></th>
          <th><h3>Quantity</h3></th>
          <th><h3>Total</h3></th>
          <th><h3>Update</h3></th>
          <th><h3>Remove</h3></th>
        </tr>
      </thead>
      <tbody>
        <tr>
        		<?php

        		if(isset($_SESSION['id'])){

        			$data = selectFromCart($conn, $uid);

						  while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
				
						  $rowBook = getBookByID($conn, $row['book_id']);

        		?>

        		 <td><div class="book-cover" style="background: url('../<?php echo $rowBook['image_path'] ?>');
  										background-size: cover;
  										background-position: center;
  										background-repeat: no-repeat;"></div></td>

          <td><p class="book-price"><?php echo $rowBook['price'] ?></p></td>
          <td><p class="quantity"><?php echo $row['quantity'] ?></p></td>

          			                <?php $sub = substr($rowBook['price'], 1) ?>
          <td><p class="total"> <?php echo '$'.($sub * $row['quantity']) ?> </p></td>
          <td> 

            <?php include 'update.php'; ?>
          </td>
          <td>
            <a href="<?php echo "delete.php?cart_id=".$row['cart_id']; ?>" class="def-button remove-item">Remove Item</a>
          </td>
        </tr>
        		<?php } } ?>
     
      </tbody>
    </table>
    <div class="cart-table-actions">
      <button class="def-button previous">previous</button>
      <button class="def-button next">next</button>
      <div class="index">
        <a href="#"><p>1</p></a>
        <a href="#"><p>2</p></a>
        <a href="#"><p>3</p></a>
      </div>
      <a href="checkout.php"><button class="def-button checkout">Checkout</button></a>
    </div>
    
  </div>

   <?php
  		#footer
  		include '../includes/front_footer.php';
  ?>