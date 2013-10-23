/*==============================================================*/
/* Table: Category                                               */
/*==============================================================*/
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL,
  `title`       int(11) NOT NULL,
  `parent_id`   int(11) NOT NULL,
  `image`       varchar(255) NOT NULL,
  `description` text NOT NULL,
  `metakeys`    varchar(255) NOT NULL,
  `order_id`    int(11) NOT NULL,
  `status`      int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
