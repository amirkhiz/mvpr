/*==============================================================*/
/* Table: Product                                               */
/*==============================================================*/
CREATE TABLE IF NOT EXISTS `product` (
	`product_id` 		int(11) NOT NULL,
	`usr_id` 		int(11) DEFAULT NULL,
	`category_id` 		int(11) DEFAULT NULL,
	`title` 		varchar(255) DEFAULT NULL,
	`description` 		text,
	`meta_desc` 		text,
	`meta_keyword` 		text,
	`model` 		varchar(255) DEFAULT NULL,
	`sku` 			varchar(255) DEFAULT NULL,
	`price` 		float(15,2) DEFAULT NULL,
	`currency_id` 		int(11) NOT NULL,
	`tax` 			int(11) DEFAULT NULL,
	`quantity` 		int(11) DEFAULT NULL,
	`dim_l` 		int(11) NOT NULL,
	`dim_w` 		int(11) NOT NULL,
	`dim_h` 		int(11) NOT NULL,
	`dim_id` 		int(11) NOT NULL,
	`weight` 		int(11) NOT NULL,
	`weight_id` 		int(11) NOT NULL,
	`manufacturer_id` 	int(11) DEFAULT NULL,
	`seo` 			text,
	`image` 		varchar(255) DEFAULT NULL,
	`date_created` 		datetime DEFAULT NULL,
	`last_updated` 		datetime DEFAULT NULL,
	`status` 		tinyint(4) NOT NULL,
	`item_order` 		int(11) DEFAULT NULL,
	PRIMARY KEY (`product_id`)
);
