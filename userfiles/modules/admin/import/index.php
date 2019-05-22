<?php only_admin_access(); ?>
<script  type="text/javascript">
	mw.require("<?php print $config['url_to_module']; ?>js/upload-file.js");
    mw.require("<?php print $config['url_to_module']; ?>js/import.js");
</script>

<?php if (isset($params['backend'])): ?>
    <module type="admin/modules/info"/>
<?php endif; ?>

<div id="mw-admin-content" class="admin-side-content">

    <div class="mw_edit_page_default" id="mw_edit_page_left">

        <div class="mw-ui-btn-nav pull-left">
            <a href="javascript:;" onclick="mw.admin_backup.create_full('.mw_edit_page_right')" class="mw-ui-btn mw-ui-btn-notification">
                <i class="mw-icon-download"></i>&nbsp; <span><?php _e("Export content"); ?></span>
            </a>
        </div>
        
        <span id="mw_uploader" class="mw-ui-btn mw-ui-btn-info pull-right">
            <i class="mw-icon-upload"></i>&nbsp;
            <span><?php _e("Upload file"); ?></span>
        </span>

		<!-- Upload file notification loader -->
        <div id="mw_uploader_loading" class="mw-ui-btn mw-ui-btn-notification" style="display:none;"><?php _e("Uploading files"); ?></div>

        <div class="vSpace">&nbsp;</div>
    </div>

    <div class="mw_edit_page_right" style="padding: 20px 0;">
        <module type="admin/import/manage"/>
    </div>
    
</div>