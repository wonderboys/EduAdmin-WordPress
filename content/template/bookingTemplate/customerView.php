		<div class="customerView">
			<h2><?php edu_e("Customer information"); ?></h2>
			<label>
				<div class="inputLabel">
					<?php edu_e("Customer name"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" required name="customerName" placeholder="<?php edu_e("Customer name"); ?>" value="<?php echo esc_attr($customer->CustomerName); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Org.No."); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerVatNo" placeholder="<?php edu_e("Org.No."); ?>" value="<?php echo esc_attr($customer->InvoiceOrgnr); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Address"); ?> 1
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress1" placeholder="<?php edu_e("Address"); ?> 1" value="<?php echo esc_attr($customer->Address1); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Address"); ?> 2
				</div>
				<div class="inputHolder">
					<input type="text" name="customerAddress2" placeholder="<?php edu_e("Address"); ?> 2" value="<?php echo esc_attr($customer->Address2); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal code"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCode" placeholder="<?php edu_e("Postal code"); ?>" value="<?php echo esc_attr($customer->Zip); ?>" />
				</div>
			</label>
			<label>
				<div class="inputLabel">
					<?php edu_e("Postal city"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="customerPostalCity" placeholder="<?php edu_e("Postal city"); ?>" value="<?php echo esc_attr($customer->City); ?>" />
				</div>
			</label>
			<div id="invoiceView" class="invoiceView" style="display: none;">
				<h2><?php edu_e("Invoice information"); ?></h2>
				<label>
					<div class="inputLabel">
						<?php edu_e("Customer name"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceName" placeholder="<?php edu_e("Customer name"); ?>" value="<?php echo esc_attr($customer->InvoiceName); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Address"); ?> 1
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceAddress1" placeholder="<?php edu_e("Address"); ?> 1" value="<?php echo esc_attr($customer->InvoiceAddress1); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Address"); ?> 2
					</div>
					<div class="inputHolder">
						<input type="text" name="invoiceAddress2" placeholder="<?php edu_e("Address"); ?> 2" value="<?php echo esc_attr($customer->InvoiceAddress2); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Postal code"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoicePostalCode" placeholder="<?php edu_e("Postal code"); ?>" value="<?php echo esc_attr($customer->InvoiceZip); ?>" />
					</div>
				</label>
				<label>
					<div class="inputLabel">
						<?php edu_e("Postal city"); ?>
					</div>
					<div class="inputHolder">
						<input type="text" name="invoicePostalCity" placeholder="<?php edu_e("Postal city"); ?>" value="<?php echo esc_attr($customer->InvoiceCity); ?>" />
					</div>
				</label>
			</div>
			<label>
				<div class="inputLabel">
					<?php edu_e("Invoice reference"); ?>
				</div>
				<div class="inputHolder">
					<input type="text" name="invoiceReference" placeholder="<?php edu_e("Invoice reference"); ?>" value="<?php echo esc_attr($customer->CustomerReference); ?>" />
				</div>
			</label>
			<label>
				<div class="inputHolder">
					<input type="checkbox" name="alsoInvoiceCustomer" value="true" onchange="eduBookingView.UpdateInvoiceCustomer();" />
					<?php edu_e("Use other information for invoicing"); ?>
				</div>
			</label>
		</div>