<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $this->has_header_logo() ) {
			$this->header_logo();
		} else {
			echo $this->get_title();
		}
		?>
		</td>
		<td class="shop-info">
            <!--			<div class="shop-name"><h3>--><?php //$this->shop_name(); ?><!--</h3></div>-->
            <!--			<div class="shop-address">--><?php //$this->shop_address(); ?><!--</div>-->
            <div class="order-status-paid">PAID</div>
		</td>
	</tr>
</table>

<!--<h1 class="document-type-label">-->
<?php //if( $this->has_header_logo() ) echo $this->get_title(); ?>
<!--</h1>-->

<?php do_action( 'wpo_wcpdf_after_document_label', $this->type, $this->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			 <h3><?php _e( 'BILL TO:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
			<?php do_action( 'wpo_wcpdf_before_billing_address', $this->type, $this->order ); ?>
			<?php $this->billing_address(); ?>
			<?php do_action( 'wpo_wcpdf_after_billing_address', $this->type, $this->order ); ?>
			<?php if ( isset($this->settings['display_email']) ) { ?>
			<div class="billing-email"><?php $this->billing_email(); ?></div>
			<?php } ?>
			<?php if ( isset($this->settings['display_phone']) ) { ?>
			<div class="billing-phone"><?php $this->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address shipping-address">
			<?php if ( isset($this->settings['display_shipping_address']) && $this->ships_to_different_address()) { ?>
			<h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
			<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->type, $this->order ); ?>
			<?php $this->shipping_address(); ?>
			<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->type, $this->order ); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $this->type, $this->order ); ?>
				<?php if ( isset($this->settings['display_number']) ) { ?>
				<tr class="invoice-number">
					<th><h3><?php _e( 'INVOICE NO.:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3></th>
					<td><h3 style="text-align: center"> <?php $this->invoice_number(); ?></h3></td>
				</tr>
				<?php } ?>
				<?php if ( isset($this->settings['display_date']) ) { ?>
				<tr class="invoice-date">
					<th><strong><?php _e( 'Invoice Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong></th>
					<td><?php $this->invoice_date(); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-number">
					<th><strong><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong></th>
					<td><?php $this->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><strong><?php _e( 'Order Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong></th>
					<td><?php $this->order_date(); ?></td>
				</tr>
				<tr class="payment-method">
					<th><strong><?php _e( 'Payment Method:', 'woocommerce-pdf-invoices-packing-slips' ); ?></strong></th>
					<td><?php $this->payment_method(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $this->type, $this->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>

<table class="order-details">
	<thead>
		<tr>
            <th>No.</th>
			<th class="product"><?php _e('Description', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e('Qty', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price" align="right"><?php _e('Unit Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
            <th align="right">Amount</th>
		</tr>
	</thead>
	<tbody>
		<?php $items = $this->get_order_items(); if( sizeof( $items ) > 0 ) :
            $num = 1;
            foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
			<td><strong><?php echo $num; ?></strong></td>
            <td class="product" style="width: 40%;">
				<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
				<span class="item-name"><?php echo $item['name']; ?></span>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
				<span class="item-meta"><?php echo $item['meta']; ?></span>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
			</td>
			<td class="quantity"><strong><?php echo $item['quantity']; ?></strong></td>
			<td class="price" align="right"><strong><?php echo $item['order_price']; ?></strong></td>
            <td align="right"><strong><?php echo $item['line_total']; ?></strong></td>
		</tr>
		<?php
            $num++;
            endforeach; endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders" colspan="3">
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php
                        $d = 0;
                        foreach( $this->get_woocommerce_totals() as $key => $total ) : ?>
						<tr class="<?php echo $key; ?> rm-border">
							<td class="no-borders"></td>
							<th class="description" style="text-align: right;"><?php echo $total['label']; ?></th>
							<td class="price" style="text-align: right;"><span class="totals-price"><?php echo $total['value']; ?></span></td>
						</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
			</td>
		</tr>
		<tr class="no-borders">
			<td class="no-borders" colspan="3">
				<div class="customer-notes">
					<h3><?php _e( 'DISCLAIMER:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
					<ul>
                        <li><strong>1.</strong> Goods sold are not returnable nor exchangeable.</li>
                        <li><strong>2.</strong> This is a computer-generated invoice. No signature is required.</li>
                    </ul>
				</div>				
			</td>
		</tr>
	</tfoot>
</table>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>

<?php if ( $this->get_footer() ): ?>
<div id="footer">
	<?php $this->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>