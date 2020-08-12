<?php must_have_access() ?>

<?php
$from_live_edit = false;
if (isset($params["live_edit"]) and $params["live_edit"]) {
    $from_live_edit = $params["live_edit"];
}
?>

<?php if (isset($params['backend'])): ?>
    <module type="admin/modules/info"/>
<?php endif; ?>

<div class="card style-1 mb-3 <?php if ($from_live_edit): ?>card-in-live-edit<?php endif; ?>">
    <div class="card-header">
        <?php $module_info = module_info($params['module']); ?>
        <h5>
            <img src="<?php echo $module_info['icon']; ?>" class="module-icon-svg-fill"/> <strong><?php echo $module_info['name']; ?></strong>
        </h5>
    </div>

    <div class="card-body pt-3">
        <script type="text/javascript">
            mw.require("<?php print mw_includes_url(); ?>css/wysiwyg.css");
            mw.require('icon_selector.js');
            mw.require('prop_editor.js');
            mw.require('module_settings.js');
        </script>
        <?php
        $settings = get_option('settings', $params['id']);

        if ($settings == false) {
            if (isset($params['settings'])) {
                $settings = $params['settings'];
            }
        }

        $defaults = array(
            'title' => '',
            'id' => 'accordion-' . uniqid(),
            'icon' => ''
        );

        $json = json_decode($settings, true);
        if (isset($json) == false or count($json) == 0) {
            $json = array(0 => $defaults);
        }

        $data = array();
        $count = 0;
        foreach ($json as $slide) {
            $count++;
            if (!isset($slide['id'])) {
                $slide['id'] = 'accordion-' . $params['id'] . '-' . $count;
            }
            array_push($data, $slide);
        }
        ?>

        <script>
            $(window).on('load', function () {
                this.accordionSettings = new mw.moduleSettings({
                    element: '#accordion-settings',
                    header: '<i class="mw-icon-drag"></i> <span data-bind="title">Move</span> <a class="pull-right" data-action="remove"><i class="mw-icon-close"></i></a>',
                    data: <?php print json_encode($data); ?>,
                    schema: [
                        {
                            interface: 'text',
                            label: ['Title'],
                            id: 'title'
                        },
                        {
                            interface: 'icon',
                            label: ['Icon'],
                            id: 'icon'
                        },
                        {
                            interface: 'hidden',
                            label: [''],
                            id: 'id',
                            value: function () {
                                return 'tab-' + mw.random();
                            }
                        }
                    ]
                });
                $(accordionSettings).on("change", function (e, val) {
                    $("#settingsfield").val(accordionSettings.toString()).trigger("change")
                });
            })
        </script>

        <nav class="nav nav-pills nav-justified btn-group btn-group-toggle btn-hover-style-3">
            <a class="btn btn-outline-secondary justify-content-center active" data-toggle="tab" href="#settings"><i class="mdi mdi-cog-outline mr-1"></i> <?php print _e('Settings'); ?></a>
            <a class="btn btn-outline-secondary justify-content-center" data-toggle="tab" href="#templates"><i class="mdi mdi-pencil-ruler mr-1"></i> <?php print _e('Templates'); ?></a>
        </nav>

        <div class="tab-content py-3">
            <div class="tab-pane fade show active" id="settings">
                <!-- Settings Content -->
                <div class="module-live-edit-settings module-accordion-settings">
                    <input type="hidden" class="mw_option_field" name="settings" id="settingsfield"/>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary btn-rounded" onclick="accordionSettings.addNew()"><i class="mdi mdi-plus"></i> <?php _e('Add new'); ?></button>
                    </div>

                    <div id="accordion-settings"></div>
                </div>
            </div>

            <div class="tab-pane fade" id="templates">
                <module type="admin/modules/templates"/>
            </div>
        </div>
    </div>
</div>