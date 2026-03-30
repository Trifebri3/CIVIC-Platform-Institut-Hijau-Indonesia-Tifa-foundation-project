<?php

namespace Tests\Feature\Livewire\Participant;

use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileActivationTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('participant.profile-activation');

        $component->assertSee('');
    }
}
