<?php
$this->startSetup();
$this->run("
ALTER TABLE `imageslider_images` ADD image_text varchar(1000);
");
$this->endSetup();
?>
