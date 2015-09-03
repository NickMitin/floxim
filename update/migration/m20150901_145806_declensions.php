<?php

use Floxim\Floxim\System\Fx as fx;

class m20150901_145806_declensions extends \Floxim\Floxim\System\Migration {

    // Run for up migration
    protected function up() {
        fx::db()->query('ALTER TABLE  `{{lang}}` ADD  `declension` TEXT NOT NULL');
        
        fx::db()->query('ALTER TABLE  `{{component}}` ADD  `declension_ru` TEXT NOT NULL');
        fx::db()->query('ALTER TABLE  `{{component}}` ADD  `declension_en` TEXT NOT NULL');
        
        $ru_decl = '{"singular":{"description":"\\u0415\\u0434\\u0438\\u043d\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0435 \\u0447\\u0438\\u0441\\u043b\\u043e","values":{"nom":{"name":"\\u0418\\u043c\\u0435\\u043d\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u0442\\u043e? \\u0427\\u0442\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044c","required":true},"gen":{"name":"\\u0420\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u0433\\u043e? \\u0427\\u0435\\u0433\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0438","required":true},"dat":{"name":"\\u0414\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u043c\\u0443? \\u0427\\u0435\\u043c\\u0443?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0438"},"acc":{"name":"\\u0412\\u0438\\u043d\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u0433\\u043e? \\u0427\\u0442\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044c","required":true},"inst":{"name":"\\u0422\\u0432\\u043e\\u0440\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u0435\\u043c? \\u0427\\u0435\\u043c?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044c\\u044e"},"prep":{"name":"\\u041f\\u0440\\u0435\\u0434\\u043b\\u043e\\u0436\\u043d\\u044b\\u0439","description":"\\u041e \\u043a\\u043e\\u043c? \\u041e \\u0447\\u0451\\u043c?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0438"}}},"plural":{"description":"\\u041c\\u043d\\u043e\\u0436\\u0435\\u0441\\u0442\\u0432\\u0435\\u043d\\u043d\\u043e\\u0435 \\u0447\\u0438\\u0441\\u043b\\u043e","values":{"nom":{"name":"\\u0418\\u043c\\u0435\\u043d\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u0442\\u043e? \\u0427\\u0442\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0438","required":true},"gen":{"name":"\\u0420\\u043e\\u0434\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u0433\\u043e? \\u0427\\u0435\\u0433\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0435\\u0439","required":true},"dat":{"name":"\\u0414\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u043c\\u0443? \\u0427\\u0435\\u043c\\u0443?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044f\\u043c"},"acc":{"name":"\\u0412\\u0438\\u043d\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u043e\\u0433\\u043e? \\u0427\\u0442\\u043e?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u0438","required":true},"inst":{"name":"\\u0422\\u0432\\u043e\\u0440\\u0438\\u0442\\u0435\\u043b\\u044c\\u043d\\u044b\\u0439","description":"\\u041a\\u0435\\u043c? \\u0427\\u0435\\u043c?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044f\\u043c\\u0438"},"prep":{"name":"\\u041f\\u0440\\u0435\\u0434\\u043b\\u043e\\u0436\\u043d\\u044b\\u0439","description":"\\u041e \\u043a\\u043e\\u043c? \\u041e \\u0447\\u0451\\u043c?","placeholder":"\\u041d\\u043e\\u0432\\u043e\\u0441\\u0442\\u044f\\u0445"}}}}';
        fx::db()->query(array(
            "UPDATE {{lang}} set `declension` = '%s' where lang_code = 'ru'",
            addslashes($ru_decl)
        ));
        
        fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044c\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044f\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u0435\\\\u0439\\"},\\"dat\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044e\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044e\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044f\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u0435\\\\u0439\\"},\\"inst\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u0435\\\\u043c\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044f\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u0435\\",\\"plural\\":\\"\\\\u043f\\\\u043e\\\\u043b\\\\u044c\\\\u0437\\\\u043e\\\\u0432\\\\u0430\\\\u0442\\\\u0435\\\\u043b\\\\u044f\\\\u0445\\"}}',
            2 => 'floxim.user.user',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u044b\\"},\\"gen\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0430\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u043e\\\\u0432\\"},\\"dat\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0443\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u044b\\"},\\"inst\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u043e\\\\u043c\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0435\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u043a\\\\u0441\\\\u0442\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.main.text',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0430\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u044b\\"},\\"gen\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u044b\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\"},\\"dat\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0435\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0443\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u044b\\"},\\"inst\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0435\\\\u0439\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0435\\",\\"plural\\":\\"\\\\u0441\\\\u0442\\\\u0440\\\\u0430\\\\u043d\\\\u0438\\\\u0446\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.main.page',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u044b\\"},\\"gen\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0430\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u043e\\\\u0432\\"},\\"dat\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0443\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u044b\\"},\\"inst\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u043e\\\\u043c\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0435\\",\\"plural\\":\\"\\\\u0440\\\\u0430\\\\u0437\\\\u0434\\\\u0435\\\\u043b\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.nav.section',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043a\\\\u043e\\\\u043d\\\\u0442\\\\u0435\\\\u043d\\\\u0442\\",\\"plural\\":\\"\\\\u043a\\\\u043e\\\\u043d\\\\u0442\\\\u0435\\\\u043d\\\\u0442\\\\u044b\\"},\\"gen\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"dat\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043a\\\\u043e\\\\u043d\\\\u0442\\\\u0435\\\\u043d\\\\u0442\\",\\"plural\\":\\"\\"},\\"inst\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"prep\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"}}',
            2 => 'floxim.main.content',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0444\\\\u043e\\\\u0442\\\\u043e\\",\\"plural\\":\\"\\"},\\"gen\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"dat\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0444\\\\u043e\\\\u0442\\\\u043e\\",\\"plural\\":\\"\\"},\\"inst\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"prep\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"}}',
            2 => 'floxim.media.photo',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u044f\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u044f\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0438\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0439\\"},\\"dat\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0435\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u044f\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u044e\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0438\\"},\\"inst\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0435\\\\u0439\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u044f\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u0438\\",\\"plural\\":\\"\\\\u043f\\\\u0443\\\\u0431\\\\u043b\\\\u0438\\\\u043a\\\\u0430\\\\u0446\\\\u0438\\\\u044f\\\\u0445\\"}}',
            2 => 'floxim.blog.publication',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u044b\\"},\\"gen\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0430\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u043e\\\\u0432\\"},\\"dat\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0443\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u044b\\"},\\"inst\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u043e\\\\u043c\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0435\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u043e\\\\u0435\\\\u043a\\\\u0442\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.corporate.project',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"gen\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"dat\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"acc\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"inst\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"prep\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"}}',
            2 => 'floxim.nav.classifier',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044c\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0438\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0435\\\\u0439\\"},\\"dat\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0438\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044f\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044c\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0438\\"},\\"inst\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044c\\\\u044e\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044f\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u0438\\",\\"plural\\":\\"\\\\u043d\\\\u043e\\\\u0432\\\\u043e\\\\u0441\\\\u0442\\\\u044f\\\\u0445\\"}}',
            2 => 'floxim.blog.news',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u0438\\\\u0432\\\\u044f\\\\u0437\\\\u043a\\\\u0430\\",\\"plural\\":\\"\\\\u043f\\\\u0440\\\\u0438\\\\u0432\\\\u044f\\\\u0437\\\\u043a\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"dat\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"acc\\":{\\"singular\\":\\"\\\\u043f\\\\u0440\\\\u0438\\\\u0432\\\\u044f\\\\u0437\\\\u043a\\\\u0443\\",\\"plural\\":\\"\\"},\\"inst\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"prep\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"}}',
            2 => 'floxim.main.linker',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0430\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u043e\\\\u0432\\"},\\"dat\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0443\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0438\\"},\\"inst\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u043e\\\\u043c\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0435\\",\\"plural\\":\\"\\\\u0442\\\\u0435\\\\u0433\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.nav.tag',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u0441\\\\u0441\\\\u044b\\\\u043b\\\\u043a\\\\u0430\\",\\"plural\\":\\"\\\\u0441\\\\u0441\\\\u044b\\\\u043b\\\\u043a\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u0441\\\\u0441\\\\u044b\\\\u043b\\\\u043a\\\\u0438\\",\\"plural\\":\\"\\"},\\"dat\\":{\\"singular\\":\\"\\\\u0441\\\\u0441\\\\u044b\\\\u043b\\\\u043a\\\\u0435\\",\\"plural\\":\\"\\"},\\"acc\\":{\\"singular\\":\\"\\\\u0441\\\\u0441\\\\u044b\\\\u043b\\\\u043a\\\\u0443\\",\\"plural\\":\\"\\"},\\"inst\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"},\\"prep\\":{\\"singular\\":\\"\\",\\"plural\\":\\"\\"}}',
            2 => 'floxim.nav.external_link',
          ));
          fx::db()->query(array (
            0 => 'update {{component}} set declension_ru = \'%s\' where keyword = \'%s\'',
            1 => '{\\"nom\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0438\\"},\\"gen\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0430\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u043e\\\\u0432\\"},\\"dat\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0443\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0430\\\\u043c\\"},\\"acc\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0438\\"},\\"inst\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u043e\\\\u043c\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0430\\\\u043c\\\\u0438\\"},\\"prep\\":{\\"singular\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0435\\",\\"plural\\":\\"\\\\u044f\\\\u0440\\\\u043b\\\\u044b\\\\u043a\\\\u0430\\\\u0445\\"}}',
            2 => 'floxim.nav.page_alias',
          ));
          
          fx::cache('meta')->flush();
    }

    // Run for down migration
    protected function down() {
        fx::db()->query('ALTER TABLE `{{lang}}` DROP `declension`');
        fx::db()->query('ALTER TABLE `{{component}}` DROP `declension_ru`');
        fx::db()->query('ALTER TABLE `{{component}}` DROP `declension_en`');
    }
}