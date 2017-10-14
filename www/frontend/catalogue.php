<?php
		
    //session_start();
    # title
		$page_title = "Catalogue";

		# body id for css
		$body_id = "catalogue";

		# load db connection
		include '../includes/db.php';

		# load function
		include '../includes/functions.php';

		# include header
		include '../includes/user_header.php'; 

    include '../includes/class.Pagination.php';

    # include RecentlyViewed Class
    include '../includes/class.RecentlyViewed.php';

    # instantiating object for recently viewed
    $recent = new RecentlyViewed();

    if(isset($_SESSION['id'])){
      $uid = $_SESSION['id'];
    }

    $paginate = new Pagination();


?>

  <?php
  
      #include category list
      include 'category.php';
  ?> 
  <!-- main content starts here -->
  <div class="main">
    <div class="main-book-list horizontal-book-list">
      <ul class="book-list">
        

        		<?php 
            if(isset($_GET['cat_id'])) {
              $catID = $_GET['cat_id'];
            } else {
            
              $catID = firstPreview($conn);
            }

            if(isset($_GET['p'])) {

              $page = $_GET['p'];
            }

            else{
              $page = $paginate->all($conn, $catID);
            }

              if(isset($_GET['s'])) {

                $start = $_GET['s'];
                
              } 
              else {
                $start = 0;
                
              }
        			$stmt = $conn->prepare("SELECT * FROM books WHERE cat_id=:id LIMIT :start, 2");
					    $stmt->bindParam(':id', $catID); 
              $j = (int)$start;
              $stmt->bindParam(':start', $j, PDO::PARAM_INT);
					    $stmt->execute(); 
             // $stmt = $paginate->query($conn, $catID, $page);

				      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>

				<li class="book">

          <a href="<?php echo "preview.php?book_id=".$row['book_id'] ?>"><div class="book-cover"  style="background: url('../<?php echo $row['image_path']; ?>');
                      background-size: cover;
                      background-position: center;
                      background-repeat: no-repeat;"></div></a>
          <div class="book-price"><p><?php echo $row['price']; ?></p></div>

        </li>
        	<?php } ?>

      </ul>
      <div class="actions">
          <?php
            $curpage = ceil($start / 2) + 1;
            $start = ($curpage - 1) * 2;
            $next = $start + 2;
            $prev = $start - 2;
            if($start > 0 ) {
              echo '<a href="catalogue.php?p='.$page.'&s='.$prev.'&cat_id='.$catID.'"><button class="def-button next">Prev</button></a>';
            }
            
            if($curpage != $page) {
              echo '<a href="catalogue.php?p='.$page.'&s='.$next.'&cat_id='.$catID.'"><button class="def-button next">Next</button></a>';
            }
          ?>
        <!-- <button class="def-button previous">Previous</button>
        <button class="def-button next">Next</button> -->
      </div>
    </div>
    <div class="recently-viewed-books horizontal-book-list">
      <h3 class="header">&nbsp;&nbsp;&nbsp;&nbsp; Recently Viewed</h3>
      <ul class="book-list">
        <div class="scroll-back"></div>
        <div class="scroll-front"></div>

         <?php
              # include recently viewed books
              include 'recentlyviewed.php';
          ?>
      </ul>
    </div>
    
  </div>

  <?php
  		include '../includes/front_footer.php';
  ?>