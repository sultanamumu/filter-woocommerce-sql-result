<style>
td { padding:0 5px; }
td, th {  border:thin solid #CCC; }
table {  border-collapse:collapse; }
</style>

<?php
$start_date = "2021-05-10 0:00:00";
$end_date = "2021-05-30 11:59:59";

$orders = wc_get_orders( array(
				'status' => array( 'wc-completed' ), //('wc-completed', 'wc-processing','on-hold')
				'date_created' => $start_date.'...'.$end_date,
				'numberposts' => -1
		  ) );

?>


<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>Order Date</td>
      <td>Order Number</td>
      <td>Customer Name</td>
      <td>City/Country</td>
      <td>Seller Name</td>
      <td>Seller ID</td>
      <td>Order Status</td>
      <td>Payment Mode</td>
      <td>Commission</td>
      <td>Stripe Fee</td>
      <td>Currency</td>
      <td>Value ex Tax</td>
      <td>Tax</td>
      <td>Tax Rate</td>
      <td>Shipping</td>
      <td>Coupon</td>
      <td>Total Amount</td>
    </tr>
	<?php

	// Loop through each WC_Order object
	foreach( $orders as $order ){
		
		$order_data = $order->get_data();
		
		$dokan_data = $wpdb->get_row( "SELECT *
			FROM {$wpdb->prefix}dokan_orders
			LEFT JOIN {$wpdb->prefix}usermeta ON {$wpdb->prefix}usermeta.user_id = {$wpdb->prefix}dokan_orders.seller_id && {$wpdb->prefix}usermeta.meta_key='dokan_store_name'
			WHERE {$wpdb->prefix}dokan_orders.order_id='$order_data[id]'
		" );
		?>
        
		<tr>
		  <td><?php echo $order_data['date_created']->date('m/d/Y'); ?></td>
		  <td><?php echo $order_data['id']; ?></td>
          <td><?php echo $order_data['billing']['first_name'].' '.$order_data['billing']['last_name']; ?></td>
          <td><?php echo $order_data['billing']['city'].', '.$order_data['billing']['country']; ?></td>
          <td><?php echo $dokan_data->meta_value; ?></td>
          <td><?php echo $dokan_data->seller_id; ?></td>
          <td><?php echo $order_data['status']; ?></td>
          <td><?php echo $order_data['payment_method_title']; ?></td>
          <td><?php echo number_format((float)($dokan_data->order_total-$dokan_data->net_amount), 2, '.', ''); ?></td>
          <td>
			  <?php
			  $fee = 0;
			  
              foreach ($order->get_meta_data() as $object) {
                $object_array = array_values((array)$object);
                foreach ($object_array as $object_item) {
                  if ('dokan_gateway_fee' == $object_item['key']) {
                    $fee = $object_item['value'];
                    break;
                  }
                }
              }
			  
			  echo number_format((float)($fee), 2, '.', '');
              ?>
          </td>
          <td><?php echo $order_data['currency']; ?></td>
          <td><?php echo number_format((float)($order_data['total']-$order_data['total_tax']), 2, '.', ''); ?></td>
          <td><?php echo number_format((float)($order_data['total_tax']), 2, '.', ''); ?></td>
          <td> </td>
          <td><?php echo number_format((float)($order_data['shipping_total']), 2, '.', ''); ?></td>
          <td><?php echo number_format((float)($order_data['discount_total']), 2, '.', ''); ?></td>
          <td><?php echo number_format((float)($order_data['total']), 2, '.', ''); ?></td>
		</tr>
        <?php
	}
	
	?>
</table>