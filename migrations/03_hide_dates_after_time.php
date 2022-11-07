<?php

class HideDatesAfterTime extends Migration {

	public function up() {
        Config::get()->create("GEBAEUDEPLAENE_HIDE_DATES_AFTER_TIME", array(
            'value' => '43200',
            'type' => "integer",
            'range' => "global",
            'section' => "GebäudeplänePlugin",
            'description' => "Usually dates will get displayed as long as they last. But you can configure the seconds after the begin so that the dates will get hidden from the plans. 43200 = hide after twelve hourse."
        ));
	}

	public function down() {
        Config::get()->delete("GEBAEUDEPLAENE_HIDE_DATES_AFTER_TIME");
	}
}
