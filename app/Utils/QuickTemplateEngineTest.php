<?php
/**
 * by stephan scheide
 */

namespace App\Utils;

use App\MyTestCase;

class QuickTemplateEngineTest extends MyTestCase
{

    public function testApplyToStringBasics()
    {
        $str = QuickTemplateEngine::create()
            ->withValue('domain', 'kingster.de')
            ->withValue('user', 'stephan')
            ->applyToString('hallo %user%@%domain%');
        $this->assertEquals('hallo stephan@kingster.de', $str);

        $str = QuickTemplateEngine::create()
            ->withValues(['domain' => 'mega.de','user' => 'judas'])
            ->applyToString('hallo %user%@%domain%');
        $this->assertEquals('hallo judas@mega.de', $str);

    }

    public function testApplyToString2()
    {
        $str = QuickTemplateEngine::create()
            ->withValue('domain', 'kingster.de')
            ->withValue('email', 'privat@%domain%')
            ->applyToString('schreibe an %email%');
        $this->assertEquals('schreibe an privat@kingster.de', $str);
    }

}
