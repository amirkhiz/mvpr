/*==============================================================*/
/* Table: slider                                                   */
/*==============================================================*/
create table if not exists slider
(
   slider_id                      int                            not null,
   date_created                   datetime,
   last_updated                   datetime,
   image1		                  varchar(255),
   title                          varchar(255),
   item_order                     int,
   primary key (slider_id)
);
