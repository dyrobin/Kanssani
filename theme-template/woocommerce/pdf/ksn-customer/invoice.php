<?php global $wpo_wcpdf; ?>
<?php echo $wpo_wcpdf->export->order->is_paid()? '<div id="watermark">' . __('Paid', 'kanssani') . '</div>' : '' ?>
<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $wpo_wcpdf->get_header_logo_id() ) {
			$wpo_wcpdf->header_logo();
		} else {
			echo apply_filters( 'wpo_wcpdf_invoice_title', __( 'Invoice', 'kanssani' ) );
		}
		?>
		</td>
		<td class="shop-info">
			<div class="shop-name"><h3><?php $wpo_wcpdf->shop_name(); ?></h3></div>
			<div class="shop-address"><?php $wpo_wcpdf->shop_address(); ?></div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
<?php if( $wpo_wcpdf->get_header_logo_id() ) echo apply_filters( 'wpo_wcpdf_invoice_title', __( 'Invoice', 'kanssani' ) ); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<?php $wpo_wcpdf->billing_address(); ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_email']) ) { ?>
			<div class="billing-email"><?php $wpo_wcpdf->billing_email(); ?></div>
			<?php } ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_phone']) ) { ?>
			<div class="billing-phone"><?php $wpo_wcpdf->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address shipping-address">
			<?php if ( isset($wpo_wcpdf->settings->template_settings['invoice_shipping_address']) && $wpo_wcpdf->ships_to_different_address()) { ?>
			<h3><?php _e( 'Ship To:', 'kanssani' ); ?></h3>
			<?php $wpo_wcpdf->shipping_address(); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
				<?php if ( isset($wpo_wcpdf->settings->template_settings['display_number']) && $wpo_wcpdf->settings->template_settings['display_number'] == 'invoice_number') { ?>
				<tr class="invoice-number">
					<th><?php _e( 'Invoice Number:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->invoice_number(); ?></td>
				</tr>
				<?php } ?>
				<?php if ( isset($wpo_wcpdf->settings->template_settings['display_date']) && $wpo_wcpdf->settings->template_settings['display_date'] == 'invoice_date') { ?>
				<tr class="invoice-date">
					<th><?php _e( 'Invoice Date:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->invoice_date(); ?></td>
				</tr>
				<?php } ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Order Date:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->order_date(); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Payment Method:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->payment_method(); ?></td>
				</tr>
				<tr class="shipping-method">
					<th><?php _e( 'Shipping Method:', 'kanssani' ); ?></th>
					<td><?php $wpo_wcpdf->shipping_method(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product" colspan="2"><?php _e('Product', 'kanssani'); ?></th>
			<th class="unit-cost"><?php _e('Unit Cost', 'kanssani'); ?></th>
			<th class="quantity"><?php _e('Quantity', 'kanssani'); ?></th>
			<th class="price"><?php _e('Price', 'kanssani'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $items = $wpo_wcpdf->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order, $item_id ); ?>">
 			<td class="thumbnail"><?php if ( ! empty($item['thumbnail'])) echo $item['thumbnail']; ?></td>
 			<td class="product">
				<div class="item-name"><?php echo $item['name']; ?></div>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>
				<div class="item-meta">
					<?php echo $item['meta']; ?>
					<dl class="meta">
						<?php if( !empty( $item['sku'] ) ) : ?>
							<dt class="sku"><?php _e( 'SKU:', 'kanssani' ); ?></dt>
							<dd class="sku"><?php echo $item['sku']; ?></dd>
						<?php endif; ?>
					</dl>
				</div>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>
			</td>
			<td class="unit-cost"><?php echo $item['single_price']; ?></td>
			<td class="quantity"><?php echo $item['quantity']; ?></td>
			<td class="price"><?php echo $item['order_price']; ?></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders" colspan="3">				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php
						$totals = $wpo_wcpdf->get_woocommerce_totals();
						$order = $wpo_wcpdf->export->order;
						// obtain price part of shipping
						if ( $order->order_shipping != 0 ) {
							if ( preg_match( '%<span\b[^>]*>.*</span>%', $totals['shipping']['value'], $matches ) )
								$totals['shipping']['value'] = $matches[0];
						} else {
							unset( $totals['shipping'] );
						}
						// obtain price part and tax part of order_totol
						if ( preg_match( '/(.*)\s+\(([Ii]ncludes.*)\)/', $totals['order_total']['value'], $matches) ) {
							$totals['order_total']['value'] = $matches[1];
							$tax_note = $matches[2];
						} else {
							$tax_note = '';
						}
						// display as table
						foreach( $totals as $key => $total ) : ?>
							<tr class="<?php echo $key; ?>">
								<th class="description"><?php echo $total['label']; ?></th>
								<td class="price"><?php echo $total['value']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
				<?php  if ( ! empty($tax_note) ) : ?>
					<div class="tax-note"><?php echo $tax_note ?><</div>
				<?php endif; ?>
			</td>
		</tr>
	</tfoot>
</table>

<?php do_action( 'wpo_wcpdf_after_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<?php if ( $wpo_wcpdf->get_footer() ): ?>
<div id="footer">
	<?php $wpo_wcpdf->footer(); ?>
</div>
<?php endif; ?>