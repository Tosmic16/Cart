<?php
	
	session_start();
	$product_ids = array();
	//session_destroy();
		// check if form has been submitted
	if (filter_input(INPUT_POST, 'submit')) {
		//check if session exist
		if (isset($_SESSION['Shopping_cart'])) {
			//keep track of how many product are in the shopping cart
			$count = count($_SESSION['Shopping_cart']);
			//create sequrntial array for mappiing array keys to product id
			$product_ids = array_column($_SESSION['Shopping_cart'], 'id');

			if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
				$_SESSION['Shopping_cart'] [$count] = array(

				'id' => filter_input(INPUT_GET, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity')


			);
			}
			else{//product already exist, increase quantity
				//match array key to is of the product being added to cart
				for ($i=0; $i < count($product_ids) ; $i++) { 
					if ($product_ids[$i] == filter_input(INPUT_GET, 'id')) {
						//add item quantity to the existing product in the array
						$_SESSION['Shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
					}
				}
			}
		}
		//if session doesn't exist create first product with array key 0
		else
		{// create array using submitted form data, start frrom 0 and fill it with values
			$_SESSION['Shopping_cart'] [0] = array(

				'id' => filter_input(INPUT_GET, 'id'),
				'name' => filter_input(INPUT_POST, 'name'),
				'price' => filter_input(INPUT_POST, 'price'),
				'quantity' => filter_input(INPUT_POST, 'quantity')


			);

		}
	}
	if (filter_input(INPUT_GET, 'action') == 'delete') {
		//loop through all product in the shopping card until it matches with GET id variable
		foreach ($_SESSION['Shopping_cart'] as $key => $product) {
			if ($product['id'] == filter_input(INPUT_GET, 'id')) {
				//remove product from shopping cart when it matches with the GET id
				unset($_SESSION['Shopping_cart'][$key]);
			}
		}
			//reset session array keys so they match with $product_ids numeric array
		$_SESSION['Shopping_cart'] = array_values($_SESSION['Shopping_cart']);
	}
	
		?>
<!DOCTYPE html>
<html>
<head>
	<title>Shopping Cart</title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
			<link rel="stylesheet" type="text/css" href="cart.css">
</head>


			<body>
<div class="container">
	<div class="row">
		<?php  
	
$conn=	mysqli_connect('localhost','root','','cart');

$q = 'SELECT * FROM products ORDER by id ASC';
$res = mysqlI_query($conn, $q);

while ($row = mysqli_fetch_assoc($res)):
	
?>
<div class="col-sm-4 col-md-3">
	<div class="l-img">
	<form method="post" action="cart.php?action=add&id=<?php echo $row['id'] ?>">
		<div class="products">
			<img src="<?php echo $row['image'] ?>"  >
			<h4 class="text-info">
				<?php echo $row['name']; ?>
			</h4>

			<h4>
				<?php echo $row['price']; ?>
			</h4>

			<input type="text" name="quantity" class="form-control" value="1" />
				<input type="hidden" name="name" value="<?php echo $row['name']; ?>">
					<input type="hidden" name="price" value="<?php echo $row['price']; ?>">
					<input type="submit" name="submit" class="btn btn-info but" value="Add To Cart">

		</div>
	</form>
</div>
</div>
	
<?php  
	
	endwhile;
?>
	</div>
		<div class="clear:both"></div>
<br>
<div class="table-responsive">
	<table class="table">
	<tr><th colspan="5"><h3>Order Details</h3></th></tr>		
	
	<tr>
		<th width="38%">Product Name</th>
		<th width="10%">Quantity</th>
		<th width="20%">Price</th>
		<th width="15%">Total</th>
		<th width="5%">Action</th>
	</tr>
	<?php
		if (!empty($_SESSION['Shopping_cart'])):
			
			$total = 0;
			foreach ($_SESSION['Shopping_cart'] as $key => $product):
				?>
				<tr>
					<td><?php echo $product['name'] ?></td>
					<td><?php echo $product['quantity'] ?></td>
					<td><?php echo $product['price'] ?></td>
					<td><?php echo number_format( $product['quantity'] * $product['price'], 2); ?> </td>
				<td>
				<a href="cart.php?action=delete&id=<?php echo $product['id']; ?>">
					<div class="btn-danger">Remove</div>

				</a>

</td>
</tr>
				<?php
$total = $total + ($product['quantity'] * $product['price']);
endforeach; 
	?>
	<tr>
		<td colspan="3" align="right">Total</td>
		<td align="right"># <?php echo number_format($total, 2); ?></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5">
			<?php
if (isset($_SESSION['Shopping_cart'])):
	if (count($_SESSION['Shopping_cart']) > 0):

		?>
		<a href="#" class="button" >Checkout</a>
	<?php endif; endif;  ?>
			
		</td>
	</tr>
<?php
 endif;
?>
</div>
</table>
</div>	
</div>

</body>
</html>