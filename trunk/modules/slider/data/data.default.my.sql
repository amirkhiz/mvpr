INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'slider', 'Slider', 'This Module use for dynamic pictures for web page', 'slider/slider', 'sliders.png', '', NULL, NULL, NULL);

SELECT @moduleId := MAX(module_id) FROM module;

INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_add', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_insert', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_edit', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_update', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_delete', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_list', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_reorder', '', @moduleId);
INSERT INTO permission VALUES ({SGL_NEXT_ID}, 'slidermgr_cmd_reorderUpdate', '', @moduleId);

#member role perms
SELECT @permissionId := permission_id FROM permission WHERE name = 'slidermgr_cmd_list';
INSERT INTO role_permission VALUES ({SGL_NEXT_ID}, 2, @permissionId);
