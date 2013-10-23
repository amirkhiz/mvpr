INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'category', 'Category', 'Created by Sina Saderi', '', '48/module_default.png', 'Sina Saderi', NULL, 'NULL', 'NULL');

SELECT @moduleId := MAX(module_id) FROM module;

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_add', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_insert', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_edit', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_update', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_delete', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_list', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_reorder', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'categorymgr_cmd_reorderUpdate', '', @moduleId);

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_add', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_insert', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_edit', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_update', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_delete', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_list', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_reorder', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'admincategorymgr_cmd_reorderUpdate', '', @moduleId);


#member role perms
SELECT @permissionId := permission_id FROM permission WHERE name = 'categorymgr_cmd_list';
INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 2, @permissionId);
