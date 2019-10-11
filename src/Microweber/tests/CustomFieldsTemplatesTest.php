<?php

namespace Microweber\tests;

class CustomFieldsTemplatesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // set permission to save custom fields (normally available to admin users)
        mw()->database_manager->extended_save_set_permission(true);
    }

    public function testBootstrapTempalte()
    {
        $rel = 'module';
        $rel_id = 'layouts-' . rand(1111, 9999) . '-contact-form';
        $fields_csv_str = 'text, select, number, phone, website, email, fileupload, message';
        $fields_csv_array = explode(',', $fields_csv_str);

        $fields = mw()->fields_manager->make_default($rel, $rel_id, $fields_csv_str);
        foreach ($fields as $key => $field_id) {

            $option = array();
            $option['option_value'] = 'bootstrap3/index.php';
            $option['option_key'] = 'data-template';
            $option['option_group'] = $field_id;
            $save = save_option($option);

            $output = mw()->fields_manager->make($field_id);
            $field = mw()->fields_manager->get_by_id($field_id);

            $checkRow = false;
            if (strpos($output, 'class="col-md-12"') !== false) {
                $checkRow = true;
            }
            if (!$checkRow) {
                //   echo $field['type'] . PHP_EOL;
            }
            $this->assertEquals(true, $checkRow);

            $checkInputClass = false;
            if (strpos($output, 'class="form-control"') !== false) {
                $checkInputClass = true;
            }
            if (!$checkInputClass) {
                //  echo $field['type'] . PHP_EOL;
            }

            $this->assertEquals(true, $checkInputClass);

            $checkFormGroup = false;
            if (strpos($output, 'class="form-group"') !== false) {
                $checkFormGroup = true;
            }
            if (!$checkFormGroup) {
                // echo $field['type'] . PHP_EOL;
            }
            $this->assertEquals(true, $checkFormGroup);

        }
    }

    public function testMwUiTempalte()
    {
        $rel = 'module';
        $rel_id = 'layouts-' . rand(1111, 9999) . '-contact-form';
        $fields_csv_str = 'text, select, number, phone, website, email, fileupload, message';
        $fields_csv_array = explode(',', $fields_csv_str);

        $fields = mw()->fields_manager->make_default($rel, $rel_id, $fields_csv_str);
        foreach ($fields as $key => $field_id) {

            $option = array();
            $option['option_value'] = 'mw-ui/index.php';
            $option['option_key'] = 'data-template';
            $option['option_group'] = $field_id;
            $save = save_option($option);

            $output = mw()->fields_manager->make($field_id);
            $field = mw()->fields_manager->get_by_id($field_id);

            $checkRow = false;
            if (strpos($output, 'class="mw-flex-col-md-12"') !== false) {
                $checkRow = true;
            }
            if (!$checkRow) {
                // echo $field['type'] . PHP_EOL;
            }

            $this->assertEquals(true, $checkRow);


            $checkInputClass = false;
            if (strpos($output, 'class="mw-ui-field"') !== false) {
                $checkInputClass = true;
            }
            if (!$checkInputClass) {
                //   echo $field['type'] . PHP_EOL;
            }
            $this->assertEquals(true, $checkInputClass);

            $checkFormGroup = false;
            if (strpos($output, 'class="mw-ui-controls"') !== false) {
                $checkFormGroup = true;
            }
            if (!$checkFormGroup) {
                // echo $field['type'] . PHP_EOL;
            }

            $this->assertEquals(true, $checkFormGroup);
        }
    }
}