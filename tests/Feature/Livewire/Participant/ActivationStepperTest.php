<?php

namespace Tests\Feature\Livewire\Participant;

use Livewire\Volt\Volt;
use Tests\TestCase;

class ActivationStepperTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('participant.activation-stepper');

        $component->assertSee('');
    }
}
