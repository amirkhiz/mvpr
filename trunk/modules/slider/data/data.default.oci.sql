--
-- Dumping data for table module
--

INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'faq', 'سوال‌های متداول', 'با استفاده از ماژول سوال‌های متداول امکان ایجاد یک لیست از سوال‌هایی که به کررات پرسیده می‌شوند به همراه جواب‌هایشان برای قرارگرفتن در سایت وجود دارد.', 'faq/faq', 'faqs.png', '', NULL, NULL, NULL);

--
-- Dumping data for table permission
--

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_add', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_insert', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_edit', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_update', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_delete', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_list', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_reorder', '', (SELECT MAX(module_id) FROM module));
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'faqmgr_cmd_reorderUpdate', '', (SELECT MAX(module_id) FROM module));

-- guest role perms
-- INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 0, (SELECT permission_id FROM permission WHERE name = 'faqmgr_cmd_list'));

-- member role perms
INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 2, (SELECT permission_id FROM permission WHERE name = 'faqmgr_cmd_list'));